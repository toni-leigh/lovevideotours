<?php
    echo dump_include(array("level"=>"Fm-2","include_name"=>"form-testing.php"));
    //$form_values - is set by the submitted form, or initialised if the form is to edit some item
    echo "<div id='form_test' class='form_centre'>";
    echo    "<form name='testing_form' method='post' action='".$_SERVER["REDIRECT_URL"]."'>";
    /* text fields */
    echo        "<div class='form_partition'>";
    echo            "<h2>Basic Text Fields</h2>";
    echo            text_field(array("id"=>"hidden_field","label"=>"Hidden Field","val"=>$form_values["hidden_field"],"err"=>$errors,"type"=>"hidden"));
    echo            text_field(array("id"=>"text_field","label"=>"Text Field","val"=>$form_values["text_field"],"err"=>$errors,"type"=>"text"));
    echo            text_field(array("id"=>"text_area_field","label"=>"Text Area Field","val"=>$form_values["text_area_field"],"err"=>$errors,"type"=>"textarea"));
    echo            text_field(array("id"=>"tinymce_field","label"=>"TinyMCE Field","val"=>$form_values["tinymce_field"],"err"=>$errors,"type"=>"tinymce"));
    echo        "</div>";
    /* login / sign-up fields */
    echo        "<div class='form_partition'>";
    echo            "<h2>Login / Signup Fields</h2>";
    echo            email_field(array("email"=>$form_values["email"],"err"=>$errors));
    echo            password_field(array("password"=>$form_values["password"],"err"=>$errors,"form_name"=>"testing_form","show_message"=>"click here to show password as plain text"));
    echo        "</div>";
    
    /* select fields */
    echo        "<div class='form_partition'>";
    echo            "<h2>Select Set Fields</h2>";
    $p=array(
        "id"=>"category",
        "label"=>"Category Select",
        "set"=>get_master_categories(1),
        "val"=>$form_values["item_category"],
        "err"=>$errors
    );
    echo            select_set($p);
    $p=array(
        "id"=>"array",
        "label"=>"Array Based Select",
        "set"=>array(
            0=>"lemon",
            1=>"ginger",
            2=>"chilli",
            3=>"garlic",
            4=>"turmeric"
        ),
        "val"=>$form_values["item_category"],
        "err"=>$errors
    );
    echo            select_set($p);
    $p=array(
        "id"=>"query",
        "label"=>"Query Based Select (also demonstrates 'any' capability)",
        "set"=>get_items(1),
        "val"=>$form_values["item_category"],
        "err"=>$errors,
        "any"=>"please choose",
        "query_ID_field"=>"itemID",
        "query_name_field"=>"itemName"
    );
    echo            select_set($p);
    $p=array(
        "id"=>"price",
        "label"=>"Query Based Variation Select",
        "set"=>get_item_variations(get_item(1,1)),
        "val"=>$form_values["price"],
        "err"=>$errors,
        "any"=>"any",
        "query_ID_field"=>"variationID",
        "query_name_field"=>"price"
    );
    echo            select_set($p);
    echo        "</div>";
    
    /* check box fields */
    echo        "<div class='form_partition'>";
    echo            "<h2>Check Box Fields</h2>";
    $p=array(
        "id"=>"simple_query",
        "label"=>"checked from \$_POST, or simple query (unlikely)",
        "set"=>get_items(1),
        "val"=>$form_values,
        "query_ID_field"=>"itemID",
        "query_name_field"=>"itemName",
        "grid_width"=>5
    );
    echo            checkbox_set($p);
    $p=array(
        "id"=>"link_table_query",
        "label"=>"checked from link table, such as if n~m modelled",
        "set"=>get_items(1),
        "val"=>$form_values,
        "query_ID_field"=>"itemID",
        "query_name_field"=>"itemName",
        "grid_width"=>7
    );
    echo            checkbox_set($p);
    echo        "</div>";
    /* file upload fields */
    echo        "<div class='form_partition'>";
    echo            "<h2>File Upload Fields</h2>";
    echo        "</div>";
    /* plupload field */
    echo        "<div class='form_partition'>";
    echo            "<h2>PLUpload Field</h2>";
    echo        "</div>";
    /* date fields */
    echo        "<div class='form_partition'>";
    echo            "<h2>Date Fields</h2>";
    $p=array(
        "id"=>"date_picker",
        "label"=>"date picker",
        "val"=>$form_values["date_picker"],
        "err"=>$errors,
    );
    echo            date_picker($p);
    $p=array(
        "id"=>"date_select",
        "label"=>"date modelled as select",
        "val"=>$form_values["date_select"],
        "err"=>$errors,
        "start"=>"1970",
        "end"=>"2069"
    );
    echo            date_range_select($p);
    echo        "</div>";   
    /* map input field */
    echo        "<div class='form_partition'>";
    echo            "<h2>Map Input Field</h2>";
    echo        "</div>";
    echo    "</form>";
    echo "</div>";
?>