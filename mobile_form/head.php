<?php
    echo    "<head>";
    echo dump_include(array("level"=>1,"include_name"=>"head.php"));
    //stop robots from test server
    if ($_SERVER["HTTP_HOST"]=="template.excitedstatelaboratory.com")
        echo "<meta name='ROBOTS' content='noindex, nofollow'>";
    ?>
        <script type="text/javascript" src="/form/jquery.min.js"></script>
        <script type="text/javascript" src="/form/jquery-ui.min.js"></script>
    <?php
    if ($page["videoPage"])
    {
        echo "<script src='/form/video.js' type='text/javascript' charset='utf-8'></script>";
        echo "<link rel='stylesheet' href='/form/video-js.css' type='text/css' media='screen' title='Video JS' charset='utf-8'>";
    }
    include "form/map_load.php";
    if ($_GET["element_reference"]=="image-upload")
    {
        ?>
            <script type="text/javascript" src="/form/jquery.imgareaselect.pack.js"></script>
        <?php
    }
    if (!isset($_COOKIE["device"]))
    {
        ?>
            <script type="text/javascript">
                //only do any js if it is enabled
                if (window.focus)
                {
                    var screenWidth=$(window).width();
                    if (screenWidth<480)
                        document.cookie="device=mobile_; expires=Fri, 5 Aug 2050 20:47:11 UTC";
                    else
                        document.cookie="device=; expires=Fri, 5 Aug 2050 20:47:11 UTC";
                }
            </script>         
        <?php
    }
    //facebook open graph like stuff
    if ($page["URL"]=="order-complete"&&!is_numeric($_GET["code"]))
    {
        echo "<meta property='og:title' content='I Just Placed an Order with ".$site_name."'/>";
        echo '<meta property="og:type" content="food"/>';
        echo '<meta property="og:url" content="http://'.$host.'/order-complete/'.$_GET["code"].'"/>';
        echo '<meta property="og:image" content="http://'.$host.'/img/style/logo.jpg"/>';
        echo '<meta property="og:site_name" content="'.$site_name.'"/>';
        echo '<meta property="og:description" content="I just bought some great products from '.$site_name.'"/>';
        echo '<meta property="fb:admins" content="'.$facebook_admin_ID.'"/>';
    }
    if (is_array($item)&&!isset($_GET["admin_divert"]))
    {
        //open graph for item here
        echo "<meta property='og:title' content='".$item["itemName"]." on ".$site_name."'/>";
        echo '<meta property="og:type" content="hotel"/>';
        echo '<meta property="og:url" content="http://'.$host.'/'.build_item_link($item).'"/>';
        $item_image=mysql_fetch_array(get_images("item",$item["itemID"]));
        echo '<meta property="og:image" content="http://'.$host.$item_image["thumbnailPath"].'"/>';
        echo '<meta property="og:site_name" content="'.$site_name.'"/>';
        echo '<meta property="og:description" content="'.$item["itemTweet"].'"/>';
        echo '<meta property="fb:admins" content="'.$facebook_admin_ID.'"/>';
    }
    if (isset($items))
    {
        //open graph for category here
    }
    echo "<link rel='stylesheet' type='text/css' href='/".$device."form/style.css'/>";
    /* modernizr for HTML5 elements */
    echo "<script type='text/javascript' src='/form/modernizr.js'></script>";
    echo "<link rel='icon' type='image/png' href='/img/favicon.png' />";
    //google analytics
    echo "<script type='text/javascript'>var _gaq = _gaq || [];_gaq.push(['_setAccount', '".$analytics_ID."']);_gaq.push(['_trackPageview']);(function() {var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);})();</script>";
    //e-commerce tracking here - as it is output and must go in the head
    if (isset($latest_order["viewed"])&&$latest_order["viewed"]==0)
    {
        echo "<script type='text/javascript'>";
        echo    "_gaq.push(['_addTrans','".$latest_order["orderID"]."','".$site_name."','".$latest_order["orderTotal"]."','','".$latest_order["postageTotal"]."','".$latest_order["billing_city"]."','','".$latest_order["billing_country_code"]."']);";
        //add item '_','orderID','productVariationCode','productName','variationstring','unit price','quanityt ordered'
        $order_products=get_basket($latest_order["orderID"]);
        while ($order_product=mysql_fetch_array($order_products))
        {
            echo    "_gaq.push(['_addItem','".$latest_order["orderID"]."','".$order_product["variationID"]."','".str_replace("'","",$order_product["itemName"])."','".$product_details_string."','".$order_product["price"]."','".$order_product["quantity"]."']);";
        }
        echo    "_gaq.push(['_trackTrans']);";
        echo "</script>";
        //set order to viewed, then subsequent views of the order will not be tracked
        site_query("update UserOrder set viewed=1 where orderID=".$latest_order["orderID"],"order-complete_controller.php - set order summary page viewed, for tracking",$dev);
    }
    echo    "</head>";
?>