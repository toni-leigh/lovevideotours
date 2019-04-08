<?php
    if (isset($_POST["contact_submitted"]))
    {
        if ($_POST["contact"]==""&&$_POST["email"]=="")
        {
            
        }
        else
        {
            if ($_POST["contact"]=="")
            {
                $errors["contact"]=1;
                $contact_details=$_POST["email"];
            }
            else
            {
                site_query("insert into Contact (contactText,contactEmail) values ('".$_POST["contact"]."','".$_POST["email"]."')","insert contact");
                $email_body="Contact: ".$_POST["email"]." <br/><br/>";
                $email_body.=$_POST["contact"];
                send_email("alysoun@lovevideotours.com","Contact sent through Love Video Tours",$email_body,0,"LIVE");
                $contact_submitted=1;
            }
        }
    }
?>