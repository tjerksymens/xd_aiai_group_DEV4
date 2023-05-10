<?php
include_once("../bootstrap.php");

try {
    $f = new \PromptPlaza\Framework\Prompt();
    $favourited = array();

    // loop through the favourited prompts and add them to the $favourited array
    foreach ($f->getAllFavourites($_SESSION['user_id']) as $favourite) {
        $favourited[] = $favourite['prompt_id'];
    }

    // create a JSON response containing the list of favourited prompts
    $response = array('favourited' => $favourited);
    echo json_encode($response);
} catch (Exception $e) {
    // handle any exceptions that are thrown
    $response = array('error' => $e->getMessage());
    echo json_encode($response);
}
