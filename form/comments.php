<?php
    if (isset($comments)) // then comments created by a controller, initially either user or item controller
    {
        echo "<section id='comments' class='left'>";
        echo dump_include(array("level"=>1,"include_name"=>"comments.php"));
        $comment_count=mysql_num_rows($comments);
        // add comments at the top of the comment list
        if ($entity_type=="item") {$entity_sub_type=$item_type["itemType"];}
        if ($entity_type=="user") {$entity_sub_type=$user["userType"];}
        echo comment_box(array("e_ID"=>$entity_ID,"e_type"=>$entity_type,"e_stype"=>$entity_sub_type,"count"=>$comment_count));
        
        // then display then in reverse chronological order
        if ($comment_count>0)
        {
            $counter=$comment_count;
            while ($comment=mysql_fetch_array($comments))
            {
                echo comment_panel(array("counter"=>$counter,"comment"=>$comment));
                $counter--;
            }
        }
        else
        {
            echo "<span id='no_comments_yet'>no comments yet</span>";
        }
        echo "</section>";
    }
?>