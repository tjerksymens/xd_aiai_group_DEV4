<?php
include_once(__DIR__ . "/bootstrap.php");

use Cloudinary\Cloudinary;
use Cloudinary\Transformation\Resize;

$cloudinary = new Cloudinary(
    [
        'cloud' => [
            'cloud_name' => 'doxzjrtjh',
            'api_key'    => '436969446252812',
            'api_secret' => 'JMz0eaR82cExLX0ZgEWmgcn8lb4',
        ],
    ]
);

$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$offset = ($page - 1) * 10;
$prompts = \PromptPlaza\Framework\Prompt::getAll($offset);
$totalPrompts = \PromptPlaza\Framework\Prompt::countAll();
$totalPages = ceil($totalPrompts / 10);

//session login check
if ($_SESSION['loggedin'] !== true) {
    header('location: index_no_login.php');
}

//prompt toevoegen
if (!empty($_POST)) {
    //img upload en check
    if (isset($_FILES['image'])) {
        $img_name = $_FILES['image']['name'];
        $img_size = $_FILES['image']['size'];
        $tmp_name = $_FILES['image']['tmp_name'];
        $img_error = $_FILES['image']['error'];

        if ($img_error === 0) {
            if ($img_size > 1000000) {
                $error = "Sorry, your file is too large.";
            } else {
                $img_ex = pathinfo($img_name, PATHINFO_EXTENSION);
                $img_ex_lc = strtolower($img_ex);

                $allowed_exs = array("jpg", "jpeg", "png");

                if (in_array($img_ex_lc, $allowed_exs)) {
                    $new_img_name = uniqid("IMG-", true) . '.' . $img_ex_lc . $img_ex_lc;
                    $cloudinary->uploadApi()->upload(
                        $_FILES['image']['tmp_name'],
                        ["public_id" => $new_img_name]
                    );
                    try {
                        $prompt = new \PromptPlaza\Framework\Prompt();
                        $prompt->setPrompt($_POST['prompt']);
                        $prompt->setImage($new_img_name);
                        $prompt->setPrice($_POST['price']);
                        $prompt->setDetails($_POST['details']);
                        $prompt->setUserId($_SESSION['user_id']);
                        $prompt->save();
                    } catch (Throwable $e) {
                        $error = $e->getMessage();
                    }
                } else {
                    $error = "You can't upload files of this type.";
                }
            }
        } else {
            $error = "Unknown error occurred.";
        }
    } else {
        $error = "No image selected.";
    }
}

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
    <title>PromptPlaza</title>
</head>

<body>
    <?php include_once("nav.inc.php"); ?>
    <h1>Homepage</h1>

    <!-- Toont formulier om prompt toe te voegen -->
    <form action="" method="post" enctype="multipart/form-data">
        <?php if (isset($error)) : ?>
            <div class="form__error">
                <p>
                    <?php echo $error; ?>
                </p>
            </div>
        <?php endif; ?>
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

    <!-- Toont filter op prijs -->
    <form action="" method="get">
        <label for="filter">Filter on price</label>
        <select name="filter">
            <option value="free">Free</option>
            <option value="1 credit">1 credit</option>
            <option value="2 credits">2 credits</option>
        </select>
        <input type="submit" value="Filter" class="btn btn--primary">
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

    <!-- Toont prompts -->
    <div class="prompts">
        <?php foreach ($prompts as $prompt) : ?>
            <div class="prompt">
                <strong><?php echo htmlspecialchars($prompt['firstname']) . " " . htmlspecialchars($prompt['lastname']); ?></strong>
                <p><?php echo "prompt: " . htmlspecialchars($prompt['prompt']); ?></p>
                <img src="<?php echo $cloudinary->image($prompt['image'])->resize(Resize::fill(100, 150))->toUrl(); ?>" alt="prompt image">
                <p><?php echo "price: " . htmlspecialchars($prompt['price']); ?></p>
                <p><?php echo "details: " . htmlspecialchars($prompt['details']); ?></p>
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

</body>

</html>