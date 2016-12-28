<?php

class keywordGrabber {

	protected $database;
	public $status;
	
	private $values = array();
	private $ID;
	private $switchQ;
	
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
	
	public function getArticleId(){
	
		$this -> add('P_Id');
	
	}
	
	public function getWord() {
	
		$this -> add('word');
	
	}
		
	private function add($column){
	
        $this->values[] = $column;
		
    }
	
	/*
	* @param /Integer $articleId the id of the article 
	*/
	public function searchByP_Id($articleID){
	
		$this->search = "WHERE P_Id = ?";
		$this->ID = $articleID;
		$this->switchQ = 0;
	
	}
	
	/*
	* @param /String $word the keyword you are search for
	*/
	public function searchByWord($word) {
	
		$this->search = "WHERE word = ?";
		$this->ID = $word;
		$this->switchQ = 1;
	
	}
	
	/*
	* @return $resArray /ARRAY associate array of profile row
	*/
	public function executeQuery(){
	
		//construct the MYSQL Query
		$query = 'SELECT ';
		$query .= implode(",",$this -> values);
		$query .= " FROM KeyWords ";
		$query .= $this -> search;
		
		//Bind the Paramaters and Execute
		$resArray = array();
		if($stmt = $this -> database -> prepare($query)){
		
			if($this -> switchQ == 0){$stmt->bindParam(1, $this -> ID, PDO::PARAM_INT);}
			if($this -> switchQ == 1){$stmt->bindParam(1, $this -> ID, PDO::PARAM_STR);}
		
			$stmt -> execute();
			$reArray = $stmt -> fetchAll(PDO::FETCH_ASSOC);
			$this -> status = "Select Successful";
		
		}else{
			
			$this -> status = "Select failed with ".$this -> database -> error;
			
		}
		
		//Return Result
		return $reArray;
	
	}
	
	
	
}

class keywordInserter {

	protected $database;
	public $status;
	
	private $ID;
	private $word;
	
	/*
	* @param /Integer $articleID Id of the article you are commenting on
	* @param /Integer $userID Id of the user who is commenting
	* @sets /MYSQLI $database
	* @sets /String $status
	* reports on the status of the database connection
	*/
	public function __construct ($articleID) {
	
	require('databasesettings.php');
		
		$dsn = "mysql:host=".$server.";dbname=".$dbName.";charset=utf8";
		$opt = array(
						PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
						PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
				);
		$this -> database = new PDO($dsn,$user,$password,$opt);
		
		$this -> ID = $articleID;
		
	}
	
	
	/*
	* @param /String $content the content of the article
	*/
	public function setWord($word){
	
		$this->word = $word;
	
	}
	
   
	public function executeQuery(){
		
			$query = "INSERT INTO KeyWords (P_Id,word) VALUES (?,?)";
			
			if($stmt = $this -> database -> prepare($query)){
			
				$stmt->bindParam(1, $this -> ID, PDO::PARAM_INT);
				$stmt->bindParam(2, $this -> word, PDO::PARAM_STR);
				
				$stmt -> execute();
				$this -> status = "Insert Successful";
			
			}else{
				
				$this -> status = "Prepare failed with ".$this -> database -> error;
				
			}
			
		
	}
	
	public function executeQueryDelete(){
		
			$query = "DELETE FROM KeyWords WHERE P_Id = ? AND word = ?";
			
			if($stmt = $this -> database -> prepare($query)){
			
				$stmt->bindParam(1, $this -> ID, PDO::PARAM_INT);
				$stmt->bindParam(2, $this -> word, PDO::PARAM_STR);
				
				$stmt -> execute();
				$this -> status = "Delete Successful";
			
			}else{
				
				$this -> status = "Prepare failed with ".$this -> database -> error;
				
			}
			
		
	}
	
}
	
/*  	
$unittest = new keywordGrabber();
$unittest -> getArticleId();
$unittest -> getWord();
$unittest -> searchByWord("amazing");
$result = $unittest -> executeQuery();

echo $unittest -> status;
var_dump($result);
*/
?>