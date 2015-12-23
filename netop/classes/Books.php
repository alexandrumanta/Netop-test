<?php
/**
* Clasa Books
*/

/**
* Clasa Books contine toate metodele specifice cartilor.
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
class Books extends Common{

	/**
	* Tabela cartilor
	*
	* @var 		string
	* @access 	public
	**/
	static $table = "books";

	/**
	* Tabela categoriilor
	*
	* @var 		string
	* @access 	public
	**/
	static $category = "categories";


	/**
	* Campul de ordonare predefinit
	*
	* @var 		string
	* @access 	public
	**/
	static $order_field = "name";

	/**
	* Campul de ordonare dupa autori
	*
	* @var 		string
	* @access 	public
	**/
	static $a_field = "author";

	/**
	* Campul de ordonare dupa pret
	*
	* @var 		string
	* @access 	public
	**/
	static $p_field = "price";

	/**
	* Campul de ordonare dupa imagini
	*
	* @var 		string
	* @access 	public
	**/
	static $i_field = "image";

	/**
	* Campul de ordonare din categorii
	*
	* @var 		string
	* @access 	public
	**/
	static $c_field = "category_name";

	/**
	* Campul de legatura cu id-ul categoriei
	*
	* @var 		string
	* @access 	public
	**/
	static $c_id = "category_id";

	/**
	* Campul de legatura din tabela categoriei
	*
	* @var 		string
	* @access 	public
	**/
	static $cat_id = "id";
	
	/**
    * Extrage cartea din tabela in functie de id-ul setat
    * in parametrul $value
    *
    * @param    int      	Id-ul setat in parametru
    * @return   array       Vectorul cu rezultate
    * @access   public
    */
	public function getBook($value){
		$query = "SELECT * FROM " . self::$table . " WHERE id = {$value}";
		$result = $this->link->execute($query);
		$book = $this->link->getObject($result);
			$books [] = $book;
		return $books;
	}

	/**
	* Extrage toate cartile din tabela, ordonand rezultatele
    * dupa campul specificat in parametrul $order_field si in ordine
    * ascendenta
    *
    * @param    string      Campul folosit pentru ordonare
    * @return   array       Vectorul cu rezultate
    * @access   public
    */
	public function getBooks(){
		$query = "SELECT * FROM " . self::$table . " ORDER BY " . self::$order_field . " ASC";
		$result = $this->link->execute($query);
		if ($this->link->getCount($result) > 0){
			while ($book = $this->link->getObject($result)){
				$books[]= $book;
			}	
		return $books;
		}
	}
	/**
	* Extrage toate cartile din tabela, ordonand rezultatele
    * dupa campul specificat in parametrul $keyword si in ordine
    * ascendenta
    *
    * @param    string      Campul folosit pentru ordonare
    * @return   array       Vectorul cu rezultate
    * @access   public
    */
	public function searchKeyword($keyword){
        //$query = "SELECT * FROM " . self::$table." WHERE " . self::$order_field . " like '%$words%'";
		$query = "SELECT 
		b." . self::$order_field . ", 
		b." . self::$a_field . ", 
		b." . self::$p_field . ",
		b." . self::$i_field . ",
		c." . self::$c_field . "
		FROM " . self::$table . " as b JOIN " . self::$category . " as c
		ON b." . self::$c_id . " = c." . self::$cat_id . " WHERE b." . self::$order_field . " 
		LIKE '%$keyword%' OR c." . self::$c_field . " 
		LIKE '%$keyword%' OR b." . self::$a_field . " 
		LIKE '%$keyword%' OR b." . self::$p_field . "
		LIKE '%$keyword%'";
        $result = $this->link->execute($query);
        if ($this->link->getCount($result) > 0){
            while ($book = $this->link->getObject($result)){
                $books[]= $book;
            }   
            return $books;
        }else{
            throw new Exception("Cartea nu exista in baza de date!");
        }
    }
}

?>