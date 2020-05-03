#!/usr/bin/env perl

use strict;
use warnings;

use IO::Socket::INET;
use IO::Select;
use DateTime;
use JSON;
use Try::Tiny;

use constant {
    LOCAL_HOST => '0.0.0.0',
    LOCAL_PORT => 10000,
};

# Disable output buffering
$| = 1;

sub create_server() {
    my $socket = new IO::Socket::INET(
        LocalHost => LOCAL_HOST,
        LocalPort => LOCAL_PORT,
        Proto     => 'tcp',
        Listen    => 5,
        Reuse     => 1
    ) or die "cannot create socket $!\n";

    return $socket;
}

sub send_tcp_data {
    my ( $sock, $req ) = @_;

    eval { $sock->send($req); };
    if ($@) {
        return '';
    }
}

sub recv_tcp_data {
    my ($sock) = @_;

    my $sock_data = '';
    $sock->recv( $sock_data, 2048 );

    return $sock_data;
}

sub log_msg {
    my ( $msg, @args ) = @_;

    printf "[%s] $msg\n", DateTime->now(), @args;
}

sub exec_shell_cmd {
    my $command = join ' ', @_;
    my ( $ec, $res ) = ( $? >> 8, $_ = qx{$command 2>&1} );

    return wantarray ? ($ec, $res) : $res;
}

sub restart_service {
    my ($args) = @_;

    my $service_name = $args->{service};
    my $mode         = $args->{mode};

    `docker service update --force ${mode}_${service_name}`;

    return JSON::encode_json( { success => 1, } );
}

sub update_stack {
    my ($args) = @_;

    my $host_root       = $ENV{HOST_ROOT};
    my $current_version = $args->{current_version};
    my $new_version     = $args->{new_version};
    my $mode            = $args->{mode};

    my ( $status, $result ) = exec_shell_cmd( "$host_root/films/bin/film", 'update', $current_version, $new_version );
    if ( !$status ) {
        log_msg($result);
    }

    my $cid = exec_shell_cmd('docker container ls --filter "name=mainapp" --format "{{.ID}}"');
    chomp($cid);
    exec_shell_cmd( 'docker exec', $cid, 'rm -f /tmp/web_nologin /tmp/upgrade.lock' );
}

sub restart_stack {
    my ($args) = @_;

    my $mode      = $args->{mode};
    my $host_root = $ENV{HOST_ROOT};

    `$host_root/films/bin/film restart`;
}

my $server = create_server();
my $select = IO::Select->new($server);

my %actions = (
    restart_service => \&restart_service,
    update          => \&update_stack,
    reboot          => \&restart_stack,
);

log_msg( "started listeting on %s:%s", LOCAL_HOST, LOCAL_PORT );

while (1) {
    foreach my $sock ( my @ready = $select->can_read(0.5) ) {
        if ( $sock == $server ) {
            my $connection = $sock->accept();

            my $peerhost = $connection->peerhost();
            my $peerport = $connection->peerport();

            log_msg( "got connection from %s:%s", $peerhost, $peerport );

            my $json = recv_tcp_data($connection);

            my $resp = try {
                my $data = JSON::decode_json($json);

                my $command = $data->{command};
                my $args    = $data->{args};

                log_msg( "received command '%s', processing ...", $command );

                $actions{$command}->($args);
            }
            catch {
                log_msg( "invalid request from %s:%s", $peerhost, $peerport );

                JSON::encode_json( { success => 0, } );
            };

            log_msg( "sending response to %s:%s", $peerhost, $peerport );

            send_tcp_data( $connection, $resp );
        } ## end if ( $sock == $server )
    } ## end foreach my $sock ( my @ready...)
} ## end while (1)
