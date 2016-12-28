<?php

	require('objects/articleobject.php');
	require('objects/voteobject.php');
	require('objects/keywordobject.php');
	require('objects/categoryobject.php');

	$articlelist = new articleGrabber();
	$votelist = new voteGrabber();
	$keywords = new keywordGrabber();
	$category = new categoryGrabber();
	
	//grab the articles
	$articlelist -> getArticleId();
	$articlelist -> getTitle();
	$articlelist -> getCreatedBy();
	$articlelist -> getNumOfComments();
	$articlelist -> getChangedOn();
	$result = $articlelist -> executeQuery('all');
	
	//loop through the articles
	foreach($result as $item){
		
		
		//grab the upvote and downvotes for each article
		$upVoteResult = $votelist -> getUpVotes($item['P_Id']);
		$downVoteResult = $votelist -> getDownVotes($item['P_Id']);
		
		//get the keywords
		$keywords -> getWord();
		$keywords -> searchByP_Id($item['P_Id']);
		$result_keyword = $keywords -> executeQuery();
		
		$keywordArray = array();
		foreach($result_keyword as $word){
		
			$keywordArray[] = '<a style="cursor:pointer" onclick="parent.keywordSearch(\''.$word['word'].'\')">'.$word['word']."</a>";
		
		}
		
		$kHider = "";
		if(count($keywordArray)>0){
			$kHider = "Keywords: ";
		}
		
		//display the articles
		echo '<div class="articlelisting" >'."\n";
		echo 	"\t".'<a class="upvote" id="u'.$item['P_Id'].'" onclick="upVote('.$item['P_Id'].')"><img height="22px" src="images/upvote.png" /></a>'."\n";
		echo 	"\t".'<a class="downvote" id="d'.$item['P_Id'].'" onclick="downVote('.$item['P_Id'].')"><img src="images/upvote.gif" /></a>'."\n";
		echo	"\t".'<div class="listingtitle">'."\n";
		echo		"\t\t".'<span id="'.$item['P_Id'].'" class="votebox">'."\n";
		echo 			"\t\t\t".count($upVoteResult)-count($downVoteResult)."\n";
		echo 		"\t\t".'</span>'."\n";
		echo		"\t\t".'<span class="title">'."\n";
		echo 		"\t\t\t".'<a class="articlelink" href="index.php?mode=load&kb='.$item['P_Id'].'">'.$item['title'].'</a>'."\n";
		echo		"\t\t".'</span>'."\n";
		echo		'<div class="listingtitle-sub">'."\n";
		echo		"\t\t".'<span class="category">'."\n";
		echo 		"\t\t\t".'<a class="articlelink" onclick="parent.loadCategory('.$item['C_Id'].')">'.$names[$item['C_Id']-1]['category'].'</a>'."\n";
		echo		"\t\t".'</span>'."\n";
		echo		"\t\t".'<span class="keywords">'."\n";
		echo 		"\t\t\t".$kHider.implode(",",$keywordArray)."\n";
		echo		"\t\t".'</span>'."\n";
		echo		"\t\t".'<span class="category">'."\n";
		echo 		"\t\t\t".'Created By <a class="articlelink" onclick="parent.loadUserArticles('.$item['createdBy'].')">'.$item['username'].'</a>'."\n";
		echo		"\t\t".'</span>'."\n";
		echo		"\t\t".'<span class="category">'."\n";
		echo 		"\t\t\t".'Last updated on'.$item['changed_on'].'</a>'."\n";
		echo		"\t\t".'</span>'."\n";
		echo		"</div>";
		echo	"\t".'</div>'."\n";
		echo '</div>'."\n\n";
	
	}
	
?>
