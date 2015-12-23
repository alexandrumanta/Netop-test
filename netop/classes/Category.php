<?php
/**
* Clasa Category
*/

/**
* Clasa Category contine toate metodele specifice categoriilor.
* Deoarece toate metodele comune sunt implementate in clasa
* abstracta Common sau in clasa Database, ea nu are nevoie de 
* nicio metoda speciala. Constructorul este redefinit pentru 
* crearea unui obiect cu date existente
*
* @package  	classes
* @author   	Alexandru Manta <alexandru.manta@hotmail.com>
* @version  	Version: 1.0
* @access   	public
*/
class Category extends Common{

	/**
	* Tabela categoriilor
	*
	* @var 		string
	* @access 	public
	**/
	static $table = "categories";

	/**
	* Campul de ordonare predefinit
	*
	* @var 		string
	* @access 	public
	**/
	static $order_field = "category_name";
	
	/**
    * Extrage categoria din tabela in functie de id-ul setat
    * in parametrul $value
    *
    * @param    int      	Id-ul setat in parametru
    * @return   array       Vectorul cu rezultate
    * @access   public
    */
	public function getCategory($value){
		$query = "SELECT * FROM " . self::$table . " WHERE id = {$value}";
		$result = $this->link->execute($query);
		$category = $this->link->getObject($result);
			$categories [] = $category;
		return $categories;
	}

	/**
	* Extrage toate categoriile din tabela, ordonand rezultatele
    * dupa campul specificat in parametrul $order_field si in ordine
    * ascendenta
    *
    * @param    string      Campul folosit pentru ordonare
    * @return   array       Vectorul cu rezultate
    * @access   public
    */
	public function getCategories(){
		$query = "SELECT * FROM " . self::$table . " ORDER BY " . self::$order_field . " ASC";
		$result = $this->link->execute($query);
		if ($this->link->getCount($result) > 0){
			while ($category = $this->link->getObject($result)){
				$categories[]= $category;
			}	
		return $categories;
		}
	}
}

?>