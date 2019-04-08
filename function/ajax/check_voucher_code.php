<?php
    session_start();
    include "../../../lovev/nrth.php";
    include '../dev_functions.php';
    include '../global_functions.php';
    include '../basket_functions.php';
    include '../voucher_functions.php';
    
    //get product details
    $voucher_code=$_REQUEST["voucher_code"];
    $order_total=$_REQUEST["order_total"];
    $product_total=$_REQUEST["product_total"];
    $postage_total=$_REQUEST["postage_total"];
    $voucher=get_code_details($voucher_code);
    $ajax_order_total="&pound;".number_format($order_total,2);
    $ajax_product_total="Products: &pound;".number_format($product_total,2);
    $ajax_postage_total="Postage: &pound;".number_format($postage_total,2);
    if ($voucher=="voucher_fail")
        $ajax_message="<span class='voucher_fail_message'>please check you have entered the code correctly</span>";
    else
        include '../voucher_process.php';
    $html[0]=$ajax_order_total;
    $html[1]=$ajax_product_total;
    $html[2]=$ajax_postage_total;
    $html[3]=$ajax_message;
    echo json_encode($html);