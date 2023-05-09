<?php

namespace PromptPlaza\Framework;

class User
{
    private string $email;
    private string $username;
    private string $firstname;
    private string $lastname;
    private string $password;
    private string $confirmpassword;
    private string $validationcode;
    private string $validated;
    private string $image;


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

    public function setUsername($username)
    {
        if (empty($username)) {
            throw new \Exception("Username cannot be empty.");
        } else {
            $this->username = $username;
            return $this;
        }
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function setFirstname($firstname)
    {
        if (empty($firstname)) {
            throw new \Exception("Firstname cannot be empty.");
        } else {
            $this->firstname = $firstname;
            return $this;
        }
    }

    public function getFirstname()
    {
        return $this->firstname;
    }

    public function setLastname($lastname)
    {
        if (empty($lastname)) {
            throw new \Exception("Lastname cannot be empty.");
        } else {
            $this->lastname = $lastname;
            return $this;
        }
    }

    public function getLastname()
    {
        return $this->lastname;
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

    public function setConfirmPassword($confirmpassword)
    {
        //confirm passwo
        if (empty($confirmpassword)) {
            throw new \Exception("Confirm password cannot be empty.");
        } else {
            $options = [
                'cost' => 12,
            ];
            $confirmpassword = password_hash($_POST['password'], PASSWORD_DEFAULT, $options);
            $this->confirmpassword = $confirmpassword;
            return $this;
        }
    }

    public function getConfirmPassword()
    {
        return $this->confirmpassword;
    }

    public function setValidationCode($validationcode)
    {
        $this->validationcode = $validationcode;
        return $this;
    }

    public function getValidationCode()
    {
        return $this->validationcode;
    }

    public function setValidated($validated)
    {
        $this->validated = $validated;
        return $this;
    }

    public function getValidated()
    {
        return $this->validated;
    }

    public function confirmPass($password, $confirmpassword)
    {
        if ($password !== $confirmpassword) {
            throw new \Exception("Passwords do not match.");
        } else {
            return true;
        }
    }

    public function updatePassword($email, $password)
    {
        if (strlen($password) < 8) {
            throw new \Exception("Password must be at least 8 characters.");
        } else {
            $options = [
                'cost' => 12,
            ];
            $hash = password_hash($password, PASSWORD_DEFAULT, $options);
            $conn = Db::getConnection();
            $statement = $conn->prepare("UPDATE users SET password = :password WHERE email = :email");
            $statement->bindValue(":email", $email);
            $statement->bindValue(":password", $hash);
            return $statement->execute();
        }
    }

    public function resetPassword($sendEmail)
    {
        $conn = Db::getConnection();
        $statement = $conn->prepare("SELECT * FROM users WHERE email = :email");
        $statement->bindValue(":email", $sendEmail);
        $statement->execute();
        $user = $statement->fetch(\PDO::FETCH_ASSOC);

        $config = parse_ini_file("config/config.ini");
        $apiKey = $config['SENDGRID_API_KEY'];
        $nameEmail = $user['firstname'] . ' ' . $user['lastname'];
        $firstnameEmail = $user['firstname'];
        //deze werkt alleen als je de juiste link voor jouw localhost hebt naar de reset password pagina, pas deze aaan in de config ZIE README
        $link =  $config['password_reset_url'];

        $email = new \SendGrid\Mail\Mail(); // create new email
        $email->setFrom("promptplaza@hotmail.com", "Wouter From Promptplaza"); // set sender
        $email->setSubject("Reset you're password now!"); // set subject
        $email->addTo($sendEmail, $nameEmail); // set recipient
        $email->addContent("text/plain", "Hey $firstnameEmail! Click <a href=\"$link\">here</a> to reset your password.<br> 
        If you didn't request a password reset, just ignore this email."); //set title
        $email->addContent(
            "text/html",
            "Hey $firstnameEmail! Click <a href=\"$link\">here</a> to reset your password.<br> 
            If you didn't request a password reset, just ignore this email."
        ); //set text
        $sendgrid = new \SendGrid($apiKey);
        try { // try to send email
            $response = $sendgrid->send($email);
            print $response->statusCode() . "\n";
            print_r($response->headers());
            print $response->body() . "\n";
        } catch (\Exception $e) { // if email could not be sent, return error
            $error = 'Caught exception: ' . $e->getMessage() . "\n";
            return $error;
        }
    }

    public function save()
    {
        $conn = Db::getConnection();
        $statement = $conn->prepare("INSERT INTO users (email, username, password, firstname, lastname, validationcode, validated) VALUES (:email, :username, :password, :firstname, :lastname, :validationcode, :validated)");
        $statement->bindValue(":email", $this->email);
        $statement->bindValue(":username", $this->username);
        $statement->bindValue(":password", $this->password);
        $statement->bindValue(":firstname", $this->firstname);
        $statement->bindValue(":lastname", $this->lastname);
        $statement->bindValue(":validationcode", $this->validationcode);
        $statement->bindValue(":validated", $this->validated);
        return $statement->execute();
    }

    public function canLogin($email, $password)
    {
        if (empty($email) || empty($password)) {
            throw new \Exception("Email and password are required.");
        } else {
            $conn = \PromptPlaza\Framework\Db::getConnection();
            $statement = $conn->prepare("SELECT * FROM users WHERE email = :email");
            $statement->bindValue(":email", $email);
            $statement->execute();
            $user = $statement->fetch(\PDO::FETCH_ASSOC);
            $hash = $user['password'];

            if (password_verify($password, $hash)) {
                return true;
            } else {
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

    public static function getId($email)
    {
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
        return $result;
    }

    public static function getByUsername($username)
    {
        $conn = Db::getConnection();
        $statement = $conn->prepare("SELECT * FROM users WHERE username = :username");
        $statement->bindValue(":username", $username);
        $statement->execute();
        $result = $statement->fetch(\PDO::FETCH_ASSOC);
        return $result;
    }

    public static function checkExistingEmail($email)
    {
        $conn = Db::getConnection();
        $statement = $conn->prepare("SELECT * FROM users WHERE email = :email");
        $statement->bindValue(":email", $email);
        $statement->execute();
        $result = $statement->fetch(\PDO::FETCH_ASSOC);

        if (!empty($result)) {
            // The email already exists in the database
            return true;
        } else {
            // The email does not exist in the database
            return false;
        }
    }

    public static function checkExistingUsername($username)
    {
        $conn = Db::getConnection();
        $statement = $conn->prepare("SELECT * FROM users WHERE username = :username");
        $statement->bindValue(":username", $username);
        $statement->execute();
        $result = $statement->fetch(\PDO::FETCH_ASSOC);

        if (!empty($result)) {
            // The username already exists in the database
            return true;
        } else {
            // The username does not exist in the database
            return false;
        }
    }

    public static function compareValidationcodeById($id, $validationcode)
    {
        $conn = \PromptPlaza\Framework\Db::getConnection();
        $statement = $conn->prepare("SELECT * FROM users WHERE id = :id");
        $statement->bindValue(":id", $id);
        $statement->execute();
        $user = $statement->fetch(\PDO::FETCH_ASSOC);
        $hash = $user['validationcode'];

        if (password_verify($validationcode, $hash)) {
            return true;
        } else {
            return false;
        }
    }

    public static function Validate($id)
    {
        $conn = Db::getConnection();
        $statement = $conn->prepare("UPDATE users SET validated = 1 WHERE id = :id");
        $statement->bindValue(":id", $id);
        return $statement->execute();
    }

    public static function checkValidated($email)
    {
        $conn = Db::getConnection();
        $statement = $conn->prepare("SELECT validated FROM users WHERE email = :email");
        $statement->bindValue(":email", $email);
        $statement->execute();
        $result = $statement->fetch(\PDO::FETCH_ASSOC);
        $validate = $result['validated'];

        if ($validate == 1) {
            // the user is validated
            return true;
        } else {
            // the user is not validated
            return false;
        }
    }

    public function getImage()
    {
        if (isset($this->image)) {
            return $this->image;
        } else {
            return null;
        }
    }

    public function setImage($image)
    {
        if (empty($image)) {
            throw new \Exception("Image cannot be empty.");
        } else {
            $this->image = $image;
            return $this;
        }
    }

    public function imageSave($image, $id)
    {
        $conn = Db::getConnection();
        $statement = $conn->prepare("UPDATE users SET image = :image WHERE id = :id");
        $statement->bindValue(":image", $image);
        $statement->bindValue(":id", $id);
        return $statement->execute();
    }
}
