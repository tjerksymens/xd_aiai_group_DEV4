<?php

include_once(__DIR__ . "/bootstrap.php");

if(!empty($_POST)){
	$validationcode = $_POST['validationcode'];
	$user_id = $_SESSION['user_id'];

	try{
		$user = new \PromptPlaza\Framework\User();
		if($user->compareValidationcodeById($user_id, $validationcode)) {
			$user->validate($user_id); //verandert value van validate van 0 naar 1 waardoor deze gechecked kan worden in de login.php
			header("Location: login.php");
		}
		else {
			$error = true;
		}
	}catch(Throwable $e){
		$error = $e->getMessage();
	}
}
?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Validate</title>
</head>
<body>
    <div class="validate">
		<div class="form form--validate">
			<form action="" method="post">
				<h2 form__title>Validate</h2>
                <p>Thank you for registering you're account to <strong>Promptplaza.</strong> <br>
                We have send you an email with a validation code.</p>
				
				<?php if( isset($error) ):?>
				<div class="form__error">
					<p>
						This validation code isn't right. Please check you're mail for the right code.
					</p>
				</div>
				<?php endif; ?>

				<div class="form__field">
					<label for="ValidationCode">validation code: </label>
					<input type="text" name="validationcode">
				</div>

				<div class="form__field">
					<input type="submit" value="Validate" class="btn btn--primary">	
				</div>
			</form>
		</div>
	</div>
</body>
</html>