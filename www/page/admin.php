<?php

// ULogin(1);
UGroup(2);
Head('Админ панель') ?>
<style xmlns="http://www.w3.org/1999/html">
    .dropbtn {
        background-color: #4CAF50;
        color: white;
        padding: 16px;
        font-size: 16px;
        border: none;
    }

    .dropdown {
        position: relative;
        display: inline-block;
    }

    .dropdown-content {
        display: none;
        position: absolute;
        background-color: #f1f1f1;
        min-width: 160px;
        box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
        z-index: 1;
    }

    .dropdown-content a {
        color: black;
        padding: 12px 16px;
        text-decoration: none;
        display: block;
    }

    .dropdown-content a:hover {background-color: #ddd;}

    .dropdown:hover .dropdown-content {display: block;}

    .dropdown:hover .dropbtn {background-color: #3e8e41;}
</style>
<body >

<?php
Menu();

sidebar();
echo '
<div class="row" style="padding-top: 1%">
<div style="height: 510px; margin-left: 250px; max-width: 20%; margin-top: 10%; padding: 0 1% ; float: left">
<form method="POST" action="/account/addcat" role="form" style="height: 250px;  padding: 0 1% ; float: left">
    <label for="email"><h3>Додавання категорії фільму</h3></label>
    <div class="form-group">
        <label for="email">Назва категорії:</label>
        <input type="text" class="form-control" id="name" name="name" required AUTOCOMPLETE="off">
    </div>
    <input  type="submit" name="enter" value="Додати" class="btn btn-success btn-block" >
</form>
<form method="POST" action="/account/addcountry" role="form" style="height: 250px; padding: 0 1% ; float: left">
    <label for="email"><h3>Додавання країни</h3></label>
    <div class="form-group">
        <label for="email">Назва країни:</label>
        <input type="text" class="form-control" id="name" name="name" required AUTOCOMPLETE="off">
    </div>
    <input  type="submit" name="enter" value="Додати" class="btn btn-success btn-block" >
</form>
</div>

<form enctype="multipart/form-data" method="POST" action="/account/addfilm" role="form" style=" margin-left: 100px; width: 40%; margin-top: 10%; padding: 0 1% ; float: left">
    <label for="film"><h3>Додавання фільму</h3></label>
    <br>
    <label for="email">Виберіть категорію: </label><br>
    <select name="cat" class="selectpicker" style="width: 100%; height:40px;border-radius: 5px">';
$Row = mysqli_query($CONNECT, "SELECT *  FROM `category`");
while ($data=mysqli_fetch_assoc($Row)) {
    echo '
       <option title="Combo 1">'.$data['name'].'</option>
    ';
}
    echo '</select>
    <br><br>
    <label for="country">Виберіть країну: </label><br>
    <select name="country" class="selectpicker" style="width: 100%; height:40px;border-radius: 5px">';
$Row = mysqli_query($CONNECT, "SELECT *  FROM `countries`");
while ($data=mysqli_fetch_assoc($Row)) {
    echo '
       <option title="Combo 1">'.$data['country'].'</option>
    ';
}
    echo '</select>
    <br> <br>

    <div class="form-group">
        <label for="email">Назва фільму: </label>
        <input type="text" class="form-control" id="nameb" name="nameb" required AUTOCOMPLETE="off">
    </div>
    <div class="form-group">
        <label for="pwd">Режисер: </label>
        <input type="text" class="form-control" id="author" name="author" required AUTOCOMPLETE="off">
    </div>
    <div class="form-group">
        <label for="email">Актори фільму: </label>
        <input type="text" class="form-control" id="actor" name="actor" required AUTOCOMPLETE="off">
    </div>
    <div class="form-group">
        <label for="pwd">Короткий опис фільму: </label>
        <textarea class="form-control" id="opus" rows="3" name="opus" required autocomplete="off"></textarea>
    </div>
    <div class="form-group">
     <label>Виберіть зображення для обкладинки : </label>&#8195;
    <input type="file" name="filename" accept="image/*">
    </div>
    <div class="form-group">
    <label>Виберіть файл трейлеру : </label>&#8195;&#8195;&#8195;&#8195;&#8195;&nbsp;&nbsp;&nbsp;&nbsp;
    <input type="file" name="trailer" accept=".mp4">
    </div>
    <div class="form-group">
    <label>Виберіть файл фільму : </label>&#8195;&#8195;&#8195;&#8195;&#8195;&nbsp;&nbsp;&nbsp;&nbsp;
    <input type="file" name="filename1" accept=".mp4">
    </div>
   
    <input  type="submit" name="enter" value="Додати" class="btn btn-success btn-block" >
</form>


</div>

';


MessageShow();
Footer() ?>

</body>
</html>