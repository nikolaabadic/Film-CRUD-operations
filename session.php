<?php
    include 'operations.php';

    session_start();

    if(isset($_SESSION['loggedIn'])){
        exit('loggedIn');
    }

    if(isset($_POST['login'])){
        $operations = new Operations();
        $operations->login();
    }
?>