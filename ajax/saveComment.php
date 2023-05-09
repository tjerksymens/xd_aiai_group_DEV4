<?php
include_once("../bootstrap.php");

if (!empty($_POST)) {

    //new comment
    $comment = new \PromptPlaza\Framework\Comment();
    $comment->setText($_POST['text']);
    $comment->setPromptId($_POST['id']);
    $comment->setUserId($_SESSION['user_id']);

    //save comment
    $comment->save();

    //get firstname and lastname
    $user = \PromptPlaza\Framework\User::getById($_SESSION['user_id']);
    $lastname = $user['username'];


    //succes teruggeven
    $response = [
        'status' => 'success',
        'body' => htmlspecialchars($comment->getText()),
        'username' => htmlspecialchars($username),
        'message' => 'Comment saved'
    ];

    header('Content-Type: application/json');
    echo json_encode($response); //json_encode zet een array om naar een json string
}
