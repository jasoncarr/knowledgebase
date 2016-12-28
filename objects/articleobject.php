<?php

class articleGrabber {

	protected $database;
	public $status;
	
	private $colArray = array();
	private $values = array();
	private $search;
	
	/*
	* @param /Integer $ID can be either a User ID or an Article ID (specify with $this -> searchByU_Id or searchByP_Id)
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
		
		$this -> add('mustRead');
		$this -> add('visible');
		$this -> add('previousVersion');
		
	}
	
	public function getArticleId(){
	
		$this -> add("Article.P_Id");
	
	}
	
	public function getLinkName() {
	
		$this -> add("Article.linkName");
	
	}
	
	public function getTitle() {
	
		$this -> add("Article.title");
	
	}
	
	public function getContent() {
	
		$this -> add("Article.content");
	
	}
	
	public function getCreatedBy(){
	
		$this -> add("login.username");
		$this -> add("Article.createdBy");
	
	}
	
	public function getNumOfComments() {
	
		$this -> add("Article.numOfComments");
	
	}
	
	public function getNumOfUpdates() {
	
		$this -> add("Article.numOfUpdates");
	
	}
	
	public function getNumOfUpVotes() {
	
		$this -> add("Article.numOfUpVotes");
	
	}
	
	public function getNumOfDownVotes() {
	
		$this -> add("Article.numOfDownVotes");
	
	}
	
	public function getPreviousVersionOf() {
	
		$this -> add("Article.previousVersionOf");
	
	}
	
	public function getChangedOn(){
	
		$this -> add("Article.changed_on");
	
	}
	
		
	public function searchByU_Id() {
	
		$this -> search = "WHERE createdBy = ?";
	
	}
	
	public function searchByP_Id() {
	
		$this -> search = "WHERE Article.P_Id = ?";
	
	}
	
	
	private function add($column){
	
        $this->values[] = $column;
		
    }
	
	/*
	* @return $resArray /ARRAY associate array of profile row
	*/
	public function executeQuery($Id){
	
		$query = 'SELECT ';
		$query .= implode(",",$this -> values);
		$query .= " FROM Article INNER JOIN login ON Article.createdBy=login.U_Id ";
		
		
		$resArray = array();
		
		if($Id != "all"){
		
			$query .= $this -> search;
			
		
			if($stmt = $this -> database -> prepare($query)){
			
			
				$stmt->bindParam(1, $Id, PDO::PARAM_INT);
				//$stmt->bindParam(2, $this -> visible, PDO::PARAM_STR);
			
				$stmt -> execute();
				$reArray = $stmt -> fetchAll();
				$this -> status = "Select Successful";
			
			}else{
				
				$this -> status = "Select failed with ".$this -> database -> error;
				
			}
			
		}else{
		
			$query .=  'ORDER BY changed_on DESC';
			$stmt = $this -> database -> query($query);
			$reArray = $stmt -> fetchAll();
			
		
		}
		return $reArray;
	
	}
	
	
	
}

class articleInserter {

	protected $database;
	public $status;
	
	private $colArray = array();
	private $valArray = array();
	private $values = array();
	private $P_Id;
	private $U_Id;
	
	public $insert_id;
	
	/*
	* @param /Integer $articleID sets that article Id ('new' = new article)
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
		
		if($articleID == 'new'){
			
			$this -> U_Id = $userID;
			$this -> setCreateBy();
		
		}
			
		$this -> P_Id = $articleID;
		
		
	}
	
	/*
	* @param /String $title the title of the article
	*/
	public function setTitle($title){
	
		$this->add($title,'title');
	
	}
	
	/*
	* @param /String $content the content of the article
	*/
	public function setContent($content){
	
		$this->add($content,'content');
	
	}
	
	/*
	* @param /String $catergory the catergory of the article
	*/
	public function setCatergory($catergory){
	
		$this->add($catergory,'catergory');
	
	}
	
	/*
	* @param /String $mustRead whether or not the article is must read
	*/
	public function setMustRead($mustRead){
	
		$this->add($mustRead,'mustRead');
	
	}
	
	public function setVisible($visible){
	
		$this->add($visible, "visible");
	
	}
	
	public function setPreviousVersion($previousVersion){
	
		$this->add($previousVersion,"previousVersion");
	
	}
	
	public function setPreviousVersionOf($previousVersionOf){
	
		$this->add($setPreviousVersionOf,"previousVersionOf");
	
	}
	
	public function setDefaults(){
	
		$this->add("n",'mustRead');
		$this->add("y", "visible");
		$this->add("n","previousVersion");
	
	}
	
	
	
	private function setCreateBy(){
	
		$this->add($this -> U_Id,'createdBy');
	
	}
	
	private function add($value,$column){
	
		$this->colArray[] = $column;
		$this->valArray[] = "?";
        $this->values[] = $value;
		
    }
   
	public function executeQuery(){
	
		// If the article is insert data without the Article ID
		
		if($this -> P_Id == "new"){
		
			$this -> setDefaults();
		
			$query = "INSERT INTO Article (";
			$query .= implode(",",$this -> colArray);
			$query .= ") VALUES (";
			$query .= implode(",",$this -> valArray);
			$query .= ")";
			
			if($stmt = $this -> database -> prepare($query)){
			
				$stmt -> execute($this -> values);
				$this -> status = "Insert Successful";
				
				//set the insert id 
				$this -> insert_id = $this -> database -> lastInsertId();
				
				
				//update profile
				$query = "UPDATE profile SET numOfArticles = numOfArticles + 1 WHERE U_Id = ?";
				$stmt = $this -> database -> prepare($query);
				$stmt -> bindParam(1,$this -> U_Id,PDO::PARAM_INT);
				$stmt -> execute();
				
			
			}else{
				
				$this -> status = "Prepare failed with ".$this -> database -> error;
				
			}
			
		
		}else{
		
			$colArrayP = array();
			
			foreach($this->colArray as $item){
			
				$colArrayP[] = $item."=?";
			
			}
		
			$query = 'UPDATE Article SET ';
			$query .= implode(",",$colArrayP);
			$query .= " WHERE P_Id = ".$this -> P_Id;
			
			if($stmt = $this -> database -> prepare($query)){
			
				$stmt -> execute($this -> values);
				$this -> status = "Update Successful";
				
				//update profile
				$query = "UPDATE profile SET numOfUpdates = numOfUpdates + 1 WHERE U_Id = ?";
				$stmt = $this -> database -> prepare($query);
				$stmt -> bindParam(1,$this -> U_Id,PDO::PARAM_INT);
				$stmt -> execute();
			
			}else{
				
				$this -> status = "Prepare failed with ".$this -> database -> error;
				
			}
		
		}
	
	}
	
  	
}
/*
$unittest = new articleInserter("new",9);
$unittest -> setTitle("This end is nigh 2");
$unittest -> setContent("The end is comming and there is nothing you can do about it");
$result = $unittest -> executeQuery();

echo $unittest -> status;
var_dump($result);
*/
?>