
<?php
/**
* Clasa Database
*/
 
/**
* Clasa Database realizeaza conexiunea cu baza de date si implemeteaza
* functii specifice interactiunii intre PHP si baza de date
*
* Clasa Database implementeaza functionalitatile de executie si 
* inregistrare a interogarilor. Clasa ofera atat posibilitatea 
* de a rula o interogare particulara, cat si o interfata Fluent
* pentru cele mai frecvente interogari de selectie a datelor
* Interfata Fluent va fi folosita de clasele specifice aplicatiei
*
* @package  	classes
* @author   	Alexandru Manta <alexandru.manta@hotmail.com>
* @version  	Version: 1.0
* @access   	public
*/
class Database{
 
    /**
    * Conexiunea cu baza de date
    *
    * @var      mysqli
    * @access   private
    **/
    private $link;
 
    /**
    * Rezultatul ultimei interogari
    *
    * @var      mysqli_result
    * @access   private
    **/
 
    private $result;        
 
    /**
    * Setari pentru inregistrarea interogarilor non-Fluent
    *
    * @var      int
    * @access   private
    **/
 
    private $debug = 0;     
 
    /**
    * Datele ce vor fi parsate interogarii SQL
    *
    * @var      array
    * @access   private
    **/
 
    private $binds = array();
 
    /**
    * Tipurile datelor ce vor fi parsate interogarii SQL
    *
    * @var      string
    * @access   private
    **/
 
    private $bind_types = "";
 
    /**
    * Constructor - creeaza conexiunea cu baza de date
    * si returneaza variabila de conexiune
    *
    * @return   mysqli
    * @access   public
    */
     
    public function __construct(){
        require_once DOC_ROOT . "/config/config.php";
        $this->link = new mysqli(DB_HOST,DB_USER,DB_PASS,DB_NAME);   
        if ($this->link->connect_error){
            throw new Exception("Eroare conexiune","100");
        }
        if (DEBUG){
            $this->debug = 1;
        }
    }
 
    /**
    * Pregateste interogarea parsata prin interfata Fluent
    *
    * @param    string      Interogarea MySQL in format prepared statements
    * @return   mysqli_stmt
    * @access   public
    */
 
    public function prepare($query){
        return $this->link->prepare($query);
    }
 
    /**
    * Ruleaza interogarile non-Fluent si le inregistreaza in fisierul
    * de log
    *
    * @param    string      Interogarea MySQL
    * @return   mysqli_result
    * @access   public
    */
     
    public function execute($query){
        $this->result = $this->link->query($query);
        if ($this->debug){
            $output = date("d.m.Y H:i:s") . " - MySQLi query\n";
            $output .= "\tQuery: " . $query . "\n";
            $output .= "\tResult: "; 
            $output .= ($this->link->errno) ? $this->link->error : "Succes";
            $output .= "\n\n";
            $output .= "----------------------------------------------------------------";
            $output .= "\n\n";
            file_put_contents(LOG_FOLDER . "db.log",$output, FILE_APPEND);
        }
        return $this->result;
    }
 
    /**
    * Realizeaza escaparea textului pentru prevenirea SQL Injection
    * pentru interogarile non-Fluent
    *
    * @param    string      Textul ce urmeaza a fi escapat
    * @return   string      Textul escapat
    * @access   public
    */
     
    public function escape($text){
        return $this->link->real_escape_string($text);
    }
 
    /**
    * Returneaza rezultatul ultimei interogari
    *
    * @return   mysqli_result
    * @access   public
    */
     
    public function getResult(){
        return $this->result;
    }
 
    /**
    * Returneaza numarul de rezultate ale ultimei interogari. Se aplica
    * doar pentru interogari SELECT
    *
    * @param    mysqli_result   Rezultatul ultimului SELECT
    * @return   int             Numarul de rezultate returnat
    * @access   public
    */
     
