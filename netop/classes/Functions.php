<?php
/**
* Clasa Functions
*/

/**
* Clasa Functions grupeaza functiile de uz general, ce nu sunt 
* specifice unei clase anume
*
* In aceasta clasa sunt implementate functiile de formatare pret,
* formatare data, extragerea si verificarea extensiei unui fisier,
* verificarea datelor numerice si validarea datelor din formulare
*
* @package  	classes
* @author   	Alexandru Manta <alexandru.manta@hotmail.com>
* @version  	Version: 1.0
* @access   	public
*/
class Functions{

	/**
    * Extrage extensia unui fisier si verifica daca este o imagine
	*
	* @param 	string 	Numele fisierului
    * @return 	string 	Extensia fisierului
    * @access 	public
    */

	static function GetExtension($filename){
		if (!strrpos ($filename, '.')) {
			throw new Exception($filename . " - Nume fisier invalid","22");
		}
        $filename_sections = pathinfo ($filename);
		if (!in_array(strtolower($filename_sections['extension']),array("jpg","jpeg","png","gif")))
		{
			throw new Exception($filename . " - Tip fisier invalid","243");
		}
        return $filename_sections['extension'];	
	}

	/**
    * Verifica daca o variabila este un numar intreg sau un numar real.
    * Functia este un inlocuitor pentru is_numeric() ce accepta o mare
    * varietate de tipuri numerice (ex. 0x13af23, 56386038e10)
	*
	* @param 	mixed 	Valoarea testata
    * @return 	boolean
    * @access 	public
    */

	static function IsNumber($val){
		$pattern = "/^-?(?:\d+|\d*\.\d+)$/";
		return preg_match($pattern, $val);
	}
	
	/**
    * Formateaza un pret pentru afisarea zecimalelor cu ajutorul 
    * stilurilor CSS
	*
	* @param 	float 	Pretul original
    * @return 	string 	Pretul formatat
    * @access 	public
    */

	static function FormatPrice($pret){
		$price = explode('.',$pret);
		if (!isset($price[1])){
			$price[1] = "00";
		}
		return number_format($price[0],0,',','.') . "<sup>" . $price[1] . "</sup>";
	}

    /**
    * Functie pentru validarea datelor. Permite o serie de validari
    * predefinite, atat pentru date transmise prin metoda GET cat si
    * POST. Utilizeaza iteratori pentru parcurgerea datelor ce 
    * urmeaza a fi validate
	*
	* @param 	array 	Vectorul ce contine verificarile si
	* mesajele de eroare. Structura acestuia este urmatoarea:<br>
	* $data - array('nume_camp'=>array('tip_validare'=>'mesaj_eroare'))
	*			<ul><li>nume_camp - Numele campului de formular sau a parametrului GET</li>
	*			<li>tip_validare - Tipul verificarii. Optiuni posibile:
	*				<ul><li>required - camp obligatoriu, nu poate fi gol</li>
	*				<li>check_db - verificarea valorii in baza de date. Format: check_db=>array(tabela, camp, mesaj_eroare)</li>
	*				<li>email - camp email valid. Nu se considera obligatoriu, nu va genera eroare daca nu este completat</li>
	*				<li>url - adresa web (URL) valida. Nu se considera obligatoriu, nu va genera eroare daca nu este completat</li>
	*				<li>match - compara doua valori din formular. Exemplu: repetarea parolei sau a adresei de email. Format: match=>array(nume_camp_repetare, mesaj_eroare)</li>
	*				<li>price - verifica un pret sa fie numar intreg sau numar real pozitiv</li>
	*				<li>numeric - verifica o variabila sa fie un numar intreg pozitiv sau 0</li></ul></li>
	*			<li>mesaj_eroare - Mesajul de eroare afisat pentru campul respectiv</li></ul>
	* @param 	array 	Metoda de transmitere a datelor.
	* Se transmite direct vectorul $_GET sau $_POST
    * @return 	array 	Vectorul de erori generat
    * @access 	public
    */

    static function Validate($data, $method){
		$tests = array('required', 'check_db', 'email', 'url', 'match', 'price', 'numeric');
		$errors = array();
		if (!is_array($data)){
			throw new Exception("Invalid data");	
		}
		else{
			$dataObject = new ArrayObject($data);
			$iterator = $dataObject->getIterator();
			while ($iterator->valid()){
				if (!is_array($iterator->current())){
					throw new Exception("Datele nu sunt un vector");
				}
				else{
					$validation = new ArrayObject($iterator->current());
					$validationIterator = $validation->getIterator();
					while ($validationIterator->valid()){
						switch($validationIterator->key()){
							case 'required':
								if ((empty($method[$iterator->key()])) or (ctype_space($method[$iterator->key()]))){
									$errors[] = $validationIterator->current();
								}
								break;
							case 'email':
								if (!empty($method[$iterator->key()])){
									if (!filter_var($method[$iterator->key()],FILTER_VALIDATE_EMAIL)){
										$errors[] = $validationIterator->current();
									}
								}
								break;
							case 'url':
								if (!empty($method[$iterator->key()])){
									if (!filter_var($method[$iterator->key()],FILTER_VALIDATE_URL)){
										$errors[] = $validationIterator->current();
									}
								}
								break;
							case 'match':
								if (!is_array($validationIterator->current())){
									throw new Exception("Date invalide match");	
								}
								else{
									$compare = $validationIterator->current();
									if ($method[$iterator->key()] != $method[$compare[0]]){
										$errors[] = $compare[1];
									}
								}
								break;
							case 'check_db':
								if (!is_array($validationIterator->current())){
									throw new Exception("Date invalide check_db");	
								}
								else{
									$db_data = $validationIterator->current();
									$db = new Database();
									$query = "SELECT * FROM " . $db_data[0] . " WHERE " . $db_data[1] . " = '" . $method[$iterator->key()] . "'";
									$result = $db->execute($query);
									if ($db->getCount($result) > 0){
										$errors[] = $db_data[2];	
									}
								}
								break;
							case 'price':
								if (!empty($method[$iterator->key()])){
									if ((!filter_var($method[$iterator->key()],FILTER_VALIDATE_INT, array('options'=>array('min_range' => 0)))) and
										(!filter_var($method[$iterator->key()],FILTER_VALIDATE_FLOAT) or ($method[$iterator->key()]<0))) {
										$errors[] = $validationIterator->current();
									}
								}
								break;
							case 'numeric':
								if ((!empty($method[$iterator->key()])) or (!is_numeric($method[$iterator->key()]))){
									if (!filter_var($method[$iterator->key()],FILTER_VALIDATE_INT, array('options'=>array('min_range' => 0)))){
										$errors[] = $validationIterator->current();
									}
								}
								break;
							default:
								throw new Exception("Verificare invalida " . $validationIterator->key());
						}
						$validationIterator->next();
					}
					$iterator->next();
				}
			}
		}
		if (empty($errors)){
			return false;
		}
		else{
			return $errors;		
		}
	}
}
?>