#!/usr/bin/env bash

set -o errexit

SELF=$(basename $0)

: ${HOST_FILM_ROOT:=$(realpath "$(dirname $0)/../..")}
: ${FILM_ROOT:=/data/diplom}
: ${FILM_AUX_SERVICE:=film-aux}
: ${FILM_REGISTRY:=hub.docker.com}
: ${FILM_REGISTRY_PROTO:=https}
: ${FILM_IMAGE:=/monoann/film}
: ${FILM_TAG:=latest}
: ${FILM_VERSION:="$(date +%Y%m%d)0"}

run_cmd() {
    local cmd=${1:?"command is required"}
    local subcmd=${2:?$(eval "${cmd}_help")}
    shift 2
    debug "running ${cmd}_${subcmd} (ARGV: $@)..."

    local cmdfunc=$(trcmd "${cmd}_${subcmd}")

    __setup_cmdenv ${cmdfunc}
    ${cmdfunc} $@
    local ec=$?
    __teardown_cmdenv ${cmdfunc} || true

    return ${ec}
}


film_help() {
    {
        printf "Usage:\n"
        printf "\t${SELF} film [build|deploy|help][ ARGS] - commands related to building images\n\n"
        printf "Commands:\n"
        printf "\t${SELF} film build\n"
        printf "\t${SELF} film deploy devel|production\n"
        printf "\t${SELF} film help\n"
    } >&2
}

assert_help() {
    {
        printf "Usage:\n"
        printf "\t${SELF} assert <WHAT>[ ARGS] - commands related to assertions\n\n"
        printf "Commands:\n"
        printf "\t${SELF} assert binary <PROGRAM> <BAILOUT>\n"
    } >&2
}

deploy_help() {
    {
        printf "Usage:\n"
        printf "\t${SELF} deploy <WHAT>[ ARGS] - commands related to deployments\n\n"
        printf "Commands:\n"
        printf "\t${SELF} deploy swarm devel|prodution\n"
    } >&2
}

ct_help() {
    {
        printf "Usage:\n"
        printf "\t${SELF} ct <COMMAND>[ <ARGS>] - commands that shall be run in a container\n\n"
        printf "Commands:\n"
        printf "\t${SELF} ct exec <CID> <COMMAND>[ <ARGS>]\n"
    } >&2
}

service_help() {
    {
        printf "Usage:\n"
        printf "\t${SELF} service <SERVICE> <COMMAND>[ <ARGS>] - a wrapper around ct\n\n"
        printf "Commands:\n"
        printf "\t${SELF} service exec <SERVICE> <COMMAND>[ <ARGS>]\n"
        printf "\t${SELF} service list-film-services\n"
    } >&2
}


build_config() {
    local tpl=${1:?"template is required"}
    eval "cat <<EOF
                $(cat ${tpl})
EOF" | sed -re '1{s/^[[:space:]]*//;}'
}

film_pull() {
    assert_binary "docker"
    docker pull "monoann/film:${FILM_TAG}"
}

make_config() {
    local mode=${1:?$(assert_mode)}
    local tpl=${2:?"config template is required"}

    assert_mode ${mode}
    assert_file ${tpl}

    pushd ${HOST_FILM_ROOT} >/dev/null

    # subprocess to make sure env vars do not polute parent process env
    (
        . film/docker/env/common.env
        build_config "${tpl}"
    )

    popd > /dev/null
}


assert_file() {
    local file=${1:-""}
    local bailout${2:-0}

    [ -n ${file:-""} -a -e ${file:-""} ] && return 0 || true
    [ ${bailout} -eq 0 ] && fatal "file ${file} not found" || true

    return 1
}

assert_binary() {
    local bin=${1:-$(assert_help)}
    local bailout=${2:-0}
    local fbin=$(which ${bin})
    if [ -z ${fbin:-""} ] || [ ! -x ${fbin:-""} ]; then
        [ ${bailout} -eq 0 ] && fatal "${bin} not found (please install ${bin})"
        return 1
    fi

    return 0
}

assert_swarm() {
    local bailout=${1:-0}
    assert_binary "docker"
    if [ "$(docker info --format '{{.Swarm.LocalNodeState}}')" != 'active' ]; then
        [ ${bailout} -gt 0 ] && return 1
        fatal "swarm is not initialized (please initialize swarm)"
    fi

    return 0
}

assert_mode() {
    local mode=${1:?"mode is expected ('devel' or 'production')"}
    local bailout=${2:-0}

    case "${mode}" in
        devel)
            return 0
            ;;
        production)
            return 0
            ;;
        *)
            [ ${bailout} -gt 0 ] && return 1 || fatal "mode '${mode}' is not implemented/supported" ;;
    esac
}

