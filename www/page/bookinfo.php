<?php
ULogin(1);
Head('Тести онлайн') ?>

<body>

<?php


Menu();
sidebar();

MessageShow();?>

<div class="container" style="margin-top: 10%;max-width: 80%;margin-left: 15%">


    <?php

    $Row = mysqli_query($CONNECT, "SELECT *  FROM `films` WHERE `id_film` ='$_GET[id_film]'");
    $data = mysqli_fetch_assoc($Row);
        $Trailer_path = substr("$data[trailer]",18);
        $Trailer_name = $film_name = explode(".",$Trailer_path);
        $Film_path = substr("$data[file]",15);
        $film_name = explode(".",$Film_path);
        echo '<div class="row" style="border: 1px #212529 solid;border-radius: 10px">
             <div class="col-md-4"style="margin-top: 2%">
                 <img class="img-fluid rounded mb-1 mb-md-0" style="max-width: 80%; max-height: 80%; padding: 2% ;margin-bottom: -1%;" src='.$data['img'].' >
             </div>
             <div class="col-md-8" style="word-wrap:break-word;margin-top: 5%;margin-left:-1%;margin-bottom: -1%">
                 <h3>Назва : '.$data['namef'].'</h3>
                 <h5>Автор : '.$data['author'].'</h5>
                 <h5>Короткий опис :</h5>
                 <p>'.$data['opus'].'</p>
                  <p>'.$film_name[0].'</p>
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
              <!-- <embed src="/resource/bookfile/'.$file_name[0].'.pdf" type="application/pdf" width="100%" height="600px" /> -->
         </div> <br><br>



<?php

    Footer(); ?>

</body>

</html>