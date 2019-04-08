<?php
    session_start();
    include "../../../lovev/nrth.php";
    include '../dev_functions.php';
    include '../global_functions.php';
    include '../variation_functions.php';
    
    $vtype_ID=$_REQUEST["vtype_ID"];
    $new_setting=$_REQUEST["new_setting"];
    
    $c=site_query("select * from ProductVariationType where variationTypeID=".$vtype_ID." and productID=".$_SESSION["product"]["itemID"],"save_pvariations.php - check");
    if (mysql_num_rows($c)>0)
        site_query("update ProductVariationType set removed=".$new_setting." where variationTypeID=".$vtype_ID." and productID=".$_SESSION["product"]["itemID"],"save_pvariations.php - set");
    else
        site_query("insert into ProductVariationType (variationTypeID,productID) values (".$vtype_ID.",".$_SESSION["product"]["itemID"].")","save_pvariations.php - insert");
    
    $inputs=explode("&",$_REQUEST["inputs"]);
    $pvtypes=get_product_variation_types("variationTypeID");    
    $selections=array();
    foreach ($inputs as $input)
    {
        $values=explode("=",$input);
        $pvtype_ID=str_replace("s","",str_replace("value","",$values[0]));
        if (in_array($pvtype_ID,$pvtypes)&&$values[1]!="all")
            $selections[$pvtype_ID][]=str_replace("+"," ",$values[1]); 
    }
    echo json_encode(variation_preview(array("selections"=>$selections)));