assert_mode_devel() {
    local mode=${1:?"mode is expected ('devel' or 'production')"}
    local bailout=${2:-0}
    if [ ${mode:-""} != 'devel' ]; then
        [ ${bailout} -gt 0 ] && return 1 || fatal "mode is not 'devel' (mode: ${mode})"
    fi

    return 0
}

assert_version() {
    local version=${1:?"version is required"}
    local bailout=${2:-0}

    local versions=$(curl -s "${FILM_REGISTRY_PROTO}://${FILM_REGISTRY}/v2/film/film/tags/list")

    echo "${versions}" | grep -e "\"${version}\"" >/dev/null 2>&1  && return 0

    [ ${bailout} -gt 0 ] && return 1 || fatal "version '%s' is unavailable" ${version}
}

assert_service_up() {
    local srv=${1:?"service name is required"}
    local bailout=${2:-0}

    # so far each service runs excactly one task
    # thus tasks count -ge 1 is ok to confirm that service is up and running
    local ntasks=$(docker service ls \
        --filter name=${srv} \
        --format '{{index (split .Replicas "/") 0}}')

    [ ${ntasks:-0} -gt 0 ] && return 0 || true
    [ ${bailout} -eq 0 ] && fatal "assert service-up failed for '${srv}'" || true

    return 1
}

assert_stack_up() {
    local stack=${1:?"stack name is required"}
    local bailout=${2:-0}

    assert_swarm 1 && true || return 1

    docker stack ls --format '{{.Name}}' | grep -q "^${stack}\$" && return 0
    [ ${bailout} -eq 0 ] && fatal "assert stack-up failed for '${stack}'" || true

    return 1
}

assert_aux_service_enabled() {
    local service_name=${1:?'service name is required'}
    systemctl is-enabled "${service_name}.service" >/dev/null 2>&1
    return $?
}

assert_aux_service_active() {
    local service_name=${1:?'service name is required'}
    systemctl is-active "${service_name}.service" >/dev/null 2>&1
    return $?
}


film_deploy() {
    local mode=${1:?$(assert_mode)}

    assert_mode "${mode}"
    assert_binary "docker"
    assert_swarm 1 || deploy_swarm "${mode}"

    film_deploy_aux_service
    #film_deploy_portainer_stack

    #deploy_create_configs ${mode}

    docker stack deploy \
        -c ${HOST_FILM_ROOT}/films/devel.yml \
        ${mode}

    if ! stack_wait_services ${mode} ${DEPLOY_WAIT:-300}; then
        notice 'some services did not come up: giving up waiting them'
    fi
}

film_destroy() {
    local mode=${1:?$(assert_mode)}

    # only 'devel' stacks are allowed to be destroyed
    assert_mode_devel ${mode}

    if assert_stack_up ${mode} 1; then
        notice "removing stack ${mode}..."
        docker stack rm ${mode}
        wait_containers_shutdown ${mode}
    fi

    film_rm_aux_service
    film_rm_portainer_stack

    docker container prune -f
    docker volume prune -f
}

film_deploy_aux_service() {
    local service_name=${1:-${FILM_AUX_SERVICE}}

    if ! assert_aux_service_active "${service_name}"; then
        notice "deploying aux service, root access is required"

        if ! assert_aux_service_enabled "${service_name}"; then
            sudo cp "${HOST_FILM_ROOT}/films/tools/aux-service/${service_name}.service" "/lib/systemd/system/${service_name}.service"
            sudo cp "${HOST_FILM_ROOT}/films/tools/aux-service/${service_name}.pl" "/data/diplom/${service_name}.pl"

            echo "HOST_ROOT=${HOST_FILM_ROOT}" > "/tmp/${service_name}.env"

            sudo systemctl enable "${service_name}.service" >/dev/null 2>&1
        fi

        sudo systemctl start "${service_name}.service" >/dev/null 2>&1
    fi
}

film_rm_aux_service() {
    local service_name=${1:-${FILM_AUX_SERVICE}}

    if assert_aux_service_active "${service_name}" || assert_aux_service_enabled "${service_name}"; then
        notice "destroying aux service, root access is required"

        sudo systemctl stop "${service_name}.service" >/dev/null 2>&1

        if assert_aux_service_enabled "${service_name}"; then
            sudo systemctl disable "${service_name}.service" >/dev/null 2>&1

            sudo rm -f /tmp/${service_name}.env \
                /opt/${service_name}.pl \
                /lib/systemd/system/${service_name}.service  \
                /var/log/${service_name}.log \
                /var/run/${service_name}.pid
        fi
    fi
}

