<?php
    $recommend_query=site_query("select * from Action where entityID=".$entity_ID." and entityType='".$entity_type."' and actionType='".$action_type."' and actioningUserID=".$_SESSION["user"]["userID"],"build_social_action_button() - check for activity",$dev);
    //check for the state of recommend, has this signed in user already recommended this entity? if so they may wish to unrecommend it
    if (mysql_num_rows($recommend_query))
    {
        $recommend_check=mysql_fetch_array($recommend_query);
        if ($recommend_check["removed"]==1)
        {
            site_query("update Action set removed=0 where actionID=".$recommend_check["actionID"],"set action off - record_basic_social_action.php");
            $button_name=$action_type;
        }
        else
        {
            site_query("update Action set removed=1 where actionID=".$recommend_check["actionID"],"set action on - record_basic_social_action.php");
            $button_name="un-".$action_type;
        }
    }
    else
    {
        record_action(array("e_ID"=>$entity_ID,"e_type"=>$entity_type,"e_stype"=>$entity_sub_type,"a_type"=>$action_type));
        $button_name=$action_type;
    }
?>