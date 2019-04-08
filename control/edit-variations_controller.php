<?php
    if (isset($_POST["vtype_sub"]))
    {
        $variation_type_check=site_query("select * from VariationType where variationTypeName='".$_POST["variation_type"]."' and userID=".$_SESSION["user"]["userID"],"check for variation type - edit-variations_controller.php");
        if (mysql_num_rows($variation_type_check)==0)
            site_query("insert into VariationType (variationTypeName,userID) values ('".$_POST["variation_type"]."',".$_SESSION["user"]["userID"].")","add variation type - edit-variations_controller.php");
    }
    $variation_types=get_variation_types();
?>