<?php 
    ULogin(0);
    Head('Регистрация') ?>
<body>
<?php
Menu();



?>


<div style=" width:100%; height:1px; clear:both;">.</div>
<?php sidebar();  ?>
<div style="margin-top:10%; padding-left: 25%;padding-right: 20%">
    <div class="form-group text-center" style="padding-top: 2%">
        <h2>Заповніть свої дані</h2>
    </div>
    <div class="menu text-left">


                <form method="POST" action="/account/register" >


                    <div class="form-group">
                        <strong>Логін:*</strong>
                        <input  type="text" name="login"  placeholder="Логін" maxlength="10" class="form-control" pattern="[A-Za-z-0-9]{3,10}" title="Не менше 3 та не більше 10 латинських символів або цифр." required>
                    </div>
                    <div class="form-group">
                        <strong>Пароль:*</strong>
                        <input type="password" name="password" placeholder="Пароль" maxlength="15"  pattern="[A-Za-z-0-9]{5,15}" title="Не менше 5 та не більше 15 латинських символів або цифр." required class="form-control">
                    </div>
                    <div class="form-group">
                        <strong>Повторіть пароль:*</strong>
                        <input  type="password" name="password2" placeholder="Пароль" maxlength="15"  pattern="[A-Za-z-0-9]{5,15}" title="Не менше 5 та не більше 15 латинських символів або цифр." required class="form-control">
                    </div>
                    <div class="form-group">
                        <strong>Ваш E-Mail:*</strong>
                        <input type="email" name="email" placeholder="E-Mail" required class="form-control">
                    </div>
                    <div class="form-group" >
                        <strong>Заповніть капчу:*</strong>
                        <input  type="text" class="form-control"  name="captcha"  maxlength="10" pattern="[0-9]{1,5}" title="Только цифры." required AUTOCOMPLETE="off">

                    </div>
                    <div class="form-group text-center">
                         <img src="/resource/captcha.php" class="capimg">
                    </div>
                    <div class="form-group" style="padding-top: 12px;">
                        <input  type="submit" name="enter" value="Далі" class="btn btn-success btn-block" >
                    </div>

                </form>

    </div>
</div>

<div style=" width:100%; height:0px; clear:both;"></div>


<?php
MessageShow();
Footer(); ?>
</body>
</html>