<?php
    include "../../../lovev/nrth.php";
    include '../dev_functions.php';
    include '../global_functions.php';
    include '../order_functions.php';
    $current_state=$_REQUEST["current_state"];
    $html=get_billing_states($current_state);
    echo json_encode($html);
?>