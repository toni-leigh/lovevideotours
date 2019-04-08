<?php
    session_start();
    include "../../../lovev/nrth.php";
    include '../dev_functions.php';
    include '../global_functions.php';
    include '../variation_functions.php';
    
    $master_price=$_REQUEST["price_value"];
    site_query("update Product set masterPrice=".$master_price." where itemID=".$_SESSION["product"]["itemID"],"update master price in set_master_price.php");
    $_SESSION["product"]["masterPrice"]=$master_price;
    echo json_encode(master_variation_form());
