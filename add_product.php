

<?php
include 'components/connection.php';

if (isset($_COOKIE['user_id'])) {
    $user_id = $_COOKIE['user_id'];
} else {
    setcookie('user_id', unique_id(), time() + 60 * 60 * 24 * 30);
}

if (isset($_POST['add'])) {
    $id = unique_id();
    $name = $_POST['name'];
    $name=filter_var($name,FILTER_SANITIZE_STRING);
    $price = $_POST['price'];
    $price=filter_var($price,FILTER_SANITIZE_STRING);
    $image = $_FILES['image']['name'];
    $image=filter_var($image,FILTER_SANITIZE_STRING);
    $ext = pathinfo($image, PATHINFO_EXTENSION);
    $rename = unique_id() . '.' . $ext;
    $image_tmp_name = $_FILES['image']['tmp_name'];
    $image_size = $_FILES['image']['size'];
    $image_folder = 'images/' . $rename;

    if ($image_size > 2000000) {
        $warning_msg[] = 'Image size is too large';
    } else {
        $add_product = $conn->prepare("INSERT INTO products (id, name, price, image) VALUES (?, ?, ?, ?)");
        $add_product->execute([$id, $name, $price, $rename]);
        move_uploaded_file($image_tmp_name, $image_folder);
        $success_msg[] = "Product added successfully";
    }
}
?>


<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Add products</title>
        <link rel="stylesheet" type="text/css" href="style.css">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.0/font/bootstrap-icons.css" rel="stylesheet">

    </head>
    <body>
        <!-- background circle -->
        <div class="bg-circles">
            <div class="circle-1"></div>
            <div class="circle-2"></div>
            <div class="circle-3"></div>
            <div class="circle-4"></div>
            <div class="circle-5"></div>
            <div class="circle-6"></div>
        </div>
        <?php include 'components/header.php'; ?>
        <section>
            <div class="product-form">
                <form action="" method="post" enctype="multipart/form-data">
                    <h3>product info</h3>
                    <div class="input-field">
                        <p>Product Name <span>*</span></p>
                        <input type="text" name="name" placeholder="Enter product name" required class="box">

                    </div>
                    <div class="input-field">
                        <p>Product Price <span>*</span></p>
                        <input type="text" name="price" placeholder="Enter product price" required min="0" max="9999999999" max="10" class="box">

                    </div>
                    <div class="input-field">
                        <p>Product Image <span>*</span></p>
                        <input type="file" name="image" accept="image/*" placeholder="Enter product image" required class="box">

                    </div>

                    <button type="submit" name="add" class="btn">add product</button>
                </form>

            </div>
        </section>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
       <?php include 'components/alert.php';
       ?>
        
    </body>

</html>