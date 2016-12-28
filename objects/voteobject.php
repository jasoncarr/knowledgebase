<?php

class VoteGrabber{

	protected $database;
	public $status;

	public function __construct () {
	
		require('databasesettings.php');
		
		$dsn = "mysql:host=".$server.";dbname=".$dbName.";charset=utf8";
		$opt = array(
						PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
						PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
				);
		$this -> database = new PDO($dsn,$user,$password,$opt);
		
	}
	
	/*
	* @param /Integer $articleId the Id of the article to return the votes
	* @return /Array $result an array of the votes with the U_Id who voted
	*/
	public function getUpVotes($articleId){
	
		$query = "SELECT * FROM UpVotes WHERE P_Id = ?";
		if($stmt = $this -> database -> prepare($query)){
		
			$stmt -> bindParam(1,$articleId,PDO::PARAM_INT);
			$stmt -> execute();
			$result = $stmt -> fetchAll();
			$this -> status .= "Selecting upvotes successful";
		
		}else{
			$this -> status .= "Failed to grab upvotes ".$this -> database -> error."<br />";
		}
		
		return $result;
	
	}
	
	public function getDownVotes($articleId){
	
		$query = "SELECT * FROM DownVotes WHERE P_Id = ?";
		if($stmt = $this -> database -> prepare($query)){
		
			$stmt -> bindParam(1,$articleId,PDO::PARAM_INT);
			$stmt -> execute();
			$result = $stmt -> fetchAll();
			$this -> status .= "Selecting upvotes successful";
		
		}else{
			$this -> status .= "Failed to grab upvotes ".$this -> database -> error."<br />";
		}
		
		return $result;
	
	}

}

class VoteInserter{

	protected $database;
	public $status;
	private $switchD;

	public function __construct () {
	
		require('databasesettings.php');
		
		$dsn = "mysql:host=".$server.";dbname=".$dbName.";charset=utf8";
		$opt = array(
						PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
						PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
				);
		$this -> database = new PDO($dsn,$user,$password,$opt);
		
	}
	
	/*
	* Run these to tell the object if you are upvoting or downvoting
	*/
	public function downVote(){
	
		$this -> switchD = "0";
	
	}
	
	public function upVote(){
	
		$this -> switchD = "1";
	
	}
	
	/*
	* @function vote Takes the votes and directs them towards another fumcton depending on if the vote has been already placed
	* @param /Integer $articleID the Id of the article being votes on
	* @param /Integer $userID the ID of the user placing the vote
	*/
	public function vote($articleID, $userID){
	
		// query database for vote
		
		if($this -> switchD == 0){
			$query = "SELECT * FROM DownVotes WHERE P_Id = ? AND U_Id = ?";
		}else if($this -> switchD == 1){
			$query = "SELECT * FROM UpVotes WHERE P_Id = ? AND U_Id = ?";
		}
		
		if($stmt = $this -> database -> prepare($query)){
		
			$stmt -> bindParam(1, $articleID, PDO::PARAM_INT);
			$stmt -> bindParam(2, $userID, PDO::PARAM_INT);
			$stmt -> execute();
			
			//test to see if vote had alredy been placed
			if($result = $stmt -> fetchAll()){
				$this -> unVoteUp($articleID, $userID);
			}else{
				$this -> voteUp($articleID, $userID);
			}
		
		}else{
		
			$this -> status .= "Could not Select Votes ".$this -> database -> error."<br />";
		
		}
	
	}
	
