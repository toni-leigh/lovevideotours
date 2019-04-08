<?php
    echo dump_include(array("level"=>"Cn2","include_name"=>"admin-item_error_check.php"));
    $success=1;
    if ($_POST["itemName"]=="") {$errors["itemName"]=1;$success=0;}
    if ($_POST["itemTweet"]=="") {$errors["itemTweet"]=1;$success=0;}
    if ($_POST["itemHTML"]=="") {$errors["itemHTML"]=1;$success=0;}  
    if (check_length($_POST["itemTweet"],140)) {$errors["itemTweet"]["field_length"]=140;$success=0;}
    $item_URL_append=rework($_POST["itemName"]);
    if (is_numeric($_POST["itemID"])) $item_ID=$_POST["itemID"];
    else $item_ID=0;
    $item_URL_append_check_string="select * from Item where itemTypeID=".$item_type["itemTypeID"]." and itemUrlAppend='".$item_URL_append."' and itemID!=".$item_ID;
    $item_URL_append_check_query=site_query($item_URL_append_check_string,"check URL append string for uniqueness",$dev);
    if (mysql_num_rows($item_URL_append_check_query)>0) {$errors["itemName"]["taken"]=1;$success=0;}
?>