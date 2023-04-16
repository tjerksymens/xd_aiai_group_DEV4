<?php
    class Account {
        private string $email;
        private string $password;
        
        public function setEmail($email){
            if(empty($email)){
                throw new Exception("Email cannot be empty.");
            }
            else{
                $this->email = $email;
                return $this;
            }
        }
        
        public function getEmail(){
            return $this->email;
        }

        public function setPassword($password)
        {
            if(strlen($password) < 8){
                throw new Exception("Password must be at least 8 characters.");
            }
            else{
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

        public function save() {
            $conn = Db::getConnection();
            $statement = $conn->prepare("INSERT INTO users (email, password) VALUES (:email, :password)");
            $statement->bindValue(":email", $this->email);
            $statement->bindValue(":password", $this->password);
            return $statement->execute();
        }
    }