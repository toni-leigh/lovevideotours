<?php
    $page_function_array=explode(",",$page["functionList"]);
    if ($page_function_array[0]!="")
        foreach($page_function_array as $function_set)
            include "function/".$function_set."_functions.php";
?>