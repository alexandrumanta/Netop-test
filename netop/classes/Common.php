
<?php    
/**
* Clasa Common
*/
 
/**
* Clasa abstracta Common implementeaza functionalitatea comuna tuturor
* claselor aplicatiei. Ea contine metodele de salvare, actualizare si
* stergere a datelor si defineste proprietatile comune acestora. Calsa 
* defineste alias-uri pentru functiile de selectie implementate in clasa
* Database. Clasa Common implementeaza interfata IteratorAggregate pentru
* a parcurge proprietatile unui obiect prin intermediul iteratorului 
* generat. Metoda getIterator() este obligatorie acestei interfete.
* Toate clasele aplicatiei vor extinde aceasta clasa.
*
* @package  	classes
* @author   	Alexandru Manta <alexandru.manta@hotmail.com>
* @version  	Version: 1.0
* @access   	public
*/
abstract class Common implements IteratorAggregate{
    /**
    * Tabela specifica fiecarei clase copil
    *
    * @var      string
    * @access   public
    **/
    static $table;
 
    /**
    * Campul ID al fiecarei tabele. Valoare implicita: id
    *
    * @var      string
    * @access   protected
    **/
    protected $id_field = "id";
 
    /**
    * Conexiunea cu baza de date
    *
    * @var      mysqli
    * @access   protected
    **/
    protected $link = null;
 
    /**
    * Vectorul de campuri din baza de date. Iteratorul este asociat acestei variabile
    *
    * @var      array
    * @access   protected
    **/
    protected $fields = array();
 
    /**
    * Campurile protejate pentru operatii de actualizare
    *
    * @var      array
    * @access   protected
    **/
    protected $protected_fields = array();
 
    /**
    * Numarul de pagini, pentru functia de paginare
    *
    * @var      int
    * @access   public
    **/
    static $num_pages;
 
    /**
    * Constructor - creeaza conexiunea cu baza de date
    *
    * @access   public
    */
    public function __construct(){
        $this->link = new Database();
    }
 
    /**
    * Functia getIterator este impusa prin interfata IteratorAggregate.
    * Ea returneaza instanta unui iterator extern predefinit sau 
    * sau particular. Ea va genera iteratorul pentru vectorul de campuri.
    *
    * @return   external iterator
    * @access   public
    */
    public function getIterator() {
        return new ArrayIterator($this->fields);
    }
 
    /**
    * Metoda magica __set adauga date in vectorul de campuri
    *
    * @access   public
    */
    public function __set($prop, $val){
        $this->fields[$prop] = $val;
    }
 
    /**
    * Metoda magica __get extrage informatii din vectorul de campuri
    *
    * @return   mixed
    * @access   public
    */
    public function __get($prop){
        return $this->fields[$prop];
    }
 
    /**
    * Functie de test pentru afisarea continutului unui obiect creat
    * prin intermediul metodelo magice
    *
    * @access   public
    */
    public function show(){
        foreach ($this as $prop=>$val){
            echo "<p>{$prop}: {$val}</p>";
        }
    }
 
    /**
    * Functia save() va efectua operatiile de inserare si de actualizare
    * a datelor. Ea va prelua informatia din vectorul de campuri si va
    * decide ce operatie trebuie efectuata in functie de prezenta unui id
    * in vectorul de date specific obiectului. Daca id-ul este furnizat,
    * va efectua operatia de UPDATE, altfel va crea o inregistrare noua
    * prin operatia de INSERT
    *
    * @return   int     Numarul de randuri afectate
    * @access   public
    */
     
