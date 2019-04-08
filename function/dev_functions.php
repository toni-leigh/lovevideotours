<?php
    /*DEBUG FUNCTIONS*/
    /*
     function that outputs development info coherently
     $heading - some info about where the call was made and a description to embolden above the dev output
     $argument - the actual data to output, ususally a variable value or query string
     $developer_mode - whether or not to output anything - calls are activated page wide or function wide allowing developer info to be switched on / off easily
    */
    function dev_dump($argument,$heading="",$dev=1,$query_trigger=0)
    {
        //print_r($_SERVER);
        //allows the whole lot to be switched off
        $all_off=0;
        //allows every dev dump encountered to be switched on
        $all_on=0;
        if (($dev&&!$all_off)||$all_on)
        {
            if (is_array($argument))
            {
                if ($query_trigger)
                {
                    echo "<span class='dd_q_out full_screen_width bold'>".$heading."</span>";
                }
                else
                {
                    echo "<span class='dd_a_out full_screen_width bold'>ARRAY DUMP - ".$heading."</span>";
                    echo "<span class='dd_a_out full_screen_width'>".dirname(__FILE__)."</span>";
                }
                echo "<span class='dd_a_out full_screen_width'>";
                print_array($heading,$argument);
                echo "</span>";
            }
            else
            {
                echo "<span class='dd_v_out full_screen_width bold'>VALUE DUMP - ".$heading."</span>";
                echo "<span class='dd_v_out full_screen_width'>".dirname(__FILE__)."</span>";
                echo "<span class='dd_v_out full_screen_width'>";
                var_dump($argument);
                echo "</span>";
            }
            if (!$query_trigger) {echo "<span style='width:100%;height:10px;float:left;'></span>";}
        }
    }
    function print_array($title,$array){

        if(is_array($array)){

            echo $title."<br/>".
            "||---------------------------------||<br/>".
            "<pre>";
            print_r($array); 
            echo "</pre>".
            "END ".$title."<br/>".
            "||---------------------------------||<br/>";

        }else{
             echo $title." is not an array.";
        }
    }
    function dev_dump_query($query,$heading="",$dev=1)
    {
        //allows the whole lot to be switched off
        $all_off=0;
        //allows every dev dump encountered to be switched on
        $all_on=0;
        $counter=1;
        if (($dev&&!$all_off)||$all_on)
        {
            //echo "!!! *** !!! - OUTPUTS QUERY - THEREFORE YOU NEED mysql_data_seek!";
            echo "<span class='dd_q_out full_screen_width bold'>QUERY DUMP - ".$heading."</span>";
            echo "<span class='dd_q_out full_screen_width'>".dirname(__FILE__)."</span>";
            while ($row=mysql_fetch_array($query))
            {
                echo "<span class='dd_q_out full_screen_width'>";
                dev_dump($row,"Row ".$counter,$dev,1);
                echo "</span>";
                $counter=$counter+1;
            }
        }
    }
    /*
     dumps php include details
     $in["include_name"] - the included file
     $in["level"] - the level, i.e. header is level one, page content is level two etc.
    */
    function dump_include($in)
    {
        $output=0;
        if ($output)
            return "<span class='include_details full_screen_width'>".$in["level"].": ".$in["include_name"]."</span>";
        else
            return null;
    }
    function dump_globals()
    {
        dev_dump($_POST,"Post Values Array",1);
        dev_dump($_GET,"Get Values Array",1);
        dev_dump($_SESSION["user"],"Signed in user Values Array",1);
        dev_dump($_SESSION,"All Session Values Array",1);
        dev_dump($_FILES,"Files Array",1);
        dev_dump($_COOKIE,"Cookie Array",1);
        //dev_dump($_SERVER,"Server Values Array",1);
        //phpinfo();
    }
    /*
     Better formatted or die functionality for mysql
     $place_of_death - where the fatal mysql error occured
     $cause_of_death - the sql string that has caused the death
     $coroners_report - the output of mysql_error()
    */
    function mysql_die($place_of_death,$cause_of_death)
    {
        echo "MySQL has gone wrong in: <strong>".$place_of_death."</strong><br/><br/>The cause is:<br/><br/>".$cause_of_death."<br/><br/>More:<br/><br/> ".mysql_error();
        return;
    }
?>