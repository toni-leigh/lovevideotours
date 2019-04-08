<?php
    echo "<span class='file-structure-header-dev-level2'>2:order-complete.php</span>";
    echo "<div id='order-response-panel'>";
    if (is_numeric($_GET["code"]))
    {
        if ($_GET["code"]==2)
        {
            echo "The transaction was not authorised - the card entry attempts failed, please try another card";
        }
        elseif ($_GET["code"]==3)
        {
            echo "The transaction was aborted";
        }
        elseif ($_GET["code"]==4)
        {
            echo "The transcation verification was rejected";
        }
        elseif ($_GET["code"]==5)
        {
            echo "An error occured, please try again";
        }
    }
    else
    {
        echo "Thank you, Your Payment was successful.<br/><br/>";
        if (isset($latest_order))
        {
            echo "<div id='order-completed-product-list'>";
            echo "<script src='http://connect.facebook.net/en_US/all.js#xfbml=1'></script>";
            echo "<fb:like href='http://".$host."/order-complete/".$latest_order["orderLinkKey"]."' show_faces='false' width='450'></fb:like>";
            //twitter button
            echo "<div id='twitter-button'>";
            echo    "<a href='http://twitter.com/share?text=".urlencode("I just bought some great products from ".$site_name)."'><img src='/img/style_images/live/button-product-twitter.png' alt='tweet'/></a>";
            echo "</div>";
            $x=0;
            $order_products=get_basket($latest_order["orderID"]);
            $item_count=mysql_num_rows($order_products);
            $type_reference="product";
            while ($order_product=mysql_fetch_array($order_products))
            {
                dev_dump($order_product,"Order Complete Product List",1);
            }
            echo "</div>";
            echo "<div id='view-orders-link'><a href='/account/orders/new'>View My Orders</a></div>";
        }
    }
    echo "</div>";
?>