    public function save($id){
        $fields = "";
        $values = "";
        $bind_types = "";
        $bind_data = array();
 
        // Daca este initializat id-ul inainte de apelul functiei save, voi face update
        // Daca nu, voi face insert
 
        if (isset($id)) {
            // Pregatesc query UPDATE
            if (($id > 0) and (filter_var($id, FILTER_VALIDATE_INT))){
                if ($this->find($id)){
                    $query = "UPDATE " . static::$table . " SET ";
                    foreach ($this as $field=>$value){
                        if (!in_array($field, $this->protected_fields)){
                            $query .= $field . " = ?, ";
                            if (filter_var($value, FILTER_VALIDATE_INT)){
                                $bind_types .= "i";
                            }elseif (filter_var($value, FILTER_VALIDATE_FLOAT)){
                                $bind_types .= "d"; 
                            }else{
                                $bind_types .= "s";     
                            }           
                            $bind_data[] = &$this->fields[$field];
                        }
                    }
                    $params[] = &$bind_types;
                    $query = substr($query,0,-2);
                    $query .= " WHERE " . $this->id_field . " = " . $id; 
                    //echo $query;
                    //prepared stmt
                    if($stmt = $this->link->prepare($query)){
                        /* Adauga parametrii s - string, b - blob, i - int */
                        call_user_func_array(array($stmt, 'bind_param'), array_merge($params,$bind_data));
                        /* Executa */
                        $stmt -> execute();
 
                        $affected = $stmt->affected_rows;
                        /* Inchide statement */
                        $stmt -> close();
                    }else{
                        throw new Exception("Eroare query!");
                    }

                }else{
                    throw new Exception("ID inexistent");
                }
            }else{
                throw new Exception("Bad ID");
            }
            if (($affected == 1) and (LOG)){
				$user = new User();
				try{
					$logged_in = $user->loggedIn();
					$user = $user->name .' '. $user->last_name; 
				}
				catch (Exception $e){
					echo $e->getMessage();
					$logged_in = false;
				}
				$output = date("d.m.Y H:i:s") . " - Edited " . $value . " from " . static::$table . " by " . $user . "\n";
				foreach ($this as $field=>$value){
					$output .= "\t{$field}: {$value}\n";
				}
				$output .= "\n";
				$output .= "----------------------------------------------------------------";
				$output .= "\n\n";
				file_put_contents(LOG_FOLDER . "/data.log",$output, FILE_APPEND);		
			}
 
        }else{
            // Pregatesc query INSERT
            foreach ($this as $field=>$value){
                $fields .= $field . ", ";
                $values .= "?, ";
                if (filter_var($value, FILTER_VALIDATE_INT)){
                    $bind_types .= "i";
                }
                elseif (filter_var($value, FILTER_VALIDATE_FLOAT)){
                    $bind_types .= "d"; 
                }
                else{
                    $bind_types .= "s";     
                }           
                $bind_data[] = &$this->fields[$field];
            }
            $params[] = &$bind_types;
            $fields = substr($fields,0,-2);
            $values = substr($values,0,-2);
 
            $query = "INSERT INTO " . static::$table . "({$fields}) VALUES({$values})"; 
 
            //prepared stmt
            if($stmt = $this->link->prepare($query)){
                /* Adauga parametrii s - string, b - blob, i - int */
                //$stmt -> bind_param($bind_types, $bind_data);
                call_user_func_array(array($stmt, 'bind_param'), array_merge($params,$bind_data));
                /* Executa */
                $stmt -> execute();
 
                $affected = $stmt->affected_rows;
                if ($affected == 1){
                    $this->id = $stmt->insert_id;
                }
 
                /* Inchide statement */
                $stmt -> close();
            }else{
                throw new Exception("Eroare query!");
            }
            if (($affected == 1) and (LOG)){

				$user = new User();
				try{
				  $logged_in = $user->loggedIn();
				  $user = $user->name .' '. $user->last_name; 
				}
				catch (Exception $e){
				  echo $e->getMessage();
				  $logged_in = false;
				}
				$output = date("d.m.Y H:i:s") . " - Added " . $value . " in " . static::$table . " by " . $user . "\n";
				foreach ($this as $field=>$value){
					$output .= "\t{$field}: {$value}\n";
				}
				$output .= "\n";
				$output .= "----------------------------------------------------------------";
				$output .= "\n\n";
				file_put_contents(LOG_FOLDER . "/data.log",$output, FILE_APPEND);		
			}
        }
        return $affected;
    }
     
    /**
    * Functia log() va salva operatiile efectuate intr-un fisier pe disc pentru
    * depanarea aplicatiei. Dezactivarea optiunilor de arhivare se poate face 
    * din fisierul config/setting.php
    *
    * @access   public
    */
    private function log(){
        $output = date("d.m.Y H:i:s") . " - Adaugare " . static::$table . "\n";
        foreach ($this as $field=>$value){
            $output .= "\t{$field}: {$value}\n";
        }
        $output .= "\n";
        $output .= "----------------------------------------------------------------";
        $output .= "\n\n";
        file_put_contents(LOG_FOLDER . "/data.log",$output, FILE_APPEND);
    }
 
    /**
    * Scurtatura pentru functia select() din clasa Database, apelabila
    * ca metoda a obiectului curent
    *
    * @return   Database object
    * @access   public
    */
    public function select(){
        return $this->link->table(static::$table)->select();
    }
 
    /**
    * Scurtatura pentru functia find() din clasa Database, apelabila
    * ca metoda a obiectului curent
    *
    * @return   mysqli_result
    * @access   public
    */
    public function find($id){
        return $this->link->table(static::$table)->find($id, $this->id_field);
    }
 
