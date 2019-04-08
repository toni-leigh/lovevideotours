<?php
    /*
     returns an array of all the terms in the search term to be searched for
    */
    function return_term_array($search_term)
    {
        $terms=array();
        $prepositions_conjunctions=array("in addition to","in front of","rather than","as if","as long as","as though","according to","because of","by way of","in place of","in regard to","in spite of","instead of","on account of","even if","even though","if","if only","in order that","now that","throughout","whenever","when","wherever","although","whereas","where","while","about","above","across","after","against","around","before","behind","below","beneath","besides","beside","between","beyond","by","down","during","except","from","inside","into","like","near","off","outside","over","since","through","till","toward","under","until","upon","with","without","because","before","once","since","than","though","unless","until","and","but","or","yet","that","at","for","nor","in","of","for","to","so","on","up","out","as");
        $punctuation=array(",",".","/");
        //sets search temrm to lower case for comparison
        $search_term=strtolower($search_term);
        //converts punctuation that may be used to separate into spaces
        $search_term=str_replace($punctuation," ",$search_term);
        //explodes the string into an array, broken on space between terms
        $terms_unstripped=explode(" ",$search_term);
        //removes prepositions and conjunctions
        $terms=array_diff($terms_unstripped,$prepositions_conjunctions);
        return $terms;
    }
    /*
     performs a search on the item table, returning all items which match the search term
     $search_type = PHRASE, AND or OR.
        PHRASE search will search for all of the search terms in the order specified - the raw $_POSTed term should passed in to this
        AND search will search for all of the search terms
        OR search will search for any of the search terms
        $item_types = will restrict the searched items to a certain type(s) - an array of item IDs or just a single integer ID
        $search_term = the string or array to search for
    */
    function perform_item_search($search_term,$search_type="PHRASE",$item_types=0)
    {
        /*$dev=0;
        $search_string="select * from User, Item where ";
        $search_string=$search_string."User.userDisplay=1 and Item.itemDisplay=1 and ";
        $search_string=$search_string."Item.userID=User.userID and ";
        //this bit does item type restriction
        if ($item_types)
        {
            if (is_array($item_types))
            {
                $array_count=count($item_types);
                $counter=1;
                //open
                $search_string=$search_string."(";
                foreach ($item_types as $type)
                {
                    $search_string=$search_string."Item.itemTypeID=".$type;
                    if ($counter==$array_count)
                        $search_string=$search_string.") and ";    
                    else
                        $search_string=$search_string." or ";
                    $counter=$counter+1;
                }
            }
            else
            {
                $search_string=$search_string."Item.itemType=".$item_types." and ";
            }
        }
        //now we do the actually term search
        $search_string=$search_string."(";
        if (is_array($search_term))
        {
            //split term search
            $array_count=count($search_term);
            $counter=1;
            foreach ($search_term as $term)
            {
                $search_string=$search_string."Item.itemName regexp '[[:<:]]".$term."[[:>:]]' or Item.itemTweet regexp '[[:<:]]".$term."[[:>:]]' or Item.itemTags regexp '[[:<:]]".$term."[[:>:]]' ";
                if ($counter==$array_count)
                    $search_string=$search_string."";
                else
                    $search_string=$search_string." or ";
                $counter=$counter+1;
            }
        }
        else
        {
            //phrase search
            $search_string=$search_string."Item.itemName regexp '[[:<:]]".$search_term."[[:>:]]' or Item.itemTweet regexp '[[:<:]]".$search_term."[[:>:]]' or Item.itemTags regexp '[[:<:]]".$search_term."[[:>:]]' ";
        }
        $search_string=$search_string.")";
        dev_dump($search_string,"search string",$dev);
        $search_query=site_query($search_string,"perform_item_search()",$dev);
        return $search_query;*/
        $search_query=get_video_items(array("search_term"=>$search_term));
        return $search_query;
    }
    function perform_comment_search()
    {
        
    }















    function result($item,$result_type,$search_term,$type_reference)
    {
        if ($search_term=="")
        {
            //display the result
            echo  "<div class='item_search_result_row'>";
            echo    "<span class='item_edit_image'>";
            echo      "<a href='".build_item_link($item,$type_reference)."'>";
            $image=get_main_image($item[$type_reference."ID"],$type_reference);
            echo        "<img src='/".$image["tinyThumbnailPath"]."' width='65' height='65'/>";
            echo      "</a>";
            echo    "</span>";
            echo    "<span class='item_search_result_details'>";
            echo        "<span class='item_search_result_title'>";
            echo          "<a href='".build_item_link($item,$type_reference)."'>".$item[$item["typeReference"]."Name"]."</a>";
            echo        "</span>";
            echo        $string;
            echo    "</span>";
            echo "</div>"; 
        }
        else
        {
            //set the case of the strings and find the first instance of the search term
            $lower_case_html=strip_tags(strtolower($item["shortDesc"]));
            $lower_case_st=strtolower($search_term);
            $term_position=strpos($lower_case_html,$lower_case_st,0);
            //if term not found in html then output message and skip term contaext and highlighting
            if ($term_position=="")
            {
            }
            else //!!! - PHP 4 needs extra work to make sure the original string retains its capitalisation but all the comparisons are down in lower case. That way the searcher doesn't need to worry about case !!!
            {
                //set up the display string, add intro and exit dots, ensure a certain amount of context either side of the first instance of the term discovered
                $html_stripped=strip_tags($item["shortDesc"]);
                $string="";
                if ($term_position<100)
                {
                    if (strlen($html_stripped)-$term_position>150)
                    {
                        $string=$string.substr($html_stripped,0,250)."...";
                    }
                    else
                    {
                        $string=$string.substr($html_stripped,0,strlen($html_stripped)-$term_position+100);
                    }
                }
                else
                {
                    if (strlen($html_stripped)-$term_position>150)
                    {
                        $string=$string."...".substr($html_stripped,$term_position-100,250)."...";
                    }
                    else
                    {
                        $string=$string."...".substr($html_stripped,$term_position-100,strlen($html_stripped)-$term_position+100);
                    }
                }
            
                //hunt for the starts and ends of each of the first three instances of the search term in the display string in order to highlight them
                $lc_display=strtolower($string);
                $counter=0;
                $term_starts=array();
                $term_ends=array();
                $term_starts[$counter]=strpos($lc_display,$lower_case_st,0);
                $term_ends[$counter]=$term_starts[$counter]+strlen($search_term);
                $term_found=1;
                $array_len=2;
                while($term_found)
                {
                    $counter=$counter+1;
                    $term_starts[$counter]=strpos($lc_display,$lower_case_st,$term_ends[$counter-1]);
                    if ($term_starts[$counter]=="")
                    {
                        $term_found=0;
                    }
                    else
                    {
                        $term_ends[$counter]=$term_starts[$counter]+strlen($search_term);
                    }
                    if ($counter>1)
                    {
                        $term_found=0;
                        $array_len=3;
                    }
                }
                //css strings to highlight the search terms
                $open_term_span="<span class='highlighted_term'>";
                $close_term_span="</span>";
                //builds a temp string (to become string) which uses the start and end arrays from last section of the term to
                //find the points to split and insert css strings to highlight. uses substr
                $temp_string=substr($string,0,$term_starts[0]);
                $temp_string=$temp_string.$open_term_span.substr($string,$term_starts[0],strlen($search_term)).$close_term_span;
                if ($term_starts[1]=="")
                {
                    $temp_string=$temp_string.substr($string,$term_ends[0],strlen($string)-$term_ends[0]);
                }
                else
                {
                    $temp_string=$temp_string.substr($string,$term_ends[0],$term_starts[1]-$term_ends[0]);
                    $temp_string=$temp_string.$open_term_span.substr($string,$term_starts[1],strlen($search_term)).$close_term_span;
                    if ($term_starts[2]=="")
                    {
                        $temp_string=$temp_string.substr($string,$term_ends[1],strlen($string)-$term_ends[1]);
                    }
                    else
                    {
                        $temp_string=$temp_string.substr($string,$term_ends[1],$term_starts[2]-$term_ends[1]);
                        $temp_string=$temp_string.$open_term_span.substr($string,$term_starts[2],strlen($search_term)).$close_term_span;
                        $temp_string=$temp_string.substr($string,$term_ends[2],strlen($string)-$term_ends[2]);
                    }
                }
                $string="<em>\"".$temp_string."\"</em>";
            }
            //display the result
            echo  "<div class='item_search_result_row'>";
            echo    "<span class='item_edit_image'>";
            echo      "<a href='".build_item_link($item,$type_reference)."'>";
            $image=get_main_image($item[$type_reference."ID"],$type_reference);
            echo        "<img src='/".$image["tinyThumbnailPath"]."' width='65' height='65'/>";
            echo      "</a>";
            echo    "</span>";
            echo    "<span class='item_search_result_details'>";
            echo        "<span class='item_search_result_title'>";
            echo          "<a href='".build_item_link($item,$type_reference)."'>".$item[$item["typeReference"]."Name"]."</a>";
            echo        "</span>";
            echo        $string;
            echo    "</span>";
            echo "</div>";                
        }
    }
?>