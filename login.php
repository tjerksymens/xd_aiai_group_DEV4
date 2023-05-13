<?php
include_once(__DIR__ . "/bootstrap.php");


if (!empty($_POST)) {
	$email = $_POST['email'];
	$password = $_POST['password'];
	$user = new \PromptPlaza\Framework\User();
	try {
		if ($user->canLogin($email, $password)) {
			$user = new \PromptPlaza\Framework\User();
			if ($user->checkValidated($email)) {
				session_start();
				$_SESSION['loggedin'] = true;
				$_SESSION['user_id'] = $user->getId($email);
				header("Location: index.php");
			} else {
				$error = "Please <a href='validate.php'>validate</a> your account first. Check you're mail for the validation code.";
			}
		} else {
			$error = "Sorry, we can't log you in with that email address and password. Can you try again?";
		}
	} catch (Throwable $e) {
		$error = $e->getMessage();
	}
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" href="css/style.css">
	<title>Login</title>
</head>

<body>
	<div class="login">
		<div class="form form__login">
			<form action="" method="post">
				<h2 form__title class="form__login__title">Login</h2>

				<?php if (isset($error)) : ?>
					<div class="form__error">
						<p>
							<?php echo $error; ?>
						</p>
					</div>
				<?php endif; ?>

				<div class="form__field">
					<label for="Email">Username or email</label>
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
			<div class="form__login__links">
				<a href="signup.php">Sign Up Here</a>
				<br>
				<a href="forgot_password.php">Forgot My Password</a>
			</div>
		</div>
	</div>
</body>

</html>