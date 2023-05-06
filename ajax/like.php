<?php
include_once("../bootstrap.php");
if (!empty($_POST)) {
    $promptId = $_POST['id'];
    $userId = $_SESSION['user_id'];

    $l = new \PromptPlaza\Framework\Like();
    $l->setPromptId($promptId);
    $l->setUserId($userId);
    $l->save();

    $p = new \PromptPlaza\Framework\Prompt();
    $p->id = $promptId;
    $likes = $p->getLikes($promptId);

    $result = [
        "status" => "success",
        "message" => "Like was saved",
        "likes" => $likes,
    ];

    echo json_encode($result);
}
