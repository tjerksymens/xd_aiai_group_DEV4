<?php
    include_once(__DIR__ . "/bootstrap.php");
	include_once("functions.inc.php");


    if(!empty($_POST)){
		$username = $_POST['email'];
		$password = $_POST['password'];

		if(canLogin($username, $password)) {
			session_start();
			$_SESSION['loggedin'] = true;
			header("Location: index.php");

			header("Location: index.php");
		}
		else {
			$error = true;
		}
	}

?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
</head>
<body>
    <div class="login">
		<div class="form form--login">
			<form action="" method="post">
				<h2 form__title>Login</h2>
				
				<?php if( isset($error) ):?>
				<div class="form__error">
					<p>
						Sorry, we can't log you in with that email address and password. Can you try again?
					</p>
				</div>
				<?php endif; ?>

				<div class="form__field">
					<label for="Email">Email</label>
					<input type="text" name="email">
				</div>
				<div class="form__field">
					<label for="Password">Password</label>
					<input type="password" name="password">
				</div>

				<div class="form__field">
					<input type="submit" value="Login" class="btn btn--primary">	
				</div>
			</form>
		</div>
	</div>
</body>
</html>