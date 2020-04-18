<?php Head('Форум') ?>




<body>

    <?php Menu();
    MessageShow()
    ?>
        <div class="container" style="margin-bottom: 600px">
        <div class="row" style="margin-left: 3%;margin-top: 5%; margin-right: 3%; border: 1px solid #1d88bb ">
            <div class="col-md-4 text-center" ><a href="/forum/add">Створити нову тему</a></div>
            <div class="col-md-4 text-center" >Останнє повідомлення</div>
            <div class="col-md-4 text-center">Оновлена тема</div>
        </div>
        <br>
            <?
            $section = array(
                1 => 'Розділ 1',
                2 => 'Розділ 2',
                3 => 'Розділ 3',
                4 => 'Розділ 4',
                5 => 'Розділ 5'
            );
            echo '<div class="row" style="margin-left: 3%;margin-bottom: 3%; margin-right: 3%; border: 1px solid #1d88bb ">';

            foreach ($section as $id => $name) {
                $upd = mysqli_fetch_assoc(mysqli_query($CONNECT, "SELECT `id`, `last_post`, `last_update`, `name` FROM `forum` WHERE `section` = $id ORDER BY `last_update` DESC LIMIT 1"));
                if ( $upd )
                    echo '
                    <div class="col-md-4 text-center"> <br><a href="/forum/add"> <a href="/forum/section/id/'.$id.'">'.$name.'</a></a></div>
                     <div class="col-md-4 text-center"><br> Від: <b>'.$upd['last_post'].'</b></div>
                       <div class="col-md-4 text-center"><br>Тема: <a href="/forum/topic/id/'.$upd['id'].'">'.$upd['name'].'</a> <br>Дата : '.$upd['last_update'].'</div>
                        ';
            }

            echo '</div>';
            ?>
    </div>





<?php Footer() ?>

</body>
</html>