<?php 
    $Param['id'] += 0;
   if ($Param['id'] == 0) MessageSend(1, 'URL адрес указан неверно.', '/news');
    $Row = mysqli_fetch_assoc(mysqli_query($CONNECT, 'SELECT `name`, `added`, `date`, `text`, `active`, `rate`, `rateusers` FROM `news` WHERE `id` = '.$Param['id']));
   if (!$Row['name']) MessageSend(1, 'Такой новости не существует.', '/news');
  if (!$Row['active'] and $_SESSION['USER_GROUP'] != 2) MessageSend(1, 'Новость ожидает модерации.', '/news');
    Head($Row['name']);
?>


    <body>

    <?php

     Menu();
     MessageShow()
    ?>
    <hr>
    <div class="container">

        <div >


            <div class="col-xs-12" style="word-wrap:break-word" >
                    <div class="container">
                        <?php
                            echo '     
                            
                                <div class="row " style="margin-left: 20px;border: 1px solid lightskyblue "">
                                    <h3 class="text-primary" style="margin-left: 1%">'.$Row['name'].'</h3>
                                    <p class="text-justify" style="margin-left: 3%">
                                    '.$Row['text'].'
                                    </p>
                                    
                                </div> ';

                            if ($Row['rateusers']) {
                                $Exp = explode(',', $Row['rateusers']);

                                     foreach ($Exp as $value) {
                                     if ($value) {
                                     $Row2 = mysqli_fetch_assoc(mysqli_query($CONNECT, "SELECT `login` FROM `users` WHERE `id` = $value"));
                                     $RATED .= '<a href="/user/'.$Row2['login'].'" class="lol">'.$Row2['login'].'</a> ';
                                     }
                                     }
                            } else $RATED = 'n/a';
                            if (!$Row['active']) $Active = '| <a href="/news/control/id/'.$Param['id'].'/command/active" >Активировать новость</a>';
                            if ($_SESSION['USER_GROUP'] == 2) $EDIT = '| <a href="/news/edit/id/'.$Param['id'].'" class="lol">Редагувати новину</a> | <a href="/news/control/id/'.$Param['id'].'/command/delete" class="lol">Видалити новину</a>'.$Active;

                            echo '<div class="col-xs-12 img-rounded"><p style="margin-left: 15px">Додав : '.$Row['added'].' | Оцінок: '.$Row['rate'].' | Дата: '.$Row['date'].' '.$EDIT.'<br>Оцінили: '.$RATED.'</p></div> <br><br><a href="/rate/news/id/'.$Param['id'].'';
                        echo '<a href="/rate/news/id/'.$Param['id']. '" class="btn btn-default glyphicon glyphicon-hand-up">Сподобалось</a>';
                        ?>
                        <div>
                            <?php
                             include("module/comments/main.php");
                            ?>
                        </div>
                    </div>
            </div>
        </div>
    </div>
    <?php Footer(); ?>
    </body>
</html>