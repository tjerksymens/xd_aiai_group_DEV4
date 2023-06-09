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

$user_id = $_SESSION['user_id'];
$user = \PromptPlaza\Framework\User::getById($user_id);
$credits = \PromptPlaza\Framework\User::getCredits($user_id);
$prompts = \PromptPlaza\Framework\Prompt::getAllPromptsFromUser($user_id);
$profile_picture = $user['image'];

if ($_SESSION['loggedin'] !== true) {
    header("Location: login.php");
}

if ($user['moderator'] == 1) {
    header("Location: moderator_profile.php");
}

if (!empty($_POST)) {
    if (isset($_POST['set_image'])) {
        if (isset($_FILES['image'])) {
            try {
                $image = new \PromptPlaza\Framework\Image($cloudinary);
                $newImgName = $image->upload($_FILES['image']);

                $user = new \PromptPlaza\Framework\User();
                $user->imageSave($newImgName, $user_id);

                $user = \PromptPlaza\Framework\User::getById($user_id);
                $profile_picture = $user['image'];
            } catch (Throwable $e) {
                $error = $e->getMessage();
            }
        } else {
            $error = "No image selected.";
        }
    }

    if (isset($_POST['delete_account'])) {
        $user->delete();
        session_destroy();
        header("Location: login.php");
    }

    if (isset($_POST['reset_password'])) {
        header("Location: reset_password.php");
    }
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <title><?php echo htmlspecialchars($user['firstname']) . ' ' . htmlspecialchars($user['lastname']); ?></title>
</head>

<body>
    <?php include_once("nav.inc.php"); ?>
    <div id="profile_header">
        <?php if (!empty($profile_picture)) : ?>
            <div class="profile_picture">
                <img src="<?php echo $cloudinary->image($profile_picture)->resize(Resize::fill(300, 300))->toUrl(); ?>" alt="profile picture">
            </div>
        <?php else : ?>
            <div class="profile_picture">
                <img src="uploads/profile_picture_placeholder.jpg" alt="profile picture" width="300px">
            </div>
        <?php endif; ?>
        <div>
            <h1><?php echo htmlspecialchars($user['firstname']) . ' ' . htmlspecialchars($user['lastname']); ?></h1>
            <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
            <div class="credits">
                <h2>Your credits</h2>
                <p><?php echo htmlspecialchars($credits['credits']); ?> credits</p>
            </div>
        </div>
        <div id="profile_settings">
            <form action="" method="post" enctype="multipart/form-data">
                <div class="form__field" id="img_upload">
                    <label for="image">Upload a new profile picture: </label>
                    <input type="file" name="image">
                </div>
                <div class="form__field">
                    <input type="submit" value="Upload" class="btn btn--primary" name="set_image">
                </div>
            </form>

            <form action="" method="post">
                <button type="submit" name="reset_password" class="btn btn--primary">Reset Password</button>
            </form>
            <form action="" method="post">
                <button id="delete_account" type="submit" name="delete_account" class="btn btn--primary">Delete Account</button>
            </form>
        </div>
    </div>

    <?php if (isset($error)) : ?>
        <div class="form__error">
            <p>
                <?php echo $error; ?>
            </p>
        </div>
    <?php endif; ?>

    <!-- Toont zoeken op details -->
    <form action="" method="get">
        <label for="details">Browse your prompts by details</label>
        <input type="text" name="details">
        <input type="submit" value="Browse" class="btn btn--primary">
    </form>


    <h2>Your prompts</h2>
    <!-- Toont prompts -->
    <div class="prompts">
        <?php foreach ($prompts as $prompt) : ?>
            <?php if ($prompt['username'] == $user['username']) : ?>
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
            <?php endif; ?>
        <?php endforeach; ?>
    </div>
</body>

</html>