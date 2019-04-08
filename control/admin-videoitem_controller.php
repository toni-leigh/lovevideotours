<?php
    //save the specific item details here (VideoItem table)
    //item will be reset by the outcome of $_POST processing
    if (isset($_POST["itemID"]))
    {
        //then specific to video item
        if (!is_numeric($_POST["sleeps"])) {$errors["sleeps"]["not_numeric"]=1;$success=0;}
        if (!is_numeric($_POST["minPrice"])) {$errors["minPrice"]["not_numeric"]=1;$success=0;}
        if (!is_numeric($_POST["maxPrice"])) {$errors["maxPrice"]["not_numeric"]=1;$success=0;}
        if (check_unique_string($_POST["videoSRC"],"VideoItem","videoSRC","itemID",$_POST["itemID"])&&strlen($_POST["videoSRC"])) {$errors["videoSRC"]["not_unique"]=1;$success=0;}
        //save if successful, else set $item to post and reload form with errors
        if ($success)
        {
            $dev=0;
            //then save the video item details
            if (is_numeric($_POST["itemID"]))
            {
                //update SQL
                $update_string="update VideoItem set longitude=".$_POST["longitude"].",";
                $update_string=$update_string."latitude=".$_POST["latitude"].",";
                $update_string=$update_string."nearestTown='".$_POST["nearestTown"]."',";
                $update_string=$update_string."county='".$_POST["county"]."',";
                $update_string=$update_string."sleeps=".$_POST["sleeps"].",";
                $update_string=$update_string."minPrice=".$_POST["minPrice"].",";
                $update_string=$update_string."maxPrice=".$_POST["maxPrice"].",";
                $update_string=$update_string."videoSRC='".$_POST["videoSRC"]."' ";
                $update_string=$update_string."where itemID=".$item_ID;
                site_query($update_string,"update VideoItem in admin_videoitem_controller",$dev);
                //save item specific details here
            }
            else
            {
                //insert SQL
                $insert_string="insert into VideoItem (";
                $insert_string=$insert_string."itemID,";
                $insert_string=$insert_string."longitude,";
                $insert_string=$insert_string."latitude,";
                $insert_string=$insert_string."nearestTown,";
                $insert_string=$insert_string."county,";
                $insert_string=$insert_string."sleeps,";
                $insert_string=$insert_string."minPrice,";
                $insert_string=$insert_string."maxPrice,";
                $insert_string=$insert_string."videoSRC";
                $insert_string=$insert_string.")";
                $insert_string=$insert_string." values ";
                $insert_string=$insert_string."(";
                $insert_string=$insert_string.$item_ID.",";
                $insert_string=$insert_string.$_POST["longitude"].",";
                $insert_string=$insert_string.$_POST["latitude"].",";
                $insert_string=$insert_string."'".$_POST["nearestTown"]."',";
                $insert_string=$insert_string."'".$_POST["county"]."',";
                $insert_string=$insert_string.$_POST["sleeps"].",";
                $insert_string=$insert_string.$_POST["minPrice"].",";
                $insert_string=$insert_string.$_POST["maxPrice"].",";
                $insert_string=$insert_string."'".$_POST["videoSRC"]."'";
                $insert_string=$insert_string.")";
                site_query($insert_string,"insert VideoItem in admin_videoitem_controller",$dev);
                //create the video item record too
            }
            //save the item features
            $features=get_features(null,1);
            //this extra complication with a removed value is so we can turn off delete privelege on the DB thus reducing the chance of DB attacks
            while ($feature=mysql_fetch_array($features))
            {
                //is the feature selected, and catch any stray 0 ID's
                if ($_POST[$feature["featureID"]."feature"]=="on")
                {
                    $exists_query=site_query("select * from VideoItemFeature where itemID=".$item_ID." and featureID=".$feature["featureID"],"check fetaure in admin video item controller");
                    if (mysql_num_rows($exists_query)) site_query("update VideoItemFeature set removed=0 where itemID=".$item_ID." and featureID=".$feature["featureID"],"set feature to not removed in admin video item controller");
                    else site_query("insert into VideoItemFeature (itemID,featureID) values (".$item_ID.",".$feature["featureID"].")","record feature in admin video item controller");
                }
                else
                    site_query("update VideoItemFeature set removed=1 where itemID=".$item_ID." and featureID=".$feature["featureID"],"set feature to removed in admin video item controller");
            }
        }
        else
        {
            $item=$_POST;
        }
    }
?>