<?php

    function canLogin($email, $password) {
		$conn = Db::getConnection();
		$statement = $conn->prepare("SELECT * FROM users WHERE email = :email");
		$statement->bindValue(":email", $email);
		$statement->execute();
		$user = $statement->fetch(PDO::FETCH_ASSOC);
		$hash = $user['password'];
		
		if(password_verify($password, $hash)){
			return true;
		}
		else {
			return false;
		}
	}

?>