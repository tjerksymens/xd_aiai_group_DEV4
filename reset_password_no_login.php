<?php
include_once(__DIR__ . "/bootstrap.php");

if (!empty($_POST)) {
	$email = $_POST['email'];
	$password = $_POST['password'];
	$confirmpassword = $_POST['confirmpassword'];
	$user = new \PromptPlaza\Framework\User();
	if ($user->checkExistingEmail($email)) {
		if ($password == $confirmpassword) {
			try {
				$user->updatePassword($email, $password);
				$success = "Your password has been reset.";
			} catch (Throwable $e) {
				$error = $e->getMessage();
			}
		} else {
			$error = "Password and confirm password are not the same.";
		}
	} else {
		$error = "Sorry, we can't find an account with that email address.";
	}
}
?>
<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="UTF-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<title>Sign up</title>
	</head>

	<body>
		<div class="reset_password">
			<div class="form form--reset_password">
				<form action="" method="post">
					<h2 form__title>Reset password	</h2>

					<?php if (isset($error)) : ?>
						<div class="form__error">
							<p>
								<?php echo $error; ?>
							</p>
						</div>
					<?php endif; ?>

					<?php if( isset($success) ):?>
					<div class="form__success">
						<p>
							<?php echo $success; ?>
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
						<label for="ConfirmPassword">Confirm Password</label>
						<input type="password" name="confirmpassword">
					</div>
					<div class="form__field">
						<input type="submit" value="Reset password" class="btn btn--primary">
					</div>
				</form>
			</div>
		</div>
		<div>
			<a href="login.php">Go back to login</a>
		</div>
	</body>
</html>