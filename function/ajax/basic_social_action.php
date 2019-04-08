<?php
    session_start();
    include "../../../lovev/nrth.php";
    include '../global_functions.php';
    include '../action_functions.php';
    include '../dev_functions.php';
    include '../js_functions.php';
    
    $entity_ID=$_REQUEST["entityID"];
    $entity_type=$_REQUEST["entityType"];
    $entity_sub_type=$_REQUEST["entitySubType"];
    $action_type=$_REQUEST["actionType"];
    include "../record_basic_social_action.php";
    $html=social_action_span(array("e_ID"=>$entity_ID,"e_type"=>$entity_type,"e_stype"=>$entity_sub_type,"a_type"=>$action_type,"button_name"=>$button_name));
    echo stripslashes(json_encode($html));