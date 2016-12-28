<?php

class commentGrabber {

	protected $database;
	public $status;
	
	private $colArray = array();
	private $values = array();
	private $Id;
	
	/*
	* @sets /MYSQLI $database
	* @sets /String $status Reports on the status of the database connection
	*/
	public function __construct () {
	
		require('databasesettings.php');
		
		$dsn = "mysql:host=".$server.";dbname=".$dbName.";charset=utf8";
		$opt = array(
						PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
						PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
				);
		$this -> database = new PDO($dsn,$user,$password,$opt);
		
	}
	
	public function getUserId(){
	
		$this -> add("Comments.U_Id");
	
	}
	
	public function getArticleId() {
	
		$this -> add("Comments.P_Id");
	
	}
	
	public function getContent() {
	
		$this -> add("Comments.content");
	
	}
	
	public function getUserName() {
	
		$this -> add("login.username");
	
	}
	
	public function searchByU_Id($Id) {
	
		$this -> search = "WHERE U_Id = ?";
		$this -> Id = $Id;
	}
	
	public function searchByP_Id($Id) {
	
		$this -> search = "WHERE P_Id = ?";
		$this -> Id = $Id;
	
	}
	
	private function add($column){
	
        $this->values[] = $column;
		
    }
	
	/*
	* @return $resArray /ARRAY associate array of profile row
	*/
	public function executeQuery(){
	
		$query = 'SELECT ';
		$query .= implode(",",$this -> values);
		$query .= " FROM Comments INNER JOIN login ON Comments.U_Id=login.U_Id ";
		$query .= $this -> search;
		
		//echo $query;
		
		$resArray = array();
		
		if($stmt = $this -> database -> prepare($query)){
		
			$stmt->bindParam(1, $this -> Id, PDO::PARAM_INT);
		
			$stmt -> execute();
			$reArray = $stmt -> fetchAll();
			$this -> status = "Select Successful";
		
		}else{
			
			$this -> status = "Select failed with ".$this -> database -> error;
			
		}
		
		return $reArray;
	
	}
	
	
	
}

class commentInserter {

	protected $database;
	public $status;
	
	private $colArray = array();
	private $valArray = array();
	private $values = array();
	
	/*
	* @param /Integer $articleID Id of the article you are commenting on
	* @param /Integer $userID Id of the user who is commenting
	* @sets /MYSQLI $database
	* @sets /String $status
	* reports on the status of the database connection
	*/
	public function __construct ($articleID,$userID) {
	
	require('databasesettings.php');
		
		$dsn = "mysql:host=".$server.";dbname=".$dbName.";charset=utf8";
		$opt = array(
						PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
						PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
				);
		$this -> database = new PDO($dsn,$user,$password,$opt);
		
		$this -> add($userID,'U_Id');
		$this -> add($articleID, 'P_Id');
		
	}
	
	
	/*
	* @param /String $content the content of the article
	*/
	public function setContent($content){
	
		$this->add($content,'content');
	
	}
	
	
	private function add($value,$column){
	
		$this->colArray[] = $column;
		$this->valArray[] = "?";
        $this->values[] = $value;
		
    }
   
	public function executeQuery(){
		
			$query = "INSERT INTO Comments (";
			$query .= implode(",",$this -> colArray);
			$query .= ") VALUES (";
			$query .= implode(",",$this -> valArray);
			$query .= ")";
			
			var_dump($this -> values);
			
			if($stmt = $this -> database -> prepare($query)){
			
				$stmt -> execute($this -> values);
				$this -> status = "Insert Successful";
				
				//update profile
				$query = "UPDATE profile SET numOfComments = numOfComments + 1 WHERE U_Id = ?";
				$stmt = $this -> database -> prepare($query);
				$stmt -> bindParam(1,$this -> U_Id,PDO::PARAM_INT);
				$stmt -> execute();
			
			}else{
				
				$this -> status = "Prepare failed with ".$this -> database -> error;
				
			}
			
		
		}
	
}
	
  	


//$unittest = new commentGrabber();
//$unittest -> getArticleId();
//$unittest -> getUserId();
//$unittest -> getContent();
//$unittest -> searchByU_Id(8);
//$result = $unittest -> executeQuery();

//echo $unittest -> status;
//var_dump($result);

?>