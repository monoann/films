<?php
// ULogin(1);

Head('Тести онлайн') ?>

<body>

<?php


Menu();
sidebar();

?>

<div class="container" style="margin-top: 10%;max-width: 80%;margin-left: 15%">


    <?php

    $Row = mysqli_query($CONNECT, "SELECT *  FROM `films` WHERE `id_film` ='$_GET[id_film]'");
    $data = mysqli_fetch_assoc($Row);
        $Trailer_path = substr("$data[trailer]",18);
        $Trailer_name = $film_name = explode(".",$Trailer_path);
        $Film_path = substr("$data[file]",15);
        $film_name = explode(".",$Film_path);
        $country_id = $data['country_id'];
        $country = mysqli_fetch_assoc(mysqli_query($CONNECT, "SELECT `country` FROM `countries` WHERE `id` = '$country_id'"));
        echo '<div class="row" style="border: 1px #212529 solid;border-radius: 10px">
             <div class="col-md-4"style="margin-top: 2%">
                 <img class="img-fluid rounded mb-1 mb-md-0" style="max-width: 80%; max-height: 80%; padding: 2% ;margin-bottom: -1%;" src='.$data['img'].' >
             </div>
             <div class="col-md-8" style="word-wrap:break-word;margin-top: 5%;margin-left:-1%;margin-bottom: -1%">
                 <h3>Назва : '.$data['nameb'].'</h3>
                 <h5>Режисер : '.$data['author'].'</h5>
                 <h5>Актори : '.$data['actor'].'</h5>
                 <h5>Країна : '.$country['country'].'</h5>
                 <h5>Короткий опис :</h5>
                 <p>'.$data['opus'].'</p>
                 <br>
             </div>
    ';?>
            <div class="tabs">
                <input type="radio" name="inset" value="" id="tab_1" checked>
                <label for="tab_1">Трайлер</label>

                <input type="radio" name="inset" value="" id="tab_2">
                <label for="tab_2">Фільм</label>

                <div id="txt_1">
                    <video controls width="100%" poster=<?php echo $data['img'] ?>>
                        <?php echo '<source src="/resource/trailers/'. $Trailer_name[0].'.mp4" type="video/mp4">';?>
                        Sorry, your browser doesn\'t support embedded videos.
                    </video>
                </div>
                <div id="txt_2">
                    <video controls width="100%" poster=<?php echo $data['img'] ?>>
                    <?php echo '<source src="/resource/films/'. $film_name[0].'.mp4" type="video/mp4">';?>
                        Sorry, your browser doesn\'t support embedded videos.
                    </video>
                </div>
            </div>
            <hr>
            <br>
            <div class="forum" style="width:100%;">
                <form enctype="multipart/form-data" method="POST" action="/account/addcomment">
                    <input hidden name="film" value=<?php echo $_GET['id_film'] ?>>
                    <input hidden name="user" value=<?php if ($_SESSION['USER_ID_USER']) {echo $_SESSION['USER_ID_USER'];} else {echo 0;} ?>>
                    <br>
                    <textarea class="form-control comment"  rows="3" name="comment" required="" autocomplete="off"></textarea>
                    <div class="form-group" style="padding-top: 12px; width:200px;">
                        <input type="submit" name="enter" class="btn btn-success btn-block">
                    </div>
                </form>
            </div>
            <div class="votes_container">
                <table style="width:100%;">
                    <?php
                        $votes_date = mysqli_query($CONNECT, "SELECT *  FROM `votes` WHERE `film_id` ='$_GET[id_film]'");
                        while ($votes=mysqli_fetch_assoc($votes_date)) {
                            $user_row = mysqli_query($CONNECT, "SELECT id_user, login, avatar  FROM `users` WHERE `id_user` ='$votes[user_id]'");
                            $user = mysqli_fetch_assoc($user_row);
                            $Avatar_name = substr("$user[avatar]",16);
                            if (!strlen($Avatar_name)) {
                                $Avatar_name = "guest.jpg";
                            }
                    ?>
                    <tr class="votes">
                        <td class="avatar">
                            <?php echo '<img src="resource/avatar/'.$Avatar_name.'" width="80px">' ?> 
                        </td>
                        <td class="messages">
                            <p><b><?php echo $user['login'] ?></b>, залишив <?php echo $votes['date'] ?></p>
                            <p><?php echo $votes['votes'] ?></p>
                        </td>
                    </tr>
                    <?php
                        } ?>
                </table>
            </div>
         </div> <br><br>



<?php
    MessageShow();
    Footer(); ?>

</body>

</html>