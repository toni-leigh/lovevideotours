<?php
    echo "<span class='file-structure-header-dev-level2'>2:user.php</span>";
    if ($user_display)
    {
        $dev=0;
        echo "<h1>".$user["displayName"]."</h1>";
        if ($_SESSION["user"]&&$user["userID"]!=$_SESSION["user"]["userID"])
        {
            echo "<span id='social_button_panel'>";
            echo build_social_action_button($user["userID"],"user",$user["userType"],"follow",0);
            echo "</span>";
        }
        dev_dump($user,"User on the user display page user.php",$dev);
        echo "Recommendations";
        while ($product_recommendation=mysql_fetch_array($product_recommendations))
        {
            $item=get_item($product_recommendation["entityID"],$product_recommendation["entitySubType"]);
            echo "<a href='".build_item_link($item)."'>".$item["itemName"]."</a>";            
        }
        /*dev_dump_query($product_recommendations,"Users product recommendations on their page",$dev);*/
        dev_dump_query($actions,"Users actions on their page",$dev);
    }
    else
    {
        echo "This user cannot be found";
    }
?>
<script type='text/javascript'>
    <?php
        if ($_SESSION["user"]&&$user["userDisplay"])
        {
            echo "var social_action_button=\"".build_social_action_button($user["userID"],"user",$user["userType"],"follow",1)."\";";
        }
    ?>    
</script>