<?php
$ID = ModuleID($Page);
$Count = mysqli_fetch_row(mysqli_query($CONNECT, 'SELECT COUNT(`id`) FROM `comments` WHERE `module` = '.$ID.' AND `material` = '.$Param['id']));


if (!$Param['page']) {
    $Param['page'] = 1;
    $Result = mysqli_query($CONNECT, 'SELECT `id`, `added`, `date`, `text` FROM `comments` WHERE `module` = '.$ID.' AND `material` = '.$Param['id'].' ORDER BY `id` DESC LIMIT 0, 5');

} else {
    $Start = ($Param['page'] - 1) * 5;
    $Result = mysqli_query($CONNECT, str_replace('START', $Start, 'SELECT `id`, `added`, `date`, `text` FROM `comments` WHERE `module` = '.$ID.' AND `material` = '.$Param['id'].' ORDER BY `id` DESC LIMIT START, 5'));
}





echo '<div style="margin-left: 2%"> 
    <h4 class="text-info"> Коментарі </h4>
    </div>';


while ($Row = mysqli_fetch_assoc($Result)) {

    if ($_SESSION['USER_GROUP'] == 2) $Admin = ' | <a href="/comments/control/action/delete/id/'.$Row['id'].'" class="lol">Удалить</a> | <a href="/comments/control/action/edit/id/'.$Row['id'].'" class="lol">Редактировать</a>';
    if ($Row['id'] == $_SESSION['COMMENTS_EDIT']) $Row['text'] = '<form method="POST" action="/comments/control"><textarea class="ChatMessage" name="text" placeholder="Текст сообщения" required>'.$Row['text'].'</textarea><br><input type="submit" name="save" value="Сохранить"> <input type="submit" name="cancel" value="Отменить"> <input type="reset" value="Очистить"></form>';
    echo '

        <div class="row " style="margin-left: 20px;border: 1px solid lightskyblue ">
            <div class="col-sm-3" style="border-right: 1px solid lightskyblue "> '.$Row[''].'Написав : '.$Row['added'].'<br> '.$Row['date'].$Admin.'</div>
            
            <div class="col-xs-6" >
            '.$Row['text'].'
            </div>
         </div><br>';

}
 PageSelector("/$Page/$Module/id/$Param[id]/page/", $Param['page'], $Count);
if ($_SESSION['USER_LOGIN_IN'] != 1) echo '<br><br> <h4 class="text-danger">Залишати коментарі можуть тшлки зареєстровані користувачі</h4>';
else echo '
<br>



<br>
<form style="margin-bottom: 50px; margin-left: 5%" class="form-group" method="POST" action="/comments/add/module/'.$Page.'/id/'.$Param['id'].'">

    <textarea class="form-control" rows="5" style="width: 40%" name="text" placeholder="Текст сообщения" required></textarea>
    <br>
    <div class="capdiv">
        <input type="text" class="col-xs-2 "  name="captcha" placeholder="Капча" maxlength="10" pattern="[0-9]{1,5}" title="Только цифры." required> 
        <img  src="/resource/captcha.php" class="img-rounded" style="margin-left: 10px" alt="Каптча">
    </div>
    <br>
    <input class="btn btn-primary" type="submit" name="enter" value="Отправить"> <input class="btn btn-danger" type="reset" value="Очистить">
    
</form>
';



?>