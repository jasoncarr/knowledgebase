<?php

		$P_Id = $_GET['pid']; 

		require('../objects/voteobject.php');
		$votelist = new voteGrabber();
		$upVoteResult = $votelist -> getUpVotes($P_Id);
		$downVoteResult = $votelist -> getDownVotes($P_Id);
		
		
?>
<script>

	window.onload = function () {
	
		var vote = <?php echo count($upVoteResult)-count($downVoteResult); ?>;
		
		parent.appVote(<?php echo $P_Id; ?>,vote);
	
	
	}


</script>