    public function getCount($result){
        return $result->num_rows;
    }
 
    /**
    * Returneaza numarul de randuri afectate de ultima interogare. Se
    * aplica numai pentru interogari INSERT, UPDATE sau DELETE
    *
    * @return   int     Numarul de randuri modificate
    * @access   public
    */
     
    public function getAffected(){
        return $this->link->affected_rows;
    }
     
    /**
    * Extrage primul rezultat dintr-o interogarea select sub forma unui
    * vector asociativ
    *
    * @param    mysqli_result   Rezultatul ultimului SELECT
    * @return   array           Vectorul asociativ cu datele primului rand
    * @access   public
    */
 
    public function getAssoc($result){
        return $result->fetch_assoc();
    }
 
    /**
    * Extrage primul rezultat dintr-o interogarea select sub forma unui
    * obiect
    *
    * @param    mysqli_result   Rezultatul ultimului SELECT
    * @return   object          Obiectul cu datele primului rand
    * @access   public
    */
     
    public function getObject($result){
        return $result->fetch_object();
    }
 
    /**
    * Selectarea tabelei pentru interfata Fluent. Functia este folosita
    * si in clasele specifice aplicatiei ca o scurtatura pentru utilizarea
    * interfetei fluent. Ruleaza constructorul clasei Database pentru a
    * realiza conexiunea si reseteaza datele parsate interogarii si tipurile
    * acestora
    *
    * @param    string      Tabele din care se vor selecta datele
    * @return   Database object Obiectul creat din clasa Database
    * @access   public
    */
 
    static function table($table){
        if (is_string($table)){
            $db = new self();
            $db->table = $table;
            $db->binds = array();
            $db->bind_types = "";
            return $db;
        }else{
            throw new Exception("Tabela inexistenta");
        }
    }
 
    /**
    * Pregateste interogarea SELECT pentru tabela selectata prin functia 
    * table()
    *
    * @return   Database object Obiectul creat din clasa Database
    * @access   public
    */
 
    public function select(){
        $this->select = "SELECT * FROM " . $this->table;
        return $this;
    }
 
    /**
    * Adauga clauza WHERE interogarii SELECT formate prin functia select().
    * Daca aceasta nu a fost creata, va fi apelata functia select() si apoi 
    * se va adauga clauza WHERE. Functia poate fi apelata inlantuit pentru 
    * adaugarea de clauze multiple, grupate in mod implicit prin operatorul
    * AND (si logic)
    *
    * @param    string      Campul ce va fi testat in clauza WHERE
    * @param    string      Operatorul de test. Camp optional. In
    * cazul in care nu se specifica, va fi utilizat operatorul =
    * @param    string      Valoarea ce se testeaza
    * @param    string      Operatorul folosit pentru gruparea
    * clauzelor multiple. Parametru optional. Valori acceptate: AND sau OR.
    * Valoarea implicita este AND in cazul in care nu se specifica sau este 
    * eronat
    * @return   Database object Obiectul creat din clasa Database
    * @access   public
    */
 
    public function where($field, $operator = null, $value = null, $boolean = 'and'){
        if (!isset($this->select)){
            $this->select();
        }
 
        if (func_num_args() == 2){
            $value = $operator;
            $operator = '=';
        }elseif(!in_array(strtolower($operator), array('=','!=','<>','<','<=','>','>=','like','is null','is not null'))){
            throw new Exception("Eroare operator");
        }
 
        if (is_string($field)){
            $this->field = $field;
            $this->operator = $operator;
            if (in_array(strtolower($boolean), array('and','or'))){
                $this->boolean = $boolean;
            }else{
                // Bad operator, defaults to AND
                $this->boolean = 'and';
            }
 
            if (!in_array(strtolower($operator),array('like', 'not like'))){
                $this->value = $value;
            }else{
                $this->value = "%{$value}%";
            }
 
 
            $this->binds[] = $this->value;
            if (filter_var($this->value, FILTER_VALIDATE_INT)){
                $this->bind_types .= "i";
            }elseif (filter_var($this->value, FILTER_VALIDATE_FLOAT)){
                $this->bind_types .= "d";    
            }else{
                $this->bind_types .= "s";        
            }
             
            if (!isset($this->where)){
                $this->select .= " WHERE {$this->field} {$this->operator} ?";
                $this->where = true;
            }else{
                $this->select .= " {$this->boolean} {$this->field} {$this->operator} ?";    
            }
        }else{
            throw new Exception("Camp inexistent");
        }
        return $this;
    }
 
