<?php
include 'components/connection.php';

if (isset($_COOKIE['user_id'])) {
    $user_id = $_COOKIE['user_id'];
} else {
    setcookie('user_id', unique_id(), time() + 60 * 60 * 24 * 30);
}
if(isset($_POST['place_order'])){
 
    $name = $_POST['name'];
    $name=filter_var($name,FILTER_SANITIZE_STRING);
    $number = $_POST['number'];
    $number=filter_var($number,FILTER_SANITIZE_STRING);
    $email = $_POST['email'];
    $email=filter_var($email,FILTER_SANITIZE_STRING);
    $address = $_POST['flat'].','.$_POST['street']. ','.$_POST['city']. ',' .$_POST['country']. ',' .$_POST['pin'];
    $address=filter_var($address,FILTER_SANITIZE_STRING);
    $address_type = $_POST['address_type'];
    $address_type=filter_var($address_type,FILTER_SANITIZE_STRING);
    $method = $_POST['method'];
    $method=filter_var($method,FILTER_SANITIZE_STRING);

    $varify_cart =$conn->prepare("SELECT * FROM cart WHERE user_id=?");
    $varify_cart->execute([$user_id]);
    
    if(isset($_GET['get_id'])){
        $get_product = $conn->prepare("SELECT *FROM  products WHERE id=? LIMIT 1" );
        $get_product->execute($_GET['get_id']);
        if($get_product->rowCount()>0){
            while ($fetch_p = $get_product->fetch(PDO::FETCH_ASSOC)) {
                $insert_order = $conn->prepare("INSERT INTO orders(id,user_id,name,number,email,address,
                address_type,method,product_id,price,qty)
                VALUES(?,?,?,?,?,?,?,?,?,?,?)");
                $insert_order->execute([unique_id(), $user_id,$name,$number,$email,$address,$address_type,
                $method,$fetch_p['id'],$fetch_p['price'],1]);
                header('location:order.php');
            }
        }else{
            $warning_msg[]='something went wrong';
        }
    }elseif($varify_cart->rowCount()>0){
        while($f_cart=$varify_cart->fetch(PDO::FETCH_ASSOC)){
            $insert_order = $conn->prepare("INSERT INTO orders(id,user_id,name,number,email,address,
            address_type,method,product_id,price,qty)
            VALUES(?,?,?,?,?,?,?,?,?,?,?)");
            $insert_order->execute([unique_id(), $user_id,$name,$number,$email,$address,$address_type,
            $method,$f_cart['product_id'],$f_cart['price'],$f_cart['qty']]);
            header('location:order.php');

        }
        if($insert_order){
            $delete_cart_id = $conn->prepare("DELETE FROM cart WHERE user_id=?");
            $delete_cart_id->execute([$user_id]);
            header('location:order.php');
        }
    }else{
        $warning_msg[]='something went wrong';

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
        <section class="checkout">
            <h1 class="heading">checkout summary</h1>
            <div class="row">
                <form method="post">
                    <h3>billing details</h3>
                    <div class="flex">
                        <div class="box">
                        <div class="input-field">
                        <p>your name <span>*</span></p>
                        <input type="text" name="name" required maxlength="50" placeholder="Enter your name" class="input"  >
                    </div>
                 
                        <div class="input-field">
                        <p>Your number <span>*</span></p>
                        <input type="text" name="number" required maxlength="11" placeholder="Enter your number" class="input">
                        </div>



                    <!-- </div> -->
                    <div class="input-field">
                        <p>your email <span>*</span></p>
                        <input type="email" name="email" required maxlength="50" placeholder="Enter your email" class="input"  >
                    </div>
                    <div class="input-field" >
                        <p>payment method<span>*</span></p>
                        <select name="method" class="input">
                            <option value="cash on delivery">cash on delivery</option>
                            <option value="credit or debit card">credit or debit card</option>
                            <option value="net banking">net banking</option>
                            <option value="NayaPay or SadaPay">NayaPay or SadaPay</option>
                        </select>

                    </div>
                    <div class="input-field" >
                        <p>address type<span>*</span></p>
                        <select name="address_type" class="input">
                            <option value="home">home</option>
                            <option value="office">office</option>
                           
                        </select>

                    </div>
                        </div>
                        <div class="box">
                        <div class="input-field">
                        <p>address line 01 <span>*</span></p>
                        <input type="text" name="flat" required maxlength="50" placeholder="e.g flat & building number" class="input"  >
                    </div>
                    <div class="input-field">
                        <p>address line 02 <span>*</span></p>
                        <input type="text" name="street" required maxlength="50" placeholder="e.g street name" class="input"  >
                    </div>
                    <div class="input-field">
                        <p>city name <span>*</span></p>
                        <input type="text" name="city" required maxlength="50" placeholder="Enter your city name" class="input"  >
                    </div>
                    <div class="input-field">
                        <p>country name <span>*</span></p>
                        <input type="text" name="country" required maxlength="50" placeholder="Enter your country name" class="input"  >
                    </div>
                    <div class="input-field">
                        <p>pincode <span>*</span></p>
                        <input type="number" name="pin" required maxlength="6" placeholder="e.g.123456" min="0" max="999999" class="input"  >
                    </div>

                        </div>
                    </div>
                <button type="submit" name="place_order" class="btn">place order</button>
                   
                </form>
                <div class="summary">
                    <h3 class="title">cart item</h3>
                    <div class="box-container">
                    <?php
                      $grand_total = 0;
                     if(isset($_GET['get_id'])){
                        $select_get = $conn->prepare("SELECT * FROM products WHERE id=?");
                        $select_get->execute([$_GET['get_id']]);
                        while($fetch_get=$select_get->fetch(PDO::FETCH_ASSOC)){
                        $sub_total =  $fetch_get['price'];
                        $grand_total+=$sub_total;
                         
                     
                    

                       
                    ?>
                        <div class="flex">
                            <img src="images/<?=$fetch_get['image'];?>" class="image" >
                            <div>
                            <h3 class="name"><?=$fetch_get['name'];?></h3>
                        <p class="price"><?=$fetch_get['price'];?>/-</p>
                            </div>
                        </div>
                       

                        <?php
                            }
                        }else{
                            $select_cart=$conn->prepare("SELECT * FROM cart WHERE user_id=?");
                            $select_cart->execute([$user_id]);
                            if($select_cart->rowCount()>0){
                                while($fetch_cart=$select_cart->fetch(PDO::FETCH_ASSOC)){
                                   $select_products=$conn->prepare("SELECT * FROM products WHERE id=? "); 
                                   $select_products->execute([$fetch_cart['product_id']]);
                                   $fetch_product = $select_products->fetch(PDO::FETCH_ASSOC);
                                   $sub_total = ($fetch_cart['qty']* $fetch_product['price']);

                                   $grand_total+=$sub_total;

                         

                        ?>
                        <div class="flex">
                        <img src="images/<?=$fetch_product['image'];?>" class="img" >
                            <div>
                            <h3 class="name"><?=$fetch_product['name'];?></h3>
                        <p class="price"><?=$fetch_product['price'];?>/- x <?=$fetch_cart['qty']; ?></p>
                            </div>

                        </div>
                        <?php
                                  
                                }
                            }else{
                                echo '<p class="empty"> your cart is empty</p>';
                            }
                        }
                        ?>
                    </div>
                    <div class="grand-total"><span>total amount payable :</span>Rs<?=$grand_total; ?></div>
                </div> 
            </div>
             
            
        </section>
        
        <script src="https://unpkg.com/sweetalert/dist/sweetalert.min.js"></script>
       


      
    
       
       <?php include 'components/alert.php';
       ?>
        
    </body>

</html>