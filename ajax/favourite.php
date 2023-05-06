<?php
include_once("../bootstrap.php");
if (!empty($_POST)) {
    $promptId = $_POST['id'];
    $userId = $_SESSION['user_id'];

    $l = new \PromptPlaza\Framework\Favourite();
    $l->setPromptId($promptId);
    $l->setUserId($userId);
    $l->save();

    $p = new \PromptPlaza\Framework\Prompt();
    $p->id = $promptId;
    $favourites = $p->getFavourites($userId, $promptId);

    $result = [
        "status" => "success",
        "message" => "Favourite was saved",
        "favourites" => $favourites,
    ];

    echo json_encode($result);
}
