<?php
    session_start();
    include "../../../lovev/nrth.php";
    include '../dev_functions.php';
    include '../global_functions.php';
    include '../postage_functions.php';
    
    $threshold=$_REQUEST["threshold"];
    
    site_query("update Supplier set postageThreshold=".$threshold." where userID=".$_SESSION["user"]["userID"],"set postage threshold set_threshold.php");
    $_SESSION["user"]["postageThreshold"]=$threshold;
    echo json_encode(format_price($threshold));