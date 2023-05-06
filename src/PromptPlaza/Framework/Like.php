<?php

namespace PromptPlaza\Framework;

class Like
{
    private $promptId;
    private $userId;

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
        $statement = $conn->prepare("SELECT * FROM likes WHERE prompt_id = :promptid AND user_id = :userid");
        $statement->bindValue(":promptid", $this->getPromptId());
        $statement->bindValue(":userid", $this->getUserId());
        $statement->execute();
        $result = $statement->fetch();

        if (!$result) {
            $statement = $conn->prepare("INSERT INTO likes (prompt_id, user_id, date_created) VALUES (:promptid, :userid, NOW())");
            $statement->bindValue(":promptid", $this->getPromptId());
            $statement->bindValue(":userid", $this->getUserId());
            return $statement->execute();
        }

        return false; // user has already liked the post
    }
}
