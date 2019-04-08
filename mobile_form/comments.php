<?php
    echo "<section id='comments'>";
    echo dump_include(array("level"=>1,"include_name"=>"comments.php"));
    if ($comments)
    {
        if (mysql_num_rows($comments)>0)
        {
            $counter=1;
            while ($comment=mysql_fetch_array($comments))
            {
                echo $counter.". ".$comment["commentHTML"]."<br/>";
                $counter=$counter+1;
            }
        }
        else
        {
            echo "no comments yet<br/>";
        }
        if ($entity_type=="item") {$entity_sub_type=$item_type["itemType"];}
        if ($entity_type=="user") {$entity_sub_type=$user["userType"];}
        echo build_comment_box(array("entityID"=>$entity_ID,"entityType"=>$entity_type,"entitySubType"=>$entity_sub_type));
    }
    echo "</section>";
?>