<?php 
$Param['id'] += 0;
if ($Param['id'] == 0) MessageSend(1, 'URL адрес указан неверно.', '/loads');
$Row = mysqli_fetch_assoc(mysqli_query($CONNECT, 'SELECT `name`, `added`, `date`, `text`, `active`, `dimg`, `dfile`, `link`, `rate`, `rateusers` FROM `loads` WHERE `id` = '.$Param['id']));
if (!$Row['name']) MessageSend(1, 'Такой новости не существует.', '/loads');
if (!$Row['active'] and $_SESSION['USER_GROUP'] != 2) MessageSend(1, 'Новость ожидает модерации.', '/loads');


if ($Row['link'] and !$Row['dfile']) $Download = $Row['link'];
else $Download = '/loads/download/id/'.$Param['id'];


Head($Row['name']);
?>
<body>

<?php Menu();
MessageShow() 
?>


<div class="container"  >

    <div >


        <div class="col-xs-12" style="word-wrap:break-word;" >
            <div class="container">
                <?php
                echo '     
                                    
                                <div class="row " style="margin-left: 20px; margin-top: 20px ;border: 1px solid lightskyblue; ">
                                    <h3 class="text-primary" style="margin-left: 2%">'.$Row['name'].'</h3>
                                    <img  style="margin-left: 2%;" src="/catalog/img/'.$Row['dimg'].'/'.$Param['id'].'.jpg" alt="'.$Row['name'].'">
                                    
                                    <p class="text-justify" style="margin-left: 3%;margin-top: 3%">
                                    '.$Row['text'].'
                                    </p>
                                    
                                </div> ';

                        if (!$Row['active']) $Active = '| <a href="/loads/control/id/'.$Param['id'].'/command/active">Активувати новину</a>';
                        if ($_SESSION['USER_GROUP'] == 2) $EDIT = '| <a href="/loads/edit/id/'.$Param['id'].'">Редагувати новину</a> | <a href="/loads/control/id/'.$Param['id'].'/command/delete" class="lol">Видалити новину</a>'.$Active;
                        echo '<a  href="'.$Download.'" class="lol" style="margin-left: 3%;">Завантажити</a> | Додав : '.$Row['added'].' | Оцінок : '.$Row['rate'].' | Дата: '.$Row['date'].' '.$EDIT.'</a><br><br>
                            <a style="margin-left: 2%" href="/rate/loads/id/'.$Param['id'].'" class="btn btn-default glyphicon glyphicon-hand-up">Сподобалось</a><br><br><b>';
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




<?php Footer() ?>

</body>
</html>