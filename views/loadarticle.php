<?php

	$P_Id = $_GET['kb'];
	$U_Id = $_SESSION['U_Id'];
				
	require('objects/articleobject.php');
	require('objects/commentobject.php');
	require('objects/voteobject.php');
	require('objects/keywordobject.php');
			
				
	//get the article
	$article = new ArticleGrabber();
	$article -> getTitle();
	$article -> getContent();
	$article -> getCreatedBy();
	$article -> searchByP_Id();
	$result_article = $article -> executeQuery($P_Id);
	
	//get the comments
	$comment = new CommentGrabber();
	$comment -> getUserId();
	$comment -> getContent();
	$comment -> getUserName();
	$comment -> searchByP_Id($P_Id);
	$result_comment = $comment -> executeQuery();
	
	//get the votes
	$vote = new VoteGrabber();
	$upvotes = $vote -> getUpVotes($P_Id);
	$downvotes = $vote -> getDownVotes($P_Id);
	
	//get the keywords
	$keywords = new KeywordGrabber();
	$keywords -> getWord();
	$keywords -> searchByP_Id($P_Id);
	$keywords -> searchByP_Id($P_Id);
	$result_keyword = $keywords -> executeQuery();
	
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


?>
				
		<style>
		
			
			#article{
			
				width: 100%;
			
			}
			
			#article-title{
				padding-top: 10px;
				padding-bottom: 5px;
				text-align: center;
				
			}
			
			#article-content{
				padding: 10px;
				border-bottom: 1px dashed black;
				border-top: 1px dashed black;
			}
			
			.comment{
				
				width: 100%
				border: 1px solid black;
			}
			
			.comment-content{
			
				width: 100%;
				border-bottom: 1px solid black;
				padding-left: 10px;
			
			}
			
			.comment-username{
			
				text-align: right;
			}
		
		</style>
		
		<div id="all">
			<?php

				
				//create the article HTML
				echo '<div id="article">';
				echo '<div id="article-title">';
				echo $result_article[0]['title'];
				echo '</div>';
				echo '<div id="article-content">';
				echo $result_article[0]['content'];
				echo '</div>';
				echo '</div>';
				
			?>

			<?php
			
				//display the comments
				if($result_comment != NULL){
				
					foreach($result_comment as $item){
				
						//create the comment HTML
						echo '<div class="comment">';
						echo '<div class="comment-content">';
						echo $item['content'];
						echo '</div>';
						echo '<div class="comment-username">';
						echo 'Comment by '.$item['username'];
						echo '</div>';
						echo '</div>';
					
					}
				
				}
				
				//display the comment box if the user is logged in
				if($_SESSION['loggedIn'] == "y"){
				
					$displayCommenter = "display:block";
					$displayCommenter2 = "display:none";
				
				}else{
				
					$displayCommenter = "display:none";
					$displayCommenter2 = "display:block";
				}

			?>
			
			<div id="commenter" style="<?php echo $displayCommenter; ?>">
			Leave a comment:<br />
			<form  action="controller/submitcomment.php" method="post" >
				<textarea id="comment-area" name="content" ></textarea>
				<input name="pid" style="display:none" value="<?php echo $P_Id; ?>"></input>
				<input name="uid" style="display:none" value="<?php echo $_SESSION['U_Id']; ?>"></input>
				<button >Submit</button>
			</form>
			</div>
			
			<div id="commenter2" style="padding:5px;<?php echo $displayCommenter2; ?>">
			
				<a onclick="displayLogin()" style="cursor:pointer">Login</a> to leave a comment
			
			</div>
		</div>
