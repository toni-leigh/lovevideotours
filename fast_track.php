<?php
    include "function/global_functions.php";
    include "function/initialise.php";
    include "function/basket_functions.php";
    include "function/dev_functions.php";
    include "function/order_functions.php";
    include "function/voucher_functions.php";
    $totals=get_basket_total();
    $last_order_query=site_query("select * from UserOrder where basketID='".$_SESSION["basket_ID"]."' order by orderID desc limit 1","index.php - last order query",$dev);
    $last_order=mysql_fetch_array($last_order_query);
    $last_order["voucherID"]=$_SESSION["voucher_ID"];
    $last_order["voucher_ID"]=$_SESSION["voucher_ID"];
    $order_id=create_order($last_order);
    initialise_sagepayment($last_order,$order_id,$host,$sage_pay_vendor_name,$site_production_status,$sage_pay_connect_string);
?>