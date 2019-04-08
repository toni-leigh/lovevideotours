<?php
    session_start();
    include "../../../lovev/nrth.php";
    include '../dev_functions.php';
    include '../global_functions.php';
    include '../image_functions.php';
    include '../item_functions.php';
    include '../videoitem_display_functions.php';
    include '../videoitem_functions.php';
    
    $chosen_pairs=explode("&",$_REQUEST["checks"]);
    unset($_SESSION["sparams"]);
    foreach ($chosen_pairs as $pair)
    {
        $vals=explode("=",$pair);
        $sparams["cats"][]=$vals[0];
    }    
    // set the $_SESSION to hold the search values
    $_SESSION["sparams"]=$sparams;
    
    $items=get_video_items($sparams);
    // dev_dump(mysql_num_rows($items));
    echo json_encode(item_list($items));