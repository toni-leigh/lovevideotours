<?php
    echo dump_include(array("level"=>3,"include_name"=>"blog.php"));
    if (is_array($item))
    {
    }
    else
    {
        echo blog_list(array("blogs"=>$items,"width"=>4));
    }
?>