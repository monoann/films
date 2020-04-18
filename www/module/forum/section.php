<?php 

$Param['id'] += 0;

if (!preg_match('/^[1-5]{1,1}$/', $Param['id'])) NotFound();


Head('Раздел') ?>
<body>

<?php Menu();
MessageShow() 
?>


<div class="container" style="margin-bottom: 600px">
    <div class="row" style="margin-left: 3%;margin-top: 5%; margin-right: 3%; border: 1px solid #1d88bb ">
        <div class="col-md-3 text-center" >Тема</a></div>
        <div class="col-md-3 text-center" >Дата створення</div>
        <div class="col-md-3 text-center">Автор</div>
        <div class="col-md-3 text-center">Останнє повідомлення</div>
    </div>
    <br>
    <?

    $Count = mysqli_fetch_row(mysqli_query($CONNECT, 'SELECT COUNT(`id`) FROM `forum` WHERE `section` = '.$Param['id']));



    if (!$Param['page']) {
        $Param['page'] = 1;
        $Result = mysqli_query($CONNECT, 'SELECT * FROM `forum` WHERE `section` = '.$Param['id'].' ORDER BY `id` DESC LIMIT 0, 5');
    } else {
        $Start = ($Param['page'] - 1) * 5;
        $Result = mysqli_query($CONNECT, str_replace('START', $Start, 'SELECT * FROM `forum` WHERE `section` = '.$Param['id'].' ORDER BY `id` DESC LIMIT START, 5'));
    }

    echo '<div class="row" style="margin-left: 3%;margin-bottom: 3%; margin-right: 3%; border: 1px solid #1d88bb ">';
    while ($Row = mysqli_fetch_assoc($Result)) {

        echo '
                <div class="col-md-3 text-center"> <br><a href="/forum/topic/id/'.$Row['id'].'">'.$Row['name'].'</a></div>       
                <div class="col-md-3 text-center"><br>   '.$Row['date'].'</div>
                <div class="col-md-3 text-center"><br>  '.$Row['author'].'</div>
                <div class="col-md-3 text-center"><br>  '.$Row['last_post'].'</div>
            
            ';


    }

    echo '</div>';


    ?>
</div>

<?php











PageSelector('/forum/main/section/'.$Param['id'].'/page/', $Param['page'], $Count);


?>




</table>
</div>
<?php Footer() ?>
</div>
</body>
</html>