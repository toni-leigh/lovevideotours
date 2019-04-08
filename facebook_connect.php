<?php
    $dev=0;
    include "function/global_functions.php";
    include "function/initialise.php";
    include "function/action_functions.php";
    include "function/dev_functions.php";
    include "function/engage_functions.php";
    include "function/user_functions.php";
    //facebook object instantiated
    require 'function/facebook.php';    
    // Create our Application instance (replace this with your appId and secret).
    $facebook = new Facebook(array(
      'appId' => $facebook_appid,
      'secret' => $facebook_secret,
      'cookie' => true,
    ));
    $session = $facebook->getSession();
    //dev_dump("facebook - session",$session,1);
    $me = null;
    // Session based API call.
    if ($session) {
        try {
            $uid = $facebook->getUser();
            $me = $facebook->api('/me'); 
        } catch (FacebookApiException $e) {
            error_log($e);
        }
    }
    
    dev_dump($facebook,"facebook_connect.php - facebook object",$dev);
    dev_dump($session,"facebook_connect.php - session object",$dev);
    dev_dump($me,"facebook_connect.php - me object",$dev);
    dev_dump($e,"facebook_connect.php - exception object",$dev);
    
    $facebook_ID=$me["id"];
    $facebook_email=$me["email"];
    $birthday=explode("/",$me["birthday"]);
    $birthdate=$birthday[2]."-".$birthday[1]."-".$birthday[0];
    dev_dump($facebook_ID."-".$facebook_email,"facebook_connect.php - id and email",$dev);
    $user_exists=site_query("select * from User where facebookID='".$facebook_ID."'","facebook_connect.php - check for user ID",$dev);
    //if fb acc new to LYL - then register / merge accounts
    if (mysql_num_rows($user_exists)==0)
    {
        dev_dump("","facebook account not found",$dev);
        $LYL_user_exists=site_query("select * from User where email='".$facebook_email."'","facebook_connect.php - check for user email",$dev);
        //if email in LYL db
        if (mysql_num_rows($LYL_user_exists)>0)
        {
            dev_dump("","BUT email found in LYL",$dev);
            $merge_user_query="update User set facebookID='".$facebook_ID."', ";
            $merge_user_query=$merge_user_query."displayName='".$me["name"]."', ";
            $merge_user_query=$merge_user_query."facebookFirstName='".$me["first_name"]."', ";
            $merge_user_query=$merge_user_query."facebookLastName='".$me["last_name"]."', ";
            $merge_user_query=$merge_user_query."facebookEmail='".$facebook_email."', ";
            $merge_user_query=$merge_user_query."facebookLink='".$me["link"]."', ";
            $merge_user_query=$merge_user_query."dateOfBirth='".$birthdate."', ";
            $merge_user_query=$merge_user_query."gender='".$me["gender"]."', ";
            $merge_user_query=$merge_user_query."locale='".$me["locale"]."' ";
            $merge_user_query=$merge_user_query."where email='".$facebook_email."'";
            site_query($merge_user_query,"facebook_connect.php - merge facebook credentials with current LYL user",$dev);
            $user_exists=site_query("select * from User where email='".$facebook_email."'","facebook_connect.php - get user after merge",$dev);
        }
        else
        {
            dev_dump("","AND email not found - completely new account",$dev);
            //create new LYL acc
            //random password
            $chars="abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
            $char_count=strlen($chars);
            $new_password="";
            for ($i=1;$i<=10;$i++)
            {
                $char_position=rand(0,$char_count-1);
                $selected_char=substr($chars,$char_position,1);
                $new_password=$new_password.$selected_char;
            }
            $user_name=generate_username($facebook_email);
            $more_tasty=hash("sha256",$user_name.time());
            $password_hash=make_password($new_password,$more_tasty);
            //insert new user
            $create_user_query="insert into User (userTypeID,userName,displayName,email,facebookEmail,password,facebookID,facebookFirstName,facebookLastName,facebookLink,dateOfBirth,gender,locale,extraStuff) values ";
            $create_user_query=$create_user_query."(";
            $create_user_query=$create_user_query."1,";
            $create_user_query=$create_user_query."'".$user_name."',";
            $create_user_query=$create_user_query."'".$me["name"]."',";
            $create_user_query=$create_user_query."'".$facebook_email."',";// new user, both email fiedls are their facebook email - they can change their communication email later
            $create_user_query=$create_user_query."'".$facebook_email."',";
            $create_user_query=$create_user_query."'".$password_hash."',";
            $create_user_query=$create_user_query."'".$facebook_ID."',";
            $create_user_query=$create_user_query."'".$me["first_name"]."',";
            $create_user_query=$create_user_query."'".$me["last_name"]."',";
            $create_user_query=$create_user_query."'".$me["link"]."',";
            $create_user_query=$create_user_query."'".$birthdate."',";
            $create_user_query=$create_user_query."'".$me["gender"]."',";
            $create_user_query=$create_user_query."'".$me["locale"]."',";
            $create_user_query=$create_user_query."'".$more_tasty."'";
            $create_user_query=$create_user_query.")";
            site_query($create_user_query,"facebook_connect.php - create new LYL account after fb login",$dev);
            $new_id=mysql_insert_id();
            //create customer also
            site_query("insert into Customer (userID) values (".$new_id.")","facebook_connect.php - also Customer record for new user",$dev);
            record_action(array("e_ID"=>$new_id,"e_type"=>"user","e_stype"=>"customer","a_type"=>"registered"));
            $user_exists=site_query("select * from User where userID=".$new_id,"facebook_connect.php - get user after merge",$dev);
        }        
    }
    //else
    else
    {
        dev_dump("","facebook and LYL account found",$dev);
        //retrieve LYL user details
        //set user $_SESSION
    }
    $user=mysql_fetch_array($user_exists);
    dev_dump($user,"facebook_connect.php - user dump",$dev);
    set_user_signed_in($user,1,"login");
    //redirect
    header("location:".$_SERVER["HTTP_REFERER"]);
?>