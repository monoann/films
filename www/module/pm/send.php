<?php 
ULogin(1);

if ($_POST['enter'] and $_POST['text'] and $_POST['login']) {
Uaccess(2);
SendMessage($_POST['login'], $_POST['text']);
MessageSend(3, 'Сообщение отправлено');
}

Head('Отправить сообщение');
?>
<body>

<?php Menu();
MessageShow() 
?>

<div class="container" style="margin-top: 2%">

<div class="row">
    <a style="margin-top: 3%" href="/pm/dialog" class="btn btn-info">МОЇ ДІАЛОГИ</a><br><br>
<form method="POST" action="/pm/send">
<input  type="text" style="width: 40%" name="login" placeholder="Логин получателя" required><br>
<br><textarea class="form-control" rows="5" style="width: 40%"  name="text" placeholder="Текст сообщения" required></textarea>
<br><input type="submit" name="enter" value="Отправить" class="btn btn-info"> <input type="reset" value="Очистить" class="btn btn-danger">
<br> <br>

</form>
</div>
</div>


<?php Footer() ?>

</body>
</html>