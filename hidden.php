<?php
    session_start();

    if(!isset($_SESSION['loggedIN'])){
        header('Location: redeem.php');
        exit();
    }

    echo 'You are registered';
?>

<a href='exit.php'>Return to signIn</a><br>