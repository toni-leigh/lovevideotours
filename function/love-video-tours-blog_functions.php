<?php
    function lvtb_details($in)
    {
        $vi_d="<div id='vi_details'>";
        $vi_d.=vi_text($in["item"]);
        $vi_d.="</div>";
        return $vi_d;
    }
    function lvtb_image($in)
    {
        $ivid="<div id='ivid_panel'>";
        if (strlen($in["item"]["videoSRC"]))
        {
            $ivid.="<div class='ivid_heading'>";
            $ivid.="<span class='ivid_htext'>VIDEO</span>";
            $ivid.="<span class='full_screen_show' onclick='fs_show()'>view full screen gallery</span>";
            $ivid.="</div>";
            $ivid.=video_player(array("item"=>$in["item"],"width"=>440,"player_name"=>"lvplayer"));
        }
        $ivid.="<div class='ivid_heading'>";
        $ivid.="<span class='ivid_htext'>IMAGES</span>";
        $ivid.="<span class='full_screen_show' onclick='fs_show()'>view full screen gallery</span>";
        $ivid.="</div>";

        if (mysql_num_rows($in["item_images"])>2)
        {
            $ivid.="<div id='idir_links'>";
            $ivid.="<div class='ivid_scroll' class='left stop_select' onclick='scroll(\"left\")'>";
            $ivid.="<span class='circle_icon margin_right' style='background-position: -266px -232px;'></span><span class='left orange ivid_link'>previous</span>";
            $ivid.="</div>";
            $ivid.="<div class='ivid_scroll' class='right stop_select' onclick='scroll(\"right\")'>";
            $ivid.="<span id='right' class='circle_icon margin_left' style='background-position: -266px -261px;'></span><span class='right orange ivid_link'>next</span>";
            $ivid.="</div>";
            $ivid.="</div>";
        }

        $ivid.="<div id='thumbnail_row'>";
        $ivid.="<div id='slwin_iefix'>";
        $ivid.="<div id='slider_window'>";
        $tp_width=(mysql_num_rows($in["item_images"])*217);
        $ivid.="<div id='all_thumbnails' style='width:".$tp_width."px'>";
        $ivid.=$add_vid_thumb;
        while ($image=mysql_fetch_array($in["item_images"]))
        {
            $ivid.="<div class='thumbnail_panel'>";
            $ivid.="<img src='/".$image["largeSquarePath"]."'/>";
            $ivid.="</div>";
        }
        $ivid.="</div>";
        $ivid.="</div>";
        $ivid.="</div>";
        $ivid.="</div>";
        $ivid.="</div>";
        return $ivid;
    }


    function build_rss_xml()
    {
        $item_type=get_item_type(5);
        $items=get_items(array("i_type"=>$item_type,"order_by"=>"itemCreated desc"));
        $item=mysql_fetch_array($items);
        $rss='<?xml version="1.0" encoding="UTF-8" ?>';
        $rss.='<rss version="2.0">';
        $rss.='<channel>';
        $rss.='<title>Love Video Tours Blog</title>';
        $rss.='<description>Blog keeping you up to date with activity from Love Video Tours</description>';
        $rss.='<link>http://lovevideotours.com</link>';
        $year=substr($item["itemCreated"],0,4);
        $month=substr($item["itemCreated"],5,2);
        $day=substr($item["itemCreated"],8,2);
        $hour=substr($item["itemCreated"],11,2);
        $min=substr($item["itemCreated"],14,2);
        $second=substr($item["itemCreated"],17,2);
        $rss.='<lastBuildDate>'.date("D, d M Y H:i:s",mktime($hour,$min,$second,$month,$day,$year)).' +0000 </lastBuildDate>';
        $rss.='<pubDate>Sat, 08 Oct 2011 17:06:00 +0000 </pubDate>';
        $rss.='<ttl>0</ttl>';

        $rss.='<item>';
        $rss.='<title>'.$item["itemName"].'</title>';
        $rss.='<description>'.$item["itemTweet"].'</description>';
        $rss.='<link>http://lovevideotours.com/love-video-tours-blog/'.$item["itemUrlAppend"].'</link>';
        //$rss.='<guid>'.$item["itemID"].'</guid>';
        $rss.='<pubDate>'.date("D, d M Y H:i:s",mktime($hour,$min,$second,$month,$day,$year)).' +0000 </pubDate>';
        $rss.='</item>';

        while ($item=mysql_fetch_array($items))
        {
            $year=substr($item["itemCreated"],0,4);
            $month=substr($item["itemCreated"],5,2);
            $day=substr($item["itemCreated"],8,2);
            $hour=substr($item["itemCreated"],11,2);
            $min=substr($item["itemCreated"],14,2);
            $second=substr($item["itemCreated"],17,2);
            $rss.='<item>';
            $rss.='<title>'.$item["itemName"].'</title>';
            $rss.='<description>'.$item["itemTweet"].'</description>';
            $rss.='<link>http://lovevideotours.com/love-video-tours-blog/'.$item["itemUrlAppend"].'</link>';
            //$rss.='<guid>'.$item["itemID"].'</guid>';
            $rss.='<pubDate>'.date("D, d M Y H:i:s",mktime($hour,$min,$second,$month,$day,$year)).' +0000 </pubDate>';
            $rss.='</item>';
        }

        $rss.='</channel>';
        $rss.='</rss>';
        $fp=fopen("rss/blog.xml","w");
        fwrite($fp,$rss);
        fclose($fp);
    }
?>