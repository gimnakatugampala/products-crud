<?php

$pdo = new PDO('mysql:host=localhost;port=3306;dbname=products_crud', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$id = $_GET['id'] ?? null;

if (!$id) {
    header('Location:index.php');
    exit;
}

$statement = $pdo->prepare('SELECT * FROM products WHERE id = :id');
$statement->bindValue(':id', $id);
$statement->execute();
$product = $statement->fetch(PDO::FETCH_ASSOC);

// echo '<pre>';
// var_dump($product);
// echo '</pre>';



//if get supergloal is applied then this will help
$title = $product['title'];
$description = $product['description'];
$price = $product['price'];
$date = date('Y-m-d H:i:s');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $date = date('Y-m-d H:i:s');

    $errors = [];

    if (!$title) {
        $errors[] = 'Product title is required!';
    }

    if (!$price) {
        $errors[] = 'Product Price is required!';
    }

    if (!is_dir('images')) {
        mkdir('images');
    }

    if (empty($errors)) {
        $image  = $_FILES['image'] ?? null;
        //if the image path does not exist

        $imagePath = $product['image'];

        if ($image && $image['tmp_name']) {

            if ($product['image']) {
                //unlike deletes the xisting image
                unlink($product['image']);
            }

            $imagePath = 'images/' . randomString(8) . '/' . $image['name'];
            mkdir(dirname($imagePath));
            move_uploaded_file($image['tmp_name'], $imagePath);
        }

        $statement = $pdo->prepare("UPDATE  products SET title = :title,image = :image,description = :description,price = :price WHERE id = :id");

        $statement->bindValue(':title', $title);
        $statement->bindValue(':image', $imagePath);
        $statement->bindValue(':description', $description);
        $statement->bindValue(':price', $price);
        $statement->bindValue(':id', $id);
        $statement->execute();

        header('Location:index.php');
    }
}

function randomString($n)
{
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $str = '';
    for ($i = 0; $i < $n; $i++) {
        $index = rand(0, strlen($characters) - 1);
        $str .= $characters[$index];
    }

    return $str;
}
?>

<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-giJF6kkoqNQ00vy+HMDP7azOuL0xtbfIcaT9wjKHr8RbDVddVHyTfAAsrekwKmP1" crossorigin="anonymous">
    <link rel="stylesheet" href="app.css">
    <title>Update Product</title>
</head>

<body>
    <a href="index.php" class="btn btn-primary">Go Back</a>
    <h1>Update Product <b><?php echo $product['title'] ?></b></h1>

    <?php if (!empty($errors)) : ?>
        <div class="alert alert-danger">
            <?php foreach ($errors as $error) : ?>
                <div><?php echo $error ?></div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <form action="" method="POST" enctype="multipart/form-data">

        <?php if ($product['image']) : ?>
            <img src="<?php echo $product['image'] ?>" class="update-image">
        <?php endif; ?>

        <div class="mb-3">
            <label>Product Image</label>
            <input type="file" name="image">
        </div>
        <div class="mb-3">
            <label>Product Title</label>
            <input type="text" class="form-control" name="title" value="<?php echo $title ?>">
        </div>
        <div class="mb-3">
            <label>Product Description</label>
            <textarea class="form-control" name="description"><?php echo $description ?></textarea>
        </div>
        <div class="mb-3">
            <label>Product Price</label>
            <input type="number" step=".01" class="form-control" name="price" value="<?php echo $price ?>">
        </div>
        <button type="submit" class="btn btn-primary">Submit</button>
    </form>

</body>

</html>