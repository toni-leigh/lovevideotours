<?php
    session_start();
    include "../../../lovev/nrth.php";
    include '../dev_functions.php';
    include '../global_functions.php';
    include '../variation_functions.php';
    
    $vtype_ID=$_REQUEST["vtype_ID"];
    $vvalue_ID=$_REQUEST["vvalue_ID"];
    $vvalue=$_REQUEST["vvalue"];
    
    $variation_check=site_query("select * from VariationValue where variationValue='".$vvalue."' and variationTypeID=".$vtype_ID." and removed=0","check for vvalue already defined");
    if (mysql_num_rows($variation_check)<1)
        if (is_numeric($vvalue_ID))
            site_query("update VariationValue set variationValue='".$vvalue."' where variationValueID=".$vvalue_ID,"update variation value save_variation_value.php");
        else
            site_query("insert into VariationValue (variationTypeID,variationValue) values (".$vtype_ID.",'".$vvalue."')","insert variation value save_variation_value.php");
        
    echo json_encode(vtype_values($vtype_ID));