<?php
include_once(__DIR__ . "/bootstrap.php");


if (!empty($_POST)) {
	$email = $_POST['email'];
	$user = new \PromptPlaza\Framework\User();
	if ($user->checkExistingEmail($email)) {
		try {
			$user->resetPassword($email);
			$success = "Check your email for a link to reset your password. If it doesn’t appear within a few minutes, check your spam folder.";
		} catch (Throwable $e) {
			$error = $e->getMessage();
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
	<link rel="stylesheet" href="css/style.css">
	<title>Reset password</title>
</head>

<body>
	<div class="reset_password">
		<div class="form form--reset_password">
			<form action="" method="post">
				<h2 form__title>Reset password</h2>

				<?php if (isset($success)) : ?>
					<div class="form__success">
						<p>
							<?php echo $success; ?>
						</p>
					</div>
				<?php endif; ?>

				<?php if (isset($error)) : ?>
					<div class="form__error">
						<p>
							<?php echo $error; ?>
						</p>
					</div>
				<?php endif; ?>

				<div class="form__field">
					<label for="Email">Email</label>
					<input type="text" name="email">
				</div>

				<div class="form__field">
					<input type="submit" value="Reset password" class="btn btn--primary">
				</div>

				<div>
					<a href="login.php" class="btn btn--primary">Or log in here</a>
				</div>
			</form>
		</div>
	</div>
</body>

</html>