    /**
    * Realizeaza stergerea unei inregistrari din baza de date, in functie
    * de id-ul setat in vectorul de campuri. Pentru o stergere conditionata
    * de alte campuri, se poate rula interogarea DELETE corespunzatoare cu
    * ajutorul functiei execute() din clasa Database
    *
    * @return   int     Numarul de randuri afectate
    * @access   public
    */
    public function delete($value) {
        if(isset($value)){
            // Pregatesc query DELETE
            if (($value > 0) and (filter_var($value, FILTER_VALIDATE_INT))){
                $query = "DELETE FROM " . static::$table . " WHERE " . $this->id_field . " = ?"; 
                //prepared stmt
                if($stmt = $this->link->prepare($query)){
                    /* Adauga parametrii s - string, b - blob, i - int */
                    $stmt -> bind_param("i", $value);
                    
                    /* Executa */
                    $stmt -> execute();

                    $affected = $stmt->affected_rows;
                    /* Inchide statement */
                    $stmt -> close();  
                    if (($affected == 1) and (LOG))
                    {
                        //$this->log(); 

                        $user = new User();
                        try{
                          $logged_in = $user->loggedIn();
                          $user = $user->name .' '. $user->last_name; 
                        }
                        catch (Exception $e){
                          echo $e->getMessage();
                          $logged_in = false;
                        }
                        $output = date("d.m.Y H:i:s") . " - Deleted id " . $value . " from " . static::$table . " by " . $user . "\n";
                        foreach ($this as $field=>$value){
                            $output .= "\t{$field}: {$value}\n";
                        }
                        $output .= "\n";
                        $output .= "----------------------------------------------------------------";
                        $output .= "\n\n";
                        file_put_contents(LOG_FOLDER . "/data.log",$output, FILE_APPEND);       
                    }
                    return $affected; 
                }else{
                    throw new Exception("Eroare query!");
                }
            }
            else{
                throw new Exception("Bad ID");
            }
        }else{
            throw new Exception("ID inexistent");
        }
    }

 
    /**
    * Extrage toate rezultatele dintr-o tabela , ordonand rezultatele
    * dupa campul specificat in parametrul $field si in ordinea specificata
    * de parametrul $dir
    *
    * @param    string      Campul folosit pentru ordonare
    * @param    string      Directia (sensul) ordonarii 
    * @return   array       Vectorul cu rezultate
    * @access   public
    */
 
    static function All($field = "id", $dir = "DESC"){
        if (!$results = Database::table(static::$table)->select()->order($field, $dir)->get()){
            return array();
        }
        foreach ($results as $result){
            $class = get_called_class();
            $object = new $class;
            foreach (get_object_vars($result) as $prop=>$val){
                $object->$prop = $val;
            }
            $objects[] = $object;
             
        }
        return $objects;
    }
 
    /**
    * Extrage toate rezultatele dintr-o tabela, ordonand rezultatele
    * dupa campul specificat in parametrul $field si in ordinea specificata
    * de parametrul $dir. Rezultatele vor fi paginate, fiind afisate un
    * numar de rezultate dat de parametrul $show
    *
    * @param    string      Campul folosit pentru ordonare
    * @param    string      Directia (sensul) ordonarii 
    * @param    int         Numarul de rezultate pe pagina
    * @return   array       Vectorul cu rezultate
    * @access   public
    */
 
    static function Paginate($field = "id", $dir = "DESC", $show = 5){
        if (func_num_args() == 1){
            $show = $field;
            $field = "id";
        }
        if (!$results = Database::table(static::$table)->select()->order($field, $dir)->take($show)){
            return array();
        }
        static::$num_pages = ceil(count(static::All())/$show);
        foreach ($results as $result){
            $class = get_called_class();
            $object = new $class;
            foreach (get_object_vars($result) as $prop=>$val){
                $object->$prop = $val;
            }
            $objects[] = $object;
             
        }
        return $objects;
    }
 
    /**
    * Afiseaza link-urile de paginatie, atunci cand se foloseste afisarea
    * paginata. Adauga in mod automat parametrul GET page si verifica 
    * daca acesta are o valoare valida inainte de a crea link-urile.
    *
    * @return   string      Codul HTML pentru link-urile de paginatie
    * @access   public
    */
 
    static function Links(){
		if ((!empty(static::$num_pages)) and (static::$num_pages > 1)){
			$output = "<ul class=\"pagination\">";
			if (isset($_GET['page']) and is_numeric($_GET['page']) and $_GET['page'] > 0){
				$page = (int) $_GET['page'];
			}else{
				$page = 1;
			}

			if ($page > 1){
				$prev = $page - 1;
				$output .= "<li><a href=\"" . $_SERVER['PHP_SELF'] . "?page=1\">&lsaquo;&lsaquo;</a></li>";
				$output .= "<li><a href=\"" . $_SERVER['PHP_SELF'] . "?page=" . $prev . "\">&lsaquo;</a></li>";
			}

			for ($i = 1; $i <= static::$num_pages; $i++){
				$output .= "<li><a href=\"" . $_SERVER['PHP_SELF'] . "?page=" . $i . "\">" . $i . "</a></li>";	
			}

			if ($page < static::$num_pages){
				$next = $page + 1;
				$output .= "<li><a href=\"" . $_SERVER['PHP_SELF'] . "?page=" . $next . "\">&rsaquo;</a></li>";
				$output .= "<li><a href=\"" . $_SERVER['PHP_SELF'] . "?page=" . static::$num_pages . "\">&rsaquo;&rsaquo;</a></li>";
			}

			$output .= "</ul>";
			return $output;

		}else{
			return false;
		}
	} 
}
?>