<?php
include_once(__DIR__ . "/bootstrap.php");
$config = parse_ini_file("config/config.ini");

if($_SESSION['loggedin'] !== true){
    header("Location: login.php");
}

use Cloudinary\Cloudinary;
use Cloudinary\Transformation\Resize;

$cloudinary = new Cloudinary(
    [
        'cloud' => [
            'cloud_name' => $config['cloud_name'],
            'api_key'    => ['api_key'],
            'api_secret' =>  $config['api_secret'],
        ],
    ]
);

$user_id = $_SESSION['user_id'];
$user = \PromptPlaza\Framework\User::getById($user_id);
$profile_picture = $user['image'];

if(!empty($_POST)){
    if(isset($_POST['set_image'])){
        if (isset($_FILES['image'])) {
            try {
                $image = new \PromptPlaza\Framework\Image($cloudinary);
                $newImgName = $image->upload($_FILES['image']);
    
                $user->imageSave($newImgName, $user_id);
            } catch (Throwable $e) {
                $error = $e->getMessage();
            }
        } else {
            $error = "No image selected.";
        }
    }

    if(isset($_POST['delete_account'])){
        //ziet er gevaarlijk uit. misschien een popup maken om het account deleten te beschermen
        $user->delete();
        session_destroy();
        header("Location: login.php");
    }

    if(isset($_POST['reset_password'])){
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
    <title>Profile</title>
</head>

<body>
    <?php include_once("nav.inc.php"); ?>
    <h1>Profile</h1>
    
    <form action="" method="post" enctype="multipart/form-data">
        <?php if (!empty($profile_picture)) : ?>
            <div class="profile_picture">
                <img src="<?php echo $cloudinary->image($profile_picture)->resize(Resize::fill(100, 150))->toUrl(); ?>" alt="profile picture">
            </div>
        <?php else: ?>
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

        <div class="form__field">
            <label for="image">Upload image</label>
            <input type="file" name="image">
        </div>
        <div class="form__field">
            <input type="submit" value="Upload a profile picture" class="btn btn--primary" name="set_image">
        </div>
    </form>
    
    <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
    <form action="" method="post">
        <button type="submit" name="reset_password">Reset Password</button>
    </form>
    <form action="" method="post">
        <button type="submit" name="delete_account">Delete Account</button>
    </form>
</body>

</html>