    /**
    * Scuratura pentru adaugarea unei clauze WHERE folosind operatorul OR
    * Utilizeaza functia where() cu urmatorul prototip:
    * where($field, $operator, $value, 'or')
    *
    * @param    string      Campul ce va fi testat in clauza WHERE
    * @param    string      Operatorul de test. Camp optional. In
    * cazul in care nu se specifica, va fi utilizat operatorul =
    * @param    string      Valoarea ce se testeaza
    * @return   Database object Obiectul creat din clasa Database
    * @access   public
    */
 
    public function orWhere($field, $operator = null, $value = null){
        if (!isset($this->select)){
            $this->select();
        }
        return $this->where($field, $operator, $value, 'or');
    }
 
    /**
    * Adaugarea unei clauze WHERE de tipul IS NULL sau IS NOT NULL
    *
    * @param    string      Campul ce va fi testat in clauza WHERE
    * @param    boolean     Specifica daca se va folosi operatorul 
    * logic NOT in evaluarea conditiei. Parametru optional. Valoare predefinita: 
    * false
    * @param    string      Operatorul folosit pentru gruparea
    * clauzelor multiple. Parametru optional. Valori acceptate: AND sau OR.
    * Valoarea implicita este AND in cazul in care nu se specifica sau este 
    * eronat
    * @return   Database object Obiectul creat din clasa Database
    * @access   public
    */
 
    public function whereNull($field, $not = false, $boolean = 'and'){
        if (!isset($this->select)){
            $this->select();
        }
        if (is_string($field)){
            $this->field = $field;
            if (in_array(strtolower($boolean), array('and','or'))){
                $this->boolean = $boolean;
            }else{
                // Bad operator, defaults to AND
                $this->boolean = 'and';
            }
 
            $this->operator = ($not) ? "is null" : "is not null";
 
            if (!isset($this->where)){
                $this->select .= " WHERE {$this->field} {$this->operator}";
                $this->where = true;
            }else{
                $this->select .= " {$this->boolean} {$this->field} {$this->operator}";  
            }
 
        }else{
            echo "Bad field name";
        }
        return $this;
    }
 
    /**
    * Scurtatura pentru adaugarea unei clauze WHERE de tipul IS NULL sau 
    * IS NOT NULL folosind operatorul OR. Foloseste functia whereNull()
    * cu urmatorul prototip:
    * whereNull($field, $not, 'or')
    *
    * @param    string      Campul ce va fi testat in clauza WHERE
    * @param    boolean     Specifica daca se va folosi operatorul 
    * logic NOT in evaluarea conditiei. Parametru optional. Valoare predefinita: 
    * false
    * @param    string      Operatorul folosit pentru gruparea
    * clauzelor multiple. Parametru optional. Valori acceptate: AND sau OR.
    * Valoarea implicita este AND in cazul in care nu se specifica sau este 
    * eronat
    * @return   Database object Obiectul creat din clasa Database
    * @access   public
    */
 
    public function orWhereNull($field, $not = false){
        if (!isset($this->select)){
            $this->select();
        }
        return $this->whereNull($field, $not, 'or');
    }
 
