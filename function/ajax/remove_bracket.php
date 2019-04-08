<?php
    session_start();
    include "../../../lovev/nrth.php";
    include '../dev_functions.php';
    include '../global_functions.php';
    include '../postage_functions.php';
    
    $charge_ID=$_REQUEST["charge_ID"];
    $calc_type=$_REQUEST["calc_type"];
    
    site_query("update PostageCharges set removed=1 where postageChargeID=".$charge_ID,"remove_bracket.php");
    
    // get the variation value reference ID
    
    //set that variation value to removed
    
    $brackets=get_postages(array("bracket"=>$calc_type,"user_ID"=>$_SESSION["user"]["userID"]));
    $new_html=utf8_encode(bracket_updater(array("brackets"=>$brackets,"calc_type"=>$calc_type)));
    echo json_encode($new_html);