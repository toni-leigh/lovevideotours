<?php
    /*
     records an action - includes follows and recommends - both are action types
     $in["e_ID"], ["e_type"], ["e_stype"] = entity values on which the action is performed, ID, type and sub-type
     $in["a_type"] = the type of action being performed
     $in["removed"] = a removed value to be used to set the action record - defaults to 0 in all cases unless it is explicitly set to 1
    */
    function record_action($in)
    {
        if ($in["removed"]!=1) $in["removed"]=0;
        if (is_numeric($_SESSION["user"]["userID"])) {$user_ID=$_SESSION["user"]["userID"];}
        else {$user_ID=0;}
        site_query("insert into Action (actioningUserID,entityID,entityType,entitySubType,actionType,removed) values (".$user_ID.",".$in["e_ID"].",'".$in["e_type"]."','".$in["e_stype"]."','".$in["a_type"]."',".$in["removed"].")","record_action()");
    }
    /*
     function displays either follow or recommend button
     $in["e_ID"], ["e_type"], ["e_stype"] = entity values on which the action is performed, ID, type and sub-type
     $in["a_type"] = the type of action being performed
    */
    function build_social_action_button($in)
    {  
        $rq=site_query("select * from Action where entityID=".$in["e_ID"]." and entityType='".$in["e_type"]."' and actionType='".$in["a_type"]."' and actioningUserID=".$_SESSION["user"]["userID"],"build_social_action_button() - check for activity",$dev);
        //check for the state of recommend, has this signed in user already recommended this entity? if so they may wish to unrecommend it
        if (mysql_num_rows($rq))
        {
            $rch=mysql_fetch_array($rq);
            if ($rch["removed"]==1)
                $in["button_name"]=$in["a_type"];
            else
                $in["button_name"]="un-".$in["a_type"];
        }
        else
            $in["button_name"]=$in["a_type"];  
        $b="<span id='social_button_panel'>";
        $b.="<form method='post' action=''>";
        $b.="<input type='hidden' name='follow_recommend' value=''>";
        $b.="<input type='hidden' name='entity_ID' value='".$in["e_ID"]."'>";
        $b.="<input type='hidden' name='entity_type' value='".$in["e_type"]."'>";
        $b.="<input type='hidden' name='entity_sub_type' value='".$in["e_stype"]."'>";
        $b.="<input type='hidden' name='action_type' value='".$in["a_type"]."'>";
        $b.="<input id='".$in["a_type"]."_submit' class='submit_button button' type='submit' name='".$in["a_type"]."_submit' value='".$in["button_name"]."'/>";
        $b.="</form>";
        $b.="</span>";
        $b.=open_script();    
        $b.="document.getElementById('social_button_panel').innerHTML='".social_action_span($in)."';";
        $b.=close_script();
        return $b;
    }
    /*
     builds a social action button span containing on click info etc.
     $in["e_ID"], ["e_type"], ["e_stype"] = entity values on which the action is performed, ID, type and sub-type
     $in["a_type"] = the type of action being performed
     $in["button_name"] = the value to printed on the button
    */
    function social_action_span($in)
    {
        return "<span class=\"button\" onclick=\"recordBasicSocialAction(".$in["e_ID"].",\'".$in["e_type"]."\',\'".$in["e_stype"]."\',\'".$in["a_type"]."\')\">".$in["button_name"]."</span>";
    }
    /*
     function gets all actions
     $in["u_ID"] = an array of user IDs to be included in the query
     $in["action_types"] = an array of action types to be included
     is performed as an intersection, only records which contain both one of user IDs and one of the action types will be returned
    */
    function get_actions($in)
    {
        $as="select * from Action, User where Action.actioningUserID=User.userID ";
        if (is_array($in["u_ID"]))
        {
            $as.="and actioningUserID in (";
            foreach ($in["u_ID"] as $u_ID)
                $as.=$u_ID.",";
            $as=substr($as,0,-1).") ";
        }
        if (is_array($in["action_types"]))
        {
            $as.="and actionType in (";
            foreach ($in["action_types"] as $a_type)
                $as.="'".$a_type."',";
            $as=substr($as,0,-1).") ";
        }
        $as.="order by actionTime";
        return site_query($as,"get_actions()",$dev);        
    }
    function get_item_recommendations($user_ID)
    {
        $dev=0;
        $item_recommendation_string="select * from Action, Category, Item where Action.entityID=Item.itemID and entityType='item' and actionType='recommend' and actioningUserID=".$user_ID." and Action.removed=0 and Item.categoryID=Category.categoryID ";
        $item_recommendation_query=site_query($item_recommendation_string,"get_item_recommendations()",$dev);
        return $item_recommendation_query;
    }
?>