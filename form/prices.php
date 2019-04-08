<?php
    echo "<div id='basic_page_left'>";
    echo    "<div id='price_big'>&pound;300 <span id='vat'>(inc. VAT)</span></div>";
    echo    "<span id='price_strap'>&ldquo;Fires will flicker, champagne will sparkle and trees will move in the breeze&rdquo;</span>";
    echo    "<div id='prices_vid'>";    
    $item=get_item(array("i_ID"=>141,"i_type"=>3));
    
    // $item["videoSRC"]="6Nt2aaWK1Nw";
    
    echo        "<div id='mediaplayer'>JW Player goes here</div>";       
    echo        "<script type='text/javascript' src='/jwplayer/jwplayer.js'></script>";
    echo        "<script type='text/javascript'>";
    echo           "jwplayer('mediaplayer').setup({";
    echo               "flashplayer: '/jwplayer/player.swf',";
    echo               "width: '680',";
    echo               "height: '402',";
    echo               "autostart: 0,";
    echo               "backcolor: '000000',";
    echo               "frontcolor: 'de853e',";
    echo               "lightcolor: '68bfb7',";
    echo               "screencolor: '000000',";
    echo               "controlbar: 'bottom',";
    echo               "file: 'http://www.youtube.com/watch?v=tvQ4vWx4KLw',";
    echo               "image: '/img/preview.jpg'";
    echo           "});";
    echo        "</script>";
    echo    "</div>";
    echo "<div id='video_contents'>";
    echo    "<p>Included in your &pound;300 video package:</p>"; 
        
    echo    "<p>* One site visit and a telephone consultation.</p>";  
        
    echo    "<p>*  Resulting video will be a 1.15-2.00 min tour.</p>"; 
        
    echo    "<p>* High quality music.</p>"; 
        
    echo    "<p>* Full HD Mpeg on Data Disc.</p>"; 
        
    echo    "<p>* Recommended for up to four bedroom tours, larger properties competitively quoted on request.</p>"; 
        
    echo    "<p>* Video tour will also be added to the LoveVideoTours website, and associated video sharing pages (youtube, dailymotion, and vimeo) for 12 months free of charge.</p>";
    echo "</div>";
        
        /*
    echo    "<p>When you list your property with lovevideotours, you will benefit from the following:</p>"; 
        
    echo    "<p>* Full HD Video tour of your property and the surrounding area. Champagne will sparkle, fires will flicker, trees will move in the breeze and we'll showcase to your potential visitors the personal magic of your property. The video can be between 1.5 and 3 minutes long, and you are welcome to write a narration (we can help you with this) or even present it yourself. If that’s not for you, you can choose a piece of music from our high quality selection. All the footage will be captured on the day of the tour and is unique to your video. We will discuss with you what you would most like in your video and how your cottage is to be presented. We don't sequence photographs as a moving image is much more powerful and engaging for the viewer. </p>";  
        
    echo    "<p>* You will be given a listing on LoveVideoTours.com with your contact details and contact form activated to forward e-mails to your business e-mail address. We can add as many high quality images as you would like to your listing, and if your cottage facilities or description changes, or if you have new images, we will update these within 24hrs for no extra charge.</p>"; 
        
    echo    "<p>* Your video will be added to our video sharing pages (youtube etc) and shared through our Facebook and twitter accounts. We can also help you set up a youtube account at no extra cost (youtube is the biggest video search engine in the world, and one of the biggest search engines in general, so having a stand out presence on it is extremely valuable for a business).</p>"; 
        
    echo    "<p>* Our web developer will embed the youtube video into your own website  at no extra cost.</p>"; 
        
    echo    "<p>* You will also receive a full unbranded HD copy of the file on disc.</p>"; 
        
    echo    "<p>We are happy to meet up for a no obligation chat about the service and so please get in touch on the details listed.</p>"; 
        
    echo    "<p>Thank you for reading this and we can't wait to film your property.</p>";
        */
    
    echo "</div>";
    echo "<div id='basic_page_right'>";
    echo    "<span class='contact_detail'><span class='cd_head'>You can email us direct at:</span><span class='cd_details'><a href='mailto:alysoun@lovevideotours.com'>alysoun@lovevideotours.com</a></span><span class='cd_details'>01668 283 465</span></span>";
    /* echo    "<span class='contact_detail'><span class='cd_head'>Film related enquiries:</span><span class='cd_details'><a href='mailto:alysoun@lovevideotours.com'>alysoun@lovevideotours.com</a></span><span class='cd_details'>01668 283 465</span></span>";
    echo    "<span class='contact_detail'><span class='cd_head'>Tech feedback or assistance:</span><span class='cd_details'><a href='mailto:colin@lovevideotours.com'>colin@lovevideotours.com</a></span><span class='cd_details'>07786 117 638</span></span>"; */
    
    echo    "<span class='contact_detail'>";
    echo        "<span class='cd_head'>Testimonials:</span>";
    echo        "<span class='cd_details testimonial'>";
    echo            "<p>&ldquo;This looks absolutely brilliant - really pleased. Gives a really nice feel of the place; like the way you've associated things like the flowers and the fire.&rdquo;</p>";
    echo            "<a class='testimonial_link' href='https://twitter.com/#!/northumbrianman' target='_blank'>@northumbrianman</a>";
    echo        "</span>";
    echo        "<span class='cd_details testimonial'>";
    echo            "<p>&ldquo;With the sun smiling between the cotton wool clouds in a bright blue sky, it couldn't have been better! Thank you&rdquo;</p>";
    echo            "<p>&ldquo;I think it is natural, colourful and fresh - capturing the sense of space and place beautifully! Thank you so much !&rdquo;</p>";
    echo            "<a class='testimonial_link' href='https://twitter.com/#!/hendersyde' target='_blank'>@Hendersyde</a>";
    echo        "</span>";
    echo        "<span class='cd_details testimonial'>";
    echo            "<p>&ldquo;Very excited for our Video Tour of Nisbet's Tower by @lovevideotours to go live! It's fantastic!!&rdquo;</p>";
    echo            "<a class='testimonial_link' href='https://twitter.com/#!/GunsgreenHouse' target='_blank'>@GunsgreenHouse</a>";
    echo        "</span>";
    echo        "<span class='cd_details testimonial'>";
    echo            "<p>&ldquo;@Hendersyde You could not have chosen much better.&rdquo;</p>";
    echo            "<a class='testimonial_link' href='https://twitter.com/#!/Southernupland' target='_blank'>@Southernupland</a>";
    echo        "</span>";
    echo    "</span>";
    
    echo    "<div id='add_this_buttons' class='addthis_toolbox addthis_default_style addthis_32x32_style'>";
    echo    "<span id='share_this' class='cd_head'>Share this page: </span>";
    echo    "<a class='addthis_button_preferred_1'></a>";
    echo    "<a class='addthis_button_preferred_2'></a>";
    echo    "<a class='addthis_button_preferred_3'></a>";
    echo    "<a class='addthis_button_preferred_4'></a>";
    echo    "<a class='addthis_button_compact'></a>";
    echo    "</div>";
    echo "</div>";
    echo "<div id='page_bottom_padding'>&nbsp;";
    echo "</div>";
?>