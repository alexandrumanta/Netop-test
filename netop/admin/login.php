<?php
require("blocks/header-login.php");
?>
<div class="login-container">
	<div class="row">
		<div class="col-sm-6">
			<div class="errors-container">
				<?php if (isset($_POST['send'])){
					$data = array(
						'email' => array('required' => 'Introduceti email! '),
						'password' => array('required' => 'Introduceti parola!')
						);
					try{
						$errors = Functions::Validate($data,$_POST);
						if ((isset($errors)) and (is_array($errors))){
							echo"
                                <div class=\"alert alert-danger\">
                                    <button type=\"button\" class=\"close\" data-dismiss=\"alert\">
                                        <span aria-hidden=\"true\">×</span>
                                        <span class=\"sr-only\">Close</span>
                                    </button>";
                                     foreach ($errors as $error){
                                        echo "<p>{$error}</p>";
                                    }
                            echo "</div>";
						}else{
							if (isset($_POST['persistent'])){
								$persistent = true;
							}else{
								$persistent = false;
							}
							if (!$user->login($_POST['email'], $_POST['password'], $persistent)){
								echo "
								<div style=\"margin-top:15px\" class=\"alert alert-danger\">
		                            <button type=\"button\" class=\"close\" data-dismiss=\"alert\">
		                                <span aria-hidden=\"true\">×</span>
		                                <span class=\"sr-only\">Close</span>
		                            </button>
		                            Date incorecte!
		                        </div>";
							}else{
								$url = "books.php";
								echo "<META HTTP-EQUIV=\"refresh\" content=\"0; URL=".$url."\"> ";
								exit();
							}
						}
					}
					catch(Exception $e){
						echo "
							<div style=\"margin-top:15px\" class=\"alert alert-danger\">
	                            <button type=\"button\" class=\"close\" data-dismiss=\"alert\">
	                                <span aria-hidden=\"true\">×</span>
	                                <span class=\"sr-only\">Close</span>
	                            </button>
	                            
	                            ". $e->getMessage() ."
	                        </div>";
					}
				}
				if (isset($_SESSION['succes'])){
					 echo "
	                    <div class=\"alert alert-success\" style=\"margin-top:15px\">
	                        <button type=\"button\" class=\"close\" data-dismiss=\"alert\">
	                            <span aria-hidden=\"true\">×</span>
	                            <span class=\"sr-only\">Close</span>
	                        </button>
	                        
	                        {$_SESSION['succes']}
	                    </div>";
                    unset($_SESSION['succes']);
				}
				?> 
			</div>
			<form action="<?php echo $_SERVER['PHP_SELF'];?>" method="post" role="form" id="login" class="login-form fade-in-effect">
				<div class="login-header">
					<img src="../assets/images/logo.png" alt="" width="100" />
					<p>Acces zona administrare.</p>
				</div>
				<div class="form-group">
					<label class="control-label" for="email">Email</label>
					<input type="email" class="form-control" name="email" id="email" />
				</div>

				<div class="form-group">
					<label class="control-label" for="password">Password</label>
					<input type="password" class="form-control" name="password" id="password" />
				</div>
				<div class="form-group">
					<input type="checkbox" name="persistent" id="persistent" value="true" />
					<label for="persistent">Pastreaza-ma logat</label>
				</div>
				<div class="form-group">
					<input type="hidden" name="send" value="true" />
					<button type="submit" class="btn btn-primary  btn-block text-left">
						<i class="fa fa-lock"></i>
						Logare
					</button>
				</div>
			</form>
		</div>	
	</div>
<?php
require("blocks/footer.php");
?>