<?php
    function open_field($in)
    {
        $of="<span class='form_field_header' id='".$in["id"]."_header'><span class='form_field_label'>".$in["label"]."</span><span class='form_field_message' id='".$in["id"]."_message'></span></span>";
        $of.="<span class='form_field_input' id='".$in["id"]."_input'>";
        return $of;
    }
    function text_field($in)
    {
        //definitely remove any slashes from the input value
        $in["val"]=stripslashes(stripslashes($in["val"]));
        $tf="";
        if (isset($in["err"][$in["id"]]))
        {
            //if the field is too long, it can't be empty or filled with a value already taken
            //if it contains non numeric characters too then that will be caught on next pass
            if (isset($in["err"][$in["id"]]["field_length"]))
            {
                $tf.="<span class='form_field_header_error' id='".$in["id"]."_header'><span class='form_field_label'>".$in["label"]."</span><span class='form_field_message' id='".$in["id"]."_message'> is too long - max ".$in["errors"][$in["id"]]["field_length"]." characters</span></span>";
                $tf.="<span class='form_field_input_error' id='".$in["id"]."_input'>";
            }
            else
            {
                //this value must be unique - can't be hit if nothing their or numeric not allowed
                //if numeric not allowed then the value can't have laready been stored
                if (isset($in["err"][$in["id"]]["taken"]))
                {
                    $tf.="<span class='form_field_header_error' id='".$in["id"]."_header'><span class='form_field_label'>".$in["label"]."</span><span class='form_field_message' id='".$in["id"]."_message'> has been taken</span></span>";
                    $tf.="<span class='form_field_input_error' id='".$in["id"]."_input'>";
                }
                else
                {
                    //if there is a not numeric error then the field can't be flagged as empty
                    //the field will be flagged as not numeric if empty
                    if (isset($in["err"][$in["id"]]["not_numeric"]))
                    {
                        $tf.="<span class='form_field_header_error' id='".$in["id"]."_header'><span class='form_field_label'>".$in["label"]."</span><span class='form_field_message' id='".$in["id"]."_message'> must be numeric</span></span>";
                        $tf.="<span class='form_field_input_error' id='".$in["id"]."_input'>";
                    }
                    else
                    {
                        //finally, uniqueness test
                        if (isset($in["err"][$in["id"]]["notUnique"]))
                        {
                            $tf.="<span class='form_field_header_error' id='".$in["id"]."_header'><span class='form_field_label'>".$in["label"]."</span><span class='form_field_message' id='".$in["id"]."_message'> must be unique</span></span>";
                            $tf.="<span class='form_field_input_error' id='".$in["id"]."_input'>";
                        }
                        else
                        {
                            $tf.="<span class='form_field_header_error' id='".$in["id"]."_header'><span class='form_field_label'>".$in["label"]."</span><span class='form_field_message' id='".$in["id"]."_message'> must be filled</span></span>";
                            $tf.="<span class='form_field_input_error' id='".$in["id"]."_input'>";
                        }
                    }
                }
            } 
        }
        else
        {
            //no errors, we also don't show a label for a hidden field
            if ($in["type"]!="hidden")
            {
                $tf.=open_field($in);
            }
        }
        //quotation marks reversed for output of names with apostrophies
        if ($in["type"]=="text")
            $tf.='<input id="'.$in["id"].'" class="text_field" type="text" name="'.$in["id"].'" value="'.$in["val"].'"/>';
        elseif ($in["type"]=="textarea")
            $tf.='<textarea id="'.$in["id"].'" class="basic_textarea" name="'.$in["id"].'">'.$in["val"].'</textarea>';
        elseif ($in["type"]=="tinymce")
            $tf.='<textarea id="'.$in["id"].'" class="tinymce_textarea" name="'.$in["id"].'">'.$in["val"].'</textarea>';
        else
            $tf.='<input id="'.$in["id"].'" type="hidden" name="'.$in["id"].'" value="'.$in["val"].'"/>';
        $tf.="</span>";
        return $tf;
    }
    function email_field($in)
    {
        $emf="";
        if (isset($in["err"]["email"]))
        {
            if (isset($in["err"]["email"]["invalid"]))
            {
                $emf.="<span class='form_field_header_error' id='email_header'><span class='form_field_label'>Email Address</span><span class='form_field_message' id='email_message'> is invalid</span></span>";
                $emf.="<span class='form_field_input_error' id='email_input'>";
            }
            elseif (isset($in["err"]["email"]["taken"]))
            {
                $emf.="<span class='form_field_header_error' id='email_header'><span class='form_field_label'>Email Address</span><span class='form_field_message' id='email_message'> has been taken</span></span>";
                $emf.="<span class='form_field_input_error' id='email_input'>";
            }
        }
        else
        {
            $emf.="<span class='form_field_header' id='email_header'><span class='form_field_label'>Email Address</span><span class='form_field_message' id='email_message'></span></span>";
            $emf.="<span class='form_field_input' id='email_input'>";
        }
        $emf.="<input id='email' class='email_field' type='text' name='email' value='".$in["email"]."'/>";
        $emf.="</span>";
        return $emf;
    }
    function password_field($in)
    {
        $pwf="";
        if ($in["err"]["password"])
        {
            $pwf.="<span class='form_field_header_error' id='password_header'><span class='form_field_label'>Password</span><span class='form_field_message' id='email_message'> must be present</span></span>";
            $pwf.="<span class='form_field_input_error' id='password_input'>";
        }
        else
        {
            $pwf.="<span class='form_field_header' id='password_header'><span class='form_field_label'>Password</span></span>";
            $pwf.="<span class='form_field_input' id='password_input'>";
        }
        $pwf.="<input id='password' class='password_field' type='password' name='password' value='".$in["password"]."'/>";
        $pwf.="</span>";
        $pwf.=open_script();
        //if js then first create the checkbox to show password
        $pwf.="document.getElementById('password_input').innerHTML='<input id=\"password_checkbox\" type=\"checkbox\" onclick=\"show_password(1)\"/><span id=\"password_show_message\">".$in["show_message"]."</span><input id=\"password\" class=\"password_field\" type=\"password\" name=\"password\" value=\"".$in["password"]."\"/>';\n";
        //then make the function to toggle on click
        $pwf.="function show_password(show)\n";
        $pwf.="{\n";
        $pwf.="if (show==1) {show_value=0;input_type='text';checked=' checked ';}\n";
        $pwf.="else {show_value=1;input_type='password';checked='';}\n";
        $pwf.="document.getElementById('password_input').innerHTML='<input id=\"password_checkbox\" type=\"checkbox\" '+checked+' onclick=\"show_password('+show_value+')\"/><span id=\"password_show_message\">".$in["show_message"]."</span><input id=\"password\" class=\"password_field\" type=\"'+input_type+'\" name=\"password\" value=\"'+document.getElementById('password').value+'\"/>';\n";
        $pwf.="document.".$in["form_name"].".password.focus();\n";
        $pwf.="}\n";
        $pwf.=close_script();
        return $pwf;
    }
    /*
     displays a select drop down box using $select_set to iterate over
     $select_set can be either array or db query
     $any - puts an $any value in to the dropdown with the value 'any'
    */
    function select_set($in)
    {
        $ssf=open_field($in);
        if ($in["id"]=="category")
        {
            $ssf.="<select name='categoryID'>"; 
            if (isset($in["any"]))
                $ssf.="<option value='any'>".$in["any"]."</option>";
            //this is a category select so we use the categories function to build it
            while ($opt=mysql_fetch_array($in["set"]))
                $ssf.=get_categories($opt[$in["id"]."ID"],1,$in["val"],"select");
            $ssf.="</select>";
        }
        else
        {
            $ssf.="<select name='".$in["id"]."'>"; 
            if (isset($in["any"]))
                $ssf.="<option value='any'>".$in["any"]."</option>";        
            if (is_array($in["set"]))
            {
                //a basic array is input - the array will simply have values in it to use
                foreach ($in["set"] as $opt_id => $opt_name)
                {
                    $ssf.="<option value='".$opt_id."' ";
                    if ($opt_id==$in["val"]) $ssf.="selected='selected'"; 
                    if (strpos($in["id"],"price")||strpos($in["id"],"price")===0)
                        $option=format_price($opt_name);
                    else
                        $option=$opt_name;                       
                    $ssf.=">".$option."</option>";
                }
            }
            else
            {
                //a query is input - could be complicated entity query or simple value only query
                while ($opt=mysql_fetch_array($in["set"]))
                {
                    //part of a complicated result set of entities, has names and IDs, such as Items, Users, Categories
                    $ssf.="<option value='".$opt[$in["query_ID_field"]]."' ";
                    if ($opt[$in["query_ID_field"]]==$in["val"]) $ssf.="selected='selected'";
                    if (strpos($in["id"],"price")||strpos($in["id"],"price")===0)
                        $option=format_price($opt[$in["query_name_field"]]);
                    else
                        $option=$opt[$in["query_name_field"]];
                    $ssf.=">".$option."</option>";
                }
            }
            $ssf.="</select>";
        }
        $ssf.="</span>";
        return $ssf;
    }
    /* the set to be shown is always a simple query */
    /* the checked set can be a link table query or the $_POST array */
    function checkbox_set($in)
    {
        $cbf=open_field($in);
        $counter=0;
        $box_count=mysql_num_rows($in["set"]);
        while ($cb=mysql_fetch_array($in["set"]))
        {
            $js_state=0; //if the box is checked then this is used to tell the js function that responds to click that it is clicked
            $box_checked="";
            $css_checked="";
            if ($counter%$in["grid_width"]==0) echo "<span class='checkbox_row".$in["grid_width"]."wide'>";
            /* find out if this check box is checked */
            for ($i=0;$i<count($in["val"]);$i++)
                if (is_array($in["val"][$i]))
                    /* the set of values is a 2D array, reflecting a link table query */
                    if ($in["val"][$i][$in["query_ID_field"]]==$cb[$in["query_ID_field"]])
                        $checked=1; 
                else
                    /* the set of values is a 1D array, reflecting a set of $_POST values */
                    if ($in["val"][$i]==$cb[$in["query_ID_field"]])
                        $checked=1;
            if ($checked)
            {
                $box_checked=" checked='checked'";
                $css_checked="_checked"; 
                $js_state=1;
            }
            $cbf.="<span class='".$in["id"]."_checkbox_panel".$css_checked."' id='".$in["id"]."_".$cb[$in["query_ID_field"]]."'>";
            $cbf.="<input type='hidden' name='".$in["id"]."_".$cb[$in["query_ID_field"]]."_toggle' id='".$in["id"]."_".$cb[$in["query_ID_field"]]."_toggle' value='".$js_state."'/>";
            $cbf.="<input type='checkbox' name='".$cb[$in["query_ID_field"]].$in["id"]."'".$box_checked." onclick='".$in["id"]."_checkbox_click(\"".$in["id"]."_".$cb[$in["query_ID_field"]]."\")'/>";
            $cbf.="<span class='".$in["id"]."_checkbox_label'>";
            $cbf.=$cb[$in["query_name_field"]];
            $cbf.="</span>";
            $cbf.="</span>";
            if ($counter%$in["grid_width"]<$in["grid_width"]-1) echo "<span class='".$in["id"]."_checkbox_panel_spacer'></span>";
            if ($counter%$in["grid_width"]==$in["grid_width"]-1||$counter+1==$box_count) echo "</span>";
            $counter=$counter+1;
        } 
        $cbf.="</span>";
        return $cbf;
    }
    function date_picker($in)
    {
        if (isset($in["err"][$in["id"]]))
        {
            $df="<span class='form_field_header_error' id='".$in["id"]."_header'><span class='form_field_label'>".$in["label"]."</span><span class='form_field_message' id='".$in["id"]."_message'> is incorrect</span></span>";
            $df.="<span class='form_field_input_error' id='".$in["id"]."_input'>";
        }
        else
        {
            $df=open_field($in);
        }
        $df.='<input type="text" name="'.$in["id"].'" id="'.$in["id"].'" value="'.$in["val"].'"/>';
        $df.="</span>";
        $df.=open_script();
        $df.="$('#".$in["id"]."').datepicker({ dateFormat: 'dd-mm-yy' });";
        $df.=close_script();
        return $df;
    }
    /*
     displays a date of birth field
     $min_age - is the minimum allowed age
    */
    function date_range_select($in)
    {
        $date_split=explode("-",$in["val"]);
        $years=array();$months=array();$days=array();
        $years[]=$year;
        $months[]="January";$months[]="February";$months[]="March";$months[]="April";$months[]="May";$months[]="June";
        $months[]="July";$months[]="August";$months[]="September";$months[]="October";$months[]="November";$months[]="December";
        if ($in["err"][$in["id"]])
        {
            $df="<span class='form_field_header_error' id='".$in["id"]."_header'><span class='form_field_label'>".$in["label"]."</span><span class='form_field_message' id='".$in["id"]."_message'> - please check the date corresponds with the month</span></span>";
            $df.="<span class='form_field_input_error' id='".$in["id"]."_input'>";
        }
        else
        {
            $df=open_field($in);
        }
        
        $df.="<span id='".$in["id"]."_day'>";
        $df.="<select name='".$in["id"]."_day_picker'>";
        for ($day=1;$day<=31;$day++)
        {
            if ((int)$date_split[2]==$day)
                $df.="<option value='".$day."' selected='selected'>".$day."</option>";
            else
                $df.="<option value='".$day."'>".$day."</option>";
        }
        $df.="</select>";
        $df.="</span>";
        
        $df.="<span id='".$in["id"]."_month'>";
        $df.="<select name='".$in["id"]."_month_picker'>";
        for ($month=1;$month<=12;$month++)
            if ((int)$date_split[1]==$month)
                $df.="<option value='".$month."' selected='selected'>".$months[$month-1]."</option>";
            else
                $df.="<option value='".$month."'>".$months[$month-1]."</option>";
        $df.="</select>";
        $df.="</span>";
        
        $df.="<span id='".$in["id"]."_year'>";
        $df.="<select name='".$in["id"]."_year_picker'>";
        for ($year=$in["end"];$year>=$in["start"];$year--)
            if ((int)$date_split[0]==$year)
                $df.="<option value='".$year."' selected='selected'>".$year."</option>";
            else
                $df.="<option value='".$year."'>".$year."</option>";
        $df.="</select>";
        $df.="</span>";        
        $df.="</span>";
        return $df;
    }
    /*
     generates a TinyMCE html text editor back office item edit
     $elements - is a list of the textarea form elements to apply the TinyMCE to
    */
    function import_editor($elements,$type="simple")
    {
        if ($type=="simple")
        {
            echo "<script type='text/javascript' src='/tiny_mce/tiny_mce.js'></script>";
            echo "<script type='text/javascript'>";
            echo "    tinyMCE.init({";
            echo "    width: '678',";
            echo "    mode : 'exact',";
            echo "    theme : 'advanced',";
            echo "    body_class : 'tiny_mince_body',";
            echo "    content_css : '/form/tiny_mce.css',"; 
            echo "    theme_advanced_buttons1 : 'bold,italic,underline,bullist,undo,|,link,unlink',";
            echo "    theme_advanced_buttons2 : '',";
            echo "    theme_advanced_buttons3 : '',";
            echo "    elements : '".$elements."'";
            echo "    });";
            echo "</script>";
        }
        else
        {
            echo "<script type='text/javascript' src='/tiny_mce/tiny_mce.js'></script>";
            echo "<script type='text/javascript'>";
            echo "    tinyMCE.init({";
            echo "    mode : 'exact',";
            echo "    theme : 'advanced',";
            echo "    plugins : 'media',";
            echo "    body_class : 'tiny_mince_body',";
            echo "    content_css : '/form/tiny_mce.css',"; 
            echo "    theme_advanced_buttons1 : 'newdocument,|,bold,italic,underline,|,justifyleft,justifycenter,justifyright,fontselect,fontsizeselect,formatselect',";
            echo "    theme_advanced_buttons2 : 'cut,copy,paste,|,bullist,numlist,|,outdent,indent,|,undo,redo,|,link,unlink,anchor,image,media,|,code,preview,|,forecolor,backcolor',";
            echo "    theme_advanced_buttons3 : 'insertdate,inserttime,|,spellchecker,advhr,removeformat,|,sub,sup,|,charmap,emotions',";
            echo "    external_image_list_url : '/img/".$_SESSION["user"]["userName"]."/image_list.js',";
            echo "    document_base_url : 'http://".$_SERVER["HTTP_HOST"]."',";
            echo "    elements : '".$elements."'";
            echo "    });";
            echo "</script>";
        }
    }
    /*
     converts a user string of text (that probably names a site object) into an url appendable value
     $in - the string in
    */
    function rework($in)
    {
        return strtolower(str_replace("--","-",str_replace(" ","-",preg_replace("/[^0-9a-z ]+/i","",trim(stripslashes($in))))));
    } 
    function check_length($value,$length)
    {
        if (strlen($value)>$length)
            return 1;
        else
            return 0;
    }
    function check_unique_string($value,$table,$column,$avoid_column,$avoid_ID)
    {
        if (!is_numeric($avoid_ID)) $avoid_ID=0;
        $check=site_query("select * from ".$table." where ".$column."='".$value."' and ".$avoid_column."!=".$avoid_ID,"check_unique_string()");
        if (mysql_num_rows($check)>0)
            return 1;
        else
            return 0;
    }
?>