    /**
    * Adauga clauza ORDER BY interogarii SELECT. Daca interogarea nu a fost
    * creata, se va rula mai intai functia select()
    *
    * @param    string      Campul dupa care se va face ordonarea
    * @param    string      Directia ordonarii. Parametru optional.
    * Valoare implicita: ASC. Valori acceptate: ASC sau DESC
    * @return   Database object Obiectul creat din clasa Database
    * @access   public
    */
 
    public function order($field, $order = 'ASC'){
        if (!isset($this->select)){
            $this->select();
        }
 
        if (is_string($field)){
            $this->field = $field;
            if ((isset($order)) and (in_array(strtolower($order), array('asc', 'desc')))){
                $this->order = $order;
            }else{
                // Bad order - default to ASC
                $this->order = 'ASC';
            }
            $this->select .= " ORDER BY {$this->field} {$this->order}";
        }else{
            throw new Exception("Camp inexistent");
        }   
        return $this;   
    }
 
    /**
    * Adauga clauza LIMIT pentru paginare. Functia va cauta parametrul GET page
    * si va ajusta interogarea SELECT pentru a extrage rezultatele paginat. 
    * Daca parametrul GET page nu exista sau nu este valid, se va selecta prima
    * pagina. Daca numarul paginii este real, se va rotunji la cel mai apropiat
    * numar intreg.
    *
    * @param    int         Numarul de rezultate afisat pe pagina.
    * Parametru optional. Valoare implicita: 10
    * @return   array   Vectorul rezultatelor
    * @access   public
    */
 
    public function take($display = 10){
        if (!isset($this->select)){
            $this->select();
        }
         
        if (isset($_GET['page']) and is_numeric($_GET['page']) and $_GET['page'] > 0){
            $this->page = (int) $_GET['page'];
        }else{
            $this->page = 1;
        }
 
        $this->display = $display;
        if (isset($_GET['display']) and is_numeric($_GET['display'])){
            $this->display = (int) $_GET['display'];
        }
 
        $this->start = ($this->page - 1) * $this->display;
         
        $this->select .= " LIMIT ?, ?";
        $this->binds[] = $this->start;
        $this->binds[] = $this->display;
 
        $this->bind_types .= "ii";
         
        return $this->get();
    }
 
    /**
    * Ruleaza interogarea SELECT formata de functiile interfetei Fluent
    *
    * @return   array   Vectorul rezultatelor
    * @access   public
    */
 
    public function get(){
        foreach ($this->binds as $key=>$value){
            $bind_data[] = &$this->binds[$key];
        }
        $params[] = &$this->bind_types;
 
        if($stmt = $this->prepare($this->select)){
            /* Adauga parametrii s - string, b - blob, i - int */
            if (!empty($this->binds)){
                call_user_func_array(array($stmt, 'bind_param'), array_merge($params,$bind_data));
            }
            /* Executa */
            $stmt->execute();
            $result = $stmt->get_result();
            /* Inchide statement */
            $stmt -> close();
 
            if ($this->getCount($result) > 0){
                $results = array();
                while ($obj = $this->getObject($result)){
                    $results[] = $obj;
                }
                return $results;
            }else{
                return false;
            }
        }else{
            throw new Exception("Eroare query!");
        }
    }
 
    /**
    * Scuratura pentru cautarea unui id folosind functia where()
    *
    * @param    int         ID-ul cautat
    * @param    string      Permite specificarea denumirii  * campului ID. 
    * Parametru optional. Valoare implicita: id
    * @return   object      rezultatul cautarii
    * @access   public
    */
 
    public function find($id, $id_field = 'id'){
        if (filter_var($id, FILTER_VALIDATE_INT)){
            return $this->select()->where($id_field, '=', $id)->first();
        }else{
            throw new Exception("ID invalid");
        }
    }
 
    /**
    * Scuratura pentru returnarea primului rezultat al unui SELECT
    *
    * @return   object      rezultatul cautarii
    * @access   public
    */
 
    public function first(){
        return $this->get()[0];
    }
 
}
?>