film_deploy_portainer_stack() {
    local stack='portainer-agent'

    assert_binary "docker"
    assert_swarm 1 || deploy_swarm "${mode}"

    notice "deploying stack ${stack}..."

    docker stack deploy \
        -c ${HOST_FILM_ROOT}/films/devel.yml \
        ${stack}
}

film_rm_portainer_stack() {
    local stack='portainer-agent'

    if assert_stack_up ${stack} 1; then
        notice "removing stack ${stack}..."
        docker stack rm ${stack}
        wait_containers_shutdown ${stack}
    fi
}


deploy_swarm() {
    local mode=${1:-dev}
    # so far mode is ignored
    # it might be required for 2+ nodes configurations where swarm manager has
    # to be avaiable over network
    assert_binary "docker"
    docker swarm init --advertise-addr=127.0.0.1 --listen-addr=127.0.0.1
}

deploy_create_configs() {
    local mode=${1:-$(assert_mode)}

    assert_mode ${mode}
    assert_binary 'docker'

    cd ${HOST_FILM_ROOT}

    local ctr_name="${mode}_configurator"
    local cfg_volume="${mode}_configs"
    local cfg_root=/etc/film
    local img="${FILM_REGISTRY}${FILM_IMAGE}:${FILM_TAG:-latest}"
    local ref="*${FILM_IMAGE}:${FILM_TAG:-latest}"
    if [ -z "$(docker image ls --filter reference=${ref} --format '{{.Repository}}')" ]; then
        docker image pull ${img}
    fi

    (
        docker volume create --name ${cfg_volume} --label "com.film.mode=${mode}"
        docker run --rm --name ${ctr_name} -v ${cfg_volume}:${cfg_root} -d ${img} tail -f /dev/null
    ) > /dev/null 2>&1

    local cfg tpl
    for tpl in $(find film/etc/config/ -type f); do
        cfg="${tpl#film/etc/config/}"
        cfg="${cfg%.tpl}"

        notice "creating config '${cfg}' ..."
        {
            if [ ${tpl%.tpl} = ${tpl} ]; then
                cat ${tpl}
            else
                make_config ${mode} ${tpl} ${witheval:-1}
            fi
        } | docker exec -i ${ctr_name} sh -c "mkdir -p $(dirname ${cfg_root}/${cfg}); cat - > ${cfg_root}/${cfg}"
    done

    docker stop ${ctr_name} > /dev/null 2>&1
}


ct_exec() {
    local cid=${1:?"container id is required"}
    shift 1
    docker exec -it ${cid} "$@"
}


service_exec() {
    local srv=${1:?"service is required"}
    shift 1
    ct_exec $(service_cid ${srv}) "$@"
}

service_cid() {
    local srv=${1:?"service name is required!"}
    docker container ls --filter "name=${srv}" --format '{{.ID}}'
}


