<?php 
ULogin(1);

if ($_POST['enter'] and $_POST['text'] and $_POST['captcha']) {
$_POST['text'] = FormChars($_POST['text']);

if (preg_match ('/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i', $_POST['text'])) MessageSend(1, 'Ссылки запрещены.');

$_POST['captcha'] = FormChars($_POST['captcha']);
if ($_SESSION['captcha'] != md5($_POST['captcha'])) MessageSend(1, 'Капча введена не верно.');
mysqli_query($CONNECT, "INSERT INTO `chat`  VALUES (NULL , '$_POST[text]', '$_SESSION[USER_LOGIN]', NOW())");
Location('/chat');
}

Head('Чат');
?>
<body>

<?php Menu();
MessageShow() 
?>




<div class="container" style="margin-top: 3%">
    <label style="margin-left: 4%"><h3 class="text-info">Загальний ЧАТ</h3> </label>
<?php

$Query = mysqli_query($CONNECT, 'SELECT * FROM `chat` ORDER By `time` DESC LIMIT 50');
while ($Row = mysqli_fetch_assoc($Query)) echo '
            <div class="row" style="border: 1px solid lightskyblue ; margin-left: 5%;margin-right: 5%;">
                <div class="col-sm-2" style="border-right: 1px solid lightskyblue" >
                <span>'.$Row['user'].' <br> '.$Row['time'].'</span>
                </div >
                <div class=" col-sm-4" style="margin-right: 10%">
                 '.'<br>'.$Row['message'].'
                </div>
            </div><br>';
?>
</div>



<form style="margin-left: 17%;" class="form-group" method="POST" action="/chat">
<textarea class="form-control" rows="5" style="width: 40%" name="text" placeholder="Текст сообщения" required></textarea><br>
<div class="capdiv"><input type="text" class="capinp" name="captcha" placeholder="Капча" maxlength="10" pattern="[0-9]{1,5}" title="Только цифры." required> <img src="/resource/captcha.php" class="capimg" alt="Каптча"></div>
<br><input class="btn btn-primary" type="submit" name="enter" value="Отправить"> <input class="btn btn-danger"   type="reset" value="Очистить">
</form>


<?php Footer() ?>

</body>
</html>