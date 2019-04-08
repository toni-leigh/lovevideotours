<?php
    function get_postages($in)
    {
        $ps="select * from PostageCharges where userID=".$in["user_ID"]." and postageCalcType='".$in["bracket"]."' and removed=0 order by minValue";
        //dev_dump($ps);
        $pq=site_query($ps,"get_postages()");
        return $pq;
    }
    function bracket_updater($in)
    {
        $bu="<form id='".$in["calc_type"]."'>";
        $bracket_count=mysql_num_rows($in["brackets"]);
        if ($bracket_count==0)
        {
            $bu.="<span class='full_screen_width bracket_message'>[ you have no postage charges set for this type, it will not be available when creating products ]</span>";
        }
        else
        {
            $counter=1;
            while ($bracket=mysql_fetch_array($in["brackets"]))
            {
                $bracket["calc_type"]=$in["calc_type"];
                if ($counter==$bracket_count)
                    $bracket["last_bracket"]=1;
                $bu.="<div class='bracket_row left'>";
                $bu.=bracket_row($bracket);
                $bu.="</div>";
                $last_max_value=$bracket["maxValue"];
                $counter++;
            }
        }
        $bu.="<div id='new_".$in["calc_type"]."' class='bracket_row left'>";
        if ($last_max_value==-1)
        {
            $next_min_value="";
            $next_max_value="";
        }
        else
        {
            $next_min_value=$last_max_value+1;
            $next_max_value="MAX";
        }
        $bu.=bracket_row(array("postageChargeID"=>"new_".$in["calc_type"]."_","minValue"=>$next_min_value,"maxValue"=>$next_max_value));
        $bu.="</div>";
        $bu.="<span class='button right' onclick='add_bracket(\"".$in["calc_type"]."\",\"".$last_max_input."\")'>save</span>";
        $bu.="</form>";
        return $bu;
    }
    function bracket_row($bracket)
    {
        if ($bracket["maxValue"]==-1)
            $max_value="MAX";
        else
            $max_value=$bracket["maxValue"];
        $br="";
        if (is_numeric($bracket["postageChargeID"]))
            $br.="<span class='bracket_status green left'> saved";
        else
            $br.="<span class='bracket_status red left'> new! - unsaved!";
        $br.="</span>";
        $br.="<span class='bracket_label left'>minimum</span>";
        $br.="<input id='".$bracket["postageChargeID"]."min_value' class='min_value text_field left' type='text' name='".$bracket["postageChargeID"]."min_value' value='".$bracket["minValue"]."'/>";
        $br.="<span class='bracket_label left'>maximum</span>";
        $br.="<input id='".$bracket["postageChargeID"]."max_value' class='max_value text_field left' type='text' name='".$bracket["postageChargeID"]."max_value' value='".$max_value."'/>";
        $br.="<span class='cost_label bracket_label left'>&pound;</span>";
        $br.="<input id='".$bracket["postageChargeID"]."cost' class='cost text_field left' type='text' name='".$bracket["postageChargeID"]."cost' value='".$bracket["standardDelivery"]."'/>";
        if (isset($bracket["last_bracket"]))
            $br.="<span class='right' onclick='remove_postage(\"".$bracket["calc_type"]."\",\"".$bracket["postageChargeID"]."\")'><img class='right' src='/img/remove16.png' alt='remove postage'/></span>";
        return $br;
    }
?>