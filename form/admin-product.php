<?php
    echo dump_include(array("level"=>2,"include_name"=>"admin-product.php"));
    if (is_array($item)||$_GET["admin_divert"]=="create")
    {
        echo product_details_nav(array("item"=>$item));
        if ($_SESSION["user"]["userID"]!=$item["userID"]&&$_GET["admin_divert"]!="create"&&!super_admin())
        {
            echo "This is not your item to edit";
        }
        else
        {
            if ($_GET["admin_divert"]!="variations")
            {
                echo "<div id='instruction_panel' class='right'>";
                echo    "<p>These are the basic details for your product.</p>";
                echo    "<p>The <span class='highlight'>product name</span> will be used to create the products unique URL as well as being used as the page title and heading.</p>";
                echo    "<p>The <span class='highlight'>product description fields</span> are used to tell people about your product</p>";
                echo    "<p>The shorted one is used on the small product panels in search or category listings and also as a sub heading on the product page - try to keep to 140 characters, the length of a tweet.</p>";
                echo    "<p>The longer description is where you tell the customer all about the product, be detailed, products with more description sell better - you can format with paragraphs, bold, italics and bullet lists too.</p>";
                echo    "<p>Filling in the <span class='highlight'>product tags</span> field is recommended because this field is used by the product search (descriptions are not). Try to be succinct, choosing a few keywords that describe your product.</p>";
                echo    "<p>Product price is set in the variations of the product. You will set just one variation if the product does't vary.</p>";
                echo "</div>";
                echo "<div id='main_form' class='left'>";
                echo product_form(array("item"=>$item,"i_type"=>$item_type,"err"=>$errors));
                echo "</div>";
            }
            else
            {
                include "form/admin-product_variations.php";
            }
        }
    }
    else
    {
        //create item link
        echo "<h2><a href='/product/create'>Create New Product</a></h2>";
        //list the items here for editing
        echo "<div id='instruction_panel' class='right'>";
        echo    "<p>This is a list of your products. From here you can manage the products you have on the site</p>";
        echo    "<p><span class='highlight'>details</span>: such as the description and category.</p>";
        echo    "<p><span class='highlight'>variations</span>: each product has at least one variation, but can have as many as you need. The price information is stored in here, even if the product only has one variation. You also set item count and / or weight values in here to ensure that the correct postage amount is used for the customer order.</p>";
        echo    "<p><span class='highlight'>images</span>: set the products images in here, including choosing a main image which will be used to display the product in the catalogue, in your back office and also in any social media promotion that may occur.</p>";
        echo    "<p><span class='highlight'>stock</span>: setting a product as 'in stock' or 'out of stock' here will effect how all its variations are displayed on the site.</p>";
        echo "</div>";
        echo "<div id='main_form' class='left'>";
        echo    admin_product_list($items);
        echo "</div>";
    }
?>