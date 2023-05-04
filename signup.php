<?php
    include_once(__DIR__ . "/bootstrap.php");
	$config = parse_ini_file("config/config.ini");
	$apiKey = $config['SENDGRID_API_KEY'];

    if(!empty($_POST)){
        try{
			$user = new \PromptPlaza\Framework\User();
			if($user->checkExistingEmail($_POST['email'])){
				echo "This email already exists in the database. Please try again with a different email address.";
				die;
			}
			else {
				//maak  random validation code aan en hash deze voor oplsag db (2^12)
				$options = [
					'cost' => 12,
				];
				$validation_code = bin2hex(random_bytes(8)); // Generate a 16-character hexadecimal string
				$validation_code_hash = password_hash($validation_code, PASSWORD_DEFAULT, $options); // Hash the validation code for storage in the database
		
				
				$user = new \PromptPlaza\Framework\User();
				$user->setEmail($_POST['email']);
				$user->setFirstname($_POST['firstname']);
				$user->setLastname($_POST['lastname']);
				$user->setPassword($_POST['password']);
				$user->setConfirmPassword($_POST['confirmpassword']);
				$user->setValidationCode($validation_code_hash);
				$user->setValidated(0);
				$user->save();

				//sessie starten voor nieuwe user die wordt gebruikt voor controle bij validation
				$_SESSION['user_id'] = $user->getId($_POST['email']);

				//send email
				require 'vendor/autoload.php'; // If you're using Composer (recommended)
				// Comment out the above line if not using Composer
				// require("<PATH TO>/sendgrid-php.php");
				// If not using Composer, uncomment the above line and
				// download sendgrid-php.zip from the latest release here,
				// replacing <PATH TO> with the path to the sendgrid-php.php file,
				// which is included in the download:
				// https://github.com/sendgrid/sendgrid-php/releases
				$recipientEmail = $_POST['email'];
				$nameEmail = $_POST['firstname'] . ' ' . $_POST['lastname'];
				$firstnameEmail = $_POST['firstname'];

				$email = new \SendGrid\Mail\Mail(); // create new email
				$email->setFrom("promptplaza@hotmail.com", "Wouter From Promptplaza"); // set sender
				$email->setSubject("Welcome to Promptplaza! Verify your email here."); // set subject
				$email->addTo($_POST['email'] , $nameEmail); // set recipient
				$email->addContent("text/plain", "Welcome to Promptplaza $firstnameEmail! Here is your activation code: <strong>$validation_code</strong>"); //set title
				$email->addContent(
					"text/html", "Welcome to Promptplaza $firstnameEmail! Here is your activation code: <strong>$validation_code</strong>"
				); //set text
				$sendgrid = new \SendGrid($apiKey);
				try { // try to send email
					$response = $sendgrid->send($email);
					print $response->statusCode() . "\n";
					print_r($response->headers());
					print $response->body() . "\n";
				} catch (Exception $e) { // if email could not be sent, print error
					echo 'Caught exception: '. $e->getMessage() ."\n";
				}

				header("Location: validate.php");
			}
		}
		catch(Throwable $e){
            $error = $e->getMessage();
        }
    }

?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign up</title>
</head>
<body>
    <div class="login">
		<div class="form form--login">
			<form action="" method="post">
				<h2 form__title>Sign Up</h2>
				
				<?php if( isset($error) ):?>
				<div class="form__error">
					<p>
						<?php echo $error;?>
					</p>
				</div>
				<?php endif; ?>

				<div class="form__field">
					<label for="Email">Email</label>
					<input type="text" name="email">
				</div>
				<div class="form__field">
					<label for="Firstname">Firstname</label>
					<input type="text" name="firstname">
				</div>
				<div class="form__field">
					<label for="Lastname">Lastname</label>
					<input type="text" name="lastname">
				</div>
				<div class="form__field">
					<label for="Password">Password</label>
					<input type="password" name="password">
				</div>
				<div class="form__field">
					<label for="ConfirmPassword">Confirm Password</label>
					<input type="password" name="confirmpassword">
				</div>
				<div class="form__field">
					<input type="submit" value="Sign Up" class="btn btn--primary">	
				</div>
			</form>
		</div>
	</div>
</body>
</html>