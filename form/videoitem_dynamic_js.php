<?php
    echo open_script();
    echo    "var panel_html=new Array();\n";
    echo    "panel_html[0]='".vi_text($item)."';\n";
    echo    "panel_html[1]='".vi_facilities($item)."';\n";
    echo    "panel_html[2]='".vi_updates($updates)."';\n";
    if ($user_details["subscriber"]==1)
        echo "panel_html[3]='".vi_contact_form(array("item"=>$item,"contact_success"=>$contact_success))."';\n";        
    // this js function swicthes the panel contents
    echo    "function change_panel(new_panel)\n";
    echo    "{\n";
    echo        "document.getElementById('item_details_content').innerHTML=panel_html[new_panel];\n";
    echo        "for (i=0;i<=3;i++)\n";
    echo        "   if (i==new_panel)\n";
    echo        "      $('#vi_tab'+i).removeClass('unselected').addClass('selected');\n";
    echo        "   else\n";
    echo        "      $('#vi_tab'+i).removeClass('selected').addClass('unselected');\n";
    echo    "}\n";
    echo close_script();
?>