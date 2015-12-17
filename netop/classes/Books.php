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
	* Tabela categoriilor
	*
	* @var 		string
	* @access 	public
	**/
	static $table = "books";

	/**
	* Campul de ordonare predefinit
	*
	* @var 		string
	* @access 	public
	**/
	static $order_field = "name";
	
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
    * dupa campul specificat in parametrul $words si in ordine
    * ascendenta
    *
    * @param    string      Campul folosit pentru ordonare
    * @return   array       Vectorul cu rezultate
    * @access   public
    */
	public function searchKeyword($words){
        $query = "SELECT * FROM " . self::$table." WHERE " . self::$order_field . " like '%$words%'";
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