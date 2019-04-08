<?php
    $new_password=random_string(8);
    $check_user=site_query("select * from User where email='".$_POST["password_email"]."' or facebookEmail='".$email."'","new password check user");
    if (mysql_num_rows($check_user)>0)
    {
        $user=mysql_fetch_array($check_user);
        $password_hash=make_password($new_password,$user["extraStuff"]);
        site_query("update User set password='".$password_hash."' where userID=".$user["userID"],"store new password");
        send_email($_POST["password_email"],"Your new password from Love Video Tours",$new_password);
    }    
    $password_sent=1;
?>