<?php

class profileGrabber {

	protected $database;
	public $status;
	
	private $colArray = array();
	private $values = array();
	private $U_Id;
	
	/*
	* @param /Integer $userID 
	* @sets /MYSQLI $database
	* @sets /String $status Reports on the status of the database connection
	*/
	public function __construct ($userID) {
	
		require('databasesettings.php');
		
		$this -> U_Id = $userID;
		
		$dsn = "mysql:host=".$server.";dbname=".$dbName.";charset=utf8";
		$opt = array(
						PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
						PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
				);
		$this -> database = new PDO($dsn,$user,$password,$opt);
		
	}
	
	public function getName(){
	
		$this -> add("name");
	
	}
	
	public function getDepartment() {
	
		$this -> add("department");
	
	}
	
	public function getUserLevel() {
	
		$this -> add("userLevel");
	
	}
	
	public function getNumOfArticles() {
	
		$this -> add("numOfArticles");
	
	}
	
	public function getNumOfComments() {
	
		$this -> add("numOfComments");
	
	}
	
	
	public function getNumOfUpdates() {
	
		$this -> add("numOfUpdates");
	
	}
	
	public function getNumOfUpVotes() {
	
		$this -> add("numOfUpVotes");
	
	}
	
	public function getNumOfDownVotes() {
	
		$this -> add("numOfDownVotes");
	
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
		$query .= " FROM profile ";
		$query .= " WHERE U_Id = ?";
		
		$resArray = array();
		
		if($stmt = $this -> database -> prepare($query)){
		
			$stmt->bindParam(1, $this -> U_Id, PDO::PARAM_INT);
		
			$stmt -> execute();
			$reArray = $stmt -> fetchAll();
			$this -> status = "Select Successful";
		
		}else{
			
			$this -> status = "Select failed with ".$this -> database -> error;
			
		}
		
		return $reArray;
	
	}
	
	
	
}

class profileInserter {

	protected $database;
	public $status;
	
	private $colArray = array();
	private $values = array();
	private $U_Id;
	
	/*
	* @param /Integer $U_Id sets that User Id
	* @sets /MYSQLI $database
	* @sets /String $status
	* reports on the status of the database connection
	*/
	public function __construct ($userID) {
	
	require('databasesettings.php');
		
		$dsn = "mysql:host=".$server.";dbname=".$dbName.";charset=utf8";
		$opt = array(
						PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
						PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
				);
		$this -> database = new PDO($dsn,$user,$password,$opt);
		
		$this -> U_Id = $userID;
	}
	
	/*
	* @param /String $name the name of the user
	*/
	public function setName($name){
	
		$this->add($name,'name');
	
	}
	
	/*
	* @param /String $department the department of the user
	*/
	public function setDepartment($department){
	
		$this->add($department,'department');
	
	}
	
	/*
	* @param /Integer $userLevel the access level of the user
	*/
	public function setUserLevel($userLevel){
	
		$this->add($userLevel,'userLevel');
	
	}
	
	private function add($value,$column){
	
		$this->colArray[] = $column."=?";
        $this->values[] = $value;
		
    }
   
	public function executeQuery(){
	
		$query = 'UPDATE profile SET ';
		$query .= implode(",",$this -> colArray);
		$query .= " WHERE U_Id = ".$this -> U_Id;
		
		if($stmt = $this -> database -> prepare($query)){
		
			$stmt -> execute($this -> values);
			$this -> status = "Insert Successful";
		
		}else{
			
			$this -> status = "Prepare failed with ".$this -> database -> error;
			
		}
	
	}
	
  	
}

//$unittest = new profileGrabber(8);
//$unittest -> getName();
//$unittest -> getDepartment();
//$unittest -> getUserLevel();
//$result = $unittest -> executeQuery();
//$unittest -> status;
//var_dump($result);

?>