<?php
require_once 'settings.php';
session_start();
$CONNECT = mysqli_connect(HOST,USER,PASS,DB );

if (!$_SESSION['USER_LOGIN_IN'] and $_COOKIE['user'])
{
    $_COOKIE['user'] = mysqli_real_escape_string($CONNECT, $_COOKIE['user']);
    $Row = mysqli_fetch_assoc(mysqli_query($CONNECT, "SELECT `id_user`, `name`, `regdate`, `email`, `country`, `avatar`, `login`, `group` FROM `users` WHERE `password` = '$_COOKIE[user]'"));

    if (!$Row)
    {
        setcookie('user', '', strtotime('-30 days'), '/');
        unset($_COOKIE['user']);
        MessageSend(1, 'Ошибка авторизации', '/');
    }

    $_SESSION['USER_LOGIN_IN'] = 1;
    foreach ($Row as $Key => $Value) $_SESSION['USER_'.strtoupper($Key)] = $Value;
}

if ($_SERVER['REQUEST_URI'] == '/')
{
    $Page = 'index';
    $Module = 'index';
}

else
{
    $URL_Path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    $URL_Parts = explode('/', trim($URL_Path, ' /'));
    $Page = array_shift($URL_Parts);
    $Module = array_shift($URL_Parts);

    if (!empty($Module))
    {
        $Param = array();
        for ($i = 0; $i < count($URL_Parts); $i++)
        {
            $Param[$URL_Parts[$i]] = $URL_Parts[++$i];
        }
    }
    else $Module = 'main';
}




if ($_SESSION['USER_LOGIN_IN']) $User = $_SESSION['USER_LOGIN'];
else $User = 'guest';


if ($Online['ip']) mysqli_query($CONNECT, "UPDATE `online` SET `time` = NOW() WHERE `ip` = '$_SERVER[REMOTE_ADDR]'");
else if ($Online['user'] and $Online['user'] != 'guest') mysqli_query($CONNECT, "UPDATE `online` SET `time` = NOW() WHERE `user` = '$User'");
else mysqli_query($CONNECT, "INSERT INTO `online` SET `ip` = '$_SERVER[REMOTE_ADDR]', `user` = '$User', `time` = NOW()");

if (in_array($Page, array( 'index', 'login', 'register', 'account', 'profile', 'chat', 'parser', 'search',  'rate', 'language', 'user','addtest', 'admin','filminfo'))) include("page/$Page.php");

else if ($Page == 'news' and in_array($Module, array('main', 'material', 'edit', 'control', 'add'))) include("module/news/$Module.php");


else if ($Page == 'loads' and in_array($Module, array('main', 'material', 'edit', 'control', 'add', 'download'))) include("module/loads/$Module.php");

else if ($Page == 'pm' and in_array($Module, array('send', 'dialog', 'message', 'control'))) include("module/pm/$Module.php");

else if ($Page == 'comments' and in_array($Module, array('add', 'control'))) include("module/comments/$Module.php");

else if ($Page == 'forum' and in_array($Module, array('main', 'section', 'topic', 'add', 'control'))) include("module/forum/$Module.php");

else if ($Page == 'admin')
{
    if ($_SESSION['ADMIN_LOGIN_IN'] and in_array($Module, array('main', 'stats', 'query'))) include("module/admin/$Module.php");
    else if ($Module == ADMIN_PASS)
    {
        $_SESSION['ADMIN_LOGIN_IN'] = 1;
        MessageSend(3, 'Вход в Админ панель выполнен успешно.', '/admin');
    }
    else NotFound();
}

else if ($Page == 'archive' ) include("archive/engine.php");

else NotFound();


//////////////////////////////////////////// Функции.../////////////////////////////////////////////////////////////////

function NotFound(){

    header('HTTP/1.0 404 Not Found');
    exit(include("page/404.php"));
}

