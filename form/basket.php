<?php
    $dev=0;
    echo dump_include(array("level"=>2,"include_name"=>"basket.php"));
    $basket=get_basket();
    if (mysql_num_rows($basket)==0)
    {
        echo "Your basket is empty";
    }
    else
    {
        $order_total=0.0;
        $postage_total=0.0;
        if (isset($_SESSION["bag_notify"]))
        {
            echo "You have been redirected to your bag because you have just signed in while checking out with new items and you had some items already bagged in your account from your last visit. Please check the products so you only order what you want to order.";
            unset ($_SESSION["bag_notify"]);
        }
        echo "<form method='post' action='/basket'>";
        echo    "<div id='basket_products_panel' class='left'>";
        echo       "<input type='hidden' name='update_basket'/>";
        while ($basket_product=mysql_fetch_array($basket))
            echo basket_product($basket_product);
        echo       "<span class='bag_submit_button'><input id='bag_change_submit' class='submit_button button' type='submit' name='submit' value='save bag changes'/></span>";
        echo    "</div>";
        echo    "<div id='basket_summary' class='right'>";
        //automtically set the value if voucher code submitted, leave the voucher id in the field
        $totals=get_basket_total(array("v_ID"=>$_POST["voucher_ID"]));
        echo       "<span id='basket_total' class='left'>".format_price($totals["order"])."</span>";
        echo       "<span id='voucher_message' class='left'>no coupon applied yet</span>";  
        echo       "<span id='gift_voucher_header' class='left'>enter coupon code</span>";
        echo       "<input id='voucher_field' class='text_field' type='text' name='voucher_ID' maxlength='10' value='".$_POST["voucher_ID"]."'/>";
        echo       "<span class='button' onclick='checkVoucherCode(\"".$totals["order"]."\",\"".($totals["order"]-$totals["postage"])."\",\"".$totals["postage"]."\")'>redeem</span>";     
        echo       "<input id='checkout_submit' class='checkout_path_button left' type='submit' name='submit' value='checkout'/>";
        echo    "</div>";
        //only appears if straight to payment is an option
        //user must be signed in & full order address must be present (i.e. they must placed at least one order with the site)
        //retrieve last order for addresses
        $last_order_query=site_query("select * from UserOrder where basketID='".$_SESSION["basket_ID"]."' order by orderID desc limit 1","order_address.php - last order query",$dev);
        //order total must be above 0, no voucher possible
        if (mysql_num_rows($last_order_query)>0&&$totals["order"]>0)
        {
            echo "Straight to payment with this asddress?:";
            dev_dump(mysql_fetch_array($last_order_query),"last order output",1);
            echo "<input id='straight_to_payment_submit' class='checkout_path_button left' type='submit' name='submit' value='straight to payment'/>";
        }
        echo build_straight_to_payment_button("form");
        echo "</form>";
    }      
?>
