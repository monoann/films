<?php 
$Param['id'] += 0;

$topic = mysqli_fetch_assoc(mysqli_query($CONNECT, "SELECT `name` FROM `forum` WHERE `id` = $Param[id]"));
if (!$topic) NotFound();

Head('Раздел') ?>
<body>

<?php Menu();
MessageShow() 
?>



<div class="text-center" style="margin-top: 3%; margin-left: 3% ;margin-right: 3%; border: 1px solid #1d88bb "> <H3 class="text-info text-uppercase"> <?=$topic['name']?> </H3> </div>


<?php

$Count = mysqli_fetch_row(mysqli_query($CONNECT, 'SELECT COUNT(`id`) FROM `forump` WHERE `topic` = '.$Param['id']));



    if (!$Param['page']) {
        $Param['page'] = 1;
        $Result = mysqli_query($CONNECT, 'SELECT * FROM `forump` WHERE `topic` = '.$Param['id'].' ORDER BY `id` LIMIT 0, 5');
    } else {
        $Start = ($Param['page'] - 1) * 5;
        $Result = mysqli_query($CONNECT, str_replace('START', $Start, 'SELECT * FROM `forump` WHERE `topic` = '.$Param['id'].' ORDER BY `id` LIMIT START, 5'));
    }

echo '
    <br>
    <div style="margin-left: 3%"> 
    <h4 class="text-info"> Обговорення </h4>
    </div>';
    echo '<div class="row" style="margin-left: 3%;margin-bottom: 3%; margin-right: 30%;">';
    while ($Row = mysqli_fetch_assoc($Result)) {

        echo '

        <div class="row " style="margin-left: 20px;border: 1px solid lightskyblue ">
            <div class="col-sm-2" style="border-right: 1px solid lightskyblue ">Написав : '.$Row['author'].'<br> '.$Row['date'].$Admin.'</div>
            
            <div class="col-xs-6" style="margin-right: 5%" >
            '.$Row['text'].'
            </div>
         </div><br>';


    }
    echo '</div>';
PageSelector('/forum/topic/id/'.$Param['id'].'/page/', $Param['page'], $Count);
?>


<?php 

    if ($_SESSION['USER_LOGIN_IN'])
        echo '
<form style="margin-bottom: 50px; margin-left: 5%" class="form-group" method="POST" action="/forum/add/id/'.$Param['id'].'">

    <textarea class="form-control" rows="5" style="width: 40%" name="text" placeholder="Текст сообщения" required></textarea>
    <br>
  
    <br>
    <input class="btn btn-primary" type="submit" name="add_message" value="Отправить"> <input class="btn btn-danger" type="reset" value="Очистить">

</form>

';

?>





<?php Footer() ?>

</body>
</html>