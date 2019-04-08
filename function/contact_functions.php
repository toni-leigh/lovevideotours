<?php
    function save_contact($contact)
    {
        mysql_query("insert into Contact (message) values ('".strip_tags($contact,"<p>,<strong>,<em>,<ul>,<ol>,<li>,<a>")."')") or die(mysql_error());
    }
    function get_contacts()
    {
        return mysql_query("select * from Contact order by contactTime desc");
    }
?>