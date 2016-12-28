<?php

	$P_Id = $_GET['pid'];
	$U_Id = $_GET['uid'];

?>
<script type="text/javascript">

	window.onload = function(){
	
	<?php
	
	if($U_Id != NULL){
		echo "parent.reloadVote(".$P_Id.")";
	}else{
		echo "parent.displayLogin()";
	}
	?>
	
	}
</script>
<?php

	require('../objects/voteobject.php');

	if($U_Id != NULL && $P_Id != NULL){

		$voter = new VoteInserter();
		$voter -> downVote();
		$voter -> vote($P_Id,$U_Id);
	
	}
	
?>