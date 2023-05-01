<?php
include_once(__DIR__ . "/bootstrap.php");

if($_SESSION['loggedin'] !== true){
    header("Location: login.php");
}

$user_id = $_SESSION['user_id'];
$user = \PromptPlaza\Framework\User::getById($user_id);

if(!empty($_POST)){
    if(isset($_POST['delete_account'])){
        $user->delete();
        session_destroy();
        header("Location: login.php");
    }
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile</title>
</head>

<body>
    <?php include_once("nav.inc.php"); ?>
    <h1>Profile</h1>
    <p><strong>Email:</strong> <?php echo htmlspecialchars($user->getEmail()); ?></p>
    <form action="" method="post">
        <button type="submit" name="delete_account">Delete Account</button>
    </form>
</body>

</html>