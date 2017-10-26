<?php
require_once 'php/website_configuration.php';   //Grabs website configurations
require_once 'php/incl/security_questions.php'; //Grabs security questions for registration

$page_title = "Register"; //set title for page
$error      = null;       //set null error for validation output
$success    = null;       //set null for creating success alert about registration

//START Registration Process - if form is submitted.
if($_SERVER["REQUEST_METHOD"] == "POST"){
	$username  = $_POST['username'];
	$email     = filter_var($_POST['email_address'], FILTER_VALIDATE_EMAIL);
	$pass      = $_POST['password'];
	$pass2     = $_POST['password_confirm'];
	$question1 = intval($_POST['question1']); //Convert security_question_array index to from string to integer for validation
	$answer1   = $_POST['answer1'];
	$question2 = intval($_POST['question2']); //Convert security_question_array index to from string to integer for validation
	$answer2   = $_POST['answer2'];
	
	//Prep hash information
	$hash_algorithm = PASSWORD_DEFAULT;
	$cost = array('cost' => 11);
	
	//START Input Validation
	
	//Username
	if(empty($username)){
		$error .= "A username is required. <br>";
	}
	elseif(strlen($username) > 50){
		$error .= "Jeez that username is long. Not accepting it. <br>";
	}
	elseif(strlen($username) < 4){
		$error .= "Username is too short. <br>";
	}
	elseif(!preg_match("/^[a-zA-Z0-9 ]*$/",$username)){
			$error .= "Usernames are limited to letters and numbers. <br>";
	}
	else{
		$user_exists_check = $dbconnect->prepare("SELECT user_id FROM users WHERE user_name = ?");
		$user_exists_check->bind_param("s", $username);
		$user_exists_check->execute();
		$result = $user_exists_check->get_result();
		if($result->num_rows > 0){
			$error .= "Username is already in use. <br>";
		}
		$user_exists_check->close();
	}
	
	//E-mail
	if(empty($email)){
		$error .= "A valid e-mail is required. <br>";
	}
	elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)){
		$error .= "That e-mail is not valid. <br>";
	}
	
	//Password
	if(empty($pass OR $pass2)){
		$error .= "You need to enter a password and confirm it. <br>";
	}
	elseif($pass !== $pass2){
		$error .= "You did not correctly confirm your password. <br>";
	}
	elseif(!preg_match("/^[0-9A-Za-z!@#$%]*$/",$pass)){
		$error .= "Your password may only have numbers, letters, and !@#$% in it. <br>";
	}
	else{
		//Hash Password Before Insertion
		$hashed_pass = password_hash($pass,$hash_algorithm,$cost);// HASH PASSWORD HERE BEFORE INSERTING INTO DATABASE
	}

	//Security Questions
	if(empty($question1) OR empty($answer1) OR empty($question2) OR empty($answer2)){
		$error .= "Please fill out both security questions.";
	}
	elseif(strlen($answer1) > 50 OR strlen($answer2) > 50){
		$error .= "There is no way you will remember that long answer. Limit it to a word or two.";
	}
	elseif(!preg_match("/^[0-9A-Za-z ]*$/",$answer1) OR !preg_match("/^[0-9A-Za-z ]*$/",$answer2)){
		$error .= "No special characters in your answer.<br> <small>*You may also use numbers, if applicable.</small>";
	}
	elseif($question1 === $question2){
		$error .= "You cannot use a security question twice.";
	}
	else{
		//Hash answers with password_hash() since it is sensitive data that could grant access to accounts
		//password_verify can be used to see if answer is correct
		$hashed_answer1 = password_hash($answer1,$hash_algorithm,$cost);// HASH ANSWERS HERE BEFORE INSERTING INTO DATABASE
		$hashed_answer2 = password_hash($answer2,$hash_algorithm,$cost);// Database allows hash to be up to 255 characters. Validation of 50 is limit for registrant's memory.
	}

	//END Input Validation
	
	//START Insertion - if validation passed
	if($error === null){
		$registration_success = $dbconnect->prepare("INSERT INTO users 
		(user_name, user_email, user_pass, user_security_1_question,user_security_1_answer, user_security_2_question, user_security_2_answer) 
		VALUES (?,?,?,?,?,?,?)");
		$registration_success->bind_param("sssisis", $username, $email, $hashed_pass, $question1, $hashed_answer1, $question2, $hashed_answer2);
		$registration_success->execute();
		$registration_success->close();
		$success = 'You have successfully registered! <a href="login">Click here to login</a>.';
	}
	//END Insertion
}
//END Registration Process

