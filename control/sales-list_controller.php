<?php
    if ($_GET["order_by"]=="") $order_by="stName"; else $order_by=$_GET["order_by"];
    if ($_GET["order_by"]=="") $dir="asc"; else $dir=$_GET["dir"];
    $sales_list=site_query("select * from SalesTarget, Category where SalesTarget.stCatID=Category.categoryID order by ".$order_by." ".$dir,"get sales list entries");
?>