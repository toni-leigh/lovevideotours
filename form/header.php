<?php
    // facebook div required
    echo "<div id='fb-root'></div>";
    echo "<script src='http://connect.facebook.net/en_US/all.js'></script>";
    echo "<script>";
    echo     "FB.init({";
    echo     "appId:'".$facebook_appid."', cookie:true,";
    echo     "status:true, xfbml:true"; 
    echo     "});";
    echo "</script>";
    
    // full header in HTML5 tags - 'h_top', 'h_logo_links', 'h_main'
    echo "<header>";
    echo dump_include(array("level"=>1,"include_name"=>"header.php"));
    
    // setup the login form, ready to appear if login is clicked
    if (is_array($user_check)||!isset($user_check)||$page["URL"]=="register"||$page["URL"]=="login")
        $engage_state="none";
    else
        $engage_state="block";
    echo "<div style='display:".$engage_state.";' id='engage_form_display'>";
    echo dump_include(array("level"=>2,"include_name"=>"engage.php"));
    //unset($_SESSION["user"]);
    if (!is_array($_SESSION["user"]))
        echo engage_form($user_check,"login");
    echo "</div>";
        
    // the top, thirty pixel deep header, with base page links and simple search box
    echo dump_include(array("level"=>2,"include_name"=>"header.php [top]"));
    echo    "<div id='h_top_back'>";
    echo        "<div id='h_top'>";
    // builds the top level navigation menu for basic pages (about etc.)
    $pages=site_query("select * from Page where parentID=0 and pageDisplay=1 order by pageOrder","content.php - get parent pages for top level navigation");
    echo dump_include(array("level"=>1,"include_name"=>"header.php [nav]"));
    echo            "<ul title='Main site navigation'>";
    while($nav_page=mysql_fetch_array($pages))
    {
        echo "<li>";
        get_pages($nav_page["pageID"],1,$page);
        echo "<span class='h_top_pipe left'>|</span>";
        echo "</li>";
    }
    if (!is_array($_SESSION["user"]))
        echo "<li><span id='login_link' class='h_top_pipe left' onclick='show_engage_form(\"login\")'><span id='login_icon' style='background-position: -186px -92px;'></span>Login</span></li>";
    else
        echo "<li><span id='control_link' class='nav left'><a href='/control-room/videoitem/edit'>Control</a></span></li>";
    echo            "</ul>";    
    // builds a simple search box for the header
    echo            "<div id='nav_search' class='right'>";
    include $device."form/search.php";
    echo            "</div>";
    echo        "</div>";
    echo    "</div>";
    
    // the header with logo and item links
    echo dump_include(array("level"=>2,"include_name"=>"header.php [logo links]"));
    echo    "<div id='h_logo_links_back'>";   
    echo        "<div id='h_logo_links'>";
    // logo and 'LOVE VIDEO TOURS' text / home link
    echo            "<div id='home_link' class='left'>";
    echo                "<a href='/'>";
    echo                    "<div id='logo' class='left'>";
    echo                        "<img src='/img/logo.png' alt='love video tours logo links to home page'/>";
    echo                    "</div>";
    echo                    "<div id='logo_text'>";
    echo                        "LOVE VIDEO TOURS";
    echo                    "</div>";
    echo                "</a>";
    echo            "</div>";
    // master nav links
    echo            "<div id='master_nav' class='right'>";
    echo                "<a href='/'><span class='circle_icon' style='background-position: -266px -0px;'></span><span class='master_nav_text left'>HOME</span></a>";
    echo                "<a href='/places-to-stay'><span class='circle_icon' style='background-position: -266px -29px;'></span><span class='master_nav_text left'>PLACES TO STAY</span></a>";
    echo                "<a href='/things-to-do'><span class='circle_icon' style='background-position: -266px -87px;'></span><span id='last_master_nav' class='master_nav_text left'>THINGS TO DO</span></a>";
    echo            "</div>";
    echo        "</div>";
    echo    "</div>";
    
    // the deep header with text and graphics - only displayed on home
    if ($page["pageID"]==1)
    {
        echo dump_include(array("level"=>2,"include_name"=>"header.php [main]"));
        echo    "<div id='h_main_back'>";
        echo        "<div id='h_main_image'>";
        echo            "<div id='h_main'>";
        echo                "<h1 id='discover_h1'>DISCOVER SOMEWHERE NEW</h1>";
        echo                "<div id='home_page_header_text'>";
        echo                    "Love Video Tours is perfect if you are looking for a holiday destination or simply a day out somewhere new. Discover some of the most beautiful locations Northumberland and The Borders have to offer through our photo and video rich content.";
        echo                "</div>";
        echo            "</div>";
        echo        "</div>";
        echo    "</div>";
    }
    
    // builds a basket summary for the header
    /* echo    "<div id='basket_engage' class='left'>";
    echo        "<span id='basket_panel'>";
    echo            build_html_basket();
    echo        "</span>";
    echo    "</div>"; */
    
    echo "</header>";
    
    // builds the top level navigation menu for items
    /*$pages=site_query("select * from Page where parentID=0 order by pageOrder","content.php - get parent pages for top level navigation");
    echo dump_include(array("level"=>1,"include_name"=>"header.php [nav]"));
    echo        "<ul title='Main site navigation'>";
    while($nav_page=mysql_fetch_array($pages))
        get_pages($nav_page["pageID"],1,$page);
    echo        "</ul>";*/
?>