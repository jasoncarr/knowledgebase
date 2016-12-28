<?php

class categoryGrabber {

	protected $database;
	public $status;
	
	private $values = array();
	private $search = "";
	private $searchValue;

	
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
		
		
	}
	
	public function getArticleId(){
	
		$this -> add("ArticleCategories.P_Id");
	
	}
	
	public function getCategory(){
	
		$this -> add("ArticleCategories.C_Id");
		$this -> add("Categories.category");
	
	}
	
	public function searchByArticleId($P_Id){
	
		$this -> search = "WHERE ArticleCategories.P_Id=?";
		$this -> searchValue = $P_Id;
	
	}
	
	public function searchByCategory($C_Id){
			
		$this -> search = "WHERE ArticleCategories.C_Id = ?";
		$this -> searchValue = $C_Id;
	}
	
	private function add($column){
	
        $this->values[] = $column;
		
    }
	
	/*
	* @param $userId /Int the U_Id of the user getting the categories
	* @return $catArray /Array array of the available categories for that user
	*/
	public function getCategories($userId){
	
		$query = "SELECT * FROM CategoryPermissions INNER JOIN Categories ON CategoryPermissions.C_Id=Categories.C_Id WHERE U_Id = ? AND level > 1";
		if($stmt = $this -> database -> prepare($query)){
			$stmt -> bindParam(1,$userId,PDO::PARAM_INT);
			$stmt -> execute();
			$catArray = $stmt -> fetchAll();
		}else{
			$this -> status = "Could not get categories: ".$this -> database -> error;
		}
		return $catArray;
	
	}
	
	/*
	* @return $resArray /ARRAY associate array of profile row
	*/
	public function executeQuery(){
	
		$query = 'SELECT ';
		$query .= implode(",",$this -> values);
		$query .= ' FROM ArticleCategories INNER JOIN Categories ON ArticleCategories.C_Id=Categories.C_Id '.$this -> search;
		

		if($stmt = $this -> database -> prepare($query)){
		
			$stmt->bindParam(1, $this -> searchValue, PDO::PARAM_INT);
			$stmt -> execute();
			$reArray = $stmt -> fetchAll();
			$this -> status = "Select Successful";
		
		}else{
			
			$this -> status = "Select failed with ".$this -> database -> error;
			
		}
			

		return $reArray;
	
	}
	
	
	
}

class categoryInserter {

	protected $database;
	public $status;
	
	private $colArray = array();
	private $valArray = array();
	private $values = array();
	private $U_Id;
	private $type = "add";
	
	/*
	* @param /Integer $articleID sets that article Id ('new' = new article)
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
	* @param /String $title the title of the article
	* @return /Integer $success reports on if the category can be added or not (0=no,1=yes)
	*/
	public function setCategory($category){
	
		//get the Category ID
		$query = "SELECT C_Id FROM Categories WHERE category = ?";
		if($stmt = $this -> database -> prepare($query)){
		
			$stmt -> bindParam(1,$category,PDO::PARAM_STR);
			$stmt -> execute();
			$C_Id = $stmt -> fetch();
			//echo "The C_Id for this category is ".$C_Id['C_Id']."<br />";
		
		}
		
		//get the User Permissions
		$query = "SELECT C_Id,level FROM CategoryPermissions WHERE U_Id = ?";
		if($stmt = $this -> database -> prepare($query)){
		
			$stmt -> bindParam(1,$this -> U_Id,PDO::PARAM_INT);
			$stmt -> execute();
			$permArray = $stmt -> fetchAll();
		
		}
		
		
		//test if user has the permission to added add an article to this category
		foreach($permArray as $permission){
		
			if($permission['C_Id'] == $C_Id['C_Id'] && $permission['level'] > 1){
			
				$this->add((int)$C_Id['C_Id'],'C_Id');
				$success = 1;
				break;
			
			}else{
			
				$success = 0;
			
			}
		
		}
		
		return $success;
	
	}
	
	/*
	* @param /Int the article ID to set with the category
	*/
	public function setArticleId($P_Id){
	
		$this->add($P_Id,'P_Id');
	
	}
	
	
	private function add($value,$column){
	
		$this->colArray[] = $column;
		$this->valArray[] = "?";
        $this->values[] = $value;
		
    }
	
	public function unsetArrays (){
	
		unset($this->colArray); 
		unset($this->valArray); 
        unset($this->values); 
	}
   
	public function executeQuery(){
	
		if($this -> type == "add"){
		
			$query = 'INSERT INTO ArticleCategories ( ';
			$query .= implode(",",$this -> colArray);
			$query .= ') VALUES ('.implode(",",$this -> valArray).')';
		
		
		}else if($this -> type == "remove"){
			
			//add in remove functionality here
		
		}

		if($stmt = $this -> database -> prepare($query)){
		
			$stmt->bindParam(1, $this -> values[0], PDO::PARAM_INT);
			$stmt->bindParam(2, $this -> values[1], PDO::PARAM_INT);
			$stmt -> execute();
			$this -> status = "Update Successful";
			$this -> unsetArrays();
			
		
		}else{
			
			$this -> status = "Prepare failed with ".$this -> database -> error;
			
		}
		
	}
	
}
	
  	

/*
$unittest = new categoryGrabber();
$unittest -> getArticleId();
$unittest -> getCategory();
$unittest -> searchByCategory(1);
$result = $unittest -> executeQuery();

echo $unittest -> status;
var_dump($result);
*/
?>