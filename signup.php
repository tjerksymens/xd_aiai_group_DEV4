<?php
    include_once(__DIR__ . "/bootstrap.php");

    if(!empty($_POST)){
        try{
            $user = new \PromptPlaza\Framework\User();
            $user->setEmail($_POST['email']);
            $user->setPassword($_POST['password']);
            $user->save();
            header("Location: login.php");
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
					<label for="Password">Password</label>
					<input type="password" name="password">
				</div>

				<div class="form__field">
					<input type="submit" value="Sign Up" class="btn btn--primary">	
				</div>
			</form>
		</div>
	</div>
</body>
</html>