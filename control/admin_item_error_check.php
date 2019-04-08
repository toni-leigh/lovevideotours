<?php
    $success=1;
    if ($_POST["itemName"]=="") {$errors["itemName"]=1;$success=0;}
    if ($_POST["itemTweet"]==""&&$_POST["itemTypeID"]!=2) {$errors["itemTweet"]=1;$success=0;}
    if ($_POST["itemHTML"]=="") {$errors["itemHTML"]=1;$success=0;}  
    $item_URL_append=rework($_POST["itemName"]);
    if (check_unique_string($_POST["itemName"],"Item","itemName","itemID",$_POST["itemID"])&&$_POST["itemTypeID"]!=2) {$errors["itemName"]["taken"]=1;$success=0;}
?>