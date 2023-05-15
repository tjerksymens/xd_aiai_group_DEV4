<?php

namespace PromptPlaza\Framework;

class Follow 
{
    private $userId;
    private $followedId;

    public function setUserId($userId)
    {
        $this->userId = $userId;
    }

    public function getUserId()
    {
        return $this->userId;
    }

    public function setFollowedId($followedId)
    {
        $this->followedId = $followedId;
    }

    public function getFollowedId()
    {
        return $this->followedId;
    }

    public function save()
    {
        //checked of de user al volgt
        $conn = Db::getConnection();
        $statement = $conn->prepare("SELECT * FROM user_relations WHERE user_id = :userid AND followed_id = :followedid");
        $statement->bindValue(":userid", $this->getUserId());
        $statement->bindValue(":followedid", $this->getFollowedId());
        $statement->execute();
        $result = $statement->fetch();

        //als de user nog niet volgt, en het resultaat dus leeg is, dan zal hij gevolgd worden
        //als de user al volgt, en het resultaat dus niet leeg is, dan zal hij niet meer gevolgd worden
        if (!$result) {
            $statement = $conn->prepare("INSERT INTO user_relations (user_id, followed_id) VALUES (:userid, :followedid)");
            $statement->bindValue(":userid", $this->getUserId());
            $statement->bindValue(":followedid", $this->getFollowedId());
            return $statement->execute();
        } else {
            $statement = $conn->prepare("DELETE FROM user_relations WHERE user_id = :userid AND followed_id = :followedid");
            $statement->bindValue(":userid", $this->getUserId());
            $statement->bindValue(":followedid", $this->getFollowedId());
            return $statement->execute();
        }
    }

    public function checkIfFollowing($userId, $followedId)
    {
        $conn = Db::getConnection();
        $statement = $conn->prepare("SELECT * FROM user_relations WHERE user_id = :userid AND followed_id = :followedid");
        $statement->bindValue(":userid", $userId);
        $statement->bindValue(":followedid", $followedId);
        $statement->execute();
        $result = $statement->fetch();
        if ($result) {
            return true;
        } else {
            return false;
        }
    }
}