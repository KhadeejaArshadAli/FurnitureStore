
<?php
include 'components/connection.php';

if (isset($_COOKIE['user_id'])) {
    $user_id = $_COOKIE['user_id'];
} else {
    setcookie('user_id', unique_id(), time() + 60 * 60 * 24 * 30);
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
        <section class="orders">
            <h1 class="heading">my orders</h1>
            <div class="box-container">
                <?php
                $select_orders=$conn->prepare("SELECT * FROM orders WHERE user_id=? ORDER BY date DESC");
                $select_orders->execute([$user_id]);
                if ($select_orders->rowCount()>0) {
                    while ($fetch_orders = $select_orders->fetch(PDO::FETCH_ASSOC)) {
                        $select_products = $conn->prepare("SELECT * FROM products WHERE id=? ");
                        $select_products->execute([$fetch_orders['product_id']]);
                        if ($select_products->rowCount()>0) {
                           while ($fetch_product=$select_products->fetch(PDO::FETCH_ASSOC)) {

               
                ?>
                <div class="box"><?php if ($fetch_orders['status']=='cancel') {
                    echo 'style="border:2px solid red";';}?>>
                     <a href="view_order.php?get_id=<?=$fetch_orders['id']?>">
                    <p class="date"><i class="bi bi-calendar"></i><span><?=$fetch_orders['date']?></span></p>
                    <img src="images/<?=$fetch_product['image']?>" class="img">
                    <h3 class="name"><?=$fetch_product['name']?>"</h3>
                    <p class="price">Price : Rs<?=$fetch_orders['price']?> x<?=$fetch_orders['qty']?></p>
                    <p class="status" style="color: <?php if ($fetch_orders['status']=='delivered'){
                        echo 'green';
                    }elseif($fetch_orders['status']=='canceled'){
                        echo'red';
                    }
                    else{
                        echo 'orange';
                    }
                    ?>"><?=$fetch_orders['status'];?></p>
                  
                
                    </a>

                </div>
             <?php
                          }
                            
                           
                        }
                       
                    }
                }else{
                    echo '<p class="empty">no products added yet!</p>';
                }
             
             ?>
            </div>
         
        </section>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
       <?php include 'components/alert.php';
       ?>
        
    </body>

</html>