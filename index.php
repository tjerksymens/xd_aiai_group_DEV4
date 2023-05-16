<?php
include_once(__DIR__ . "/bootstrap.php");
$config = parse_ini_file("config/config.ini");
$apiKey = $config['SENDGRID_API_KEY'];

//session login check
if ($_SESSION['loggedin'] !== true) {
    header('location: index_no_login.php');
}

use Cloudinary\Cloudinary;
use Cloudinary\Transformation\Resize;

$cloudinary = new Cloudinary(
    [
        'cloud' => [
            'cloud_name' => $config['cloud_name'],
            'api_key'    => $config['api_key'],
            'api_secret' => $config['api_secret'],
        ],
    ]
);

//prompt toevoegen
if (!empty($_POST['image'])) {
    //img upload en check
    if (isset($_FILES['image'])) {
        try {
            $image = new \PromptPlaza\Framework\Image($cloudinary);
            $newImgName = $image->upload($_FILES['image']);

            $prompt = new \PromptPlaza\Framework\Prompt();
            $prompt->setPrompt($_POST['prompt']);
            $prompt->setImage($newImgName);
            $prompt->setPrice($_POST['price']);
            $prompt->setDetails($_POST['details']);
            $prompt->setUserId($_SESSION['user_id']);
            $prompt->save();
        } catch (Throwable $e) {
            $error = $e->getMessage();
        }
    } else {
        $error = "No image selected.";
    }
}

