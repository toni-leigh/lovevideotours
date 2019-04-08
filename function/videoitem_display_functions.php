<?php
    function vi_traverse($in)
    {
        if ($in["mode"]=="fs")
        {
            $url_app="/fs";
            $tip_label="Gallery";
            $close_html="<a href='".build_item_link($in["item"])."'><div id='fsx'><span title='close full screen' id='fsclose_force_width' class='circle_icon' style='background-position: -186px -163px;'></span></div></a>";
        }
        else
        {
            $url_app="";
            $tip_label="Page";
        }
        $tb="<div id='traverse_bar'>";
        $vitem_set=$_SESSION["vitem_set"];
        /*dev_dump(count($_SESSION["vitem_set"]));
        dev_dump($_SESSION["vitem_set"]);*/
        $ic=count($vitem_set);
        for($x=0;$x<$ic;$x++)
        {
            //dev_dump($vitem_set[$x]["item"]["itemName"]);
            if ($in["item"]["itemID"]==$vitem_set[$x]["item"]["itemID"]) // either side of the current item
            {
                if ($x==0) //start
                {
                    $last=$vitem_set[$ic-1]["item"];
                    $next=$vitem_set[1]["item"];
                }
                elseif ($x==($ic-1)) // end
                {
                    $last=$vitem_set[$x-1]["item"];
                    $next=$vitem_set[0]["item"];
                }
                else // middle
                {
                    $last=$vitem_set[$x-1]["item"];
                    $next=$vitem_set[$x+1]["item"];
                }
            }
        }
        if (!isset($next)||!isset($last))
        {
            $next=$vitem_set[0]["item"];
            $last=$vitem_set[0]["item"];
        }
        $tb.="<div title='Previous ".$tip_label.": ".$last["categoryName"]." - ".str_replace("'","",$last["itemName"])." - ".str_replace("'","",$last["itemTweet"])."' id='trlast_link'><a href='".build_item_link($last).$url_app."'><div class='tr_icon' style='background-position: -89px -".$last["spriteOffset"]."px;'></div></a></div>";
        $tb.="<div id='fs_heading'>";
        $tb.="<div class='left'>".vi_cat_icon($in["item"])."</div>";
        if ($in["mode"]=="fs")
        {
            $tb.="<h1 id='fs_title'>";
            $tb.=$in["item"]["itemName"]." - gallery";
        }
        else
        {
            $tb.="<h1 id='fs_title_dark'>";
            $tb.=$in["item"]["itemName"];
        }
        $tb.="</h1>";
        $tb.=vi_addthis();
        $tb.="</div>";
        $tb.=$close_html;
        $tb.="<div title='Next ".$tip_label.": ".$next["categoryName"]." - ".str_replace("'","",$next["itemName"])." - ".str_replace("'","",$next["itemTweet"])."' id='trnext_link'><a href='".build_item_link($next).$url_app."'><div class='tr_icon' style='background-position: -50px -".$next["spriteOffset"]."px;'></div></a></div>";

        $tb.="</div>";
        return $tb;
    }
    function vi_addthis()
    {
        $at="<div id='vi_add' class='addthis_toolbox addthis_default_style'>";
        $at.="<a class='addthis_button_preferred_1'></a>";
        $at.="<a class='addthis_button_preferred_2'></a>";
        $at.="<a class='addthis_button_preferred_3'></a>";
        $at.="<a class='addthis_button_preferred_4'></a>";
        $at.="<a class='addthis_button_compact'></a>";
        $at.="</div>";
        return $at;
    }
    function vi_details($in)
    {
        // set stuff
        if (is_numeric($in["contact_success"]))
        {
            $contact_display=" selected ";
            $description_display=" unselected ";
        }
        else
        {
            $description_display=" selected ";
            $contact_display=" unselected ";
        }
        $item_type=get_item_type(2);
        $vi_d="<div id='vi_details'>";

        // builds the buttons for choosing the different sections of item details
        $vi_d.="<div id='vi_opts'>";
        $vi_d.="<a href=''>";
        $vi_d.="<span class='vi_opt detail_page_link ".$description_display."'>details";
        $vi_d.="</span>";
        $vi_d.="</a>";
        $vi_d.="<a href=''>";
        $vi_d.="<span class='vi_opt detail_page_link unselected'>facilities";
        $vi_d.="</span>";
        $vi_d.="</a>";
        /*$updates=get_videoitem_updates($in["item"]);
        if (mysql_num_rows($updates)>0)
        {
            $vi_d.="<a href=''>";
            $vi_d.="<span class='vi_opt detail_page_link unselected'>updates";
            $vi_d.="</span>";
            $vi_d.="</a>";
        }*/
        $user_details=get_user($in["item"]["userID"]);
        if ($user_details["subscriber"]==1)
        {
            $vi_d.="<a href=''>";
            $vi_d.="<span class='vi_opt detail_page_link ".$contact_display."'>contact";
            $vi_d.="</span>";
            $vi_d.="</a>";
        }
        $vi_d.="</div>";

        // if js enabled then replace the buttons with javascript buttons
        $vi_d.=open_script();
        $vi_d.="new_html='<span id=\"vi_tab0\" class=\"vi_opt detail_page_link ".$description_display."\"';\n";
        $vi_d.="new_html+=' onclick=\'change_panel(0)\' ';\n";
        $vi_d.="new_html+='>details</span>';\n";
        $vi_d.="new_html+='<span id=\"vi_tab1\" class=\"vi_opt detail_page_link unselected\"';\n";
        $vi_d.="new_html+=' onclick=\'change_panel(1)\' ';\n";
        $vi_d.="new_html+='>facilities</span>';\n";
        /*if (mysql_num_rows($updates)>0)
        {
            $vi_d.="new_html+='<span id=\"vi_tab2\" class=\"vi_opt detail_page_link unselected\"';\n";
            $vi_d.="new_html+=' onclick=\'change_panel(2)\' ';\n";
            $vi_d.="new_html+='>updates</span>';\n";
        }*/
        if ($user_details["subscriber"]==1)
        {
            $vi_d.="new_html+='<span id=\"vi_tab3\" class=\"vi_opt detail_page_link ".$contact_display."\"';\n";
            $vi_d.="new_html+=' onclick=\'change_panel(3)\' ';\n";
            $vi_d.="new_html+='>contact</span>';\n";
        }
        $vi_d.="document.getElementById('vi_opts').innerHTML=new_html;\n";
        $vi_d.=close_script();

        // default to description, although based on other variables other things can be shown
        $vi_d.="<span id='item_details_content'>";
        if (is_numeric($in["contact_success"])||$_GET["other_details"]=="contact")
            $vi_d.=stripslashes(cleanup_tinymce_output(json_sanitise(vi_contact_form($in))));
        else
        {
            if (isset($_GET["other_details"]))
            {
                if ($_GET["other_details"]=="updates")
                    $vi_d.=vi_updates($updates);
                elseif ($_GET["other_details"]=="facilities")
                    $vi_d.=vi_updates($item);
            }
            else
                $vi_d.=vi_text($in["item"]);
        }
        $vi_d.="</span>";
        $vi_d.="</div>";
        return $vi_d;
    }
    function vi_image_vid($in)
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
        $ivid.="</div>"; // close slwin_iefix
        $ivid.="</div>";
        $ivid.="</div>"; // close ivid panel
        return $ivid;
    }
    function vid_marker()
    {
        $vm="<div class='vid_marker'>";
        $vm.="<span id='play_heart_small' style='background-position: -194px -92px;'></span>";
        $vm.="</div>";
        return $vm;
    }
    function video_player($in)
    {
        $vid="";
        $height=floor($in["width"]/16*9);
        $play_button_up=(($height-90)/2)+90;
        $play_button_left=($in["width"]-90)/2;
        if ($in["player_name"]=="lvplayer")
        {
            $vid.="<a href='".build_item_link($in["item"])."/fs/play'><div id='fake_vid'>";
            $vid.="<div id='".$in["player_name"]."play_button' class='play_button' style='margin:80px 0px 0px 175px;'><span id='play_heart_large' style='background-position: -186px -117px;'></span></div>";
            $vid.="</div></a>";
        }
        else
        {
            // define variables for positioning etc. based on width
            $seek_bar_width=$in["width"]-20-31; // 20 = 2*10px padding, 31 = 21px pause button + 10px margin
            $vid="";

            // ie7 play button
            $vid.="<!--[if IE 7]>";
            $vid.="<span id='ie7_play' style='display:none;' onclick='play_video(\"".$in["item"]["videoSRC"]."\",\"".$in["player_name"]."\")'>click here to play video</span>";
            $vid.="<![endif]-->";

            // get swf object
            $vid.="<script type='text/javascript' src='/swfobject.js'></script>";
            $vid.="<div id='".$in["player_name"]."Div'>";
            $vid.="<iframe width='940' height='600' src='http://www.youtube.com/embed/".$in["item"]["videoSRC"]."' frameborder='0' allowfullscreen></iframe>"; // TODO - load embedded video here direct from Youtube in their player, for apple users
            $vid.="</div>";

            // load player
            $vid.="<script type='text/javascript'>\n";
            $vid.="var params = { allowScriptAccess: 'always' , wmode: 'opaque' };\n";
            $vid.="var atts = { id: '".$in["player_name"]."' };\n";
            $vid.="swfobject.embedSWF('http://www.youtube.com/apiplayer?enablejsapi=1&version=3&playerapiid=".$in["player_name"]."','".$in["player_name"]."Div', '".$in["width"]."', '".$height."', '8', null, null, params, atts);\n";
            $vid.="</script>";

            // play button
            $vid.="<div id='".$in["player_name"]."play_button' class='play_button' style='display:none;margin:-".$play_button_up."px 0px 0px ".$play_button_left."px;' onclick='play_video(\"".$in["item"]["videoSRC"]."\",\"".$in["player_name"]."\")'><span id='play_heart_large' style='background-position: -186px -117px;'></span></div>";

            // seek bar
            $vid.="<div id='control_bar' style='width:".($in["width"]-20)."px;display:none;'>"; //20 off for padding
            $vid.="<div id='pause_button' onclick='pause_video(\"".$in["player_name"]."\")'>";
            $vid.="<div id='pause_break'>&nbsp;</div>";
            $vid.="</div>";
            $vid.="<div id='".$in["player_name"]."seek_bar' class='seek_bar' style='width:".$seek_bar_width."px'>";
            $vid.="<div id='".$in["player_name"]."played_bar' class='played_bar'>";
            $vid.="</div>";
            $vid.="<div id='current_point'>";
            $vid.="</div>";
            $vid.="<div id='".$in["player_name"]."loaded_bar' class='loaded_bar'>";
            $vid.="</div>";
            $vid.="</div>";
            $vid.="</div>";

            // ie7 recommend
            $vid.="<!--[if IE 7]>";
            $vid.="<span id='ie7_recc'>(you may like to try Google Chrome, a superior internet browser: <a href='http://www.google.co.uk/chrome' target='_blank'>click here to download</a>)</span>";
            $vid.="<![endif]-->";

            /*$vid.="<div id='vid_dev_out'>";
            $vid.="<span class='vid_dev_text'>loaded (b): <span id='loaded'></span></span>";
            $vid.="<span class='vid_dev_text'>total (b): <span id='total'></span></span>";
            $vid.="<span class='vid_dev_text'>duration (s): <span id='duration'></span></span>";
            $vid.="<span class='vid_dev_text'>current (s): <span id='current_position'></span></span>";
            $vid.="<span class='vid_dev_text'>bar width: <span id='width'></span></span>";
            $vid.="<span class='vid_dev_text'>bytes per pixel: <span id='per_pixel'></span></span>";
            $vid.="<span class='vid_dev_text'>pixels covered: <span id='covered'></span></span>";
            $vid.="<span class='vid_dev_text'>secs per pixel: <span id='secs_pixel'></span></span>";
            $vid.="<span class='vid_dev_text'>pixels played: <span id='played_pixels'></span></span>";
            $vid.="<span class='vid_dev_text'>computed: <span id='computed'></span></span>";
            $vid.="</div>";*/
        }
        return $vid;
    }
    function fs_image($in)
    {
        // do the padding, the extra 1 is added to paddings for odd numbers
        if ($in["image"]["height"]==568)
        {
            if ($in["pcount"]%2==1&&$in["count"]==$in["curr"]) $force_wide=1;
            if ($in["image"]["width"]<=460&&$force_wide!=1) // portrait ratio (centre last image regardles of ratio)
            {
                $panelw=470;
                $pw=floor(470-$in["image"]["width"])/2;
            }
            else // landcsape / widescreen
            {
                $panelw=940;
                $pw=floor(940-$in["image"]["width"])/2;
            }
            $lp=$pw;
            if ($in["image"]["width"]%2==0) $rp=$pw; else $rp=$pw+1;
            $p1_div="<div class='fs_image_pad' style='width:".$lp."px;height:568px;'></div>";
            $p2_div="<div class='fs_image_pad' style='width:".$rp."px;height:568px;'></div>";
        }
        else
        {
            // image ratio cinemascope or near
            $panelw=940;
            $ph=floor(568-$in["image"]["height"])/2;
            $tp=$ph;
            if ($ph%2==0) $bp=$ph; else $bp=$ph+1;
            $p1_div="<div class='fs_image_pad' style='width:940px;height:".$tp."px;'></div>";
            $p2_div="<div class='fs_image_pad' style='width:940px;height:".$bp."px;'></div>";
        }

       //  if ($in["count"]==$in["curr"]) $next_step=0; else $next_step=$in["full_array"][$in["curr"]];

        // output each image - here we set the onclick functionality
        /* if ($in["count"]==$in["curr"]||($in["count"]==$in["curr"]-1&&$in["pcount"]%2==0))
        {
            $fsi="<div class='fs_image_panel' style='width:".$panelw."px'>";
        }
        else
        {
            if ($in["image"]["step"]==$prev_step)
                $fsi="<div class='fs_image_panel' style='width:".$panelw."px' onclick='fs_shift(".$in["image"]["step"].",".($in["image"]["step"]+1).",".($in["image"]["step"]+2).",".($in["image"]["step"]+1).")'>";
            else
                $fsi="<div class='fs_image_panel' style='width:".$panelw."px' onclick='fs_shift(".$in["image"]["step"].",".($in["image"]["step"]+1).",".($in["image"]["step"]+2).",".($in["image"]["step"]+1).")'>";
        } */

        $fsi="<div class='fs_image_panel' style='width:".$panelw."px'>";
        $fsi.=$p1_div;
        $fsi.="<img class='fs_image' src='/".$in["image"]["display_path"]."' width='".$in["image"]["width"]."' height='".$in["image"]["height"]."'/>";
        $fsi.=$p2_div;
        $fsi.="</div>";
        return $fsi;
    }
    function vi_filter_panel($curr_cat=null)
    {
        // the current cat is used to stop list updating when filter applied, if hp list
        if (is_numeric($curr_cat)) $ccat_ID=0; else $ccat_ID=$curr_cat;
        $fcats=site_query("select * from Category where parentID=5 order by ttdCategoryName, displayOrder","get categories in vi_filter_panel()"); // all needed for output
        $vfp="";
        $vfp.="<p id='filter_p'>You can filter the places on the map using the checkboxes - only checked categories will appear. Holiday property always appears and, if on the 'Things To Do' page, the list of panels will also be filtered.</p>";
        $vfp.="<div id='f_panel' class='stop_select'>";
        $last_top_level="";
        $c=1;
        $catc=mysql_num_rows($fcats);
        $vfp.="<form id='fcat_form' method='post' action=''>";
        while ($fcat=mysql_fetch_array($fcats))
        {
            if ($fcat["ttdCategoryName"]!=$last_top_level)
            {
                if ($c>1)
                {
                    $vfp.="</div>"; // only close the last panel if there is one (i.e. not iteration 1)
                    if ($c<$catc) $vfp.="<div class='f_spacer'>&nbsp;</div>"; // add spacer after closing if not on last loop
                }
                $vfp.="<div class='ftype_panel'>";
                $vfp.="<span id='".$fcat["ttdCategoryName"]."ftype_header' class='ftype_header'>".$fcat["ttdCategoryName"]."</span>";
                $last_top_level=$fcat["ttdCategoryName"];
            }
            $vfp.="<div class='fcat' onclick='toggle_check(\"".$fcat["categoryID"]."\",\"".$curr_cat."\")'>";
            $vfp.="<div class='left fcaticon_div'>".vi_cat_icon($fcat)."</div>";
            if (isset($_SESSION["sparams"]["cats"]))
                if (in_array($fcat["categoryID"],$_SESSION["sparams"]["cats"]))
                    $checked=" checked='checked'";
                else
                    $checked="";
            else
                $checked=" checked='checked'";
            $vfp.="<input id='".$fcat["categoryID"]."' name='".$fcat["categoryID"]."' class='fcat_check' type='checkbox' ".$checked." onclick='toggle_check(\"".$fcat["categoryID"]."\",\"".$curr_cat."\")'/>";
            $vfp.="<span class='fcat_name'>".$fcat["categoryName"];
            $vfp.="</span>";
            $vfp.="</div>";
            $c++;
        }
        $vfp.="</div>"; // closes the last panel
        $vfp.="</form>";
        $vfp.="</div>";
        return $vfp;
    }
    function vi_cat_icon($cat)
    {
        $vci="";
        $vci.="<span title='".$cat["categoryName"]."' class='item_cat' style='background-position: 0px -".($cat["spriteOffset"])."px;'></span>";
        return $vci;
    }
    function vi_vid_icon()
    {
        $vvi="";
        $vvi.="<span title='".$cat["categoryName"]."' class='item_cat left' style='background-position: -186px -303px;'></span>";
        return $vvi;
    }
    function vi_map_heading()
    {
        $vimh="";
        $vimh.="<div id='map_heading' class='l_heading'>";
        $vimh.="<span class='circle_icon margin_right' style='background-position: -266px -145px;'></span><span class='l_heading_text'>ON THE MAP</span>";
        $vimh.="</div>";
        return $vimh;
    }
    function vi_map($in)
    {
        $vim.="<div id='map_border'>";
        $vim.="<a href='#'><div id='map_backtotop'><span class='circle_icon' style='background-position: -266px -203px;'></span></div></a>";
        $vim.="<div id='item_map'></div>";
        $vim.="</div>";
        return $vim;
    }
    function vi_recommends($reccs)
    {
        $vir="";
        if (mysql_num_rows($reccs)>0)
        {
            $vir.="<div class='l_heading'>";
            $vir.="<span class='circle_icon margin_right' style='background-position: -266px -174px;'></span><span class='l_heading_text'>OWNER RECCOMMENDS</span>";
            $vir.="</div>";
            $vir.=item_list($reccs,4);
        }
        return $vir;
    }
    function vi_text($item)
    {
        $vit="";
        if (!is_numeric($item["resFest"])&&$item["itemTypeID"]==3)
        {
            /*$vit.='<div id="res_fest">';
            $vit.='<p>This attraction is participating in Northumberland Residents Festival 2012 in association with Northumberland County Council. '.$item["itemName"].' is offering <span class="bold">'.$item["resFest"].'</span> entry on Saturday the 24th and Sunday 25th of March (this coming weekend).</p>';
            $vit.='<p>In order to take advantage of this offer you need to be able to prove you are a resident of Northumberland (bank statement, bill or driving license will do).</p>';
            $vit.='<p>You will also have to get a voucher and fill it in. You can download vouchers by <a href="http://www.northumberland.gov.uk/idoc.ashx?docid=6a781cb9-5377-4fa3-ab1b-0a28129e1bc6&version=-1">clicking here</a> ';
            $vit.='or by visiting your local library, council office or tourist information office.</p>';
            $vit.='</div>';*/
        }
        $vit.=stripslashes(cleanup_tinymce_output(json_sanitise($item["itemHTML"])));
        return $vit;

    }
    function vi_updates($updates)
    {
        $vi_u="";
        while ($update=mysql_fetch_array($updates))
        {
            $vi_u.='<span class=\"update_panel\">';
            $vi_u.='<span class=\"update_title\">';
            $vi_u.=$update["itemName"];
            if ($update["blogPropertyID"]==0) $vi_u.=" [general]";
            $vi_u.='</span>';
            $vi_u.='<span class=\"update_date\">';
            $date_bits=explode("-",substr($update["itemCreated"],0,10));
            $date=date("jS - M - y",mktime(0,0,0,$date_bits[1],$date_bits[2],$date_bits[0]));
            $vi_u.=$date;
            $vi_u.='</span>';
            $vi_u.='<span class=\"update_details\">';
            $vi_u.=cleanup_tinymce_output(sanitise_for_html($update["itemHTML"]));
            $vi_u.='</span>';
            $vi_u.='</span>';
        }
        return $vi_u;
    }
    function vi_facilities($item)
    {
        $fp='<span id=\"item_features\">';
        $features=get_this_video_item_features($item["itemID"]);
        while ($feature=mysql_fetch_array($features))
        {
            $fp.='<span title=\"'.$feature["featureText"].'\" class=\"item_feature left\" style=\"background-position: -128px -'.(($feature["featureID"]-1)*58).'px;\">';
            $fp.='</span>';
        }
        $fp.='</span>';
        return $fp;
    }
    function vi_contact_form($in)
    {
        $cf="";
        if ($in["item"]["categoryID"]==3)
        {
            if (strlen($in["item"]["userPhone"])>0)
                $cf.='<span class=\"label\"><p>contact number: '.$in["item"]["userPhone"].'</p></span>';
            if (strlen($in["item"]["email"])>0)
                $cf.='<span class=\"label\"><p>contact email: <a href=\"mailto:'.$in["item"]["email"].'\">'.$in["item"]["email"].'</a></p></span>';
        }
        if (is_numeric($in["contact_success"]))
            if ($in["contact_success"]==1)
                $cf.='<span class=\"contact_message green\">contact sucessfully submitted!</span>';
            else
                $cf.='<span class=\"contact_message red\">you need to fill in some fields</span>';
        $cf.='<div id=\"ic_form\">';
        $cf.='<p>This form will send a message direct to the property / attraction owner. If you want to be contacted back then you should include some contact details.</p>';
        $cf.='<form method=\"post\" action=\"\">';
        $cf.='<input type=\"hidden\" name=\"con_user_ID\" value=\"'.$in["item"]["userID"].'\"/>';
        $cf.='<input type=\"hidden\" name=\"user_phone\" value=\"\"/>';
        $cf.='<span class=\"label\">your phone number, skype id or email address:</span>';
        $cf.='<input id=\"ic_text\" type=\"text\" name=\"contact_details\" value=\"\"/>';
        $cf.='<span class=\"label\">your message for the property owner:</span>';
        $cf.='<textarea name=\"contact_message\"></textarea>';
        $cf.='<input class=\"submit right\" type=\"submit\" name=\"submit\" value=\"send contact\"/>';
        $cf.='</form>';
        $cf.='</div>';
        return $cf;
    }
?>