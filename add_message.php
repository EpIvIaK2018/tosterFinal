<?php
include ('header.html');
if(isset($_POST)){
    if(!empty($_POST['name']) && !empty($_POST['comment']) && $_POST['Option'] == 'Add'){  
        $dbc = mysqli_connect('localhost', 'root','********','toster');     
        $name = mysqli_real_escape_string($dbc,  trim($_POST['name']));
        $message = mysqli_real_escape_string($dbc, trim($_POST['comment']));
        //Если идёт Ответ на сообщение какое-то, здесь идёт обрабокта и подготовка нового сообщения и того, на которое отвечаем (цитируем его в новом сообщении).
        if ($_POST['Previos_message'] == 'Yes'){
            $id = $_POST['ID'];
            $dbc   = mysqli_connect('localhost', 'root', '********', 'toster');
            $query = "SELECT date, name, message FROM add_message WHERE ID = $id";
            $data  = mysqli_query($dbc, $query);
            $row   = mysqli_fetch_array($data);
            $source_message = ' '. $row['name'] . ' писал: ' . ($row['date']) . '<div class="answer">' . $row['message'] . '<br><br></div>';
            //Конкатенация старого сообщения (цитата) и нового в одно
            $message = $source_message . '<br><br><hr><br><br>' . $message;
        }
        $query = "INSERT INTO add_message (date, name, message) VALUES (NOW(), '$name', '$message')";
        mysqli_query($dbc, $query);
        mysqli_close($dbc);
        echo 'Уважаемый ' . $_POST['name'] . ', Ваше сообщение было добавлено! Возвращаемся назад.';
        echo'<meta http-equiv="refresh" content="2;url=index.php">';
        }
    /*Если была нажата кнопка ответить на сообщение, 
      здесь достаём из БД цитируемое сообщение его автора и дату и готовим ответ.
    */
    else if($_POST['Option'] == 'Answer'){
        $id = $_POST['ID'];
        $dbc   = mysqli_connect('localhost', 'root', '********', 'toster');
        $query = "SELECT name, message FROM add_message WHERE ID = $id";
        $data  = mysqli_query($dbc, $query);
        $row   = mysqli_fetch_array($data);
    ?>
        <div class="main">
        <?echo '<p>' . $row['name'] . ' писал:</p>';?>
            <textarea disabled name="old_message" rows="20" cols="10"><?echo $row['message']?></textarea>

            <form method="post" action="add_message.php" >
            <label for="name">Ваше имя: </label>
            <input type="text" id="name" name="name">
            <p><b>Введите Ваш ответ:</b></p>
            <textarea autofocus name="comment" rows="30" cols="50" autofocus wrap="soft"></textarea>
            <input type="submit" name="submit">
            <input type='hidden' name='Option' value = Add>
            <input type='hidden' name='Previos_message' value= Yes>
            <input type='hidden' name='ID' value=<?echo $id?>>
            </form>
        </div>
    <? 
    }
    else{
        echo 'Не удалось добавить сообщение, введите корректно все данные';
    }
}
?>