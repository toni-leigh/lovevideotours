<?php
    $weight_brackets=get_postages(array("bracket"=>"weight","user_ID"=>$_SESSION["user"]["userID"]));
    $itemcount_brackets=get_postages(array("bracket"=>"item","user_ID"=>$_SESSION["user"]["userID"]));
?>