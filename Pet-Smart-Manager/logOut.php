<?php
session_start();
        $_SESSION['logged_in_user'] = '0';
        $_SESSION['login_user'] = '0';
        header ("location: index.php");
        exit;
    //require 'view.php';
?>