<?php
    include_once(__DIR__ . "/bootstrap.php");

    $page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
    $offset = ($page - 1) * 10;
    $prompts = \PromptPlaza\Framework\Prompt::getAll($offset);
    $totalPrompts = \PromptPlaza\Framework\Prompt::countAll();
    $totalPages = ceil($totalPrompts / 10);

    //prompt toevoegen
    if(!empty($_POST)){
        //img upload en check
        if(isset($_FILES['image'])){
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
                        $new_img_name = uniqid("IMG-", true) . '.' . $img_ex_lc;
                        $img_upload_path = (__DIR__ . "/uploads/") . $new_img_name;
                        move_uploaded_file($tmp_name, $img_upload_path);
                    } else {
                        $error = "You can't upload files of this type.";
                    }
                }
            } else {
                $error = "Unknown error occurred.";
            }
        }
        else{
            $error = "No image selected.";
        }
        
        try{
            $prompt = new \PromptPlaza\Framework\Prompt();
            $prompt->setPrompt($_POST['prompt']);
            $prompt->setImage($new_img_name);
            $prompt->setPrice($_POST['price']);
            $prompt->setDetails($_POST['details']);
            $prompt->setUserId($_SESSION['user_id']);
            $prompt->save();
        }
        catch(Throwable $e){
            $error = $e->getMessage();
        }
        
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
        <?php if($_SESSION['loggedin'] !== true): ?>
            <a href="login.php">login</a>
        <?php else: ?>
            <?php include_once("nav.inc.php"); ?>
        <?php endif; ?>
    <h1>Homepage</h1>

    <!-- Toont formulier om prompt toe te voegen -->
    <?php if($_SESSION['loggedin'] === true): ?>
        <form action="" method="post" enctype="multipart/form-data">
            <?php if( isset($error) ):?>
                <div class="form__error">
                    <p>
                        <?php echo $error;?>
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
    <?php else :
        echo "<p>Login to add a prompt</p>";
    endif; ?>
    

    <!-- Toont prompts -->
    <div class="prompts">
        <?php foreach($prompts as $prompt): ?>
            <div class="prompt">
                <?php if($_SESSION['loggedin'] !== true): ?>
                    <p><strong><?php echo htmlspecialchars($prompt['firstname']) . " " . htmlspecialchars($prompt['lastname']);?></strong> <?php echo "prompt: " . htmlspecialchars(substr($prompt['prompt'], 0, 20)) . '...';?></p>
                    <a href="login.php">login to see full prompt</a>
                <?php else: ?>
                    <strong><?php echo htmlspecialchars($prompt['firstname']) . " " . htmlspecialchars($prompt['lastname']);?></strong>
                    <p><?php echo "prompt: " . htmlspecialchars($prompt['prompt']);?></p>
                    <img src="uploads/<?php echo htmlspecialchars($prompt['image']);?>" alt="prompt image">
                    <p><?php echo "price: " . htmlspecialchars($prompt['price']);?></p>
                    <p><?php echo "details: " . htmlspecialchars($prompt['details']);?></p>
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
