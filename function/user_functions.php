<?php
    /*
     returns User details
    */
    function get_user($reference)
    {
        $dev=0;
        if (is_numeric($reference))
        {
            $basic_user_query=site_query("select * from User where userID=".$reference,"get_user() - numeric reference",$dev);
        }
        else
        {
            $basic_user_query=site_query("select * from User where userName='".$reference."'","get_user() - non numeric reference",$dev);
        }
        if (mysql_num_rows($basic_user_query)>0)
        {
            $basic_user_details=mysql_fetch_array($basic_user_query);
            $user_type_string="select * from UserType where userTypeID=".$basic_user_details["userTypeID"];
            $user_type_query=site_query($user_type_string,"get user type details",$dev);
            $user_type=mysql_fetch_array($user_type_query);
            $user_type_table=ucfirst($basic_user_details["userType"]);
            $full_user_query=site_query("select * from User, UserType, ".$user_type["userTable"]." where User.userID=".$basic_user_details["userID"]." and User.userID=".$user_type["userTable"].".userID and User.userTypeID=UserType.userTypeID","get_user() - full user query",$dev);
            return mysql_fetch_array($full_user_query);
        }
        else
        {
            return 0;
        }
    }
    function get_user_actions($user)
    {
        return get_actions(array("u_ID"=>array(0=>$user["userID"])));
    }
    function username($user)
    {
        if ($user["publicUser"]==1)
            return "<a href='/".$user["userURL"]."'>".$user["displayName"]."</a>";
        else
            return $user["displayName"];
    }
    /*
     used to find out whether a given signed in user, or the anonymous user, is allowed to view the content of $page
    */
    function check_authorised($page)
    {
        if (super_admin())
            return 1;
        else
            if ($_SESSION["user"]["userType"]=="customer")
                if ($page["customerUser"])
                    return 1;
                else
                    return 0;
            elseif ($_SESSION["user"]["userType"]=="supplier")
                if ($page["supplierUser"])
                    return 1;
                else
                    return 0;
            else
                if ($page["anonymousUser"]==1)
                    return 1;
                else
                    return 0;
    }
    function set_user_signed_in($user,$fb,$type)
    {
        if (!$fb)
            session_regenerate_id();
        if ($type=="register")
            record_action(array("e_ID"=>$user["userID"],"e_type"=>"user","e_stype"=>"customer","a_type"=>"register")); 
        $_SESSION["user"]=get_user($user["userID"]);
        //update basket
        $orders=site_query("select * from UserOrder where basketID='".$_SESSION["basket_ID"]."'","set_user_signed_in() - get orders");
        while ($order=mysql_fetch_array($orders))
        {
            if ($order["primary_email"]==$_SESSION["user"]["email"]||$order["primary_email"]==$_SESSION["user"]["facebookEmail"])
            {
                site_query("update UserOrder set basketID='".$_SESSION["user"]["userID"]."' where orderID=".$order["orderID"],"set_user_signed_in() - order update");
                site_query("update Basket set basketID='".$_SESSION["user"]["userID"]."' where orderID=".$order["orderID"],"set_user_signed_in() - basket update");
            }
        }
        //update unordered basket items
        site_query("update Basket set basketID='".$_SESSION["user"]["userID"]."' where basketID='".$_SESSION["basket_ID"]."'","update unordered basket items - set_user_signed_in()");
        $_SESSION["basket_ID"]=$_SESSION["user"]["userID"];
        //log
        site_query("insert into AccessLog (userID,host,facebookLogin,logType) values (".$_SESSION["user"]["userID"].",'".$_SERVER["REMOTE_ADDR"]."',".$fb.",'".$type."')","set_user_signed_in() - record access",$dev);
    }
?>