<?php
    echo dump_include(array("level"=>3,"include_name"=>"supplier.php"));
    echo "<h1>".$user["displayName"]."</h1>";
    if ($_SESSION["user"]&&$user["userID"]!=$_SESSION["user"]["userID"])
    {
        echo "<span id='social_button_panel'>";
        echo build_social_action_button(array("e_ID"=>$user["userID"],"e_type"=>"user","a_type"=>"follow"));
        echo "</span>";
    }
    echo "<div id='supplier_main_panel'>";
    echo    "<div id='supplier_tabs'>";
    echo        "<span class='supplier_tab'>";
    echo            "<a href='/".$user["displayName"]."'>intro</a>";
    echo        "</span>";
    echo        "<span class='supplier_tab'>";
    echo            "<a href='/".$user["displayName"]."/products'>products</a>";
    echo        "</span>";
    echo        "<span class='supplier_tab'>";
    echo            "<a href='/".$user["displayName"]."/blog'>blog</a>";
    echo        "</span>";
    echo        "<span class='supplier_tab'>";
    echo            "<a href='/".$user["displayName"]."/activity'>activity</a>";
    echo        "</span>";
    echo        "<span class='supplier_tab'>";
    echo            "<a href='/".$user["displayName"]."/postage'>postage</a>";
    echo        "</span>";
    echo    "</div>";
    echo open_script();
    echo    "var js_tabs='';\n";
    echo    "js_tabs+='<span class=\"supplier_tab\" onclick=\"set_supplier_panel(\'intro\')\">intro';\n";
    echo    "js_tabs+='</span>';\n";
    echo    "js_tabs+='<span class=\"supplier_tab\" onclick=\"set_supplier_panel(\'products\')\">products';\n";
    echo    "js_tabs+='</span>';\n";
    echo    "js_tabs+='<span class=\"supplier_tab\" onclick=\"set_supplier_panel(\'blog\')\">blog';\n";
    echo    "js_tabs+='</span>';\n";
    echo    "js_tabs+='<span class=\"supplier_tab\" onclick=\"set_supplier_panel(\'activity\')\">activity';\n";
    echo    "js_tabs+='</span>';\n";
    echo    "js_tabs+='<span class=\"supplier_tab\" onclick=\"set_supplier_panel(\'postage\')\">postage';\n";
    echo    "js_tabs+='</span>';\n";
    echo    "document.getElementById('supplier_tabs').innerHTML=js_tabs;\n";
    echo close_script();
    echo    "<div id='supplier_content'>";
    //if loads different things based on URL
    if ($_GET["page"]=="products")
    {
        echo "products here";
    }
    elseif ($_GET["page"]=="blog")
    {
        echo "blog here";
    }
    elseif ($_GET["page"]=="activity")
    {
        echo "activities here";
    }
    elseif ($_GET["page"]=="postage")
    {
        echo "postage details here";
    }
    else
    {
        echo "introduction here";        
    }
    echo open_script();
    //set up the html ready for javascript tab use here
    echo "var productHTML='products here js';\n";
    echo "var blogHTML='blog here js';\n";
    echo "var activityHTML='activities here js';\n";
    echo "var postageHTML='postage details js';\n";
    echo "var introHTML='introduction here js';\n";
    echo close_script();
    echo    "</div>";
    echo "</div>";
?>