<?php include ('header.html');?>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<?php
function generate_link_page($num_pages, $cur_page){
    $page_links = '';
    if ($cur_page > 1){
        $page_links .= '<a href="' . $_SERVER['PHP_SELF'] .'?' . '&page=' . ($cur_page - 1) . '">' . '<' . '</a>';
    }

    for($i = 1; $i <= $num_pages; $i++){
        if ($i == $cur_page){
            $page_links .= " $i ";
            continue;
        }
        $page_links .= '<a href="' . $_SERVER['PHP_SELF']. '?' . '&page=' . $i . '">' . "$i" . ' </a> ';
    }

    if ($cur_page < $num_pages){
        $page_links .= '<a href="' . $_SERVER['PHP_SELF']. '?' . '&page=' . ($cur_page + 1) . '">' . '>' . '</a>';
    }
    return $page_links;
}
?>
<?
$arr = [
    'января',
    'февраля',
    'марта',
    'апреля',
    'майя',
    'июня',
    'июля',
    'августа',
    'сентября',
    'октября',
    'ноября',
    'декабря'
];
//Если нажали Удалить сообщение
if(!empty($_POST['destroy'])){
        $val = $_POST['destroy'];    
        $dbc = mysqli_connect('localhost', 'root','21101988','toster');
        $query = "DELETE FROM add_message WHERE ID = '$val'";
        mysqli_query($dbc, $query) or die(' Не удалось удалить людей <a href="index.php">Вернуться</a>');
        mysqli_close($dbc);         
    }
// Если нажали редактировать
if(!empty($_POST['edit'])){
        $Edit = True;
        $Id_edit = $_POST['edit'];
        $cur_page = 3;
    }
// Завершили редактирование
if(!empty($_POST['edit_complete']) && !empty($_POST['Edit'])){
    $Edit    = $_POST['Edit'];
    $Id_edit = $_POST['ID'];
    $dbc = mysqli_connect('localhost', 'root','21101988','toster'); 
    $query = "UPDATE add_message SET message = '$Edit' WHERE ID = '$Id_edit'";
    mysqli_query($dbc, $query);
    mysqli_close($dbc); 
    $Id_edit = 0;
    $Edit = False;
}
// Отмена редактирования
else if(!empty($_POST['Cancellations'])){
    exit();
}
?>
<div class="second_block">
        <h1><strong>Лента</strong></b></h1>
</div>
<div class="main">
    <?php 
    $cur_page = isset($_GET['page']) ? $_GET['page'] : 1;
    $results_per_page = 8;  // number of results per page
    $skip = (($cur_page - 1) * $results_per_page);
    $Answer = FALSE; 
    $row_answer_name = '';
    $row_answer_message = '';   
    //$month = date('n', $row['date'] )-1;
    $dbc = mysqli_connect('localhost', 'root','21101988','toster');

    $query = "SELECT * FROM add_message"; 
    $result = mysqli_query($dbc, $query);
    $total = mysqli_num_rows($result);
    $num_pages = ceil($total / $results_per_page);

    $query = "SELECT * FROM add_message LIMIT $skip, $results_per_page"; 
    $data = mysqli_query($dbc, $query);

    $i = 0;
    $number_of_count = $skip + 1;
    while ($row = mysqli_fetch_array($data)){
        $Id = $row['ID'];
        $month = $arr[strval(substr($row['date'], 5, 6) -1)];
        if ($Edit && $row['ID'] == $Id_edit){
            // To edit a message
            echo '<strong style="padding-left:88px;" id="mainname">' .$row['name'] . '</strong>';
            echo '<div class="date"><strong style="padding-left:88px;">' . date('H:i:s', strtotime($row['date'])) . date('d') . " $month " . date('Y') . '</strong></div>';
            echo '<id="img" src="images/ava.jpg" width="93" height="93" alt="Винни-Пух">';
            echo '<h2>Отредактируйте сообщение:</h2>';         
            echo '<form action="index.php" method="post"';
            echo "<p><textarea name='Edit' rows='20'>$row[message]</textarea></p>";
            echo "<button type='submit' name='edit_complete' value='Edit'> Ок </button>" . '&emsp;&emsp;&nbsp';
            echo "<button type='submit' name='edit_complete2' value='Cancellations'> Отмена </button>";
            echo "<input type='hidden' name='ID' value='$Id_edit'>";
            echo '</form>';

            echo '<br><br><br>';
            echo '<h5>Сообщение № ' . '<b>' . $i . '</b></h5><hr>';
        }
        else{  
   
            // To delete or edit a message        
            echo "<form action='index.php?&page=$cur_page' method='post'";
            echo "<button class='button'><button name='edit' value=$Id><img src='images/edit.jpg' width='20' height='20' align='center'></button>";
            echo "<button name='destroy' value=$Id><img src='images/delete.png' width='20' height='20' align='center'>";
            echo '</form>';

            // To answer to a message
            echo '<form action="add_message.php" method="post"';
            echo "<button id='button_answer'><button id='button_answer' name='Answer'>Ответить</button>";
            echo "<input type='hidden' name='Option' value='Answer'>";
            echo "<input type='hidden' name='ID' value=$Id>";
            echo '</form>';
            // Вывод сообщений
            echo '<strong id="mainname">' .$row['name'] .'</strong>'; 
            echo '<div class="date">' . "&emsp;&emsp;&nbsp". date('d' , strtotime($row['date'])) . " $month " . date('Y' , strtotime($row['date'])) . ' ' . date('H:i:s', strtotime($row['date'])) . '</div><br><br />';          
            echo '<img id="img" src="images/ava.jpg" width="93" height="93" alt="Винни-Пух">';
            echo '<div class="main_message">'. $row['message'] . '</div>';
            echo '<div class="message_footer">Сообщение № ' . $number_of_count . '</div><hr>';
            $i++;
            $number_of_count++;
        }
    }
    ?>
    <div>
    <?php
        echo '<div id ="link" >'. generate_link_page($num_pages, $cur_page) . ' </div>';
?>
   <form method="post" action="add_message.php" >
   <label for="username">Ваше имя: </label>
   <input type="text" id="username" name="name">
   <p><b>Введите Ваше сообщение:</b></p>
   <div editable><textarea name="comment" rows="10" placeholder='Введите текст'>
   </textarea autofocus wrap="soft"></div>
   <input type="submit" name="submit">
   <input type='hidden' name='Option' value= 'Add'>
   </form>
<?php
include('footer.html');