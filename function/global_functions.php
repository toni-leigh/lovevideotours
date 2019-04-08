<?php
    /*
     converts a numerical value into a formatted pounds and pence value
    */
    function format_price($price)
    {
        return "&pound;".number_format($price,2);
    }
    /*
        //file dump
        ob_start();
        var_dump($GLOBALS);
        $data = ob_get_clean();
        $fp = fopen("debug.txt", "w");
        fwrite($fp, $data);
        fclose($fp);
    */
    /*
     Love Your Larders query function - this returns the query, which then must be treated as such, it does not return a fetched array from a query designed to fetch one record
     $query_string = the query string in
     $called_from = for debug output
     $dev = whether or not to output dev stuff
    */
    function site_query($argument_query,$heading,$dev=0)
    {
        dev_dump($argument_query,"<strong>QUERY DEV DUMP: </strong>".$heading." query",$dev);
        $query_result=mysql_query($argument_query) or die (mysql_die($heading,$argument_query));
        return $query_result;
    }
    /*
     send an email, we get a copy sent to our internal email addresses and also store the send in the DB. uses phpmailer class
     $to - the target email address
     $subject - the subject line for the email
     $body - the html body
     $registration - if this is a registration email we don't send to order@ internally
     $send - can be set to "" - this is done to stop emails getting sent during testing, particularly important if payment processing is being tested as we don't want to send emails to suppliers telling them to post things as a result of a test!
   */
    function send_email($to,$subject,$body,$registration=1,$send="LIVE")
    {
        //add content to db
        $insert_email=mysql_query("insert into Email (emailAddress,emailSubject,emailBody,ipAddress) values ('".addslashes($to)."','".addslashes($subject)."','".addslashes($body)."','".$_SERVER["REMOTE_ADDR"]."')") or die(mysql_error());
        $insert_ID=mysql_insert_id();
        //only actually send if live
        if ($send=="LIVE")
        {
            //send the email
            $headers="MIME-Version: 1.0\r\nContent-type: text/html; charset=iso-8859-1\r\n";
            $headers=$headers."From: lovevideotours.com Contact Form <contactfrom@lovevideotours.com> \r\n";
            if (mail($to,$subject,$body,$headers))
            {
                //echo "Success";
                $update=mysql_query("update EmailSent set success=1 where emailSentID=".$insert_ID);
            }
            else
            {
                //echo "Fail";
                $update=mysql_query("update EmailSent set success=0 where emailSentID=".$insert_ID);
            }
        }
    }
    /*
     runs db protect on $_REQUEST values, protecting from $_GET and $_POST type attacks
    */
    function strip_request_values($request_values)
    {
        $return_request_array=array();
        while (list($key,$value) = each($request_values))
        {
            //we let html and styles thrugh for some fields
            if ($key=="description"||$key=="shortDesc"||$key=="longDesc"||$key=="notes"||$key=="ingredients"||$key=="method")
            {
                $return_request_array[$key]=protect_database($value,"html_through");
            }
            else
            {
                $return_request_array[$key]=protect_database($value);
            }
        }
        //unset insecure session values from URLs and forms
        unset($_GET["PHPSESSID"]);
        unset($_POST["PHPSESSID"]);
        return $return_request_array;
    }
    /*
     a protect database function containing regexp to remove unwanted field entries such as javascript or html
   */
    function protect_database($check,$nothing_through=1)
    {
        //don't let anything through
        if ($nothing_through!="html_through")
        {
            $search = array
            (
                '@<script[^>]*?>.*?</script>@si',   // Strip out javascript
                '@<[\/\!]*?[^<>]*?>@si',            // Strip out HTML tags
                '@<style[^>]*?>.*?</style>@siU',    // Strip style tags properly
                '@<![\s\S]*?--[ \t\n\r]*>@'         // Strip multi-line comments
            ); 
        }
        //don't strip html and style, this is a submission from a rich text html editor (TinyMCE)
        else
        {
            $search = array
            (
                '@<script[^>]*?>.*?</script>@si',   // Strip out javascript
                '@<![\s\S]*?--[ \t\n\r]*>@'         // Strip multi-line comments
            ); 
        }
        $output = preg_replace($search, '', $check);
        return mysql_real_escape_string($output);
    }
    /*
     works out whether the entity it is called on should be displayed on the site or not
    */
    function calculate_display($entity,$entity_type)
    {
        if (is_array($entity))
        {
            if ($entity[$entity_type."Display"]==0&&$entity["userID"]!=$_SESSION["user"]["userID"])
                return 0;
            else
            {
                $user=get_user($entity["userID"]);
                if ($user["userDisplay"]==0&&$user["userID"]!=$_SESSION["user"]["userID"])
                    return 0;
                else
                    return 1;
            } 
        }
        else
            return 0;
    }
    function make_password($password,$more_tasty)
    {
        $ylatircestsu="a^#0']x5&U%2(8s£P9%343(8zU%8]f{";
        $add_some=$more_tasty.$password.$ylatircestsu;
        return hash_hmac("sha512",$add_some,$ylatircestsu);
    }
    /*
     users the user agent to work out if this is a mobile device or not
     used in the case of the js screen width check failing due to know js enabled
    */
    function check_device()
    {
        $http_user_agent=protect_database($_SERVER['HTTP_USER_AGENT']);
        $accept=protect_database($_SERVER['HTTP_ACCEPT']);
        switch(true)
        {
            case(preg_match('/ipod/i',$http_user_agent));
            case(preg_match('/iphone/i',$http_user_agent));
            case(preg_match('/android/i',$http_user_agent));
            case(preg_match('/opera mini/i',$user_agent));
            case(preg_match('/blackberry/i',$user_agent));
            case(preg_match('/(pre\/|palm os|palm|hiptop|avantgo|plucker|xiino|blazer|elaine)/i',$user_agent));
            case(preg_match('/(iris|3g_t|windows ce|opera mobi|windows ce; smartphone;|windows ce; iemobile)/i',$user_agent));
            case(preg_match('/(mini 9.5|vx1000|lge |m800|e860|u940|ux840|compal|wireless| mobi|ahong|lg380|lgku|lgu900|lg210|lg47|lg920|lg840|lg370|sam-r|mg50|s55|g83|t66|vx400|mk99|d615|d763|el370|sl900|mp500|samu3|samu4|vx10|xda_|samu5|samu6|samu7|samu9|a615|b832|m881|s920|n210|s700|c-810|_h797|mob-x|sk16d|848b|mowser|s580|r800|471x|v120|rim8|c500foma:|160x|x160|480x|x640|t503|w839|i250|sprint|w398samr810|m5252|c7100|mt126|x225|s5330|s820|htil-g1|fly v71|s302|-x113|novarra|k610i|-three|8325rc|8352rc|sanyo|vx54|c888|nx250|n120|mtk |c5588|s710|t880|c5005|i;458x|p404i|s210|c5100|teleca|s940|c500|s590|foma|samsu|vx8|vx9|a1000|_mms|myx|a700|gu1100|bc831|e300|ems100|me701|me702m-three|sd588|s800|8325rc|ac831|mw200|brew |d88|htc\/|htc_touch|355x|m50|km100|d736|p-9521|telco|sl74|ktouch|m4u\/|me702|8325rc|kddi|phone|lg |sonyericsson|samsung|240x|x320|vx10|nokia|sony cmd|motorola|up.browser|up.link|mmp|symbian|smartphone|midp|wap|vodafone|o2|pocket|kindle|mobile|psp|treo)/i',$user_agent));
            case((strpos($accept,'text/vnd.wap.wml')>0)||(strpos($accept,'application/vnd.wap.xhtml+xml')>0));
            case(isset($_SERVER['HTTP_X_WAP_PROFILE'])||isset($_SERVER['HTTP_PROFILE']));
            case(in_array(strtolower(substr($user_agent,0,4)),array('1207'=>'1207','3gso'=>'3gso','4thp'=>'4thp','501i'=>'501i','502i'=>'502i','503i'=>'503i','504i'=>'504i','505i'=>'505i','506i'=>'506i','6310'=>'6310','6590'=>'6590','770s'=>'770s','802s'=>'802s','a wa'=>'a wa','acer'=>'acer','acs-'=>'acs-','airn'=>'airn','alav'=>'alav','asus'=>'asus','attw'=>'attw','au-m'=>'au-m','aur '=>'aur ','aus '=>'aus ','abac'=>'abac','acoo'=>'acoo','aiko'=>'aiko','alco'=>'alco','alca'=>'alca','amoi'=>'amoi','anex'=>'anex','anny'=>'anny','anyw'=>'anyw','aptu'=>'aptu','arch'=>'arch','argo'=>'argo','bell'=>'bell','bird'=>'bird','bw-n'=>'bw-n','bw-u'=>'bw-u','beck'=>'beck','benq'=>'benq','bilb'=>'bilb','blac'=>'blac','c55/'=>'c55/','cdm-'=>'cdm-','chtm'=>'chtm','capi'=>'capi','cond'=>'cond','craw'=>'craw','dall'=>'dall','dbte'=>'dbte','dc-s'=>'dc-s','dica'=>'dica','ds-d'=>'ds-d','ds12'=>'ds12','dait'=>'dait','devi'=>'devi','dmob'=>'dmob','doco'=>'doco','dopo'=>'dopo','el49'=>'el49','erk0'=>'erk0','esl8'=>'esl8','ez40'=>'ez40','ez60'=>'ez60','ez70'=>'ez70','ezos'=>'ezos','ezze'=>'ezze','elai'=>'elai','emul'=>'emul','eric'=>'eric','ezwa'=>'ezwa','fake'=>'fake','fly-'=>'fly-','fly_'=>'fly_','g-mo'=>'g-mo','g1 u'=>'g1 u','g560'=>'g560','gf-5'=>'gf-5','grun'=>'grun','gene'=>'gene','go.w'=>'go.w','good'=>'good','grad'=>'grad','hcit'=>'hcit','hd-m'=>'hd-m','hd-p'=>'hd-p','hd-t'=>'hd-t','hei-'=>'hei-','hp i'=>'hp i','hpip'=>'hpip','hs-c'=>'hs-c','htc '=>'htc ','htc-'=>'htc-','htca'=>'htca','htcg'=>'htcg','htcp'=>'htcp','htcs'=>'htcs','htct'=>'htct','htc_'=>'htc_','haie'=>'haie','hita'=>'hita','huaw'=>'huaw','hutc'=>'hutc','i-20'=>'i-20','i-go'=>'i-go','i-ma'=>'i-ma','i230'=>'i230','iac'=>'iac','iac-'=>'iac-','iac/'=>'iac/','ig01'=>'ig01','im1k'=>'im1k','inno'=>'inno','iris'=>'iris','jata'=>'jata','java'=>'java','kddi'=>'kddi','kgt'=>'kgt','kgt/'=>'kgt/','kpt '=>'kpt ','kwc-'=>'kwc-','klon'=>'klon','lexi'=>'lexi','lg g'=>'lg g','lg-a'=>'lg-a','lg-b'=>'lg-b','lg-c'=>'lg-c','lg-d'=>'lg-d','lg-f'=>'lg-f','lg-g'=>'lg-g','lg-k'=>'lg-k','lg-l'=>'lg-l','lg-m'=>'lg-m','lg-o'=>'lg-o','lg-p'=>'lg-p','lg-s'=>'lg-s','lg-t'=>'lg-t','lg-u'=>'lg-u','lg-w'=>'lg-w','lg/k'=>'lg/k','lg/l'=>'lg/l','lg/u'=>'lg/u','lg50'=>'lg50','lg54'=>'lg54','lge-'=>'lge-','lge/'=>'lge/','lynx'=>'lynx','leno'=>'leno','m1-w'=>'m1-w','m3ga'=>'m3ga','m50/'=>'m50/','maui'=>'maui','mc01'=>'mc01','mc21'=>'mc21','mcca'=>'mcca','medi'=>'medi','meri'=>'meri','mio8'=>'mio8','mioa'=>'mioa','mo01'=>'mo01','mo02'=>'mo02','mode'=>'mode','modo'=>'modo','mot '=>'mot ','mot-'=>'mot-','mt50'=>'mt50','mtp1'=>'mtp1','mtv '=>'mtv ','mate'=>'mate','maxo'=>'maxo','merc'=>'merc','mits'=>'mits','mobi'=>'mobi','motv'=>'motv','mozz'=>'mozz','n100'=>'n100','n101'=>'n101','n102'=>'n102','n202'=>'n202','n203'=>'n203','n300'=>'n300','n302'=>'n302','n500'=>'n500','n502'=>'n502','n505'=>'n505','n700'=>'n700','n701'=>'n701','n710'=>'n710','nec-'=>'nec-','nem-'=>'nem-','newg'=>'newg','neon'=>'neon','netf'=>'netf','noki'=>'noki','nzph'=>'nzph','o2 x'=>'o2 x','o2-x'=>'o2-x','opwv'=>'opwv','owg1'=>'owg1','opti'=>'opti','oran'=>'oran','p800'=>'p800','pand'=>'pand','pg-1'=>'pg-1','pg-2'=>'pg-2','pg-3'=>'pg-3','pg-6'=>'pg-6','pg-8'=>'pg-8','pg-c'=>'pg-c','pg13'=>'pg13','phil'=>'phil','pn-2'=>'pn-2','pt-g'=>'pt-g','palm'=>'palm','pana'=>'pana','pire'=>'pire','pock'=>'pock','pose'=>'pose','psio'=>'psio','qa-a'=>'qa-a','qc-2'=>'qc-2','qc-3'=>'qc-3','qc-5'=>'qc-5','qc-7'=>'qc-7','qc07'=>'qc07','qc12'=>'qc12','qc21'=>'qc21','qc32'=>'qc32','qc60'=>'qc60','qci-'=>'qci-','qwap'=>'qwap','qtek'=>'qtek','r380'=>'r380','r600'=>'r600','raks'=>'raks','rim9'=>'rim9','rove'=>'rove','s55/'=>'s55/','sage'=>'sage','sams'=>'sams','sc01'=>'sc01','sch-'=>'sch-','scp-'=>'scp-','sdk/'=>'sdk/','se47'=>'se47','sec-'=>'sec-','sec0'=>'sec0','sec1'=>'sec1','semc'=>'semc','sgh-'=>'sgh-','shar'=>'shar','sie-'=>'sie-','sk-0'=>'sk-0','sl45'=>'sl45','slid'=>'slid','smb3'=>'smb3','smt5'=>'smt5','sp01'=>'sp01','sph-'=>'sph-','spv '=>'spv ','spv-'=>'spv-','sy01'=>'sy01','samm'=>'samm','sany'=>'sany','sava'=>'sava','scoo'=>'scoo','send'=>'send','siem'=>'siem','smar'=>'smar','smit'=>'smit','soft'=>'soft','sony'=>'sony','t-mo'=>'t-mo','t218'=>'t218','t250'=>'t250','t600'=>'t600','t610'=>'t610','t618'=>'t618','tcl-'=>'tcl-','tdg-'=>'tdg-','telm'=>'telm','tim-'=>'tim-','ts70'=>'ts70','tsm-'=>'tsm-','tsm3'=>'tsm3','tsm5'=>'tsm5','tx-9'=>'tx-9','tagt'=>'tagt','talk'=>'talk','teli'=>'teli','topl'=>'topl','hiba'=>'hiba','up.b'=>'up.b','upg1'=>'upg1','utst'=>'utst','v400'=>'v400','v750'=>'v750','veri'=>'veri','vk-v'=>'vk-v','vk40'=>'vk40','vk50'=>'vk50','vk52'=>'vk52','vk53'=>'vk53','vm40'=>'vm40','vx98'=>'vx98','virg'=>'virg','vite'=>'vite','voda'=>'voda','vulc'=>'vulc','w3c '=>'w3c ','w3c-'=>'w3c-','wapj'=>'wapj','wapp'=>'wapp','wapu'=>'wapu','wapm'=>'wapm','wig '=>'wig ','wapi'=>'wapi','wapr'=>'wapr','wapv'=>'wapv','wapy'=>'wapy','wapa'=>'wapa','waps'=>'waps','wapt'=>'wapt','winc'=>'winc','winw'=>'winw','wonu'=>'wonu','x700'=>'x700','xda2'=>'xda2','xdag'=>'xdag','yas-'=>'yas-','your'=>'your','zte-'=>'zte-','zeto'=>'zeto','acs-'=>'acs-','alav'=>'alav','alca'=>'alca','amoi'=>'amoi','aste'=>'aste','audi'=>'audi','avan'=>'avan','benq'=>'benq','bird'=>'bird','blac'=>'blac','blaz'=>'blaz','brew'=>'brew','brvw'=>'brvw','bumb'=>'bumb','ccwa'=>'ccwa','cell'=>'cell','cldc'=>'cldc','cmd-'=>'cmd-','dang'=>'dang','doco'=>'doco','eml2'=>'eml2','eric'=>'eric','fetc'=>'fetc','hipt'=>'hipt','http'=>'http','ibro'=>'ibro','idea'=>'idea','ikom'=>'ikom','inno'=>'inno','ipaq'=>'ipaq','jbro'=>'jbro','jemu'=>'jemu','java'=>'java','jigs'=>'jigs','kddi'=>'kddi','keji'=>'keji','kyoc'=>'kyoc','kyok'=>'kyok','leno'=>'leno','lg-c'=>'lg-c','lg-d'=>'lg-d','lg-g'=>'lg-g','lge-'=>'lge-','libw'=>'libw','m-cr'=>'m-cr','maui'=>'maui','maxo'=>'maxo','midp'=>'midp','mits'=>'mits','mmef'=>'mmef','mobi'=>'mobi','mot-'=>'mot-','moto'=>'moto','mwbp'=>'mwbp','mywa'=>'mywa','nec-'=>'nec-','newt'=>'newt','nok6'=>'nok6','noki'=>'noki','o2im'=>'o2im','opwv'=>'opwv','palm'=>'palm','pana'=>'pana','pant'=>'pant','pdxg'=>'pdxg','phil'=>'phil','play'=>'play','pluc'=>'pluc','port'=>'port','prox'=>'prox','qtek'=>'qtek','qwap'=>'qwap','rozo'=>'rozo','sage'=>'sage','sama'=>'sama','sams'=>'sams','sany'=>'sany','sch-'=>'sch-','sec-'=>'sec-','send'=>'send','seri'=>'seri','sgh-'=>'sgh-','shar'=>'shar','sie-'=>'sie-','siem'=>'siem','smal'=>'smal','smar'=>'smar','sony'=>'sony','sph-'=>'sph-','symb'=>'symb','t-mo'=>'t-mo','teli'=>'teli','tim-'=>'tim-','tosh'=>'tosh','treo'=>'treo','tsm-'=>'tsm-','upg1'=>'upg1','upsi'=>'upsi','vk-v'=>'vk-v','voda'=>'voda','vx52'=>'vx52','vx53'=>'vx53','vx60'=>'vx60','vx61'=>'vx61','vx70'=>'vx70','vx80'=>'vx80','vx81'=>'vx81','vx83'=>'vx83','vx85'=>'vx85','wap-'=>'wap-','wapa'=>'wapa','wapi'=>'wapi','wapp'=>'wapp','wapr'=>'wapr','webc'=>'webc','whit'=>'whit','winw'=>'winw','wmlb'=>'wmlb','xda-'=>'xda-',)));
                return "mobile_";
            default;
                return "";
        }
    }
    /*
     builds a link to a category page
     $category - the category
    */
    function build_category_link($category)
    {
        $item_type=get_item_type($category["itemTypeID"]);
        return "/".$item_type["itemType"]."/".$category["categoryUrlAppend"];
    }
    /*
     defines whether a value is an item type, by looking to see if there are any items that have it's type
    */
    function is_item_type($item_type)
    {
        $items=site_query("select * from ItemType where itemType='".$item_type."'","is_item_type()");
        return mysql_num_rows($items);
    }
    function get_item_type($item_type_ID)
    {
        $dev=0;
        if (is_numeric($item_type_ID))
            $item_type_string="select * from ItemType where itemTypeID=".$item_type_ID;
        else
            $item_type_string="select * from ItemType where itemType='".$item_type_ID."'";
        $item_type_query=site_query($item_type_string,"get_item_type()",$dev);
        return mysql_fetch_array($item_type_query);
    }
    /*
     builds a link to an item on the site
     $item - the item to link to
     $type_reference - the type of item
    */
    function build_item_link($item)
    {
        $dev=0;
        dev_dump($item,"item in build_item_link()",$dev);
        $item_type=get_item_type($item["itemTypeID"]);
        if ($item_type["itemTypeID"]==3) $item_type_append=""; else $item_type_append="/".$item_type["itemType"];
        return $item_type_append."/".$item["categoryUrlAppend"]."/".$item["itemUrlAppend"];
    }
    function home_item_panel($item)
    {
        if (is_array($item)) return item_panel($item); else return item_panel(array("itemID"=>0,"itemName"=>"live site item"));
    }
    function h1($in)
    {
        $h1="";
        if ($in["page"]["pageID"]!=1)
        {
            if (is_array($in["user"]))
                $h1.="<h1>".$in["user"]["displayName"];
            else
            {
                if (is_array($in["item"]))
                {
                     if ($in["item"]["itemTypeID"]==3)
                    {              
                         /* $h1.="<h1 class='l_heading'>";
                        $h1.="<span class='circle_icon margin_right' style='background-position: -266px -58px;'></span><span class='l_heading_text'>".$in["item"]["itemName"]."</span>";
               */
                    }
                    elseif ($in["item"]["itemTypeID"]==5)
                    {
                        $h1.="<h1 class='l_heading'>";
                        $h1.="<span class='circle_icon margin_right' style='background-position: -266px -377px;'></span><span class='l_heading_text'>".$in["item"]["itemName"]."</span>";
                    } 
                }
                else
                {
                    if (is_array($in["category"]))
                    {
                        if ($in["category"]["parentID"]==0)
                        {
                            if ($in["category"]["categoryName"]=="Things To Do")
                            {                                
                                $h1.="<h1 class='l_heading'>";
                                $h1.="<span class='circle_icon margin_right' style='background-position: -266px -116px;'></span><span class='l_heading_text'>THINGS TO DO</span>";
                              
                            }
                            else
                            {
                                if ($in["category"]["categoryID"]==502)
                                {
                                    $h1.="<h1 class='l_heading'>";
                                    $h1.="<span class='circle_icon margin_right' style='background-position: -266px -406px;'></span><span class='l_heading_text'>LOVE VIDEO TOURS BLOG</span>";
                             
                                }
                                else
                                {
                                    $h1.="<h1 class='l_heading'>";
                                    $h1.="<span class='circle_icon margin_right' style='background-position: -266px -116px;'></span><span class='l_heading_text'>PLACES TO STAY</span>";
                             
                                }
                            }
                        }
                        else
                        {
                            $category_details=get_category($in["category"]["parentID"]);
                            $h1.="<h1 class='l_heading'>";
                            $h1.="<span class='circle_icon margin_right' style='background-position: -266px -116px;'></span><span class='l_heading_text'>".strtoupper($category_details["categoryName"])." - ".strtoupper($in["category"]["categoryName"])."</span>";
                      
                        }
                        /*if ($in["category"]["categoryName"]=="Things To Do")
                        else
                        $h1.="<h1>".$in["category"]["categoryName"];*/
                    }
                    else
                    {
                        if ($in["page"]["pageID"]==23)
                        {
                            $h1.="<h1><span class='l_heading_text'><span class='circle_icon margin_right' style='background-position: -266px -348px;'></span>".$in["page"]["title"]."</span>";
                        }
                        else
                        {
                            $h1.="<h1><span class='l_heading_text'>".$in["page"]["title"]."</span>";
                        }
                    }
                }
            }
            $h1.="</h1>";
        }
        return $h1;
    }
    function sanitise_for_html($in)
    {
        return utf8_encode(trim( preg_replace( '/\s+/', ' ',$in)));
    }
    function remove_blank_paras($in)
    {
        return str_replace("<p>&nbsp;</p>","",$in);
    }
    function cleanup_tinymce_output($in)
    {
        return preg_replace('/style=.+?/', '', $in);
    }
    function reverse_date($date_in)
    {
        $date_bits=explode("-",$date_in);
        return $date_bits[2]."-".$date_bits[1]."-".$date_bits[0];    
    }
    function entity_not_found()
    {
        $enf="";
        $enf.="<div id='not_found_panel'>";
        $enf.="<p>We're sorry, we can't find the place you are looking for.</p>";
        $blog=mysql_fetch_array(get_items(array("i_type"=>5,"only"=>1,"order_by"=>" itemCreated desc ","limit"=>1)));
        $enf.="<p>Fortunately we can find these beautiful places where you can relax and have fun (or you could read <a href='/love-video-tours-blog/".$blog["itemUrlAppend"]."'>our latest blog post by clicking here</a>):</p>";
        $enf.="</div>";     
        $enf.=item_list(get_items(array("i_type"=>3,"only"=>1,"order_by"=>" Rand() ","limit"=>8,"extra_where"=>" videoSRC!='' ")));   
        return $enf;
    }
    /*
     return the correct category string from the url parameters passed in
    */
    function parse_url_category($category_string)
    {
        $replace_slashes=array();$replace_slashes[]="////";$replace_slashes[]="///";$replace_slashes[]="//";
        $concatenate_category=str_replace($replace_slashes,"/",$category_string);
        if (substr($concatenate_category,0,1)=="/")
        {
            $concatenate_category=substr($concatenate_category,1,strlen($concatenate_category)-1);
        }
        if (substr($concatenate_category,strlen($concatenate_category)-1,1)=="/")
        {
            $concatenate_category=substr($concatenate_category,0,strlen($concatenate_category)-1);
        }
        return $concatenate_category;
    }
    /*
     validates an entered email address
    */
    function validate_email_format($email)
    {
        if(eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$", $email))
            return 1;
        else
            return 0;
    }
    /*
     generate random string
    */
    function random_string($length)
    {
        $chars="abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $char_count=strlen($chars);
        $random_string="";
        for ($i=1;$i<=$length;$i++)
        {
            $char_position=rand(0,$char_count-1);
            $selected_char=substr($chars,$char_position,1);
            $random_string=$random_string.$selected_char;
        }
        return $random_string;
    } 
    /*
     returns whether or the user signed in is super-admin or not
    */
    function super_admin()
    {
        if ($_SESSION["user"]["userID"]==1||$_SESSION["user"]["userID"]==2)
            return 1;
        else
            return 0;
    }
    function show_map($in)
    {
        if ($in["page"]["mapPage"]&&($in["item_type"]["itemTypeID"]==3||$in["page"]["pageID"]==18)) return 1; else return 0;
    }
    /*
     works out if a user may view a page
    */
    function authorised($page)
    {
        //if the page is viewable by anyone, or if a super admin user is signed in, then the page is authorised to be viewed
        if ($page["anonymousUser"]||super_admin())
            return 1;
        else
            if ($page[$_SESSION["user"]["userType"]."User"])
                return 1;
            else
                return 0;            
    }
    function json_sanitise($in)
    {
        return utf8_encode(str_replace("'","",trim( preg_replace( '/\s+/', ' ', $in))));
    }
?>