<?php

	require('../objects/commentobject.php');

	$comment = new CommentInserter($_POST['pid'],$_POST['uid']);
	$comment -> setContent($_POST['content']);
	$comment -> executeQuery();
	
	header("Location:../index.php?mode=load&kb=".$_POST['pid']);

?>