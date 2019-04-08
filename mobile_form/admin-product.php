<?php
    if (is_array($item)||$_GET["admin_divert"]=="create")
    {
        echo "<a href='/product/edit'>Back to Product List</a>";
        if ($_SESSION["user"]["userID"]!=$item["userID"]&&$_GET["admin_divert"]!="create"&&!super_admin())
        {
            echo "This is not your item to edit";
        }
        else
        {
            echo "<div id='centre_admin_form'>";
            build_product_form($item,$item_type,$errors);
            echo "</div>";
        }
    }
    else
    {
        //create item link
        echo "<a href='/product/create'>Create New Product</a>";
        //list the items here for editing
        while ($item=mysql_fetch_array($items))
        {
            echo "<a href='/product/edit/".$item["itemID"]."'>".$item["itemName"]."</a>&nbsp;&nbsp;-&nbsp;&nbsp;<a href='/image-upload/product/".$item["itemID"]."'>IMAGES</a><br/>";
        }
    }
?>