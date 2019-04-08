<?php
    echo "Hi <strong>".$_SESSION["user"]["displayName"]."</strong>, welcome to the Love Video Tours control room";
    if (super_admin())
    {
        //super admin dashboard panels
        echo "<div id='dashboard_row'>";
        echo    "<div class='dashboard_panel'>";
        echo        "<span class='control_room_link'><a href='/control-room/videoitem/edit'>Accommodation</a></span>";
        echo    "</div>";
        echo    "<div class='dashboard_spacer'>&nbsp;</div>";
        echo    "<div class='dashboard_panel'>";
        echo        "<span class='control_room_link'><a href='/control-room/user/edit'>Users</a></span>";
        echo    "</div>";
        echo    "<div class='dashboard_spacer'>&nbsp;</div>";
        echo    "<div class='dashboard_panel'>";
        echo        "<span class='control_room_link'><a href='/control-room/extra-map-items'>Extra Map Items</a></span>";
        echo    "</div>";
        echo    "<div class='dashboard_spacer'>&nbsp;</div>";
        echo    "<div class='dashboard_panel'>";
        echo    "</div>";
        echo "</div>";
    }
    else
    {
        //user dashboard panels
        echo "<div id='dashboard_row'>";
        echo    "<div class='dashboard_panel'>";
        echo        "<span class='control_room_link'><a href='/control-room/videoitem/edit'>My Property</a></span>";
        echo    "</div>";
        echo    "<div class='dashboard_spacer'>&nbsp;</div>";
        echo    "<div class='dashboard_panel'>";
        echo        "<span class='control_room_link'><a href='/control-room/user/edit/".$_SESSION["user"]["userID"]."'>My Details</a></span>";
        echo    "</div>";
        echo    "<div class='dashboard_spacer'>&nbsp;</div>";
        echo    "<div class='dashboard_panel'>";
        echo        "<span class='control_room_link'><a href='/control-room/recommendations'>My Recommendations</a></span>";
        echo    "</div>";
        echo    "<div class='dashboard_spacer'>&nbsp;</div>";
        echo    "<div class='dashboard_panel'>";
        echo        "<span class='control_room_link'><a href='/control-room/images/".$_SESSION["user"]["userID"]."'>My Images</a></span>";
        echo    "</div>";
        echo "</div>";
        echo "<div id='dashboard_row'>";
        echo    "<div class='dashboard_panel'>";
        echo        "<span class='control_room_link'><a href='/control-room/blog/edit'>My Updates</a></span>";
        echo    "</div>";
        echo    "<div class='dashboard_spacer'>&nbsp;</div>";
        echo    "<div class='dashboard_panel'>";
        echo    "</div>";
        echo    "<div class='dashboard_spacer'>&nbsp;</div>";
        echo    "<div class='dashboard_panel'>";
        echo    "</div>";
        echo    "<div class='dashboard_spacer'>&nbsp;</div>";
        echo    "<div class='dashboard_panel'>";
        echo    "</div>";
        echo "</div>";
    }
?>