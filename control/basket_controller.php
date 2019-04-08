<?php
    //dev_dump($_POST);
    if (isset($_POST["update_basket"]))
    {
        //update quantities / remove
        $basket=get_basket();
        while ($basket_product=mysql_fetch_array($basket))
        {
            if (!is_numeric($_POST[$basket_product["basketItemID"]])||$_POST[$basket_product["basketItemID"]]<0)
            {
                $error=1;
            }
            else
            {
                if (isset($_POST[$basket_product["basketItemID"]."remove"])||$_POST[$basket_product["basketItemID"]]==0)
                {
                    $remove_item=site_query("update Basket set removed=1 where basketItemID=".$basket_product["basketItemID"],"bag.php - set bag product to removed",$dev);
                }
                site_query("update Basket set quantity=".$_POST[$basket_product["basketItemID"]]." where basketItemID=".$basket_product["basketItemID"],"bag.php - update bag quantities in bag",$dev);
            }
        }
        $_SESSION["voucher_ID"]=$_POST["voucher_ID"];
        if ($_POST["submit"]=="straight to payment")
        {
            header("Location:/fast-track");
        }
        if ($_POST["submit"]=="checkout"&&!isset($error))
        {
            header("Location:/address");
        }
    }
?>