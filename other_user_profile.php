<?php
include_once(__DIR__ . "/bootstrap.php");
$config = parse_ini_file("config/config.ini");

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

$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$offset = ($page - 1) * 10;
$prompts = \PromptPlaza\Framework\Prompt::getAll($offset);
$totalPrompts = \PromptPlaza\Framework\Prompt::countAll();
$totalPages = ceil($totalPrompts / 10);

//ga naar home als er geen profilename is meegegeven
if (!isset($_GET['username'])) {
    header('location: index.php');
}

$user = \PromptPlaza\Framework\User::getByUsername($_GET['username']);
//ga naar eigen profiel als je op je eigen naam klikt
if($_GET['username'] == $user['username']){
    header('location: profile.php');
}
$profile_picture = $user['image'];

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($user['firstname']) . ' ' . htmlspecialchars($user['lastname']) ; ?></title>
</head>

<body>
    <?php include_once("nav.inc.php"); ?>
    <h1><?php echo htmlspecialchars($user['firstname']) . ' ' . htmlspecialchars($user['lastname']) ; ?></h1>

    <?php if (!empty($profile_picture)) : ?>
        <div class="profile_picture">
             <img src="<?php echo $cloudinary->image($profile_picture)->resize(Resize::fill(100, 150))->toUrl(); ?>" alt="profile picture">
        </div>
    <?php else : ?>
        <div class="profile_picture">
            <img src="uploads/profile_picture_placeholder.jpg" alt="profile picture" width="300px">
        </div>
    <?php endif; ?>

    <?php if (isset($error)) : ?>
        <div class="form__error">
            <p>
                <?php echo $error; ?>
            </p>
        </div>
    <?php endif; ?>

    <h2>Prompts by this user</h2>

    <!-- Toont zoeken op details -->
    <form action="" method="get">
        <label for="details">Browse user prompts by details</label>
        <input type="text" name="details">
        <input type="submit" value="Browse" class="btn btn--primary">
    </form>

    <!-- Toont prompts -->
    <div class="prompts">
    <?php foreach ($prompts as $prompt) : ?>
            <?php if ($prompt['username'] == $user['username']) : ?>
                <div class="prompt">
                    <strong><a href="other_user_profile.php?username=<?php echo htmlspecialchars($prompt['username']); ?>"><?php echo htmlspecialchars($prompt['username']); ?></a></strong>
                    <p><?php echo "prompt: " . htmlspecialchars($prompt['prompt']); ?></p>
                    <img src="<?php echo $cloudinary->image($prompt['image'])->resize(Resize::fill(100, 150))->toUrl(); ?>" alt="prompt image">
                    <p><?php echo "price: " . htmlspecialchars($prompt['price']); ?></p>
                    <p><?php echo "details: " . htmlspecialchars($prompt['details']); ?></p>

                    <!-- Toont likes-->
                    <div>
                        <a href="#" data-id="<?php echo htmlspecialchars($prompt['id']) ?>" class="like">Like</a>
                        <span class='likes' id="likes<?php echo htmlspecialchars($prompt['id']) ?>"><?php echo $prompts = \PromptPlaza\Framework\Prompt::getLikes($prompt['id']); ?></span>
                        <span class="status"></span>
                        people like this
                    </div>

                    <!-- Toont add to favourite -->
                    <div>
                        <a href="#" data-id="<?php echo htmlspecialchars($prompt['id']) ?>" class="favourite_add" id="favourite_add<?php echo htmlspecialchars($prompt['id']) ?>">Add to favourites</a>
                    </div>


                    <!-- Toont comments -->
                    <div class="post_comments">
                        <div class="post_comments_form">
                            <input type="text" placeholder="Place here your comment" id="comment<?php echo htmlspecialchars($prompt['id']) ?>">
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
            <?php endif; ?>
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
</body>

</html>