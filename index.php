<?php

//session initialization
session_start();
// end of session initialization
//$_SESSION['U_Id'] = 8;
//$_SESSION['loggedIn'] = "n";
//$_SESSION['U_Id'] = NULL;

$title = "Index Page";

if(isset($_GET['mode'])){

	$mode = $_GET['mode'];
		
}else{

	$mode = "browse";
}

?>
<!DOCTYPE html>
<html>
	<title><?php echo $title ?></title>
	
	<head>
		<script type="text/javascript" src="tinymce/js/tinymce/tinymce.min.js"></script>
		<script type="text/javascript" charset="utf-8">
		
		tinymce.init({
			menubar : false,
			selector: "textarea",
			statusbar: false,
			plugins: "image"
			});
		
		</script>
		<script type="text/javascript">
		
			var gPID;
					
			//load upvote.php into the hidden frame
			function upVote(PID){
				<?php 
				if($_SESSION['loggedIn'] == "y"){
					echo 'document.getElementById("hidden").src = "controller/upvote.php?pid="+PID+"&uid='.$_SESSION['U_Id'].'"';
				}else{
					echo 'displayLogin();'; 
				}
				?>
			}
			
			//load the downvote function into the hidden frame
			function downVote(PID){
				<?php 
				if($_SESSION['loggedIn'] == "y"){
					echo 'document.getElementById("hidden").src = "controller/downvote.php?pid="+PID+"&uid='.$_SESSION['U_Id'].'"';
				}else{
					echo 'displayLogin();'; 
				}
				?>
			
			}
			
			function reloadVote(PID){
			
				document.getElementById("hidden").src = "controller/getvote.php?pid="+PID;
			
			}
			
			function appVote(PID,vote){
			
				document.getElementById(PID).innerHTML = vote;
			
			}
						
			//login functions
			function displayLogin(){
			
				document.getElementById("window-cover").style.display ="block";
				document.getElementById("loginbox").style.display = "block";
			
			}
			
			function hideLogin(){
			
				document.getElementById("window-cover").style.display ="none";
				document.getElementById("loginbox").style.display = "none";
			
			}
			
			function login(){
		
				var username = document.getElementById("username").value;
				var password = document.getElementById("password").value;
				
				if(username == "" || password == ""){
				
					document.getElementById("loginbox").innerHTML += "Please Enter a Username and Password";
				
				}else{
				
					hideLogin();
					
				
				}
		
			}
					
		
		</script>
		
		<link rel="stylesheet" type="text/css" href="stylesheets/browsearticles.css">
		<link rel="stylesheet" type="text/css" href="stylesheets/main.css">
	
	</head>
	<body>
		
	<?php include("includes/header.php"); ?>
		
		<div id="main">
			<?php 
				if($mode == "browse"){include("views/browsearticles.php");} 
				if($mode == "load"){include("views/loadarticle.php");$articleInfo="y";}
				if($mode == "create"){include("views/createarticle.php");}
				if($mode == "edit"){include("views/createarticle.php");}
			?>
		</div>
	
	<?php include("includes/sidepane.php"); ?>
		
		<div id="window-cover"></div>
		<div id="loginbox">
			<form action="controller/verifylogin.php" method="post" >
			Username: <input name="username" />
			Password: <input name="password" type="password" />
			<input name="mode" value="<?php echo $mode; ?>" style="display:none" />
			<input name="kb" value="<?php echo $P_Id; ?>" style="display:none" />
			<button>Submit</button>
			</form><button onclick="hideLogin()">Close</button>
		</div>
		
		<iframe id="hidden" name="hidden"></iframe>
		
	</body>
	
	
</html>