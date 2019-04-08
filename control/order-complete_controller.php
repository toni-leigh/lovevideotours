<?php
    if (!is_numeric($_GET["code"]))
    {
        //successful order, hence the code being alphanumeric hash
        $latest_order_query=site_query("select * from UserOrder where orderLinkKey='".$_GET["code"]."' and orderLinkKey!=''","order_complete.php - get latest order",$dev);
        $latest_order=mysql_fetch_array($latest_order_query);        
        record_action(array("e_ID"=>$latest_order["orderID"],"e_type"=>"order","a_type"=>"bought"));
    }
?>