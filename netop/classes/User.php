<?php 
/**
* Clasa User
*/

/**
* Clasa User contine toate metodele specifice utilizatorilor.
* Deoarece toate metodele comune sunt implementate in clasa
* abstracta Common sau in clasa Database, ea va defini doar
* metodele speciale specifice utilizatorilor, cum ar fi 
* autentificarea, verificarea autentificarii sau delogarea.
*
* @package  	classes
* @author   	Alexandru Manta <alexandru.manta@hotmail.com>
* @version  	Version: 1.0
* @access   	public
*/
class User extends Common{

	/**
	* Tabela utilizatorilor
	*
	* @var 		string
	* @access 	public
	**/
	static $table = "users";

	/**
    * Functia loggedIn() verifica pe baza datelor din sesiune sau din
    * cookie daca utilizatorul este deja logat. Daca da, populeaza 
    * obiectul cu datele din tabela.
	*
    * @return 	boolean		Starea autentificarii
    * @access 	public
    */
	public function loggedIn(){
		if ((isset($_SESSION['user_id'])) and ($user = $this->find($_SESSION['user_id']))){
			foreach (get_object_vars($user) as $prop=>$val){
				$this->$prop = $val;
			}
			return true;
		}elseif (isset($_COOKIE['auth'])){
			$id = substr($_COOKIE['auth'],0,13);
			$email = urldecode(substr($_COOKIE['auth'],45));
			if ($user = $this->select()->where('id',$id)->where('email',$email)->first()){
				$_SESSION['user_id'] = $id;
				foreach (get_object_vars($user) as $prop=>$val){
					$this->$prop = $val;
				}
				return true;
			}else{
				return false;
			}	 
		}else{
			return false;
		}
	}
	
	/**
    * Functia login() autentifica utilizatorul in aplicatie. Daca este
    * setat parametrul $persistent, atunci se creaza un cookie cu datele
    * de autentificare. De asemenea, functia populeaza obiectul curent
    * cu datele din tabela.
	*
    * @param 	string 		Email-ul utilizatorului
    * @param 	string 		Parola utilizatorului
    * @param 	boolean		Optiunea de mentinere a autentificarii
    * @return 	boolean		Starea autentificarii
    * @access 	public
    */

	public function login($email, $password, $persistent=false){
        if ($user = $this->select()->where('email', $email)->first()){
            if (password_verify($password, $user->password)){
                foreach (get_object_vars($user) as $prop=>$val){
                    $this->$prop = $val;
                }
                $_SESSION['user_id'] = $user->id;
                if ($persistent){
                    setcookie('auth',$id . $user->email,time()+3600*24*7);
                }
                return true;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }

	/**
    * Delogeaza utilizatorul prin distrugerea datelor din sesiune si 
    * din cookie.
	*
    * @access 	public
    */
	static function Logout(){
		session_start();
		unset($_SESSION['user_id']);
		session_destroy();
		setcookie('auth','',time()-3600);
	}
}


?>