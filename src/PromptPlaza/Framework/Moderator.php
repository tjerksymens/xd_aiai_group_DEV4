<?php

namespace PromptPlaza\Framework;

class Moderator extends \PromptPlaza\Framework\User
{
    protected string $canEdit;
    protected string $canDelete;
    protected string $canApprove;

    public function checkEdit($id)
    {
        $conn = Db::getConnection();
        $statement = $conn->prepare("SELECT canEdit FROM users WHERE id = :id");
        $statement->bindValue(":id", $id);
        $statement->execute();
        $user = $statement->fetch(\PDO::FETCH_ASSOC);
        $this->canEdit = $user['canEdit'];

        if ($this->canEdit == '1') {
            return true;
        } else {
            return false;
        }
    }

    public function checkDelete($id)
    {
        $conn = Db::getConnection();
        $statement = $conn->prepare("SELECT canDelete FROM users WHERE id = :id");
        $statement->bindValue(":id", $id);
        $statement->execute();
        $user = $statement->fetch(\PDO::FETCH_ASSOC);
        $this->canDelete = $user['canDelete'];

        if ($this->canDelete == '1') {
            return true;
        } else {
            return false;
        }
    }

    public function checkApprove($id)
    {
        $conn = Db::getConnection();
        $statement = $conn->prepare("SELECT canApprove FROM users WHERE id = :id");
        $statement->bindValue(":id", $id);
        $statement->execute();
        $user = $statement->fetch(\PDO::FETCH_ASSOC);
        $this->canApprove = $user['canApprove'];

        if ($this->canApprove == '1') {
            return true;
        } else {
            return false;
        }
    }
}