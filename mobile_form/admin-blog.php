<?php
    if (is_array($item)||$_GET["admin_divert"]=="create")
    {
        echo "<a href='/blog/edit'>Back to Blog List</a>";
        if ($_SESSION["user"]["userID"]!=$item["userID"]&&$_GET["admin_divert"]!="create"&&!super_admin())
        {
            echo "This is not your item to edit";
        }
        else
        {
            echo "<div id='centre_admin_form'>";
            build_blog_form($item,$item_type,$errors);
            echo "</div>";
        }
    }
    else
    {
        echo "<a href='/blog/create'>Create New Blog Post</a>";
        //list the items here for editing
        while ($item=mysql_fetch_array($items))
        {
            echo "<a href='/blog/edit/".$item["itemID"]."'>".$item["itemName"]."</a>&nbsp;&nbsp;-&nbsp;&nbsp;<a href='/image-upload/blog/".$item["itemID"]."'>IMAGES</a><br/>";
        }
    }
?>