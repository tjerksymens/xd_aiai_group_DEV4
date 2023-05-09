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
</body>

</html>