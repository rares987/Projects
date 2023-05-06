<?php
session_start();
require __DIR__ . '/usermodel.php';

    if (credentialsExist($_REQUEST['username'], $_REQUEST['password'])){
        $_SESSION['fail_to_login'] = '0';
        $_SESSION['logged_in_user'] = '1';
        $_SESSION['login_user'] = $_REQUEST['username'];
        header ("location: index.php");
        exit;
    }
    else{
        $_SESSION['fail_to_login'] = '1';
        $_SESSION['logged_in_user'] = '0';
        header ("location: index.php");
        exit;
    }
?>