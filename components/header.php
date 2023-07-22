<header class="header">
    <div class="flex">
        <a href="" class="logo"> Enchanteé</a>
        <nav class="navbar">
            <a href="add_product.php">add products</a>
            <a href="view_products.php">view products</a>
            <a href="order.php">my orders</a>
            
        </nav>
        <div>
            <?php
            $count_cart_items=$conn->prepare("SELECT * FROM cart WHERE user_id=? ");
            $count_cart_items->execute([$user_id]);
            $total_cart_items=$count_cart_items->rowCount();
            ?>
        </div>
   

        <div>
            <a href="cart.php" class="cart-btn"><i class="bi bi-cart" style="font-size: 2.5rem; color: var(--main-color);"></i><sup><?=$total_cart_items;?></sup></a>
            <div class="menu-btn" class="bi bi-list"> </div>
       
</a>


        </div>
           
    </div>


</header>