	/*
	* @param /Integer $articleID the id of the article being voted up
	* @param /Integer $userID the user id of the person voting the article up
	*/
	private function voteUp($articleID, $userID){
	
		//determine which table to use based on upVote() or downVote()
		if( $this -> switchD == 0){
			$query = "INSERT INTO DownVotes (P_Id,U_Id) VALUES (?,?)";
		} else if( $this -> switchD == 1){
			$query = "INSERT INTO UpVotes (P_Id,U_Id) VALUES (?,?)";
		}

		//add vote into table
		if($stmt = $this -> database -> prepare($query)){
		
			$stmt -> bindParam(1, $articleID, PDO::PARAM_INT);
			$stmt -> bindParam(2, $userID, PDO::PARAM_INT);
			$stmt -> execute();
		
		
			//get the UserID for the user who create the article
			$votedID = $this -> getUserId($articleID);
			
			//add vote into profile table
			if($this -> switchD == 0){
				$query = "UPDATE profile SET numOfDownVotes = numOfDownVotes + 1 WHERE U_ID = ?";
			}else if($this -> switchD == 1){
				$query = "UPDATE profile SET numOfUpVotes = numOfUpVotes + 1 WHERE U_ID = ?";
			}
			
			$this -> status .= $query."<br />";
			
			if($stmt = $this -> database -> prepare($query)){
			
				$stmt -> bindParam(1, $votedID, PDO::PARAM_INT);
				$stmt -> execute();
				$this -> status .= "Vote successfully updated in the profile";
			
			}else{
				$this -> status .= "Failed to update profile ".$this -> database -> error."<br />";
			}
		
		}else{
			$this -> status .= "Failed to update upVote table ".$this -> database -> error."<br />";
		}
		
		//change the switch and call the unVoteUp function to undo any opposite voting
		if( $this -> switchD == 0){
			$this -> switchD = 1;
		} else if( $this -> switchD == 1){
			$this -> switchD = 0;
		}
		
		$this -> unVoteUp($articleID, $userID);
	
	}
	
		/*
	* @param /Integer $articleID the id of the article being voted up
	* @param /Integer $userID the user id of the person voting the article up
	*/
	private function unVoteUp($articleID, $userID){
	
		//add vote into UpVote table
		if($this -> switchD == 0){
			$query = "DELETE FROM DownVotes WHERE P_Id = ? AND U_Id = ?";
		}else if($this -> switchD == 1){
			$query = "DELETE FROM UpVotes WHERE P_Id = ? AND U_Id = ?";
		}
		
		if($stmt = $this -> database -> prepare($query)){
		
			$stmt -> bindParam(1, $articleID, PDO::PARAM_INT);
			$stmt -> bindParam(2, $userID, PDO::PARAM_INT);
			$stmt -> execute();
		
		
			//get the UserID for the user who create the article
			$votedID = $this -> getUserId($articleID);
			
			//add vote into profile table
			if($this -> switchD == 0){
				$query = "UPDATE profile SET numOfDownVotes = numOfDownVotes - 1 WHERE U_ID = ?";
			}
			else if($this -> switchD == 1){
				$query = "UPDATE profile SET numOfUpVotes = numOfUpVotes - 1 WHERE U_ID = ?";
			}
			
			$this -> status .= $query."<br />";
			
			if($stmt = $this -> database -> prepare($query)){
			
				$stmt -> bindParam(1, $votedID, PDO::PARAM_INT);
				$stmt -> execute();
				$this -> status .= "Vote successfully updated in the profile ";
			
			}else{
				$this -> status .= "Failed to update profile ".$this -> database -> error."<br />";
			}
		
		}else{
			$this -> status .= "Failed to update upVote table ".$this -> database -> error."<br />";
		}
	
	}
	
	/*
	* $param /Integer $articleID the id of the article
	* $returns /Intger $userID the id of the user that created the article
	*/
	public function getUserId($articleID){
	
		$query = "SELECT createdBy FROM Article WHERE P_Id = ?";
		if($stmt = $this -> database -> prepare($query)){
			$stmt -> bindParam(1, $articleID, PDO::PARAM_INT);
			$stmt -> execute();
			$result = $stmt -> fetchAll(MYSQL_NUM);
			$userID = $result[0]['createdBy'];
			return $userID;
		}else {
			return $this -> database -> error."<br />";
		}
	
	}

}

//$unittest = new VoteInserter();
//$unittest2 = new VoteGrabber();

//echo $unittest -> getUserId(2);


//Insert an Upvote
//$unittest -> upVote();
//$unittest -> vote(2,8);

/*
//Check Vote table to see if has been imputted
$result = $unittest2 -> getUpVotes(2);
echo "UpVote table has the following data<br />";
var_dump($result);
echo "<br />";

$result = $unittest2 -> getDownVotes(2);
echo "DownVote table has the following data<br />";
var_dump($result);
echo "<br />";

//Insert a Dowvote while the upvote still in place;
$unittest -> downVote();
$unittest -> vote(2,8);


//Check Vote Table
$result = $unittest2 -> getUpVotes(2);
echo "UpVote table has the following data<br />";
var_dump($result);
echo "<br />";

$result = $unittest2 -> getDownVotes(2);
echo "DownVote table has the following data<br />";
var_dump($result);
echo "<br />";
echo $unittest -> status;
*/
?>

