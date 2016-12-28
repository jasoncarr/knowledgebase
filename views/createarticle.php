<?php

$U_Id = $_SESSION['U_Id'];


$title = "";
$keywordString = "";
$content = "";

//get the Categories Available to the user_error
require('objects/categoryobject.php');
$category = new CategoryGrabber();
$catArray = $category -> getCategories($U_Id);

if($mode == "edit" && $_GET['kb'] != NULL){

	$P_Id = $_GET['kb'];
				
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

	
	if($U_Id == $result_article[0]['createdBy']){
	
		//populate the variables
		$title = $result_article[0]['title'];
		
		foreach($result_keyword as $word){
		
			$keyword_array[] = $word['word'];
		
		}
		
		$keywordString = implode(",",$keyword_array);
		$content = $result_article[0]['content'];
	
	}else{
	
		$P_Id = "new";
		
	}


}else{

$P_Id = "new";

}

?>

<div id="all">
	<div id="commenter">
		<!-- Place this in the body of the page content -->
		<form action="controller/submitarticle.php" method="post" >
		Title:<input name="title" value="<?php echo $title; ?>"></input><br />
		keywords(seperate with a comma):<input name="keyword" value="<?php echo $keywordString; ?>"></input>
		<select name="category">
		<?php
			foreach($catArray as $item){
				echo '<option value="'.$item['category'].'">'.$item['category'].'</option>';
			}
		?>
		</select>
		<textarea rows=50 id="article-content" name="content" ><?php echo $content;?></textarea>
		<input name="pid" style="display:none" value="<?php echo $P_Id; ?>"></input>
		<input name="uid" style="display:none" value="<?php echo $_SESSION['U_Id']; ?>"></input>
		<button >Submit</button>
		</form>
	</div>
</div>
