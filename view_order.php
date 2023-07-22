

<?php




include 'components/connection.php';



if (isset($_COOKIE['user_id'])) {
    $user_id = $_COOKIE['user_id'];
} else {
    setcookie('user_id', unique_id(), time() + 60 * 60 * 24 * 30);
}
if(isset($_GET['get_id'])){
    $get_id = $_GET['get_id'];
}else{
    $get_id='';
    header('location order.php');
}
if (isset($_POST['cancel'])) {
   $updated_orders= $conn->prepare("UPDATE  orders SET status=? WHERE id=?");
   $updated_orders->execute(['canceled',$get_id]);
   header('location:order.php');
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
        <section class="order-detail">
            <h1 class="heading">order details</h1>
            <div class="box-container">
                <?php
                $grand_total=0;
               $select_orders=$conn->prepare('SELECT * FROM orders WHERE id=? LIMIT 1');
               $select_orders->execute([$get_id]);
                // $select_orders=$conn->prepare("SELECT * FROM orders WHERE user_id=? LIMIT 1");
                // $select_orders->execute([$user_id]);
                if($select_orders->rowCount()>0){
                    while ($fetch_orders = $select_orders->fetch(PDO::FETCH_ASSOC)) {
                        $select_product= $conn->prepare("SELECT * FROM products WHERE id=? LIMIT 1");
                        $select_product->execute([$fetch_orders['product_id']]);
                        if($select_product->rowCount()>0){
                            while ($fetch_product = $select_product->fetch(PDO::FETCH_ASSOC)){
                                $sub_total=($fetch_orders['price'] * $fetch_orders['qty']);
                                $grand_total+=$sub_total;
                ?>
                <div class="box">
                    <div class="col">
                        <p class="title"><i class="bi bi-calendar-fill"></i><?=$fetch_orders['date'];?></p>
                        <img src="images/<?=$fetch_product['image'];?>" class="img">
                        <p class="price">Rs<?=$fetch_orders['price'];?>x<?=$fetch_orders['qty'];?></p>
                        <h3 class="name"><?=$fetch_product['name'];?></h3>
                        <p class="grand-total">Total amount payable: <span>Rs<?=$grand_total; ?>/-</span></p>
                    </div>
                    <div class="col">
                        <p class="title">billing address</p>
                        <p class="user"><i class="bi bi-person-bounding-box"></i><?=$fetch_orders['name'];?></p>
                        <p class="user"><i class="bi bi-phone"></i><?=$fetch_orders['number'];?></p>
                        <p class="user"><i class="bi bi-envelope"></i><?=$fetch_orders['email'];?></p>
                        <p class="user"><i class="bi bi-pin-map-fill"></i><?=$fetch_orders['address'];?></p>
                        <p class="title">status</p>
                        <p class="status"style="color: <?php if ($fetch_orders['status']=='delivered'){
                        echo 'green';
                    }elseif($fetch_orders['status']=='canceled'){
                        echo'red';
                    }
                    else{
                        echo 'orange';
                    }
                    ?>"><?=$fetch_orders['status'];?></p>
                    <?php if ($fetch_orders['status']=='canceled') {?>
                        <a href="checkout.php?get_id=<?=$fetch_product['id'];?>"class="btn">order again</a>

                    <?php }else {?>
                        <form action="" method="post">
                            <button type="submit" name="cancel" class="btn " onclick="return confirm('cancel this order?');">
                        cancel order</button>
                        </form>
                        <?php }?>
                   
                    </div>
                </div>
                <?php
                        }
                    }else{
                        echo '<p class="empty"> products not found!</p>';
                    }
                  
                }
            }else{
                echo '<p class="empty">no order found!</p>';
            }
                ?>
            </div>
           
        </section>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
       <?php include 'components/alert.php';
       ?>
        
    </body>

</html>