function SendMessage($p1, $p2)
{
    global $CONNECT;

    $p1 = FormChars($p1, 1);
    $p2 = FormChars($p2);

    if ($p1 == $_SESSION['USER_LOGIN']) MessageSend(1, 'Вы не можете отправить сообщение самому себе', '/');

    $ID = mysqli_fetch_assoc(mysqli_query($CONNECT, "SELECT `id` FROM `users` WHERE `login` = '$p1'"));

    if (!$ID) MessageSend(1, 'Пользователь не найден', '/');

    $Row = mysqli_fetch_assoc(mysqli_query($CONNECT, "SELECT `id` FROM `dialog` WHERE `recive` = $ID[id] AND `send` = $_SESSION[USER_ID] OR `recive` = $_SESSION[USER_ID] AND `send` = $ID[id]"));

    if ($Row) {

        $DID = $Row['id'];
        mysqli_query($CONNECT, "UPDATE `dialog` SET `status` = 0, `send` = $_SESSION[USER_ID], `recive` = $ID[id] WHERE `id` = $Row[id]");

    }
    else {
        mysqli_query($CONNECT, "INSERT INTO `dialog` VALUES (NULL , 0, $_SESSION[USER_ID], $ID[id])");
        $DID = mysqli_insert_id($CONNECT);

    }

    mysqli_query($CONNECT, "INSERT INTO `message` VALUES (NULL , $DID, $_SESSION[USER_ID], '$p2', NOW())");
}

function ULogin($p1) {
    if ($p1 <= 0 and $_SESSION['USER_LOGIN_IN'] != $p1) MessageSend(1, 'Данная страница доступна только для гостей.', '/');
    else if ($_SESSION['USER_LOGIN_IN'] != $p1) MessageSend(1, 'Данная сртаница доступна только для пользователей.', '/');
}

function UGroup($p1) {
  if ($p1 <= 0 and $_SESSION['USER_GROUP'] != $p1) MessageSend(1, 'Данная страница доступна только для гостей.', '/');
  else if ($_SESSION['USER_GROUP'] != $p1) MessageSend(1, 'Данная сртаница доступна только для админов.', '/');
}

function MessageSend($p1, $p2, $p3 = '', $p4 = 1) {
  $type;  
  if ($p1 == 1) {
      $p1 = 'Помилка';
      $type = 'bg-danger';
    }
    else if ($p1 == 2) {
      $p1 = 'Підказка';
      $type = 'bg-warning';
    }
    else if ($p1 == 3) {
      $p1 = 'Інформація';
      $type = 'bg-info';
    } 
    $_SESSION['message'] = '<br><div class="'.$type.' text-white" style="text-align: center"><b>'.$p1.'</b>: '.$p2.'</div>';
    if ($p4) {
        Location($p3);
    }
}

function Location ($p1) {
    if (!$p1) $p1 = $_SERVER['HTTP_REFERER'];
    exit(header('Location: '.$p1));
}

function MessageShow() {
    if ($_SESSION['message'])$Message = $_SESSION['message'];
    echo $Message;
    $_SESSION['message'] = array();
}

function UserCountry($p1) {
    if ($p1 == 0) return 'Не вказано';
    else if ($p1 == 1) return 'Украина';
    else if ($p1 == 2) return 'Россия';
    else if ($p1 == 3) return 'США';
    else if ($p1 == 4) return 'Канада';
}

function UserGroup($p1) {
    if ($p1 == 0) return 'Користувач';
     else if ($p1 == 2) return 'Администратор';
   }


   function UAccess($p1) {
    global $CONNECT;
    ULogin(1);
    if ($_SESSION['USER_GROUP'] < $p1) MessageSend(1, 'У вас нет прав доступа для просмотра данной страницйы сайта.', '/');
}

function RandomString($p1) {
 //   $Char = '0123456789abcdefghijklmnopqrstuvwxyz';
 //   for ($i = 0; $i < $p1; $i ++) $String.= $Char[rand(0, strlen($Char) - 1)];
  //  return $String;
}

function HideEmail($p1) {
    $Explode = explode('@', $p1);
    return $Explode[0].'@*****';
}

