<?php
    if ($contact_submitted)
    {
        echo "<span class='full_screen_width contact_success'>Contact successfully sent</span>";
        unset($contact_submitted);
    }
    echo "<p id='contact_us_p'>You can use this form to contact us about anything at all to do with the site. You can be sure we will receive it. If you would like us to get back to you about something then please make sure you include some contact details.</p>";
    echo "<p id='contact_us_p'><a href='/list-with-us'>To find out more about listing your accommodation or attraction with lovevideotours.com</a><a href='/list-with-us'> click here</a></p>";
    echo "<div id='basic_page_left'>";
    echo "<form method='post' action=''>";
    echo    "<input type='hidden' name='contact_submitted'>";
    echo text_field(array("id"=>"email","label"=>"Your contact details (email, phone no., skype id ... ):","val"=>$contact_details,"err"=>$errors,"type"=>"text"));
    echo text_field(array("id"=>"contact","label"=>"Your Message:","val"=>"","err"=>$errors,"type"=>"textarea"));
    echo    "<input id='contact_submit' class='submit right' type='submit' name='submit' value='Contact us'>";
    echo "</form>";
    echo "</div>";
    echo "<div id='basic_page_right'>";
    echo    "<span class='contact_detail'><span class='cd_head'>You can email us direct at:</span><span class='cd_details'><a href='mailto:alysoun@lovevideotours.com'>alysoun@lovevideotours.com</a></span><span class='cd_details'>01668 283 465</span></span>";
    echo    "<span class='contact_detail'><span class='cd_head'>Film related enquiries:</span><span class='cd_details'><a href='mailto:alysoun@lovevideotours.com'>alysoun@lovevideotours.com</a></span><span class='cd_details'>01668 283 465</span></span>";
    echo    "<span class='contact_detail'><span class='cd_head'>Tech feedback or assistance:</span><span class='cd_details'><a href='mailto:colin@lovevideotours.com'>colin@lovevideotours.com</a></span><span class='cd_details'>07786 117 638</span></span>";
    echo "</div>";
    echo "<div id='page_bottom_padding'>&nbsp;";
    echo "</div>";
?>