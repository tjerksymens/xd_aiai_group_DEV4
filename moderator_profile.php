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
$profile_picture = $user['image'];

$mod = new \PromptPlaza\Framework\Moderator();
$moderator = $mod->getById($user_id);
$moderatorEdit = $mod->checkEdit($user_id);
$moderatorDelete = $mod->checkDelete($user_id);
$moderatorApprove = $mod->checkApprove($user_id);

$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$offset = ($page - 1) * 10;
$prompts = \PromptPlaza\Framework\Prompt::getAll($offset);
$totalPrompts = \PromptPlaza\Framework\Prompt::countAll();
$totalPages = ceil($totalPrompts / 10);

if ($_SESSION['loggedin'] !== true) {
    header("Location: login.php");
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

    if (isset($_POST['approve_prompt'])) {
        $prompt_id = $_POST['prompt_id'];
        $prompt = \PromptPlaza\Framework\Prompt::approve($prompt_id);
    }

    if (isset($_POST['delete_prompt'])) {
        $prompt_id = $_POST['prompt_id'];
        $prompt = \PromptPlaza\Framework\Prompt::deletePrompt($prompt_id);
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
                <img src="<?php echo $cloudinary->image($profile_picture)->resize(Resize::fill(100, 150))->toUrl(); ?>" alt="profile picture">
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
            <p><strong>Moderator:</strong> capabilities</p>
            <ul id="mod_tier_list">
                <?php if ($moderatorEdit) : ?>
                    <li>Can Edit posts</li>
                <?php endif; ?>
                <?php if ($moderatorDelete) : ?>
                    <li>Can Delete posts</li>
                <?php endif; ?>
                <?php if ($moderatorApprove) : ?>
                    <li>Can Approve posts</li>
                <?php endif; ?>
            </ul>
        </div>
        <div id="profile_settings">
            <form action="" method="post" enctype="multipart/form-data">
                <div class="form__field">
                    <label for="image">Upload image</label>
                    <input type="file" name="image">
                </div>
                <div class="form__field">
                    <input type="submit" value="Upload a profile picture" class="btn btn--primary" name="set_image">
                </div>
            </form>

            <form action="" method="post">
                <button type="submit" name="reset_password" class="btn btn--primary">Reset Password</button>
            </form>
            <form action="" method="post">
                <button type="submit" name="delete_account" class="btn btn--primary">Delete Account</button>
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


    <?php if($moderatorApprove) : ?>
        <!-- Toont zoeken op details -->
        <form action="" method="get">
            <label for="details">Browse your prompts by details</label>
            <input type="text" name="details">
            <input type="submit" value="Browse" class="btn btn--primary">
        </form>

        <h2>Unapproved prompts</h2>
        <!-- Toont prompts -->
        <div class="prompts">
            <?php foreach ($prompts as $prompt) : ?>
                <?php if ($prompt['approved'] === 0) : ?>
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

                        <!-- Toont approve en delete prompt -->
                        <div id="Prompt__ApproveDelete">
                            <form action="" method="post">
                                <input type="hidden" name="prompt_id" value="<?php echo htmlspecialchars($prompt['id']) ?>">
                                <input type="submit" value="Approve" class="btn btn--primary" name="approve_prompt">
                                <input type="submit" value="Delete" class="btn btn--primary" name="delete_prompt">
                            </form>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</body>

</html>