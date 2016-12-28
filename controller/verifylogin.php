<?php
session_start();
require('../objects/loginobject.php');

$username = $_POST['username'];
$password = $_POST['password'];

if(isset($_POST['mode'])){

	$mode = "?mode=".$_POST['mode'];

}else{ $mode = "";}

if($mode == "load"){

	$kb = "&kb=".$_POST['kb'];

}else{ $kb = ""; }

$login = new loginGrabber();

$U_Id = $login -> login($username,$password);

if($U_Id == 0){

	header("location:../index.php".$mode.$kb);

}else{

	echo "login successful";
	$_SESSION['loggedIn'] = "y";
	$_SESSION['U_Id'] = $U_Id;
	header("location:../index.php".$mode.$kb);	

}

?>