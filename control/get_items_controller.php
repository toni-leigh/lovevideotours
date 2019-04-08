<?php
    $sparams=array();
    // set the page number variable from the URL
    if (is_numeric($_GET["page"])) $sparams["page_number"]=$_GET["page"]; else $sparams["page_number"]=1;
    
    // set the category restriction
    $sparams["cats"]=array();
    if (is_array($category))
    {
        // master catgeory set in URL, header nav clicked, get sub cats
        $scats=site_query("select * from Category where parentID=".$category["categoryID"]." and itemTypeID=3","get_items_controller.php - get scats");
        while ($scat=mysql_fetch_array($scats)) $sparams["cats"][]=$scat["categoryID"];
    }
    else
    {
        if (isset($_POST["header_dropdown"]))
        {
            // use header attraction dropdowns to set cats
            if ($_POST["at_dropdown"]=="all")
            {
                $scats=site_query("select * from Category where parentID=5 and itemTypeID=3","get_items_controller.php - get scats");
                while ($scat=mysql_fetch_array($scats)) $sparams["cats"][]=$scat["categoryID"];   
            }
            else
            {
                if ($_POST["at_dropdown"]=="Culture") {$sparams["cats"][]=8; $sparams["cats"][]=9; $sparams["cats"][]=11; $sparams["cats"][]=20; $sparams["cats"][]=21;}
                if ($_POST["at_dropdown"]=="Outdoor") {$sparams["cats"][]=10; $sparams["cats"][]=14; $sparams["cats"][]=16; $sparams["cats"][]=18;}
                if ($_POST["at_dropdown"]=="History") {$sparams["cats"][]=12; $sparams["cats"][]=13; $sparams["cats"][]=15; $sparams["cats"][]=17; $sparams["cats"][]=23;}
                if ($_POST["at_dropdown"]=="Towns & Villages") {$sparams["cats"][]=19;}
                if ($_POST["at_dropdown"]=="Visitor Centres") {$sparams["cats"][]=22;}
                if ($_POST["at_dropdown"]=="Leisure") {$sparams["cats"][]=24;$sparams["cats"][]=25;}
            }         
            
            // then add the cats from accommodation
            if ($_POST["ac_dropdown"]=="all")
            {
                $scats=site_query("select * from Category where parentID=3 and itemTypeID=3","get_items_controller.php - get scats");
            }
            else
            {
                if ($_POST["ac_dropdown"]=="Holiday Lets") {$sparams["cats"][]=2;}
                if ($_POST["ac_dropdown"]=="Hotels") {$sparams["cats"][]=4;}
            }
        }
        else
        {
            // categories must be filtered by ajax request
            
            // expect to just set as $ajax_in array
        }
    }
    
    // set the facilities restriction
    $sparams["facs"]=array();
    
    // set the sleeps / price restriction
    $sparams["price"]=null;
    $sparams["sleeps"]=null;
    
    // set the search term restriction
    if (isset($search_term)) $sparams["search_term"]=$search_term; else $sparams["search_term"]=null;
    
    // the order by
    $sparams["order_by"]="";
    
    // get items - always done for list and for map
    $sparams["map"]=0;
    $items=get_video_items($sparams);    
    $sparams["map"]=1;
    $map_items=get_video_items($sparams);
    
    // set the $_SESSION to hold the search values
    $_SESSION["sparams"]=$sparams;
?>