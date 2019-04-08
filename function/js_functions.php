<?php
    /* a set of functions for cleaning things for js and also for outputting re-usable bits of js from php */
    function open_script()
    {
        $scr="<script type='text/javascript'>";
        $scr.="if (window.focus)\n";
        $scr.="{\n";
        return $scr;
    }
    function close_script()
    {
        $scr="}\n";
        $scr.="</script>";
        return $scr;
    }
?>