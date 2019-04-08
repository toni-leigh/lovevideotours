<?php
    if (is_array($item)||$_GET["admin_divert"]=="create")
    {
        echo "<a href='/control-room/events/edit'>Back to Event List</a>";
        if ($_SESSION["user"]["userID"]!=$item["userID"]&&$_GET["admin_divert"]!="create"&&!super_admin())
        {
            echo "This is not your item to edit";
        }
        else
        {
            echo "<div id='centre_admin_form'>";
            build_events_form($item,$item_type,$errors);
            echo "</div>";
        }
    }
    else
    {
        echo "<a href='/control-room/events/create'>Create New Event</a>";
        //list the items here for editing
        while ($item=mysql_fetch_array($items))
            events_row($item);
        echo "<div id='page_bottom_padding'>&nbsp;";
        echo "</div>";
    }
?>