function FormChars($p1, $p2 = 0) {
    global $CONNECT;
    if ($p2) return mysqli_real_escape_string($CONNECT, $p1);
    else return nl2br(htmlspecialchars(trim($p1), ENT_QUOTES), false);
}


function GenPass ($p1, $p2) {
    return md5('MRSHIFT'.md5('321'.$p1.'123').md5('678'.$p2.'890'));
}



function ModuleID($p1) {
    if ($p1 == 'news') return 1;
    else if ($p1 == 'loads') return 2;
    else MessageSend(1, 'Модуль не найден.', '/');
}


function PageSelector($p1, $p2, $p3, $p4 = 5) {
    /*
    $p1 - URL (Например: /news/main/page)
    $p2 - Текущая страница (из $Param['page'])
    $p3 - Кол-во новостей
    $p4 - Кол-во записей на странице
    */

    $Page = ceil($p3[0] / $p4); //делим кол-во новостей на кол-во записей на странице.
    if ($Page > 1) { //А нужен ли переключатель?
        echo '<div style="margin-left: 50%">';

        for($i = ($p2 - 3); $i < ($Page + 1); $i++) {
            if ($i > 0 and $i <= ($p2 + 3)) {
                if ($p2 == $i) $Swch = 'SwchItemCur';
                else $Swch = 'SwchItem';
                echo '<a class="'.$Swch.'" href="'.$p1.$i.'">'.'|'.$i.'</a>';
            }
        }
        echo '</div>';
    }
}



function MiniIMG($p1, $p2, $p3, $p4, $p5 = 50) {
    /*
    $p1 - Путь к изображению, которое нужно уменьшить.
    $p2 - Директория, куда будет сохранена уменьшенная копия.
    $p3 - Ширина уменьшенной копии.
    $p4 - Высота уменьшенной копии.
    $p5 - Качество уменьшенной копии.
    */

    $Scr = imagecreatefromjpeg($p1);
    $Size = getimagesize($p1);
    $Tmp = imagecreatetruecolor($p3, $p4);
    imagecopyresampled($Tmp, $Scr, 0, 0, 0, 0, $p3, $p4, $Size[0], $Size[1]);
    imagejpeg($Tmp, $p2, $p5);
    imagedestroy($Scr);
    imagedestroy($Tmp);
}

function SearchForm() {
    global $Page;
    echo '<form method="POST" action="/search/'.$Page.'"><input type="text" name="text" value="'.$_SESSION['SEARCH'].'" placeholder="Что искать?" required><input type="submit" name="enter" value="Поиск"></form>';
}





