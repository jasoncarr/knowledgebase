<?php

	require('../objects/articleobject.php');
	require('../objects/keywordobject.php');
	
	//get the Article ID and the User ID
	$U_Id = $_POST['uid'];
	$P_Id = $_POST['pid'];
	
	if($U_Id == NULL){
	
		header("location:../index.php?mode=browse");
	
	}
	
	//create a new object 
	$submit = new articleInserter($P_Id, $U_Id);
	
	//test for data and configure object accordingly
	if($title = $_POST['title']){$submit -> setTitle($title);}
	if($content = $_POST['content']){$submit -> setContent($content);}
	if($visible = $_POST['visible']){$submit -> setVisible($visible);}
	if($mustread = $_POST['mustRead']){$submit -> setMustRead($mustread);}
	
	$submit -> executeQuery();
		
	//get the keyword string
	if($keyword = $_POST['keyword']){
	
		//make an array of the words
		$keyword_array = explode(",",$keyword);
	
		if($P_Id == "new"){
		
			$P_Id = $submit -> insert_id;
			$keyword = new KeywordInserter($P_Id);
			
			foreach($keyword_array as $word){
			
				$keyword -> setWord($word);
				$keyword -> executeQuery();
			
			}
			
		}else{
	
			//make an array of the words
			$keyword_array = explode(",",$keyword);
		
			//create the objects
			$keywordCurrent = new KeywordGrabber();
			$keyword = new KeywordInserter($P_Id);
			
			//pull current keywords for this article
			$keywordCurrent -> getWord();
			$keywordCurrent -> searchByP_Id($P_Id);
			$result = $keywordCurrent -> executeQuery();
		
			//create one-dimensional array of keywords
			$i = 0;
			foreach($result as $item){
			
				$keyword_array_current[$i] = $item['word'];
				$i++;
			}
			if($keyword_array_current != NULL){
				//test if arrays are the same
				if($keyword_array != $keyword_array_current){
				
					//delete missing words from database and add new ones
					$wordsToDelete = array_diff($keyword_array_current,$keyword_array);
					$wordsToAdd = array_diff($keyword_array, $keyword_array_current);
					
					foreach($wordsToDelete as $word){
					
						$keyword -> setWord($word);
						$keyword -> executeQueryDelete();
					
					}
					
					foreach($wordsToAdd as $word){
					
						$keyword -> setWord($word);
						$keyword -> executeQuery();
					
					}
				
				}
			}else{
			
				foreach($keyword_array as $word){
			
				$keyword -> setWord($word);
				$keyword -> executeQuery();
			
				}
			
			}
	
		}
	
	}
	
if($P_Id == "new"){
header("location:../index.php?mode=load&kb=".$submit -> insert_id);
}else{
header("location:../index.php?mode=load&kb=".$P_Id);
}
	

	
	
	
	

?>