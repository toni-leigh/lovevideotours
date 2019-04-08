<?php
    if ($_POST["engage_type"]=="register")
    {
        $user_check=validate_engage_email($_POST["email"]);
        if ($user_check=="Passed")
        {
            $user_check=register_new_user($_POST);
            set_user_signed_in($user_check,0,"register");
        }
    }
    if ($_POST["engage_type"]=="login")
    {
        $user_check=check_user_name($_POST["email"]);
        if (is_array($user_check))
        {
            $password_hash=make_password($_POST["password"],$user_check["extraStuff"]);
            $user_check=check_password($_POST["email"],$password_hash);
            if (is_array($user_check))
            {
                set_user_signed_in($user_check,0,"login");
                setcookie("usignin",1,time()+(60*60*24*365*5),"/");
                header("location:/control-room/videoitem/edit");
            }
            else
            {
                site_query("insert into AccessLog (userID,host,passwordFail) values (0,'".$_SERVER["REMOTE_ADDR"]."',1)","access log record in engage_check.php - failed password");
            }
        }
    }
?>