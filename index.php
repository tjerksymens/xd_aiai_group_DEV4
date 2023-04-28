<?php
    include_once(__DIR__ . "/bootstrap.php");

    $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
    $offset = ($page - 1) * 10;
    $prompts = \PromptPlaza\Framework\Prompt::getAll($offset);
    $totalPrompts = \PromptPlaza\Framework\Prompt::countAll();
    $totalPages = ceil($totalPrompts / 10);
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
        <?php if($_SESSION['loggedin'] !== true): ?>
            <a href="login.php">login</a>
        <?php else: ?>
            <?php include_once("nav.inc.php"); ?>
        <?php endif; ?>
    <h1>Homepage</h1>

    <div class="prompts">
        <?php foreach($prompts as $prompt): ?>
            <div class="prompt">
                <?php if($_SESSION['loggedin'] !== true): ?>
                    <p><strong><?php echo htmlspecialchars($prompt['firstname']) . " " . htmlspecialchars($prompt['lastname']);?></strong> <?php echo htmlspecialchars(substr($prompt['prompt'], 0, 20)) . '...';?></p>
                    <a href="login.php">login to see full prompt</a>
                <?php else: ?>
                    <p><strong><?php echo htmlspecialchars($prompt['firstname']) . " " . htmlspecialchars($prompt['lastname']);;?></strong> <?php echo htmlspecialchars($prompt['prompt']);?></p>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>
    </div>
    
    <!-- Teller om van pagina te veranderen voor volgende prompts te zien -->
    <div class="pagination">
            <?php if($page > 1): ?>
                <a href="?page=<?php echo $page - 1; ?>">Previous</a>
            <?php endif; ?>

            <?php for($i = 1; $i <= $totalPages; $i++): ?>
                <?php if($i === $page): ?>
                    <span><?php echo $i; ?></span>
                <?php else: ?>
                    <a href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                <?php endif; ?>
            <?php endfor; ?>

            <?php if($page < $totalPages): ?>
                <a href="?page=<?php echo $page + 1; ?>">Next</a>
            <?php endif; ?>
        </div>

</body>

</html>