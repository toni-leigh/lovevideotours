<?php
    /*
     checks the user table for the existence of the unique user identifier - this can be either the email address or the username
    */
    function check_user_name($unique_user_identifier)
    {
        $user_string="select * from User where userName='".$unique_user_identifier."' or email='".$unique_user_identifier."' or facebookEmail='".$unique_user_identifier."'";
        $user_query=site_query($user_string,"engage_functions.php check_user_name()",$dev);
        if (mysql_num_rows($user_query)>0) {return mysql_fetch_array($user_query);}
        else {return "! Login Error !";}
    }
    /*
     checks that the password matches the unique user identifier
    */
    function check_password($unique_user_identifier,$password)
    {
        $dev=0;
        $password_string="select * from User where (userName='".$unique_user_identifier."' or email='".$unique_user_identifier."' or facebookEmail='".$unique_user_identifier."') and password='".$password."'";
        $password_query=site_query($password_string,"login_check.php check_password()",$dev);
        if (mysql_num_rows($password_query)>0)
        {
            //return a successfully found user
            return mysql_fetch_array($password_query);
        }
        else
        {
            //password is wrong, check for FB id and if one is present remind user to use facebook login
            $check_facebook_ID=site_query("select * from User where (userName='".$unique_user_identifier."' or email='".$unique_user_identifier."' or facebookEmail='".$unique_user_identifier."')","login_check.php - check for facebook id in check_password()",$dev);
            $facebook_result=mysql_fetch_array($check_facebook_ID);
            if (strlen($facebook_result["facebookID"])>0)
                return "We have found that you have logged in with your facebook account, please use the facebook login button";
            else
                return "! Login Error !";
        }
    }
    function validate_engage_email($email)
    {
        if (validate_email_format($email))
        {
            $email_check=site_query("select * from User where email='".$email."' or facebookEmail='".$email."'","validate_engage_email()",$dev);
            if (mysql_num_rows($email_check))
            {
                $user=mysql_fetch_array($email_check);
                if (strlen($user["facebookID"])>0)
                    return "We have found that you have logged in with your facebook account, please use the facebook login button";
                else
                    return "The email address is already taken";
            }
            else
                return "Passed";
        }
        else
            return "The email address format was incorrect";
    }
    /*
     generate new user name
    */
    function generate_username($email)
    {
        $email_parts=explode("@",$email);
        $user_name=ereg_replace('[^a-zA_Z0-9]', '', $email_parts[0]);
        $check_username_string=site_query("select * from User where userName like '".$user_name."%'","generate_username()",$dev);
        if (mysql_num_rows($check_username_string)) {$user_name_append="_".random_string(8);}
        $check_username_string=site_query("select * from Page where URL like '".$user_name."%'","generate_username()",$dev);
        if (mysql_num_rows($check_username_string)) {$user_name_append="_".random_string(8);}
        return $user_name.$user_name_append;
    }
    function register_new_user($_POST)
    {
        $user_name=generate_username($_POST["email"]);
        $more_tasty=hash("sha256",$user_name.time());
        $password_hash=make_password($_POST["password"],$more_tasty);
        $insert_user_string="insert into User (userName,userTypeID,email,password,newsletterSignUp,extraStuff) values ('".$user_name."',1,'".$_POST["email"]."','".$password_hash."','".$_POST["newsletter"]."','".$more_tasty."')";
        site_query($insert_user_string,"register_new_user() insert user",$dev);
        $user_ID=mysql_insert_id();
        $insert_customer_string="insert into Customer (userID) values (".$user_ID.")";
        site_query($insert_customer_string,"register_new_user() insert customer",$dev);   
        //send out welcome email
        $new_registree_query=site_query("select * from User, Customer where User.userID=".$user_ID." and User.userID=Customer.userID","register_new_user() - get new registree",$dev);
        return mysql_fetch_array($new_registree_query);
    }
    function engage_form($message,$engage_type,$none_js=0)
    {         
        $engage_form_html="<div id='".$engage_type."_form'>";
        $engage_form_html=$engage_form_html."<form method='post' action='' name='engage_form'>";
        $engage_form_html=$engage_form_html."<div onclick='hide_engage()'><div id='engage_close' class='right'><img class='right' src='/img/remove20.png' alt='remove engage form'/></div></div>";
        if ($none_js)
            $engage_form_html=$engage_form_html."<input type='hidden' name='page_form' value='".$engage_type."'/>";            
        if (strlen($message)>0)
        {
            $engage_form_html=$engage_form_html."<div id='engage_form_error_message' class='form_error_message'>";
            $engage_form_html=$engage_form_html.$message;
            $engage_form_html=$engage_form_html."</div>";
        }
        else
        {
            $engage_form_html=$engage_form_html."<div id='engage_form_error_message' class='form_error_message'>";
            $engage_form_html=$engage_form_html."&nbsp;";
            $engage_form_html=$engage_form_html."</div>";
        }
        $engage_form_html=$engage_form_html."<div id='engage_hidden_field'>";
        $engage_form_html=$engage_form_html."<input type='hidden' name='engage_type' value='".strtolower($engage_type)."'/>";
        $engage_form_html=$engage_form_html."</div>";
        $engage_form_html=$engage_form_html."<div id='login_email_label' class='engage_form_item left'>Email Address:";
        $engage_form_html=$engage_form_html."</div>";
        $engage_form_html=$engage_form_html."<div id='engage_email_form_field' class='engage_form_item left'>";
        $engage_form_html=$engage_form_html."<input class='text_field' type='text' name='email'/>";
        $engage_form_html=$engage_form_html."</div>";
        $engage_form_html=$engage_form_html."<div id='login_password_label' class='engage_form_item left'>Password:";
        $engage_form_html=$engage_form_html."</div>";
        $engage_form_html=$engage_form_html."<div id='engage_password_form_field' class='engage_form_item left form_field'>";
        $engage_form_html=$engage_form_html."<input class='text_field' type='password' name='password'/>";
        $engage_form_html=$engage_form_html."</div>";
        
        $engage_form_html=$engage_form_html."<div id='engage_submit_button' class='engage_form_item'>";
        $engage_form_html=$engage_form_html."<input id='".$engage_type."_submit' class='submit_button button' name='".$engage_type."_submit' type='submit' value='".$engage_type."'>";
        $engage_form_html=$engage_form_html."</div>";
        if ($engage_type=="login")
            $engage_form_html.="<div id='new_password_link' class='engage_form_item'><a href='/new-password'>lost password?</a></div>";
        $engage_form_html=$engage_form_html."</form>";
        $engage_form_html=$engage_form_html."</div>";
        return $engage_form_html;
    }
?>