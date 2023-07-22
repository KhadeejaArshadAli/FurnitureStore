<?php
include 'components/connection.php';

if (isset($_COOKIE['user_id'])) {
    $user_id = $_COOKIE['user_id'];
} else {
    setcookie('user_id', unique_id(), time() + 60 * 60 * 24 * 30);
}
//adding product in cart
if(isset($_POST['add_to_cart'])){
    $id=unique_id();
    $product_id=$_POST['product_id'];
    $product_id=filter_var($product_id,FILTER_SANITIZE_STRING);
    $qty=$_POST['qty'];
    $qty=filter_var($qty,FILTER_SANITIZE_STRING);

    $varify_cart=$conn->prepare("SELECT * FROM cart WHERE user_id =? AND product_id=? ");
    $varify_cart->execute([$user_id, $product_id]);

    $max_cart_items=$conn->prepare("SELECT *FROM cart WHERE user_id =?");
    $max_cart_items->execute([$user_id]);
    if($varify_cart->rowCount()>0){
        $warning_msg[]=" Product already added to cart";
    }
    elseif($max_cart_items->rowCount()>=20){
        $warning_msg[]=" cart is full";

    }
    else{
        $select_price=$conn->prepare("SELECT * FROM products WHERE id=? LIMIT 1");
        $select_price->execute([$product_id]);
        $fetch_price=$select_price->fetch(PDO::FETCH_ASSOC);

        $insert_cart = $conn->prepare("INSERT INTO cart (id, user_id, product_id, price, qty) VALUES(?,?,?,?,?)");
        $insert_cart->execute([$id, $user_id,$product_id,$fetch_price['price'],$qty]);
        $success_msg[]='Product added to cart!';
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
        <section class="products">
            <h1 class="heading">all products</h1>
            <div class="box-container">
                <?php
                $select_products = $conn->prepare("SELECT * FROM products");

                $select_products->execute();
                if($select_products->rowCount()>0){
                    while ($fetch_products= $select_products->fetch(PDO::FETCH_ASSOC)) {
                        
                 
                ?>
                <form action="" method="post" class="box">
                    <img src="images/<?=$fetch_products['image'];?>"class="img">
                    <h3 class="name"><?=$fetch_products['name'];?></h3>
                    <input type="hidden" name="product_id" value="<?=$fetch_products['id'];?>">
                    <div class="flex">
                        <p class="price">price Rs<?=$fetch_products['price'];?>/-</p>
                        <input type="number" name="qty" required min="1" value="1" max="99" maxlength="2" class="qty" >
                    </div>
                    <div class="button">
                        <button type="submit" name="add_to_cart" class="btn">add to cart</button>
                        <a href="checkout.php?get_id=<?=$fetch_products['id'];?>"class="btn">buy now</a>
                    </div>
                </form>
                <?php
                    }
                }else{
                    echo '<p class="empty">no products added yet!</p>';
                }
                ?>
            </div> 
            

        </section>
        
        <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
       


      
    
       
       <?php include 'components/alert.php';
       ?>
        
    </body>

</html>