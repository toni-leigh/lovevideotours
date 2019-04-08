<?php
    session_start();
    include "../../../lovev/nrth.php";
    include '../dev_functions.php';
    include '../global_functions.php';
    include '../variation_functions.php';
    
    $inputs=explode("&",$_REQUEST["inputs"]);
    //dev_dump($inputs);
    $pvtypes=get_product_variation_types("variationTypeID");    
    $selections=array();
    foreach ($inputs as $input)
    {
        $values=explode("=",$input);
        $pvtype_ID=str_replace("s","",str_replace("value","",$values[0]));
        if (in_array($pvtype_ID,$pvtypes)&&$values[1]!="all")
            $selections[$pvtype_ID][]=str_replace("+"," ",$values[1]); 
    }
    //dev_dump($selections);
    echo json_encode(variation_preview(array("selections"=>$selections)));