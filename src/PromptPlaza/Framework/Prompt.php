<?php

namespace PromptPlaza\Framework;

class Prompt
{
    public int $id;
    public string $prompt;
    public int $user_id;

    //haalt alle prompts op en geeft ze terug
    public static function getAll($offset = 0){
        $conn = Db::getConnection();
        $statement = $conn->prepare("
            SELECT prompts.*, users.email 
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
    public static function countAll(){
        $conn = Db::getConnection();
        $statement = $conn->prepare("SELECT COUNT(*) FROM prompts");
        $statement->execute();
        return $statement->fetchColumn();
    }
}
