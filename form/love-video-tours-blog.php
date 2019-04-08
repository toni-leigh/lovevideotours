<?php
    if (is_array($item))
    {
        echo vi_traverse(array("item"=>$item));
        // echo h1(array("page"=>$page,"user"=>$user,"item"=>$item,"category"=>$current_category));
        // displays the video / image panel
        echo "<div id='item_details_top'>";
        echo lvtb_image(array("item"=>$item,"item_images"=>$item_images));
        echo lvtb_details(array("item"=>$item,"contact_success"=>$contact_success));
        echo "</div>";
        
        // include the js functions needed for the video itme display and full screen
        echo "<script type='text/javascript' src='/form/videoitem.js'></script>";
    }
    else
    {
        echo    "<span id='rss_link'>please <a href='http://feeds.feedburner.com/LoveVideoToursBlog' target='_blank'>click here</a> to subscribe to the <a href='http://feeds.feedburner.com/LoveVideoToursBlog' target='_blank'>Love Video Tours Blog RSS feed</a></span>";
        while ($lvt_blog_post=mysql_fetch_array($lvt_blog_posts))
        {
            $item_images=get_images("item",$lvt_blog_post["itemID"]);
            $item_image=mysql_fetch_array($item_images);
            echo "<div class='lvt_blog_panel'>";
            echo    "<div class='lvt_blog_name'><h2><a href='/love-video-tours-blog/".$lvt_blog_post["itemUrlAppend"]."'>".stripslashes($lvt_blog_post["itemName"])."</a></h2></div>";
            echo    "<div class='lvt_blog_image'>";
            echo        "<img src='".$item_image["largeSquarePath"]."' alt='Image supporting blog post ".$lvt_blog_post["itemName"]."'/>";
            echo    "</div>";
            echo    "<div class='lvt_blog_published'>".$lvt_blog_post["itemCreated"]."</div>";
            echo    "<div class='lvt_blog_tweet'>".stripslashes($lvt_blog_post["itemTweet"])."</div>";
            echo    "<div class='lvt_blog_body'>";
            echo        "<div class='lvt_blog_HTML'>";
            $item_HTML=substr($lvt_blog_post["itemHTML"],0,1000);
            echo            strip_tags(sanitise_for_html(cleanup_tinymce_output(stripslashes($item_HTML))),"<p>");
            echo        "</div>";
            echo        "<div class='lvt_read_more fb_connect'><a href='/love-video-tours-blog/".$lvt_blog_post["itemUrlAppend"]."'>read more ...</a></div>";
            echo    "</div>";
            echo "</div>";
        }
    }
?>