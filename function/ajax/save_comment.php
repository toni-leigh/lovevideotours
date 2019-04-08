<?php
    session_start();
    include "../../../lovev/nrth.php";
    include '../dev_functions.php';
    include '../global_functions.php';
    include '../action_functions.php';
    include '../comment_functions.php';
    include '../user_functions.php';
    
    $entity_ID=$_REQUEST["entityID"];
    $entity_type=$_REQUEST["entityType"];
    $entity_sub_type=$_REQUEST["entitySubType"];
    $latest_comment_content=$_REQUEST["latestCommentsContent"];
    $comment_to_save=$_REQUEST["commentToSave"];
    $new_comment=save_comment(array("e_ID"=>$entity_ID,"e_type"=>$entity_type,"comment"=>$comment_to_save));    
    record_action(array("e_ID"=>$entity_ID,"e_type"=>$entity_type,"e_stype"=>$entity_sub_type,"a_type"=>"commented"));
    $comments=get_comments(array("e_ID"=>$entity_ID,"e_type"=>$entity_type));
    $comment_count=mysql_num_rows($comments);
    $html=utf8_encode(comment_panel(array("counter"=>$comment_count,"comment"=>$new_comment)).$latest_comment_content);
    echo stripslashes(json_encode($html));
    