require_once 'php/incl/template_outside/html_head.php'; //Begins head for html part of the file
?>

  <div class="container">
    <div class="card card-register mx-auto mt-5">
      <div class="card-body">
        <form method="post">
		  <div class="form-row">
		    <div class="col-md-12 text-center">
		      <img src="img/logo.png" class="img-fluid">
		    </div>
		  </div>
		  
		  <?php
		  if($open_registration === FALSE){
			echo '
			<h1 class="text-center">Registration is Closed</h1>
			<div class="text-center">
              <a class="d-block small mt-3" href="login">Login Page</a>
              <a class="d-block small" href="forgot-password">Forgot Password?</a>
            </div></div></div></div>
			';
			require_once 'php/incl/template_outside/html_foot.php';
			exit();
		  }
		  if($error !== null){
			echo
			'<div class="alert alert-danger text-center" role="alert">
			'.$error.'
			</div>';
		  }
		  if($success !== null){
			echo
			'<div class="alert alert-success text-center" role="alert">
			'.$success.'
			</div>';
		  }
		  ?>
		  
          <div class="form-group">
			<div class="form-row">
              <div class="col-md-12">
                <label for="inputName">Username</label>
                <input class="form-control" id="inputName" type="text" placeholder="Enter desired username" name="username">
              </div>
            </div>
          </div>
          <div class="form-group">
            <label for="inputEmail1">E-mail address</label>
            <input class="form-control" id="inputEmail1" type="email"  placeholder="Enter e-mail address" name="email_address">
          </div>
          <div class="form-group">
            <div class="form-row">
              <div class="col-md-6">
                <label for="inputPassword1">Password</label>
                <input class="form-control" id="inputPassword1" type="password" placeholder="Password" name="password">
              </div>
              <div class="col-md-6">
                <label for="confirmPassword">Confirm password</label>
                <input class="form-control" id="confirmPassword" type="password" placeholder="Confirm password" name="password_confirm">
              </div>
            </div>
          </div>
		  <div class="form-group">
            <div class="form-row">
              <div class="col-md-12">
                <label for="selectQuestion1">Security Question 1</label>
                <select class="form-control" id="selectQuestion1" name="question1">
				  <?php 
				  foreach($security_questions_array as $key => $question){
					echo '<option value='.$key.'>'.$question.'</option>';
				  }
				  ?>
				</select>
              </div>
			</div>
			<div class="form-row">
              <div class="col-md-12">
                <label for="confirmPassword">Answer 1</label>
                <input class="form-control" id="inputAnswer1" type="text" placeholder="Answer" name="answer1">
              </div>
            </div>
          </div>
		  <div class="form-group">
            <div class="form-row">
              <div class="col-md-12">
                <label for="selectQuestion2">Security Question 2</label>
                <select class="form-control" id="selectQuestion2" name="question2">
				  <?php 
				  foreach($security_questions_array as $key => $question){
					  echo '<option value='.$key.'>'.$question.'</option>';
				  }
				  ?>
				</select>
              </div>
			</div>
			<div class="form-row">
              <div class="col-md-12">
                <label for="confirmPassword">Answer 2</label>
                <input class="form-control" id="inputAnswer2" type="text" placeholder="Answer" name="answer2">
              </div>
            </div>
          </div>
          <input type="submit" name="register" class="btn btn-primary btn-block" value="Register"></input>
        </form>
		<div class="text-center">
          <a class="d-block small mt-3" href="login">Login Page</a>
          <a class="d-block small" href="forgot-password">Forgot Password?</a>
        </div>
      </div>
    </div>
  </div>

<?php
require_once 'php/incl/template_outside/html_foot.php'; //Includes core javascript files, then closes body and ends html
?>