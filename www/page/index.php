<?php Head('Фільми онлайн') ?>

<body>

     <?php
           Menu();
         sidebar();

     MessageShow();

     $Row = mysqli_query($CONNECT, "SELECT *  FROM `films`");

     ?>

     <div class="container" style="margin-top: 10%;max-width: 80%;margin-left: 15%">


        <?php
        while ($data=mysqli_fetch_assoc($Row)) {
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
                <br> <hr>';

        echo '        <a class="btn btn-primary" href="/filminfo?id_film='.$data['id_film'].'" style="margin-bottom: 2%">Продивитися</a>
             </div>
         </div> <br><br>';}

?>



     </div>
 <?php Footer(); ?>

</body>

</html>