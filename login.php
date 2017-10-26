<?php
require_once 'php/website_configuration.php'; //Grabs website configurations

$page_title = "Login"; //set title for page
$error      = null;    //set null error for validation output

//START Login Process - if form is submitted
if($_SERVER["REQUEST_METHOD"] == "POST"){
	$username = $_POST['username'];
	$password = $_POST['password'];
	
	//START Input Validation
	
	//Use the same error code for all possible login fails to prevent hinting true information
	if(empty($username) OR empty($password)){// OR $result === FALSE OR password_verify($password, $){
		$error .= "Login failed.";
	}
	//Checks database for user and fetch its password
	else{
		$user_grab_info = $dbconnect->prepare("SELECT user_pass FROM users WHERE user_name=?");
		$user_grab_info->bind_param("s", $username);
		$user_grab_info->execute();
		$user_grab_info->bind_result($fetched_pass); //Bind the grabbed pass to $fetched_pass
		$result = $user_grab_info->fetch(); //Allows us to use $fetched_pass
		$user_grab_info->close();
		
		//Fails login if user does not exist
		if($result === NULL ){
			$error .= "Login failed.";
		}
		
		//User exists
		else{
			//Store information related to login for recording login attempt
			$client_ip = $_SERVER['REMOTE_ADDR'];
			$datetime  = date("Y-m-d H:i:s");
			
			//Prepare login attempt statement
			$record_login = $dbconnect->prepare("INSERT INTO login_history 
				(login_user_name, login_attempt, login_datetime, login_client_ip)
				VALUES (?,?,?,?)");
			$record_login->bind_param("ssss", $username, $attempt, $datetime, $client_ip);
			
			//Fails if incorrect password and logs a failure for the attempted login
			if(password_verify($password, $fetched_pass) === FALSE){
				$error .= "Login failed.";
				$attempt = 'Failure';
				$record_login->execute();
				$record_login->close();
			}
			//All checks passed and logs a successful login, and redirects accordingly
			else{
				$attempt = 'Success';
				$record_login->execute();
				$record_login->close();
				header("Location: dashboard");
			}
		}
	}
	//END Input Validation

}
//END Login Process

require_once 'php/incl/template_outside/html_head.php'; //Begins head for html part of the file
?>

  <div class="container">
    <div class="card card-login mx-auto mt-5">
      <div class="card-body">
        <form method="post">
		  <div class="form-row">
		    <div class="col-md-12 text-center">
		      <img src="img/logo.png" class="img-fluid">
		    </div>
		  </div>
		  
		  <?php
		  if($error !== null){
			echo
			'<div class="alert alert-danger text-center" role="alert">
			'.$error.'
			</div>';
		  }
		  ?>
		  
          <div class="form-group">
            <label for="inputUsername">Username</label>
            <input class="form-control" id="inputUsername" type="text" placeholder="Enter username" name="username">
          </div>
          <div class="form-group">
            <label for="inputPassword">Password</label>
            <input class="form-control" id="inputPassword" type="password" placeholder="Password" name="password">
          </div>
          <input type="submit" class="btn btn-primary btn-block" value="Login">
        </form>
		<div class="text-center">
          <a class="d-block small mt-3" href="register">Register an Account</a>
          <a class="d-block small" href="forgot-password">Forgot Password?</a>
        </div>
      </div>
    </div>
  </div>

<?php
require_once 'php/incl/template_outside/html_foot.php'; //Includes core javascript files, then closes body and ends html
?>