//checken of dit mijn prompt is
//buy this prompt
if (isset($_POST['buy_prompt'])) {
    $price = \PromptPlaza\Framework\Prompt::getPriceById($_POST['buy_prompt']);
    $credits = \PromptPlaza\Framework\User::getCredits($_SESSION['user_id']);
    $prompt = \PromptPlaza\Framework\Prompt::getById($_POST['buy_prompt']);
    if ($prompt['user_id'] == $_SESSION['user_id']) {
        $error = "You own this prompt.";
    } else {
        if ($credits < $price) {
            $error = "You don't have enough credits to buy this prompt.";
        } else {
            //credits updaten
            $user = \PromptPlaza\Framework\User::updateCreditsById($_SESSION['user_id'], $price['price']);

            //prompt kopen
            $promptId = $_POST['buy_prompt'];
            $buy = new \PromptPlaza\Framework\Bought();
            $buy->setPromptId($promptId);
            $buy->setUserId($_SESSION['user_id']);
            $buy->save();

            $user = \PromptPlaza\Framework\User::getById($_SESSION['user_id']);
            $fullname = $user['firstname'] . " " . $user['lastname'];
            $firstname = $user['firstname'];
            $title = $prompt['prompt'];
            //zend mail met de prompt
            $email = new \SendGrid\Mail\Mail(); // create new email
            $email->setFrom("promptplaza@hotmail.com", "Wouter From Promptplaza"); // set sender
            $email->setSubject("Here is your prompt"); // set subject
            $email->addTo($user['email'], $fullname); // set recipient
            $email->addContent("text/plain", "Hey $firstname! Thank you for your purchase! <br> 
                Here is your new prompt: <strong>$title</strong> <br> <br> We hope you will have fun with it and come back for more prompts at Promtplaza.");
            $email->addContent(
                "text/html",
                "Hey $firstname! Thank you for your purchase! <br> 
                Here is your new prompt: <strong>$title</strong> <br> <br> We hope you will have fun with it and come back for more prompts at Promtplaza."
            ); //set text
            $sendgrid = new \SendGrid($apiKey);
            try { // try to send email
                $response = $sendgrid->send($email);
                $responseData = $response;
            } catch (Exception $e) { // if email could not be sent, print error
                echo 'Caught exception: ' . $e->getMessage() . "\n";
            }
        }
    }
}

$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$offset = ($page - 1) * 10;
$prompts = \PromptPlaza\Framework\Prompt::getAll($offset);
$totalPrompts = \PromptPlaza\Framework\Prompt::countAll();
$totalPages = ceil($totalPrompts / 10);

//prompt filteren
if (isset($_GET['filter'])) {
    $prompts = \PromptPlaza\Framework\Prompt::getFiltered($_GET['filter']);
}

//prompt zoeken op details
if (isset($_GET['details'])) {
    $prompts = \PromptPlaza\Framework\Prompt::searchDetails($_GET['details']);
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <title>PromptPlaza</title>
</head>

<body>
    <?php include_once("nav.inc.php"); ?>
    <h1>Homepage</h1>

    <div id="Homepage_Prompt_Forms">
        <div id="Prompt_Form">
            <!-- Toont formulier om prompt toe te voegen -->
            <form action="" method="post" enctype="multipart/form-data">
                <div class="form__field">
                    <label for="prompt">Prompt</label>
                    <input type="text" name="prompt">
                </div>
                <div class="form__field">
                    <label for="image">Upload image</label>
                    <input type="file" name="image">
                </div>
                <div class="form__field">
                    <label for="price">Select price</label>
                    <select name="price">
                        <option value="free">Free</option>
                        <option value="1 credit">1 credit</option>
                        <option value="2 credits">2 credits</option>
                    </select>
                </div>
                <div class="form__field">
                    <label for="details">Type and details of model</label>
                    <input type="text" name="details">
                </div>
                <div class="form__field">
                    <input type="submit" value="Add" class="btn btn--primary">
                </div>
            </form>
            <?php if (isset($error)) : ?>
                <div class="form__error">
                    <p class="error">
                        <?php echo $error; ?>
                    </p>
                </div>
            <?php endif; ?>
        </div>
        <div id="Prompt_Filter">
            <!-- Toont filter op prijs -->
            <form action="" method="get">
                <label for="filter">Filter on price</label>
                <select name="filter">
                    <option value="free">Free</option>
                    <option value="1 credit">1 credit</option>
                    <option value="2 credits">2 credits</option>
                </select>
                <input type="submit" value="Filter" class="btn btn--primary btn--filter">
            </form>
            <?php
            if (isset($_GET['filter'])) {
                $selected_value = $_GET['filter'];
                echo "You selected: " . $selected_value;
            }
            ?>

            <!-- Toont zoeken op details -->
            <form action="" method="get">
                <label for="details">Browse by details</label>
                <input type="text" name="details">
                <input type="submit" value="Browse" class="btn btn--primary">
            </form>
        </div>
    </div>

    <!-- Toont prompts -->
    <div class="prompts">
        <?php foreach ($prompts as $prompt) : ?>
            <div class="prompt">
                <strong id="Prompt__Creator__Head">Made by: <a href="other_user_profile.php?username=<?php echo htmlspecialchars($prompt['username']); ?>"><?php echo htmlspecialchars($prompt['username']); ?></a></strong>
                <h2><?php echo "prompt: " . htmlspecialchars($prompt['prompt']); ?></h2>
                <img src="<?php echo $cloudinary->image($prompt['image'])->resize(Resize::fill(100, 150))->toUrl(); ?>" alt="prompt image">
                <div id="Prompt__Details">
                    <?php if ($prompt['price'] == 1) : ?>
                        <p><?php echo "price: " . htmlspecialchars($prompt['price'])  . " credit"; ?></p>
                    <?php else : ?>
                        <p><?php echo "price: " . htmlspecialchars($prompt['price'])  . " credits"; ?></p>
                    <?php endif; ?>
                    <?php if (!$showBuyButton = \PromptPlaza\Framework\Bought::checkIfBought($_SESSION['user_id'], $prompt['id'])) : ?>
                        <form action="" method="post">
                            <button type="submit" name="buy_prompt" value="<?php echo htmlspecialchars($prompt['id']) ?>">Buy</button>
                        </form>
                    <?php else : ?>
                        <p>Owned</p>
                    <?php endif; ?>
                    <p><?php echo "details: " . htmlspecialchars($prompt['details']); ?></p>
                </div>

                <div id="Prompt__LikeFavourite">
                    <!-- Toont likes-->
                    <div>
                        <a href="#" data-id="<?php echo htmlspecialchars($prompt['id']) ?>" class="like" id="like<?php echo htmlspecialchars($prompt['id']) ?>">Like</a>
                        <span class='likes' id="likes<?php echo htmlspecialchars($prompt['id']) ?>"><?php echo $prompts = \PromptPlaza\Framework\Prompt::getLikes($prompt['id']); ?></span>
                        <?php if ($prompts !== 1) : ?>
                            <span class="status">people like this</span>
                        <?php else : ?>
                            <span class="status">person likes this</span>
                        <?php endif; ?>
                    </div>

                    <!-- Toont add to favourite -->
                    <div>
                        <a href="#" data-id="<?php echo htmlspecialchars($prompt['id']) ?>" class="favourite" id="favourite<?php echo htmlspecialchars($prompt['id']) ?>">Add to favourites</a>
                    </div>
                </div>

                <!-- Toont comments -->
                <div class="post_comments">
                    <div class="post_comments_form">
                        <input type="text" placeholder="Place your comment here" class="comment__field__prompt" id="comment<?php echo htmlspecialchars($prompt['id']) ?>">
                        <a href="#" class="btn_comments" data-id="<?php echo htmlspecialchars($prompt['id']) ?>">Add comment</a>
                    </div>

                    <ul class="post_comments_list<?php echo htmlspecialchars($prompt['id']) ?>">
                        <?php $allComments = \PromptPlaza\Framework\Comment::getComments($prompt['id']);
                        foreach ($allComments as $c) : ?>
                            <li>
                                <strong><a href="other_user_profile.php?username=<?php echo htmlspecialchars($c['username']); ?>"><?php echo htmlspecialchars($c['username']); ?></a></strong>
                                <?php echo $c['text']; ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>

            </div>
        <?php endforeach; ?>
    </div>

    <!-- Teller om van pagina te veranderen voor volgende prompts te zien -->
    <div class="pagination">
        <?php if ($page > 1) : ?>
            <a href="?page=<?php echo $page - 1; ?>">Previous</a>
        <?php endif; ?>

        <?php for ($i = 1; $i <= $totalPages; $i++) : ?>
            <?php if ($i === $page) : ?>
                <span><?php echo $i; ?></span>
            <?php else : ?>
                <a href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
            <?php endif; ?>
        <?php endfor; ?>

        <?php if ($page < $totalPages) : ?>
            <a href="?page=<?php echo $page + 1; ?>">Next</a>
        <?php endif; ?>
    </div>

    <script src="js/script.js"></script>
</body>

</html>