<?php Head('Добавить тест') ?>

<body>

    <?php Menu(); MessageShow(); ?>

    <form method="POST" action="#" style="padding: 1% 15%">
        <div class="row-fluid">
            <div class="span12">

                <div class="span12" style="padding: 1% 15%">
                    <label > Назва тесту: </label>
                    <input style="height:30px;width: 50% " type="text" name="login" placeholder="Введіть назву тесту" maxlength="10" pattern="[A-Za-z-0-9]{3,10}" title="Не менше 3 та не більше 10 латинських символів або цифр." required>


                    <div class="form-group ">
                        <label for="comment">Короткий опис тесту:</label>
                        <textarea  rows="5" id="comment" style="width: 50%" name="opus"></textarea>
                    </div>

                    <label > Текст питання: </label>
                    <input id="input_2" style="height:30px;width: 50%" type="text" name="question" placeholder="Введіть назву тесту" maxlength="10" pattern="[A-Za-z-0-9]{3,10}" title="Не менше 3 та не більше 10 латинських символів або цифр." required>

                    <div class="form-group" id="ans">
                        <label for="answer">Варіант відвовіді_1</label>
                        <input  id="answer" style="height:30px;width: 50%" type="text" name="answer_1" placeholder="Текст" maxlength="10" pattern="[A-Za-z-0-9]{3,10}" title="Не менше 3 та не більше 10 латинських символів або цифр." required>
                    </div>
                      <br>
                    <div class="span12">
                        <input type="button" style="width: 20%" class="btn-primary" value="Додати відповідь" onclick="addanswer();">
                        <input type="button" style="width: 20%" class="btn-danger" value="Видалити відповідь" onclick="">
                    </div>
                    <br><br>
                    <div class="span12" style="margin-left: 13%">
                        <input type="button" style="width: 20%" class="btn-info" value="Додати тест" onclick="">
                    </div>

                </div>
            </div>
        </div>
    </form>


<?php Footer(); ?>


</body>

<script type="text/javascript">
    function addanswer() {
        var element = document.getElementById('ans');
        var elementch = element.getElementsByTagName('input');

        var label = document.createElement('label');
        var input = document.createElement('input');


        label.innerHTML=`Варіант відповіді_${elementch.length+1}`;
        label.id=`Варіант відповіді_${elementch.length+1}`;
        label.style='margin-top:5px ';

        input.style='height:30px;width:50%;';
        input.maxLength='10';
        input.pattern='[A-Za-z-0-9]{3,10}';
        input.title='Не менше 3 та не більше 10 латинських символів або цифр.';
        input.name= `answer_${elementch.length+1}`;
        input.placeholder='Текст';


        element.appendChild(label);
        element.appendChild(input);

    }

</script>

</html>

