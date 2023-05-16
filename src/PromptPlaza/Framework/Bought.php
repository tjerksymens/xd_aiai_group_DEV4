<?php

namespace PromptPlaza\Framework;

class Bought 
{
    private $userId;
    private $promptId;

    public function setUserId($userId)
    {
        $this->userId = $userId;
    }

    public function getUserId()
    {
        return $this->userId;
    }

    public function setPromptId($promptId)
    {
        $this->promptId = $promptId;
    }

    public function getPromptId()
    {
        return $this->promptId;
    }

    public function save()
    {
        //checked of de prompt al gekocht is door de user
        $conn = Db::getConnection();
        $statement = $conn->prepare("SELECT * FROM bought_prompts WHERE user_id = :userid AND prompt_id = :promptid");
        $statement->bindValue(":userid", $this->getUserId());
        $statement->bindValue(":promptid", $this->getPromptId());
        $statement->execute();
        $result = $statement->fetch();

        //als de user de prompt al heeft gekocht, en het resultaat dus niet leeg is, dan zal hij niet meer gekocht worden
        //als de user de prompt nog niet heeft gekocht, en het resultaat dus leeg is, dan zal hij gekocht worden
        if (empty($result)) {
            $statement = $conn->prepare("INSERT INTO bought_prompts (user_id, prompt_id) VALUES (:userid, :promptid)");
            $statement->bindValue(":userid", $this->getUserId());
            $statement->bindValue(":promptid", $this->getPromptId());
            $statement->execute();

            //mail sturen hier
            //toekoomst ook gekochte prompts weergeven op profiel
        } else {
            $statement = $conn->prepare("DELETE FROM bought_prompts WHERE user_id = :userid AND prompt_id = :promptid");
            $statement->bindValue(":userid", $this->getUserId());
            $statement->bindValue(":promptid", $this->getPromptId());
            $statement->execute();
        }
    }

    public static function checkIfBought($userId, $promptId)
    {
        $conn = Db::getConnection();
        $statement = $conn->prepare("SELECT * FROM bought_prompts WHERE user_id = :userid AND prompt_id = :promptid");
        $statement->bindValue(":userid", $userId);
        $statement->bindValue(":promptid", $promptId);
        $statement->execute();
        $result = $statement->fetch();

        if (!empty($result)) {
            return true;
        } else {
            return false;
        }
    }
}