<?php

    $db_name = 'mysql:host=localhost:3307;dbname=shop_db';
    $db_user = 'root';
    $db_password = 'Ilikemymom---246';

    try {
        $conn = new PDO($db_name, $db_user, $db_password);
        // Set PDO error mode to exception
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
    } catch (PDOException $e) {
        echo "Connection failed: " . $e->getMessage();
    }

    function unique_id() {
        $chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charLength = strlen($chars);
        $randomString = '';
        for ($i = 0; $i < 20; $i++) {
            $randomString.= $chars[mt_rand(0, $charLength - 1)];
        }
        return $randomString;
    }
?>
