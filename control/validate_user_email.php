<?php
    if (!validate_email_format($_POST["email"])) {$errors["email"]["invalid"]=1;$success=0;}
    if (is_numeric($_POST["userID"]))
    {
        if (check_unique_string($_POST["email"],"User","email","userID",$_POST["userID"]))
        {
            $errors["email"]["taken"]=1;
            $success=0;
        }
    }                
    else
    {
        if (check_unique_string($_POST["email"],"User","email","userID",$_POST["userID"]))
        {
            $errors["email"]["taken"]=1;
            $success=0;
        }
    }
?>