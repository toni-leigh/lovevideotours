<?php
    if ($_GET["admin_divert"]=="edit")
    {
        if (is_numeric($_GET["user_ID"]))
        {
            $user=get_user($_GET["user_ID"]);
        }
        else
        {
            $user=null;
            $users=get_owner_users();
        }
    }
    if (isset($_POST["userID"]))
    {
        $success=1;
        include "control/validate_user_email.php";
        if ($_POST["displayName"]=="") {$errors["displayName"]=1;$success=0;}
        if ($success==1)
        {
            if (is_numeric($_POST["userID"]))
            {
                $user_pre_save=get_user($_POST["userID"]);
                if (make_password($_POST["password"],$user_pre_save["extraStuff"])!=$user_pre_save["password"]&&$_POST["password"]!="")
                    $password_hash=make_password($_POST["password"],$user_pre_save["extraStuff"]);
                if (isset($password_hash))
                    $extra="password='".$password_hash."',";
                site_query("update User set displayName='".$_POST["displayName"]."', userHTML='".$_POST["userHTML"]."', ".$extra." email='".$_POST["email"]."' where userID=".$_POST["userID"],"update user");
                $user=get_user($_POST["userID"]);
            }
            else
            {
                $user_name=generate_username($_POST["email"]);
                $more_tasty=hash("sha256",$user_name.time());
                $password_hash=make_password($_POST["password"],$more_tasty);
                site_query("insert into User (email,userName,password,extraStuff,displayName,userHTML,userTypeID) values ('".$_POST["email"]."','".$user_name."','".$password_hash."','".$more_tasty."','".$_POST["displayName"]."','".$_POST["userHTML"]."',2)","update user");
                $user_ID=mysql_insert_id();
                site_query("insert into CottageOwner (userID) values (".$user_ID.")","new cottage owner admin_user_controller.php");
                $user=get_user($user_ID);
            }
        }
        else
        {
            $user=$_POST;
        }
    }
?>