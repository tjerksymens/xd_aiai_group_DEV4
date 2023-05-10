<?php
include_once("../bootstrap.php");

try {
    $f = new \PromptPlaza\Framework\Prompt();
    $liked = array();

    // loop through the liked prompts and add them to the $liked array
    foreach ($f->getAllLiked($_SESSION['user_id']) as $like) {
        $liked[] = $like['prompt_id'];
    }

    // create a JSON response containing the list of liked prompts
    $response = array('liked' => $liked);
    echo json_encode($response);
} catch (Exception $e) {
    // handle any exceptions that are thrown
    $response = array('error' => $e->getMessage());
    echo json_encode($response);
}
