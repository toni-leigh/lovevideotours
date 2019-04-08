<?php
    // set the session item list for the scrollers if it isn't set
    if (!isset($_SESSION["vitem_set"])) $temp=get_video_items(array());
    
    
    // retrieve data for page
    if (is_array($item))
    {
        $recommendations=get_item_recommendations($item["userID"]);
        $updates=get_videoitem_updates($item);
        $item_images=get_images("item",$item["itemID"]);
        $user_details=get_user($item["userID"]);
    }

    // process contact submission
    if (isset($_POST["con_user_ID"]))
    {
        if ($_POST["contact_details"]==""&&$_POST["contact_message"]=="")
        {
            $contact_success=0;
        }
        else
        {
            if (strlen($_POST["user_phone"])==0)
            {
                $user=get_user($_POST["con_user_ID"]);
                site_query("insert into ItemContact (itemID,contactMethod,contactDetails) values (".$item["itemID"].",'".$_POST["contact_details"]."','".$_POST["contact_message"]."')","save item contact");
                $email="";
                $email.="You have received a contact through your listing on Love Video Tours<br/><br/>";
                $email.="Their contact details are: ".$_POST["contact_details"]."<br/><br/>";
                $email.="This is their message:<br/><br/> ".$_POST["contact_message"]."<br/><br/>";
                $email.="(automated email, please don't reply)<br/><br/>";
                send_email($user["email"],"Contact from your listing on Love Video Tours",$email,0); 
                $contact_success=1;
            }
        }
    }
?>