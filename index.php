<?php
include_once(__DIR__ . "/bootstrap.php");


  if($_SESSION['loggedin'] !== true){
    header("Location: login.php");
  }

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PromptPlaza</title>
</head>

<body>
    <?php include_once("nav.inc.php"); ?>
    <h1>Homepage</h1>
</body>

</html>