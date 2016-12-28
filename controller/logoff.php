<?php

 session_start();
 
 $_SESSION['loggedIn'] = "n";
 $_SESSION['U_Id'] = NULL;

 header("location:../index.php");
?>