function Menu ()
{
    global $Page;

    echo '


  <header xmlns="http://www.w3.org/1999/html">
  <div class="fixed-top" >
  <header class="topbar">
      <div class="container">
        <div class="row">
          <!-- social icon-->
          <div class="col-sm-12">
            <ul class="social-network">
              <li><a class="waves-effect waves-dark" href="#"><i class=""></i></a></li>
              <li><a class="waves-effect waves-dark" href="#"><i class=""></i></a></li>
              <li><a class="waves-effect waves-dark" href="#"><i class=""></i></a></li>
              <li><a class="waves-effect waves-dark" href="#"><i class=""></i></a></li>
              <li><a class="waves-effect waves-dark" href="#"><i class=""></i></a></li>
            </ul>
          </div>

        </div>
      </div>
  </header>
  
  <nav class="navbar navbar-expand-lg navbar-dark mx-background-top-linear">
    <div class="container">
    
      <a class="navbar-brand" href="/" style="text-transform: uppercase;"> КІНОЗАЛ <h6>Фільми на будьякий смак</h6> </a>
       <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarResponsive">

        <ul class="navbar-nav ml-auto">';

        if ($Page == 'index')echo
        '
          <li class="nav-item active">
            <a class="nav-link" href="/">Головна
              <span class="sr-only">(current)</span>
              
            </a>
          </li>';
        else echo ' <li class="nav-item">
            <a class="nav-link" href="/">Головна
              <span class="sr-only">(current)</span>
            </a>
          </li>';



    if ($_SESSION['USER_LOGIN_IN'] != 1) {
         if($Page == 'login') echo '
          <li class="nav-item active">
            <a class="nav-link" href="/login">Вхід</a>
          </li>';
         else echo '
         <li class="nav-item">
            <a class="nav-link" href="/login">Вхід</a>
          </li>';

    if($Page == 'register') echo '
          <li class="nav-item active">
            <a class="nav-link" href="/register">Реєстрація</a>
          </li>';
    else echo '
          <li class="nav-item">
            <a class="nav-link" href="/register">Реєстрація</a>
          </li>';}

    else if ($_SESSION['USER_GROUP'] == 2) {
        echo ' 
          <li class="nav-item">
            <a class="nav-link"  href="#" style="color: #c09853"> ' . $_SESSION['USER_LOGIN'] . ' : <b class="caret"></b> </a>
          </li>';
        if($Page == 'profile')echo'
          <li class="nav-item active">
              <a class="nav-link" href="/profile">Профіль</a> 
          </li> ';
        else  echo '
          <li class="nav-item">
              <a class="nav-link" href="/profile">Профіль</a> 
          </li>';
        if ($Page =='amin')echo '
         <li class="nav-item active">
              <a class="nav-link" href="/admin"> Налаштування</a> 
          </li>';
        else echo '
          <li class="nav-item">
              <a class="nav-link" href="/admin"> Налаштування</a> 
          </li>
        
        ';
        echo '
          <li class="nav-item">
              <a class="nav-link" href="/account/logout">Вихід</a>
          </li>
        '; }
    else if ($_SESSION['USER_GROUP'] == 1) {
        echo ' 
          <li class="nav-item">
            <a class="nav-link"  href="#" style="color: #c09853"> ' . $_SESSION['USER_LOGIN'] . ' : <b class="caret"></b> </a>
          </li>';
        if($Page == 'profile')echo'
          <li class="nav-item active">
              <a class="nav-link" href="/profile">Профіль</a> 
          </li> ';
        else  echo '
          <li class="nav-item">
              <a class="nav-link" href="/profile">Профіль</a> 
          </li>';

         echo '
          <li class="nav-item">
              <a class="nav-link" href="/account/logout">Вихід</a>
          </li>
        '; }
    echo '
        </ul>
      </div>
    </div>
  </nav>
</div>
   </header>';


}


function sidebar(){
    global $CONNECT;
    $Row = mysqli_query($CONNECT, "SELECT *  FROM `category`");
    echo '

	<aside class="sidebar-left-collapse" style="margin-top: 115px;">

		<a href="/" class="company-logo" ><img src="assets/ico/kino-logo.png" alt=""></a>

		<div class="sidebar-links">';
    while ($data=mysqli_fetch_assoc($Row)){

			
echo '
			<div class="link-red">

				<a href="#">
					<i class="fa fa-map-marker"></i>'.$data['name'].'
				</a>';

            echo '<ul class="sub-links">';

                $Row2 = mysqli_query($CONNECT, "SELECT *  FROM `films` WHERE `id_category`='$data[id_cat]'");
                    if(mysqli_num_rows($Row2)>0){
                    while ($datas = mysqli_fetch_assoc($Row2)) {
                        echo '<li style="word-wrap: break-word"><a href="/filminfo?id_film=' . $datas['id_film'].'">' . $datas['nameb'].'</a></li>';
                    }}else
                        echo '<li><a href="/">Нема книг</a></li>';
            echo ' </ul>';
echo '
			</div>';}
echo'
	</div>
	</aside>';
}

function Footer () {
echo '

  <!-- JavaScript Library Files -->

  <script src="assets/js/jquery.min.js"></script>
  <script src="assets/js/jquery.easing.js"></script>
  <script src="assets/js/google-code-prettify/prettify.js"></script>
  <script src="assets/js/modernizr.js"></script>
  <script src="assets/js/bootstrap.js"></script>
  <script src="assets/js/jquery.elastislide.js"></script>
  <script src="assets/js/sequence/sequence.jquery-min.js"></script>
  <script src="assets/js/sequence/setting.js"></script>
  <script src="assets/js/jquery.prettyPhoto.js"></script>
  <script src="assets/js/application.js"></script>
  <script src="assets/js/jquery.flexslider.js"></script>
  <script src="assets/js/hover/jquery-hover-effect.js"></script>
  <script src="assets/js/hover/setting.js"></script>
  <script src="assets/js/custom.js"></script>
  <script type="text/javascript" src="assets/js/jquery-3.3.1.min.js"></script>
 
<link href="assets/css/tabs.css" rel="stylesheet">
<link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
<script src="https://code.jquery.com/jquery-1.11.1.min.js"></script>
<!------ Include the above in your HEAD tag ---------->

	<script src="http://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
	
	<script>

		$(function () {

			var links = $(\'.sidebar-links > div\');

			links.on(\'click\', function () {

				links.removeClass(\'selected\');
				$(this).addClass(\'selected\');

			});
		});

	</script>



';}
function Head($p1) {
    echo'
   
  <meta charset="utf-8">
  <title>Бібліотека</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="description" content="LIB ONLINE">
  <meta name="author" content="Ischenko PS-1401">
   <link rel="shortcut icon" href="assets/ico/ico.png">
  <link rel="apple-touch-icon-precomposed" sizes="144x144" href="assets/ico/ico.png">
  <link rel="apple-touch-icon-precomposed" sizes="114x114" href="assets/ico/ico.png">
  <link rel="apple-touch-icon-precomposed" sizes="72x72" href="assets/ico/ico.png">
  <link rel="apple-touch-icon-precomposed" href="assets/ico/ico.png">
 
    
	<link rel="stylesheet" href="assets/demo.css">
	<link rel="stylesheet" href="assets/sidebar-collapse.css">

	<link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css">
	<link href="https://fonts.googleapis.com/css?family=Cookie" rel="stylesheet" type="text/css">

 
 
 <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css" rel="stylesheet">

<style type="text/css">

    form { margin: 0px 10px; }

    h2 {
        margin-top: 2px;
        margin-bottom: 2px;
    }

    .container { max-width: 360px; }

    .divider {
        text-align: center;
        margin-top: 20px;
        margin-bottom: 5px;
    }

    .divider hr {
        margin: 7px 0px;
        width: 35%;
    }

    .left { float: left; }

    .right { float: right; }



 body {
    margin: 0;
    font-family: -apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,"Helvetica Neue",Arial,sans-serif;
    font-size: 1rem;
    font-weight: 400;
    line-height: 1.5;
    color: #212529;
    background-color: #f7f7f7;
  
}
.navbar {
    display: -ms-flexbox;
    display: flex;
    -ms-flex-wrap: wrap;
    flex-wrap: wrap;
    -ms-flex-align: center;
    align-items: center;
    -ms-flex-pack: justify;
    justify-content: space-between;
    padding: 5px;
}

/*
headeer top
*/
.topbar{
  background-color: #212529;
  padding: 0;
}

.topbar .container .row {
  margin:-7px;
  padding:0;
}

.topbar .container .row .col-md-12 { 
  padding:0;
}

.topbar p{
  margin:0;
  display:inline-block;
  font-size: 13px;
  color: #f1f6ff;
}

.topbar p > i{
  margin-right:5px;
}
.topbar p:last-child{
  text-align:right;
} 

header .navbar {
    margin-bottom: 0;
}

.topbar li.topbar {
    display: inline;
    padding-right: 18px;
    line-height: 52px;
    transition: all .3s linear;
}

.topbar li.topbar:hover {
    color: #1bbde8;
}

.topbar ul.info i {
    color: #131313;
    font-style: normal;
    margin-right: 8px;
    display: inline-block;
    position: relative;
    top: 4px;
}

.topbar ul.info li {
    float: right;
    padding-left: 30px;
    color: #ffffff;
    font-size: 13px;
    line-height: 44px;
}

.topbar ul.info i span {
    color: #aaa;
    font-size: 13px;
    font-weight: 400;
    line-height: 50px;
    padding-left: 18px;
}

ul.social-network {
  border:none;
  margin:0;
  padding:0;
}

ul.social-network li {
  border:none;  
  margin:0;
}

ul.social-network li i {
  margin:0;
}

ul.social-network li {
    display:inline;
    margin: 0 5px;
    border: 0px solid #2D2D2D;
    padding: 5px 0 0;
    width: 32px;
    display: inline-block;
    text-align: center;
    height: 32px;
    vertical-align: baseline;
    color: #000;
}

ul.social-network {
  list-style: none;
  margin: 5px 0 10px -25px;
  float: right;
}

.waves-effect {
    position: relative;
    cursor: pointer;
    display: inline-block;
    overflow: hidden;
    -webkit-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
    user-select: none;
    -webkit-tap-highlight-color: transparent;
    vertical-align: middle;
    z-index: 1;
    will-change: opacity, transform;
    transition: .3s ease-out;
    color: #fff;
}
a {
  color: #0a0a0a;
  text-decoration: none;
}

li {
    list-style-type: none;
}
.bg-image-full {
    background-position: center center;
    background-repeat: no-repeat;
    -webkit-background-size: cover;
    -moz-background-size: cover;
    background-size: cover;
    -o-background-size: cover;
}
.bg-dark {
    background-color: #222!important;
}

.mx-background-top-linear {
    background: -webkit-linear-gradient(45deg, #c21b29 48%, #1b1e21 48%);
    background: -webkit-linear-gradient(left, #c21b29 48%, #1b1e21 48%);
    background: linear-gradient(45deg, #c21b29 48%, #1b1e21 48%);
}

@import url(https://fonts.googleapis.com/css?family=Open+Sans:400,700,300);
footer { background-color:#212529; min-height:350px; font-family: \'Open Sans\', sans-serif; }
.footerleft { margin-top:50px; padding:0 36px; }
.logofooter { margin-bottom:10px; font-size:25px; color:#fff; font-weight:700;}

.footerleft p { color:#fff; font-size:12px !important; font-family: \'Open Sans\', sans-serif; margin-bottom:15px;}
.footerleft p i { width:20px; color:#999;}


.paddingtop-bottom {  margin-top:50px;}
.footer-ul { list-style-type:none;  padding-left:0px; margin-left:2px;}
.footer-ul li { line-height:29px; font-size:12px;}
.footer-ul li a { color:#a0a3a4; transition: color 0.2s linear 0s, background 0.2s linear 0s; }
.footer-ul i { margin-right:10px;}
.footer-ul li a:hover {transition: color 0.2s linear 0s, background 0.2s linear 0s; color:#ff670f; }

.social:hover {
     -webkit-transform: scale(1.1);
     -moz-transform: scale(1.1);
     -o-transform: scale(1.1);
 }
 
 

 
 .icon-ul { list-style-type:none !important; margin:0px; padding:0px;}
 .icon-ul li { line-height:75px; width:100%; float:left;}
 .icon { float:left; margin-right:5px;}
 
 
 .copyright { min-height:40px; background-color:#000000;}
 .copyright p { text-align:left; color:#FFF; padding:10px 0; margin-bottom:0px;}
 .heading7 { font-size:21px; font-weight:700; color:#d9d6d6; margin-bottom:22px;}
 .post p { font-size:12px; color:#FFF; line-height:20px;}
 .post p span { display:block; color:#8f8f8f;}
 .bottom_ul { list-style-type:none; float:right; margin-bottom:0px;}
 .bottom_ul li { float:left; line-height:40px;}
 .bottom_ul li:after { content:"/"; color:#FFF; margin-right:8px; margin-left:8px;}
 .bottom_ul li a { color:#FFF;  font-size:12px;}
  </style>
';
}
?>