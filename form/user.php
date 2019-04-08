<?php
    echo dump_include(array("level"=>2,"include_name"=>"user.php"));
    if ($user_display)
    {
        if ($user["userTypeID"]==2)
        {
            include "form/supplier.php";
        }
        elseif ($user["userTypeID"]==1)
        {
            include "form/customer.php";
        }
    }
    else
    {
        echo entity_not_found();
    }
?>