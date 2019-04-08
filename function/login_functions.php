<?php
    function check_user_name($email)
    {
        $sql_string="select userID from User where email='".$email."'";
        $result=mysql_query($sql_string) or die(mysql_error());
        return mysql_num_rows($result);
    }
    function check_password($email,$password)
    {
        $sql_string="select * from User where email='".$email."' and password='".$password."'";
        $result=mysql_query($sql_string) or die(mysql_error());
        return mysql_fetch_array($result);
    }
?>