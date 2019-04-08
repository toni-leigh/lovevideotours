<?php
    session_start();
    include "../../../lovev/nrth.php";
    include '../dev_functions.php';
    include '../global_functions.php';
    include '../variation_functions.php';
    
    $vtype_ID=$_REQUEST["vtype_ID"];
    $vvalue_ID=$_REQUEST["vvalue_ID"];
    
    site_query("update VariationValue set removed=1 where variationValueID=".$vvalue_ID,"update variation value, set removed remove_variation_value.php");
        
    echo json_encode(vtype_values($vtype_ID));