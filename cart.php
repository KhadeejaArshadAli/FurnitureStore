<?php
include 'components/connection.php';

if (isset($_COOKIE['user_id'])) {
    $user_id = $_COOKIE['user_id'];
} else {
    setcookie('user_id', unique_id(), time() + 60 * 60 * 24 * 30);
}
//update product quantity in cart
if(isset($_POST['update_cart'])){
    $cart_id =$_POST['cart_id'];
    $cart_id =filter_var($cart_id,FILTER_SANITIZE_STRING);
    $qty = $_POST['qty'];
    $qty=filter_var($qty, FILTER_SANITIZE_STRING);
    $update_qty= $conn->prepare('UPDATE cart SET qty=? WHERE id=? ');
    $update_qty->execute([$qty, $cart_id]);

    $success_msg[]='cart quantity updates';

}
//remove item from cart
if (isset($_POST['delete_item'])) {
    $cart_id=$_POST['cart_id'];
    $cart_id=filter_var($cart_id ,FILTER_SANITIZE_STRING);

    $varify_delete_item =$conn->prepare("SELECT *FROM cart WHERE id=?");
    $varify_delete_item->execute([$cart_id]);

    if($varify_delete_item->rowCount()>0){
        $delete_cart_id = $conn->prepare("DELETE FROM cart WHERE id =? ");
        $delete_cart_id->execute([$cart_id]);
        $success_msg[] ='cart item deleted successfully!';
    }
    else{
        $warning_msg[]='cart item already deleted';
    }
}

//empty cart
if(isset($_POST['empty_cart'])){
    $varify_empty_item =$conn->prepare("SELECT *FROM cart WHERE user_id=?");
    $varify_empty_item->execute([$user_id]);
    if($varify_empty_item->rowCount()>0){
        $delete_cart_id = $conn->prepare("DELETE FROM cart WHERE user_id =? ");
        $delete_cart_id->execute([$user_id]);
        $success_msg[] ='cart empty!';
    }
    else{
        $warning_msg[]='cart item already empty';
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
            <h1 class="heading">shopping cart</h1>
            <div class="box-container">
                <?php
                $grand_total = 0;
                $select_cart= $conn->prepare("SELECT *FROM cart WHERE user_id=? ");
                $select_cart->execute([$user_id]);
                if($select_cart->rowCount()>0){
                    while ($fetch_cart =$select_cart->fetch(PDO::FETCH_ASSOC)) {
                        $select_products =$conn->prepare("SELECT * FROM products WHERE id= ?" );
                        $select_products->execute([$fetch_cart['product_id']]);
                        if($select_products->rowCount()>0){
                            $fetch_product=$select_products->fetch(PDO::FETCH_ASSOC);
                           
                ?>
                <form method="post" class="box">
                    <input type="hidden" name="cart_id" value="<?=$fetch_cart['id'];?>">
                    <img src="images/<?=$fetch_product['image'];?>"class="img">
                    <h3 class="name"><?=$fetch_product['name'];?>"</h3>
                    <div class="flex">
                        <p class="price">Price Rs<?=$fetch_product['price'];?>/-</p>
                        <input type="number" name="qty" required min="1" value="<?=$fetch_cart['qty'];?>" max="99" maxlength="2" class="qty" >
                        <button type="submit" name="update_cart" class="bi-pencil-square fa-edit"></button>
                    </div>
                    <p class="sub-total">sub total: <span>Rs<?=$sub_total = ($fetch_cart['qty']*$fetch_cart['price'])?></span> </p>
                   
                    <button type="submit" name="delete_item" class="btn" onclick="return confirm('delete this item');">delete</button>
                </form>
                <?php
                      $grand_total+=$sub_total;
                  }
                  else{
                    echo '<p class="empty">product was not found</p>';
                  }

                }

                
            } else{
                echo '<p class="empty">your cart is empty</p>';
            }
           
                ?>
               
            </div> 
            <?php
            if($grand_total!=0){

            
            ?>
            <div class="cart-total">
                <p>total amount payable : Rs <span><?=$grand_total;?></span>/-</p>
                <div class="button">
                    <form method="post">
                        <button type="submit" name="empty_cart" class="btn" onclick="return confirm('are you sure to empty your cart');">empty cart</button>

                    </form>
                    <a href="checkout.php" class="btn">proceed to checkout</a>
                </div>
            </div>

            <?php
            }
            ?>

        </section>
        
        <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
       


      
    
       
       <?php include 'components/alert.php';
       ?>
        
    </body>

</html>