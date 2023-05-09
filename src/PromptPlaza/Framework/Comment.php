<?php

namespace PromptPlaza\Framework;

class Comment
{
    private $text;
    private $promptId;
    private $userId;


    public function getText()
    {
        return $this->text;
    }

    public function setText($text)
    {
        $this->text = $text;

        return $this;
    }

    public function getPromptId()
    {
        return $this->promptId;
    }

    public function setPromptId($promptId)
    {
        $this->promptId = $promptId;

        return $this;
    }

    public function getUserId()
    {
        return $this->userId;
    }

    public function setUserId($userId)
    {
        $this->userId = $userId;

        return $this;
    }

    public function save()
    {
        $conn = Db::getConnection();
        $statement = $conn->prepare("insert into comments (text, prompt_id, user_id) values (:text, :prompt_id, :user_id)");
        $statement->bindValue(":text", $this->getText());
        $statement->bindValue(":prompt_id", $this->getPromptId());
        $statement->bindValue(":user_id", $this->getUserId());
        $result = $statement->execute();
        return $result;
    }

    public static function getComments($id)
    {
        $conn = Db::getConnection();
        $statement = $conn->prepare("
            SELECT comments.*, users.firstname, users.lastname, users.username
            FROM comments 
            JOIN users ON comments.user_id = users.id 
            WHERE comments.prompt_id LIKE :promptid 
            ");
        $statement->bindValue(":promptid", $id);
        $statement->execute();
        $result = $statement->fetchAll(\PDO::FETCH_ASSOC);
        return $result;
    }
}
