<?php
include_once(__DIR__ . "/bootstrap.php");
$config = parse_ini_file("config/config.ini");


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

$prompts = \PromptPlaza\Framework\Prompt::getAllFavourites($_SESSION['user_id']);

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HomeEase Favourites</title>
</head>

<body>
    <?php include_once("nav.inc.php"); ?>
    <h1>This are your favourites!</h1>
    <!-- Toont prompts -->
    <div class="prompts">
        <?php foreach ($prompts as $prompt) : ?>
            <div class="prompt">
                <strong><?php echo htmlspecialchars($prompt['firstname']) . " " . htmlspecialchars($prompt['lastname']); ?></strong>
                <p><?php echo "prompt: " . htmlspecialchars($prompt['prompt']); ?></p>
                <img src="<?php echo $cloudinary->image($prompt['image'])->resize(Resize::fill(100, 150))->toUrl(); ?>" alt="prompt image">
                <p><?php echo "price: " . htmlspecialchars($prompt['price']); ?></p>
                <p><?php echo "details: " . htmlspecialchars($prompt['details']); ?></p>

                <!-- Toont likes-->
                <div>
                    <a href="#" data-id="<?php echo htmlspecialchars($prompt['id']) ?>" class="like" id="like<?php echo htmlspecialchars($prompt['id']) ?>">Like</a>
                    <span class='likes' id="likes<?php echo htmlspecialchars($prompt['id']) ?>"><?php echo $prompts = \PromptPlaza\Framework\Prompt::getLikes($prompt['id']); ?></span>
                    <span class="status"></span>
                    people like this
                </div>

                <!-- Toont add to favourite -->
                <div>
                    <a href="#" data-id="<?php echo htmlspecialchars($prompt['id']) ?>" class="favourite favourited" id="favourite<?php echo htmlspecialchars($prompt['id']) ?>">Remove from favourites</a>
                    <span class="status"></span>
                </div>


                <!-- Toont comments -->
                <div class="post_comments">
                    <div class="post_comments_form">
                        <input type="text" placeholder="Place here your comment" id="comment<?php echo htmlspecialchars($prompt['id']) ?>">
                        <a href="#" class="btn" data-id="<?php echo htmlspecialchars($prompt['id']) ?>">Add comment</a>
                    </div>

                    <ul class="post_comments_list<?php echo htmlspecialchars($prompt['id']) ?>">
                        <?php $allComments = \PromptPlaza\Framework\Comment::getComments($prompt['id']);
                        foreach ($allComments as $c) : ?>
                            <li>
                                <strong><?php echo htmlspecialchars($c['firstname']) . " " . htmlspecialchars($c['lastname']); ?></strong>
                                <?php echo $c['text']; ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>

            </div>
        <?php endforeach; ?>
    </div>

    <script src="js/script.js"></script>
</body>

</html>