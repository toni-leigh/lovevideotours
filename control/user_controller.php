<?php
    //gets user details and elements for user page
    $user=get_user($_GET["element_reference"]); //different from session user
    $entity_type="user";
    $entity_ID=$user["userID"];
    $user_display=calculate_display($user,"user");
    $user_type=$user["userType"];
    if ($user_display&&$user["userTypeID"]==2)
    {
        $actions=get_user_actions($user);
        //get recommendations
        //$product_recommendations=get_product_recommendations(array("u_ID"=>array("0"=>$user["userID"]),"action_types"=>array(0=>"recommend")));
    }
?>