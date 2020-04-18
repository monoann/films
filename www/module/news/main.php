<?php 
$Param['page'] += 0;
$Param['cat'] += 0;


if ($Param['cat'] and $Param['cat'] <= 0 or $Param['cat'] > 3) MessageSend(1, 'Такой категории не существует.', '/news');

Head('Новини');
?>
<body>
<?php Menu();
MessageShow()
?>

<div class="container-blog">
    <div class="container">
        <div class="page-header" id="blog">
            <div class="row">
                <div class="col-md-4">
                    <img class="img-responsive" src="http://kozlov.ua/resource/image10.jpg" style="padding-top:20px;" />
                    <br />
                    <h2 class="text-primary">Категорії новин : </h2>
                    <br />
                    <p class="text-primary"><i class="fa fa-arrow-right" aria-hidden="true"></i>&nbsp; &nbsp;<a href="/news/main/cat/">Усі категорії новин</a></p>
                    <p class="text-primary"><i class="fa fa-arrow-right" aria-hidden="true"></i>&nbsp; &nbsp; <a href="/news/main/cat/1">Категорія 1</a></p>
                    <p class="text-primary"><i class="fa fa-arrow-right" aria-hidden="true"></i>&nbsp; &nbsp; <a href="/news/main/cat/2">Категорія 2</a></p>
                    <p class="text-primary"><i class="fa fa-arrow-right" aria-hidden="true"></i>&nbsp; &nbsp; <a href="/news/main/cat/3">Категорія 3</a></p>

                    <?php if ($_SESSION['USER_LOGIN_IN']) echo '
                    <p class="text-primary"><i class="fa fa-arrow-right" aria-hidden="true"></i>&nbsp; &nbsp; <a href="/news/add">Додати новину</a></p>' ?>
                    <br/>
                    <?php SearchForm() ?>
                </div>

                <div class="col-md-8">
                    <div>
                        <div class="row">
                            <?php

                            if ($Module == 'main' and !$Param['cat']) {

                                if ($_SESSION['USER_GROUP'] != 2) $Active = 'WHERE `active` = 1';

                                $Param1 = 'SELECT `id`, `name`, `added`, `date`, `active`,`rate` ,`text`  FROM `news` '.$Active.' ORDER BY `id` DESC LIMIT 0, 5';

                                $Param2 = 'SELECT `id`, `name`, `added`, `date`, `active` ,`rate`,`text` FROM `news` '.$Active.' ORDER BY `id` DESC LIMIT START, 5';

                                $Param3 = 'SELECT COUNT(`id`) FROM `news`';

                                $Param4 = '/news/main/page/';

                            } else {

                                if ($_SESSION['USER_GROUP'] != 2) $Active = 'AND `active` = 1';

                                $Param1 = 'SELECT `id`, `name`, `added`, `date`, `active` ,`rate` ,`text` FROM `news` WHERE `cat` = '.$Param['cat'].' '.$Active.' ORDER BY `id` DESC LIMIT 0, 5';

                                $Param2 = 'SELECT `id`, `name`, `added`, `date`, `active`,`rate`,`text` FROM `news` WHERE `cat` = '.$Param['cat'].' '.$Active.' ORDER BY `id` DESC LIMIT START, 5';

                                $Param3 = 'SELECT COUNT(`id`) FROM `news` WHERE `cat` = '.$Param['cat'];

                                $Param4 = '/news/main/cat/'.$Param['cat'].'/page/';
                            }



                            $Count = mysqli_fetch_row(mysqli_query($CONNECT, $Param3));


                            if (!$Param['page']) {

                                $Param['page'] = 1;

                                $Result = mysqli_query($CONNECT, $Param1);

                            } else {

                                $Start = ($Param['page'] - 1) * 5;

                                $Result = mysqli_query($CONNECT, str_replace('START', $Start, $Param2));
                            }


                            PageSelector($Param4, $Param['page'], $Count);

                            while ($Row = mysqli_fetch_assoc($Result)) {
                                if (!$Row['active']) $Row['name'] .= ' (Ожидает модерации)';

                                echo '                            
                                <div class="col-xs-12" style="word-wrap:break-word">
                                
                                    <h3 class="text-primary">'.$Row['name'].'</h3>
                                    <p class="text-justify">
                                    '.$Row['text'].'
                                                           
                                    </p>
                                
                                <div class="text-center">
                                    <span class="glyphicon glyphicon-time"></span> Додана :  '.$Row['date'].'&nbsp; &nbsp; &nbsp;
                                    <span class="glyphicon glyphicon-star"></span> Новина сподобалась  '.$Row['rate'].'&nbsp; &nbsp; &nbsp;
                                    <a href="/news/material/id/'.$Row['id'].'"><span class="glyphicon glyphicon-plus"></span>Читати далі</a>&nbsp; &nbsp; &nbsp;
                                    
                                </div>
                            </div>
                        </div>
                        <hr />';
                        } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>





        <?php Footer() ?>

</body>
</html>