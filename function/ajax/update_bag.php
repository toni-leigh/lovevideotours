<?php
    session_start();
    include "../../../lovev/nrth.php";
    include '../dev_functions.php';
    include '../global_functions.php';
    include '../basket_functions.php';
    
    //get product details
    $product_ID=$_REQUEST["item_ID"];
    $variation_ID=$_REQUEST["variation_ID"];
    $quantity=$_REQUEST["quantity"];
    $user_ID=$_REQUEST["user_ID"];    
    bag_product(array("i_ID"=>$product_ID,"v_ID"=>$variation_ID,"q"=>$quantity,"u_ID"=>$user_ID));
    $html=utf8_encode(build_html_basket());
    echo json_encode($html);
