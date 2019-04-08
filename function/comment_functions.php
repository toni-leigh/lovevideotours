<?php
    function get_comments($in)
    {
        $cs="";
        $cs.="select * from Comment where ";
        $cs.="entityID=".$in["e_ID"]." and entityType='".$in["e_type"]."' order by commentTime desc";
        $cq=site_query($cs,"get_comments()",$dev);
        return $cq;
    }
    function get_comment($comment_ID)
    {
        return mysql_fetch_array(site_query("select * from Comment where commentID=".$comment_ID,"get_comment()"));
    }
    function save_comment($in)
    {
        $ch=addslashes($ch);
        if ($_SESSION["user"]) {$user_ID=$_SESSION["user"]["userID"];} else {$user_ID=0;}
        $cs="";
        $cs.="insert into Comment (entityID,commentingUserID,entityType,commentHTML) values ";
        $cs.="(".$in["e_ID"].",".$user_ID.",'".$in["e_type"]."','".$in["comment"]."')";
        site_query($cs,"save_comment()",$dev);
        $comment_ID=mysql_insert_id();
        return get_comment($comment_ID);
    }
    function comment_panel($in)
    {
        if ($in["comment"]["commentingUserID"]>0)
        {
            $user=get_user($in["comment"]["commentingUserID"]);
            if ($user["displayName"]=="")
                $commenting_user="registered user";
            else
                $commenting_user=username($user);
        }
        else
            $commenting_user="anonymous user";
        $cp="<div class='comment_panel left'>";
        $cp.="<span class='comment_header left'>".$in["counter"].". <span class='comment_user'>&nbsp;by&nbsp;".$commenting_user."</span><span class='comment_date right'>".date("G:i D jS-M-Y",strtotime($in["comment"]["commentTime"]))."</span></span>";
        $cp.="<span class='comment_body left'>".$in["comment"]["commentHTML"]."</span>";
        $cp.="</div>";
        return $cp;
    }
    function comment_box($in)
    {
        $box="<span id='comment_box'>";
        $box.="<form method='post' action=''>";
        $box.="<input type='hidden' name='save_comment_no_js'/>";
        $box.="<input type='hidden' name='entity_ID' value='".$in["e_ID"]."'/>";
        $box.="<input type='hidden' name='entity_type' value='".$in["e_type"]."'/>";
        $box.="<input type='hidden' name='entity_sub_type' value='".$in["e_stype"]."'/>";
        $box.="<textarea id='add_comment_box' name='add_comment_box'></textarea><br/>";
        $box.="<input id='add_comment_submit' class='submit_button button right' type='submit' name='submit' value='add comment'/>";
        $box.="</form>";     
        $box.="</span>";
        $box.=open_script();
        $box.="new_box='<textarea id=\"add_comment_box\" class=\"comment_box\" name=\"add_comment_box\"></textarea>';\n";
        $box.="new_box+='<span id=\"add_comment_submit\" class=\"button right\" onclick=\"saveComment(\'".$in["e_ID"]."\',\'".$in["e_type"]."\',\'".$in["e_stype"]."\')\">add comment</span>';\n";
        $box.="new_box+='<span id=\"latest_comments\"></span>';\n";
        $box.="document.getElementById('comment_box').innerHTML=new_box;\n";
        $box.=close_script();
        return $box;
    }
?>