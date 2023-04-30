<?php

namespace PromptPlaza\Framework;

class Prompt
{
    private $id;
    private $prompt;
    private $user_id;
    private $image;
    private $price;
    private $details;

    public function getPrompt()
    {
        return $this->prompt;
    }

    public function setPrompt($prompt)
    {
        if (empty($prompt)) {
            throw new \Exception("Prompt cannot be empty.");
        } else {
            $this->prompt = $prompt;
            return $this;
        }
    }

    public function getImage()
    {
        return $this->image;
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

    public function getPrice()
    {
        return $this->price;
    }

    public function setPrice($price)
    {
        if (empty($price)) {
            throw new \Exception("Price cannot be empty.");
        } else {
            $this->price = $price;
            return $this;
        }
    }

    public function getDetails()
    {
        return $this->details;
    }

    public function setDetails($details)
    {
        if (empty($details)) {
            throw new \Exception("Details cannot be empty.");
        } else {
            $this->details = $details;
            return $this;
        }
    }

    public function setUserId($user_id)
    {
        $this->user_id = $user_id;
        return $this;
    }


    //haalt alle prompts op en geeft ze terug
    public static function getAll($offset = 0)
    {
        $conn = Db::getConnection();
        $statement = $conn->prepare(
            "
            SELECT prompts.*, users.firstname, users.lastname 
            FROM prompts 
            JOIN users ON prompts.user_id = users.id 
            ORDER BY prompts.id DESC
            LIMIT 10 OFFSET :offset"
        );
        $statement->bindValue(":offset", (int) $offset, \PDO::PARAM_INT);
        $statement->execute();
        return $statement->fetchAll(\PDO::FETCH_ASSOC);
    }

    //telt alle prompts en geeft het aantal terug
    public static function countAll()
    {
        $conn = Db::getConnection();
        $statement = $conn->prepare("SELECT COUNT(*) FROM prompts");
        $statement->execute();
        return $statement->fetchColumn();
    }

    public function save()
    {
        $conn = Db::getConnection();
        $statement = $conn->prepare("INSERT INTO prompts (prompt, user_id, image, price, details) VALUES (:prompt, :user_id, :image, :price, :details)");
        $statement->bindValue(":prompt", $this->prompt);
        $statement->bindValue(":user_id", $this->user_id);
        $statement->bindValue(":image", $this->image);
        $statement->bindValue(":price", $this->price);
        $statement->bindValue(":details", $this->details);
        return $statement->execute();
    }

    public static function getFiltered($filter)
    {
        $conn = Db::getConnection();
        $statement = $conn->prepare(
            "
            SELECT prompts.*, users.firstname, users.lastname 
            FROM prompts 
            JOIN users ON prompts.user_id = users.id 
            WHERE prompts.price LIKE :filter 
            ORDER BY prompts.id DESC"
        );
        $statement->bindValue(":filter", "%$filter%");
        $statement->execute();
        return $statement->fetchAll(\PDO::FETCH_ASSOC);
    }
}
