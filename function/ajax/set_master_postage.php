<?php
    session_start();
    include "../../../lovev/nrth.php";
    include '../dev_functions.php';
    include '../global_functions.php';
    include '../variation_functions.php';
    
    $master_postage=$_REQUEST["postage_value"];
    site_query("update Product set masterPostage=".$master_postage." where itemID=".$_SESSION["product"]["itemID"],"update master postage in set_master_postage.php");
    $_SESSION["product"]["masterPostage"]=$master_postage;
    echo json_encode(master_variation_form());
