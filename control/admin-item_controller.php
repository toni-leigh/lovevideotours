<?php
    echo dump_include(array("level"=>"Cn1","include_name"=>"admin-item_controller.php"));
    
    // get items / item
    if ($_GET["admin_divert"]=="edit"&&!isset($_GET["item_ID"]))
        if ($_GET["element_reference"]=="product")
            $items=get_items(array("i_type"=>$_GET["element_reference"],"order_by"=>"itemName","ignore_variations"=>1));
        else
            $items=get_items(array("i_type"=>$_GET["element_reference"],"order_by"=>"itemName"));
    elseif (($_GET["admin_divert"]=="edit"||$_GET["admin_divert"]=="variations")&&isset($_GET["item_ID"]))
        $item=get_item(array("i_type"=>$_GET["element_reference"],"i_ID"=>$_GET["item_ID"]));
        
    // include the variation controller if this is a variation page
    if ($_GET["admin_divert"]=="variations")
        include "control/admin-".$_GET["element_reference"]."-variations-controller.php";
        
    // save on post
    if (isset($_POST["itemID"]))
    {
        // error check - first generic then specific
        include "control/admin-item_error_check.php";
        include "control/admin-".$_GET["element_reference"]."_error_check.php";
        // save if successful, else set $item to post and reload form with errors
        if ($success)
        {
            // save the basic item details and get item ID
            $item_ID=save_item($_POST,$item_URL_append,$item_type["itemTypeID"]);
            
            // then save the specific item details
            include "admin-".$_GET["element_reference"]."_controller.php";
            
            // finally initialise the item for showing on page refresh
            $item=get_item(array("i_type"=>$item_type,"i_ID"=>$item_ID));
        }
        else
        {
            $item=$_POST;
        }
    }
?>