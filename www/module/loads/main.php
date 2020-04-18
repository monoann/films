<?php 
$Param['page'] += 0;
$Param['cat'] += 0;

if ($Param['cat'] and $Param['cat'] <= 0 or $Param['cat'] > 3) MessageSend(1, 'Такой категории не существует.', '/news');

Head('Каталог файлов');
?>

  <body>
    <?php Menu();
    MessageShow()
    ?>

    <div class="container-blog">
        <div class="container">
            <div class="page-header" id="blog">
                <div class="row">
                    <div class="col-md-4" style="border-right: 1px solid lightskyblue">


                        <h2 class="text-primary">Категорії матеріалів : </h2>
                        <br />
                        <p class="text-primary"><i class="fa fa-arrow-right" aria-hidden="true"></i>&nbsp; &nbsp;<a href="/loads">Усі категорії матеріалів </a></p>
                        <p class="text-primary"><i class="fa fa-arrow-right" aria-hidden="true"></i>&nbsp; &nbsp; <a href="/loads/main/cat/1">Категорія матеріалів 1</a></p>
                        <p class="text-primary"><i class="fa fa-arrow-right" aria-hidden="true"></i>&nbsp; &nbsp; <a href="/loads/main/cat/2">Категорія матеріалів 2</a></p>
                        <p class="text-primary"><i class="fa fa-arrow-right" aria-hidden="true"></i>&nbsp; &nbsp; <a href="/loads/main/cat/3">Категорія матеріалів 3</a></p>

                        <?php if ($_SESSION['USER_LOGIN_IN']) echo '
                    <p class="text-primary"><i class="fa fa-arrow-right" aria-hidden="true"></i>&nbsp; &nbsp; <a href="/loads/add">Додати матеріал</a></p>' ?>
                        <br/>
                        <?php SearchForm() ?>
                    </div>

                    <div class="col-md-8" style="margin-bottom: 300px">
                        <div>
                            <div class="row">
                                <?php

                                if ($Module == 'main' and !$Param['cat']) {
                                    if ($_SESSION['USER_GROUP'] != 2) $Active = 'WHERE `active` = 1';
                                    $Param1 = 'SELECT `id`, `name`, `added`, `date`, `active` FROM `loads` '.$Active.' ORDER BY `id` DESC LIMIT 0, 5';
                                    $Param2 = 'SELECT `id`, `name`, `added`, `date`, `active` FROM `loads` '.$Active.' ORDER BY `id` DESC LIMIT START, 5';
                                    $Param3 = 'SELECT COUNT(`id`) FROM `loads`';
                                    $Param4 = '/loads/main/page/';
                                } else {
                                    if ($_SESSION['USER_GROUP'] != 2) $Active = 'AND `active` = 1';
                                    $Param1 = 'SELECT `id`, `name`, `added`, `date`, `active` FROM `loads` WHERE `cat` = '.$Param['cat'].' '.$Active.' ORDER BY `id` DESC LIMIT 0, 5';
                                    $Param2 = 'SELECT `id`, `name`, `added`, `date`, `active` FROM `loads` WHERE `cat` = '.$Param['cat'].' '.$Active.' ORDER BY `id` DESC LIMIT START, 5';
                                    $Param3 = 'SELECT COUNT(`id`) FROM `loads` WHERE `cat` = '.$Param['cat'];
                                    $Param4 = '/loads/main/cat/'.$Param['cat'].'/page/';
                                }

                                $Count = mysqli_fetch_row(mysqli_query($CONNECT, $Param3));

                                if (!$Param['page']) {
                                    $Param['page'] = 1;
                                    $Result = mysqli_query($CONNECT, $Param1);
                                } else {
                                    $Start = ($Param['page'] - 1) * 5;
                                    $Result = mysqli_query($CONNECT, str_replace('START', $Start, $Param2));
                                }




                                while ($Row = mysqli_fetch_assoc($Result)) {
                                    if (!$Row['active']) $Row['name'] .= ' (Ожидает модерации)';

                                    echo '
                                            <div style="margin-top: 3%; margin-left: 3%;">
                                            <a href="/loads/material/id/'.$Row['id'].'">
                                                <div class="ChatBlock"><span> Додав : '.$Row['added'].' | '.$Row['date'].'</span>'.' | '.$Row['name'].'</div>
                                             </a>
                                             <br>
                                            </div>';


                                }


                                PageSelector($Param4, $Param['page'], $Count);
 ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


<?php Footer() ?>
</div>
</body>
</html>