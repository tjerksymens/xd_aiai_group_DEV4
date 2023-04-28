<?php

namespace PromptPlaza\Framework;

class User
{
    private string $email;
    private string $password;

    public function setEmail($email)
    {
        if (empty($email)) {
            throw new \Exception("Email cannot be empty.");
        } else {
            $this->email = $email;
            return $this;
        }
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setPassword($password)
    {
        if (strlen($password) < 8) {
            throw new \Exception("Password must be at least 8 characters.");
        } else {
            $options = [
                'cost' => 12,
            ];
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT, $options);
            $this->password = $password;
            return $this;
        }
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function save()
    {
        $conn = Db::getConnection();
        $statement = $conn->prepare("INSERT INTO users (email, password) VALUES (:email, :password)");
        $statement->bindValue(":email", $this->email);
        $statement->bindValue(":password", $this->password);
        return $statement->execute();
    }

    public function canLogin($email, $password) {
        if(empty($email) || empty($password)){
            throw new \Exception("Email and password are required.");
        } else {
            $conn = \PromptPlaza\Framework\Db::getConnection();
		    $statement = $conn->prepare("SELECT * FROM users WHERE email = :email");
            $statement->bindValue(":email", $email);
            $statement->execute();
            $user = $statement->fetch(\PDO::FETCH_ASSOC);
            $hash = $user['password'];
            
            if(password_verify($password, $hash)){
                return true;
            }
            else {
                return false;
            }
        }
	}

    public function delete()
    {
        $conn = Db::getConnection();
        $statement = $conn->prepare("DELETE FROM users WHERE email = :email");
        $statement->bindValue(":email", $this->email);
        return $statement->execute();
    }

    public static function getId($email){
        $conn = Db::getConnection();
        $statement = $conn->prepare("SELECT id FROM users WHERE email = :email");
        $statement->bindValue(":email", $email);
        $statement->execute();
        $result = $statement->fetch(\PDO::FETCH_ASSOC);
        return $result['id'];
    }

    public static function getById($id)
    {
    $conn = Db::getConnection();
    $statement = $conn->prepare("SELECT * FROM users WHERE id = :id");
    $statement->bindValue(":id", $id);
    $statement->execute();
    $result = $statement->fetch(\PDO::FETCH_ASSOC);
    
    $user = new User();
    $user->setEmail($result['email']);
    return $user;
    }
}
