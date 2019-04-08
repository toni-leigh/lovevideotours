<?php
    echo    "<head>";
    echo dump_include(array("level"=>1,"include_name"=>"head.php"));
    // title tag
    if (is_array($item))
    {
        if ($item["itemTypeID"]==3)
            if (strlen($item["videoSRC"]))
                echo        "<title>High quality video directory listing for ".$item["itemName"]."</title>";
            else
                echo        "<title>Detailed directory listing for ".$item["itemName"]."</title>";
        else
            if (strlen($item["videoSRC"]))
                echo        "<title>Love Video Tours Video blog about ".$item["itemName"]."</title>";
            else
                echo        "<title>Love Video Tours blog about ".$item["itemName"]."</title>";
    }
    elseif (is_array($category))
        if ($category["categoryName"]=="LVT Blog")
            echo        "<title>Love Video Tours Holiday Blog Listings</title>";
        else
            echo        "<title>".$category["categoryName"]." listings</title>";
    else
        echo        "<title>".$page["title"]."</title>";
        
    // description tag
    $meta_desc="Love Video Tours uses video, images and maps to show you holiday property and attractions in colourful detail";
    if (is_array($item))
    {
        if (strlen($item["videoSRC"]))
            echo "<meta name='description' content='".$item["itemName"]." - ".$item["itemTweet"]." - includes high quality video, map, image gallery'>";
        else
            echo "<meta name='description' content='".$item["itemName"]." - ".$item["itemTweet"]." - includes map, image gallery'>";
    }
    elseif (is_array($category))
        echo        "<meta name='description' content='find detailed directory listings for ".$category["categoryName"]."s in Northumberland and The Borders, including map'>";
    else
        echo        "<meta name='description' content='".$page["description"]."'>";
    // stop robots from test server
    if ($_SERVER["HTTP_HOST"]=="lovevideotours.excitedstatelaboratory.com") echo "<meta name='ROBOTS' content='noindex, nofollow'>";
    
    ?> <meta http-equiv="Content-type" content="text/html; charset=utf-8" /> <?php
    
    // include jquery and image upload - basic jquery used for all pages
    echo "<script type='text/javascript' src='/form/jquery.min.js'></script>";
    echo "<script type='text/javascript' src='/swfobject.js'></script>";    
    if ($page["jqueryui"]) echo "<script type='text/javascript' src='/form/jquery-ui.min.js'></script>";
    if ($_GET["element_reference"]=="image-upload") echo "<script type='text/javascript' src='/form/jquery.imgareaselect.pack.js'></script>";
    if ($item["itemTypeID"]==3)
    {
        // we need some extra jquery bits for video item display, including the draggable stuff for the slider.  
    }
    // map
    include "form/map_load.php";
    
    // set device
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
    
    // add this js
    if (is_array($item)||is_array($category)||$page["pageID"]==42)
        echo "<script type='text/javascript' src='http://s7.addthis.com/js/250/addthis_widget.js#pubid=ra-4e8f192361f3bb71'></script>";
        
    include "form/ie_fix.php"; // conditional comments and css for IE
    
    // fb og like // TEST
    $site_name='<meta property="og:site_name" content="Love Video Tours"/>';
    $admins='<meta property="fb:admins" content="'.$facebook_admin_ID.'"/>';
    /* if ($page["URL"]=="order-complete"&&!is_numeric($_GET["code"]))
    {
        echo "<meta property='og:title' content='I Just Placed an Order with Love Video Tours'/>";
        echo '<meta property="og:type" content="product"/>';
        echo '<meta property="og:url" content="http://'.$host.'/order-complete/'.$_GET["code"].'"/>';
        echo '<meta property="og:image" content="http://'.$host.'/img/style/logo.jpg"/>';
        echo $site_name;
        echo '<meta property="og:description" content="I just bought some great products from Love Video Tours"/>';
        echo $admins;
    } */
    if (is_array($item)&&!isset($_GET["admin_divert"]))
    {
        echo "<meta property='og:title' content='".$item["itemName"]." on Love Video Tours'/>";
        // either place to stay or thing to do
        if ($item["parentID"]==3) echo '<meta property="og:type" content="hotel"/>';
        else echo '<meta property="og:type" content="landmark"/>';        
        echo '<meta property="og:url" content="http://'.$host.'/'.build_item_link($item).'"/>';
        $item_image=mysql_fetch_array(get_images("item",$item["itemID"]));
        echo '<meta property="og:image" content="http://'.$host."/".$item_image["largeSquarePath"].'"/>';
        echo $site_name;
        echo '<meta property="og:description" content="'.$item["itemTweet"].'"/>';
        echo $admins;
    }
    if (is_numeric($lvt_blog_post["itemID"]))
    {
        echo "<meta property='og:title' content='".$lvt_blog_post["itemName"]." on Love Video Tours Blog'/>";
        echo '<meta property="og:type" content="blog"/>';
        echo '<meta property="og:url" content="http://'.$host.'/love-video-tours-blog/'.$lvt_blog_post["itemUrlAppend"].'"/>';
        $item_images=get_images("item",$lvt_blog_post["itemID"]);
        $item_image=mysql_fetch_array($item_images);
        echo '<meta property="og:image" content="http://'.$host."/".$item_image["largeSquarePath"].'"/>';
        echo $site_name;
        echo '<meta property="og:description" content="'.$lvt_blog_post["itemTweet"].'"/>';
        echo $admins;
    }
    if ($page["pageID"]==42)
    {
        echo "<meta property='og:title' content='Prices for listings on Love Video Tours'/>";
        echo '<meta property="og:type" content="product"/>';
        echo '<meta property="og:url" content="http://'.$host.'/list-with-us"/>';
        echo '<meta property="og:image" content="http://'.$host.'/img/fblogo.png"/>';
        echo $site_name;
        echo '<meta property="og:description" content="A full list of prices and special offers for listings of attractions and holiday accommodation on Love Video Tours, along with instructions for how to get a listing"/>';
        echo $admins;
    }
    if (isset($items))
    {
        // TODO - open graph for category here
    }
    echo "<link rel='stylesheet' type='text/css' href='/".$device."form/style.css'/>";
    if (is_array($_SESSION["user"])) echo "<link rel='stylesheet' type='text/css' href='/form/admin.css'/>";
    // modernizr for HTML5 elements
    echo "<script type='text/javascript' src='/form/modernizr.js'></script>";
    echo "<link rel='icon' type='image/png' href='/img/favicon.png' />";
    // g analytics
    if (!isset($_GET["admin_divert"])&&!is_array($_SESSION["user"])) 
        echo "<script type='text/javascript'>var _gaq = _gaq || [];_gaq.push(['_setAccount', '".$analytics_ID."']);_gaq.push(['_trackPageview']);(function() {var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);})();</script>";
    // g e-commerce tracking
    if (isset($latest_order["viewed"])&&$latest_order["viewed"]==0)
    {
        echo "<script type='text/javascript'>";
        echo    "_gaq.push(['_addTrans','".$latest_order["orderID"]."','".$site_name."','".$latest_order["orderTotal"]."','','".$latest_order["postageTotal"]."','".$latest_order["billing_city"]."','','".$latest_order["billing_country_code"]."']);";
        $order_products=get_basket($latest_order["orderID"]);
        while ($order_product=mysql_fetch_array($order_products))
        {
            echo    "_gaq.push(['_addItem','".$latest_order["orderID"]."','".$order_product["variationID"]."','".str_replace("'","",$order_product["itemName"])."','".$product_details_string."','".$order_product["price"]."','".$order_product["quantity"]."']);";
        }
        echo    "_gaq.push(['_trackTrans']);";
        echo "</script>";
        // set order to viewed, then subsequent views of the order will not be tracked
        site_query("update UserOrder set viewed=1 where orderID=".$latest_order["orderID"],"order-complete_controller.php - set order summary page viewed, for tracking",$dev);
    }
    echo    "</head>";
?>