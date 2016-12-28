<div id="sidepane-right">

	<ul id="navlist">
	<?php 
		if($_SESSION['loggedIn'] != "y"){
			echo '<a id="register" style="cursor:pointer"><li>Register</li></a>'; 
			echo '<a id="login" style="cursor:pointer" onclick="displayLogin()"><li>Login</li></a>';
		}else{
			echo '<a href="controller/logoff.php"><li>Logoff</li></a>';
		}
	?>
		<a href="index.php"><li>Home</li></a>
		<a href="index.php?browse=all"><li>Categories</li></a>
		<?php if($_SESSION['loggedIn'] == "y"){ echo '<a href="index.php?mode=create"><li>Create</li></a>'; }?>
		<?php if($_SESSION['loggedIn'] == "y"){ echo '<a onclick="profile()"><li>Profile</li></a>'; }?>
		
	</ul>
	
	<div id="contextbox">
	
		<?php
		
			//get info for sidebar
	$username = $result_article[0]['username'];
	$num_of_comments = count($result_comment);
	$num_of_upvotes = count($upvotes);
	$num_of_downvotes = count ($downvotes);
	$createdBy = $result_article[0]['createdBy'];
	$article_id = $P_Id;
	
	foreach($result_keyword as $word){
	
		$keywordArray[] = $word['word'];
	
	}
	
	if($result_article[0]['createdBy'] == $_SESSION['U_Id']){
				
			$edit_button = TRUE;
				
	}
	
	//need to add this functionality
	$percentVotes = "0% liked";
		
			if($articleInfo == "y"){
			
				echo '<span id="box-buttons"><a class="upvote" onclick="upVote('.$article_id.')"><img src="images/upvote.gif" /></a><a class="downvote" onclick="downVote('.$article_id.')"><img src="images/upvote.gif" /></a></span>';
				echo '<span id="box-name">Created By <a onclick="loadProfile('.createdBy.')">'.$username.'</a></span>';
				echo '<span id="box-comments">'.$num_of_comments.' Comments</span>';
				echo '<span id="box-votes"><span id="totalvotes">'.($num_of_upvotes-$num_of_downvotes).' total votes</span><span id="percentagevotes">('.$percentVotes.')</span></span>';
			
			
			}
	
		?>
	</div>
	
	<div id="keywordbox">
	<div id="keywordbox-inner">
	</div>
	</div>

</div>