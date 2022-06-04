<?php

if(!session_id()){
    session_start();
}

unset($_SESSION['access_token']);
unset($_SESSION['state']);

unset($_SESSION['userData']);

header("Location:index.php");
?>