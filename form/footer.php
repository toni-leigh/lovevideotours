<?php
    echo "<footer>";
    echo dump_include(array("level"=>1,"include_name"=>"footer.php"));
    echo    "<div id='f_top_back'>";
    echo        "<div id='f_top'>";
    echo        "</div>";
    echo    "</div>";
    echo    "<div id='f_main_back'>";
    echo        "<div id='f_main'>";
    echo            "<div id='f_links' class='left'>";
    echo                "<span class='footer_heading left'>QUICK LINKS</span>";
    echo                "<a href='/'><span class='f_bull'>&bull;</span>Home</a>";
    echo                "<a href='/places-to-stay'><span class='f_bull'>&bull;</span>Places to Stay</a>";
    echo                "<a href='/things-to-do'><span class='f_bull'>&bull;</span>Things to Do</a>";
    echo                "<a href='/love-video-tours-blog'><span class='f_bull'>&bull;</span>Blog</a>";
    echo                "<a href='/about-love-video-tours'><span class='f_bull'>&bull;</span>About Us</a>";
    echo                "<a href='/contact-us'><span class='f_bull'>&bull;</span>Contact Us</a>";
    echo                "<a href='/list-with-us'><span class='f_bull'>&bull;</span>List With Us</a>";
    echo            "</div>";
    echo            "<div id='f_follow' class='left'>";
    echo                "<span class='footer_heading left'>FOLLOW US</span>";
    echo                "<a href='http://www.facebook.com/lovevideotours'><span class='social_icon' style='background-position: -235px -0px;'></span></a>";
    echo                "<a href='http://twitter.com/#!/lovevideotours'><span class='social_icon' style='background-position: -235px -31px;'></span></a>";
    echo                "<a href='http://www.youtube.com/lovevideotours'><span class='social_icon' style='background-position: -235px -62px;'></span></a>";
    echo            "</div>";
    echo            "<div id='f_newsletter' class='left'>";
    echo                "<span class='footer_heading left'>NEWSLETTER</span>";
    echo                "<span id='newsletter_message' class='left'>Sign up to our newsletter: ".$message."</span>";
    echo                "<a name='signup'></a>";
    $nls_email="";
    if ($signup_saved==1)
    {
        echo "<div id='nls_message_good' class='green'>signup successful - thanks !</div>";
        $signup_saved=0;
    }
    else
    {
        if ($nls_bademail==1)
        {
            echo "<div id='nls_message_bad' class='red'>incorrect email</div>";
            $nls_email=$_POST["newsletter"];
            $nls_bademail=0;
        }
    }
    echo                "<form method='post' action='#signup'>";
    echo                    "<input type='hidden' name='newsletter_submit'/>";
    echo                    "<div id='nls_inputs'>";
    echo                        "<input id='newsletter_input' class='left' type='text' name='newsletter' value='".$nls_email."'/>";
    echo                        "<input id='newsletter_submit' class='submit' type='submit' name='submit' value='sign up'/>";
    echo                    "</div>";
    echo                "</form>";
    echo                "<span class='footer_heading left'>SPONSORS</span>";
    echo                    "<img src='/img/np_logo.png' alt='sponsor logo for The Northumberland National Park'/>";
    echo            "</div>";
    echo            "<div id='f_twitter' class='left'>";
    echo                "<span class='footer_heading left'>TWITTER</span>";
    ?>
        <script src="http://widgets.twimg.com/j/2/widget.js"></script>
        <script>
            new TWTR.Widget({
              version: 2,
              type: 'profile',
              rpp: 20,
              interval: 30000,
              width: 220,
              height: 120,
              theme: {
                shell: {
                  background: '#242526',
                  color: '#f8fff0'
                },
                tweets: {
                  background: '#242526',
                  color: '#faf9f2',
                  links: '#de853e'
                }
              },
              features: {
                scrollbar: false,
                loop: false,
                live: true,
                hashtags: true,
                timestamp: true,
                avatars: false,
                behavior: 'all'
              }
            }).render().setUser('lovevideotours').start();
        </script>
    <?php
    echo            "</div>";    
    echo        "</div>";
    echo    "</div>";
    echo "</footer>";
?>