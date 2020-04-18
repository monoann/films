    <?php

    ULogin(1);
    Head('Профиль пользователя') ?>

<body>

<?php
    Menu();

sidebar();

$Row = mysqli_query($CONNECT, "SELECT *  FROM `users` WHERE `id_user` ='$_SESSION[USER_ID_USER]'");
$data = mysqli_fetch_assoc($Row);
$Avatar_name = substr("$data[avatar]",16);
if (!strlen($Avatar_name)) {
  $Avatar_name = "guest.jpg";
}
// $Avatar_name = $film_name = explode(".",$Avatar_path);
$Avatar = $_SESSION['USER_AVATAR'].'/'.$_SESSION['USER_ID_USER'];
    echo '
   <div class="container" style="margin-top: 200px;margin-left: 20%">
      <div class="row">
        <div class="panel-primary col-sm-12 form-group-lg" >
          <div class="panel panel-info">
            <div class="panel-heading col-sm-6" style="padding:0px;">
              <form enctype="multipart/form-data" method="POST" action="/account/addavatar" style="margin:0px;">
                <input hidden name="user" value="'.$_SESSION['USER_ID_USER'].'">
                <div class="form-group">
                  <img src="resource/avatar/'.$Avatar_name.'" width="100px">
                </div>
                <div class="form-group">
                  <label>Виберіть зображення для аватару : </label>&#8195;
                  <input type="file" name="avatar" accept="image/*">
                </div>
                <input type="submit" name="enter" class="btn btn-success btn-block">
              </form>
            </div>
            <div class="panel-heading">
              <h3 class="panel-title"> Login : '.$_SESSION['USER_LOGIN'].'</h3>
            </div>
            <div class="panel-body">
              <div class="row">
                         
                <div class=" col-md-9 col-lg-9 "> 
                  <table class="table table-user-information">
                    <tbody>
                      <tr>
                        <td>Вподобання:</td>
                        <td>В разработке</td>
                      </tr>
                      <tr>
                        <td>Дата реєстрації:</td>
                        <td>'.$_SESSION['USER_REG_DATE'].'</td>
                      </tr>
                      <tr>
                        <td>Дата народження</td>
                        <td>В разработке</td>
                      </tr>
                   
                         <tr>
                         
                      <tr>
                        <td>Email</td>
                        <td>'.$_SESSION['USER_EMAIL'].'</a></td>
                      </tr>
                        <td>Телефон</td>
                        <td>--------------<br><br>----------------
                        </td>
                           
                      </tr>
                     
                    </tbody>
                  </table>
                
                </div>
              </div>
            </div>
           
            
          </div>
        </div>
      </div>
    </div>
    
  
  </section>
      
    
    ';


MessageShow();
    Footer() ?>

</body>
</html>