wait_service() {
    local service=${1:?"service name is required"}
    local patience=${2:-90}
    local quiet=${3:-0}

    local tstart=$(date '+%s')
    local rv=1
    local n=0
    while( [ $(($(date '+%s')-${tstart})) -lt ${patience} ] ); do
        local res=$(docker service ls --filter name=${service} \
            --format '{{.Name}}:{{index (split .Replicas "/") 0}}' | egrep "^${service}:")
        local srv=${res%%:*}
        local repl=${res##*:}

        if [ -n ${srv:-""} -a ${repl:-0} -gt 0 ]; then
            rv=0
            [ ${quiet} -eq 0 -a ${n} -gt 0 ] && echo "" >&2 || true
            break
        fi

        if [ ${quiet} -eq 0 -a ${n} -eq 0 ]; then
            echo -n "waiting service ${service} ..." >&2
        elif [ ${quiet} -eq 0 -a $((${n}%5)) -eq 0 ]; then
            echo -n . >&2
        fi

        n=$((n+1))
        sleep 1
    done

    return ${rv}
}

wait_db_service() {
    local dbservice=${1:?"db service name is required"}
    local patience=${2:-900}

    local tstart=$(date '+%s')
    if wait_service ${dbservice} ${patience}; then
        local cid=$(service_cid ${dbservice})

        while ! docker exec -i ${cid} psql -c '\q' >/dev/null 2>&1; do
            [ $((patience + tstart - $(date '+%s'))) -le 0 ] && return 1 || true
            sleep 1
        done

        return 0
    fi

    return 1
}


stack_wait_services() {
    local stack=${1:?"stack is required ('devel' or 'production')"}
    local patience=${2:-90} # 90 seconds by default
    local quiet=${3:-1}

    local psfilter="--filter desired-state=running --filter desired-state=accepted"
    local nwait=$(docker stack ps ${psfilter} --format '{{.ID}}' ${stack} | wc -l)
    [ ${nwait:-0} -eq 0 ] && fatal "There is no services in stack ${stack}" || true

    local nwaitprev=0
    local tstart=$(date '+%s')
    while [ ${nwait:-0} -gt 0 ]; do
        if [ $(($(date '+%s') - ${tstart})) -ge ${patience} ]; then
            notice 'wait services patience timeout %d exceeded' ${patience}
            break
        fi

        nwait=0
        local srv
        for srv in $(docker stack ps ${psfilter} ${stack} \
            --format '{{if ne .DesiredState (index (split .CurrentState " ") 0) }}{{.Name}}{{end}}'); do
            [ -n ${srv:-""} ] && true || continue
            nwait=$((nwait+1))
        done

        if [ ${nwait} -gt 0 ]; then
            if [ ${quiet:-0} -ne 0 -a ${nwait} -ne ${nwaitprev} ]; then
                notice "waiting ${nwait} service(s) to come up..."
                docker stack ps ${psfilter} \
                    --format '{{if ne .DesiredState (index (split .CurrentState " ") 0) }}\t{{printf "%-36s %s" .Name .CurrentState}}{{end}}' ${stack} \
                    | egrep -v '^$'
            fi
            sleep 1
        fi
        nwaitprev=${nwait}
    done

    return 0
}


wait_containers_shutdown() {
    local mode=${1:?$(assert_mode)}

    notice 'waiting while containers shut down...'
    local cts="$(docker container ls -qf label=com.film.mode=${mode})"
    while [ ${#cts} -gt 0 ]; do
        echo -n . >&2
        sleep 1
        cts="$(docker container ls -qf label=com.film.mode=${mode})"
    done
    echo "done" >&2
}

wait_cmd_succeeds() {
    local attempts=${1:-3}
    local timeout=${2:-5}
    local cmd=${3:?"command is required"}
    shift 3

    local attempt=1
    local ec=0
    while [ ${attempt} -le ${attempts} ]; do
        if [ ${attempt} -gt 1 ]; then
            sleep ${timeout}
            notice "attempting to run ${cmd} (take# ${attempt})..."
        fi

        if ${cmd} $@; then
            return 0
        else
            ec=$?
        fi
        attempt=$((attempt+1))
    done

    notice "command ${cmd} didn't succeed after ${attempt} attempt(s). Giving up"

    return ${ec}
}


trcmd(){
    local cmd=${1:?"command or subcommand is expected"}
    # translate all dashes ("-") in (sub)command to underscores ("_")
    echo -n ${cmd//-/_}
}


fatal() {
    _output '[!!] FATAL ERROR' "$@"
    exit 2
}

notice() {
    _output '[!]' "$@"
}

debug() {
    if [ -z ${FILM_AUX_DEBUG:-""} ]; then
        return 0
    fi
    _output '[**]' "$@"
}

_output() {
    local tag=${1:-""}
    local fmt=${2:-""}
    shift 2
    printf "${tag}>> ${fmt}\n" $@ >&2
}


help() {
    cat <<-HELP
DESCRIPTION
    ${SELF} - an auxiliary utility for MetroBilling project

SEE ALSO

HELP
}


__cf_cmdenv() {
    local cmd=${1:?"command is required"}
    echo -n ${HOME:-~}/.${SELF%.*}/.com.film.cmdenv.$(trcmd "${cmd}")
}

__setup_cmdenv() {
    local cmd=${1}
    local cf=$(__cf_cmdenv ${cmd})
    [ -s ${cf} ] || return 0

    # ensure currently set env variables override those found in the file
    __teardown_cmdenv ${cmd} 0
    source ${cf}
}

__teardown_cmdenv() {
    local cmd=${1}
    local trunc=${2:-1}

    local cf=$(__cf_cmdenv ${cmd})
    [ -s ${cf} ] || mkdir -p $(dirname ${cf})

    if [ ${trunc} -eq 1 ]; then
        declare -x -p $(echo ${!FILM_@}) | sed 's/[[:space:]]/ -g /' > ${cf}
    else
        declare -x -p $(echo ${!FILM_@}) | sed 's/[[:space:]]/ -g /' >> ${cf}
    fi
}


CMD=$1
if [ -z ${CMD:-""} ]; then
    help
    exit 1
fi


shift 1
run_cmd ${CMD} $@
exit $?
