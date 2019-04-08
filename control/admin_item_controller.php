<?php
    //save the basic item details here (Item table)
    //get item if ID present in URL
    if (is_numeric($_GET["item_ID"]))
    {
        $item=get_item($_GET["item_ID"],$item_type["itemTypeID"]);
    }
    else
    {
        if ($_GET["admin_divert"]=="edit")
        {
            if (super_admin())
                $items=get_items($item_type,""," itemName asc ","",0,0);
            else
                $items=get_items($item_type,"Item.userID=".$_SESSION["user"]["userID"]," itemName asc ","",0,0);
        }
    }
?>