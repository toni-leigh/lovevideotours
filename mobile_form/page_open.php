<?php
    if ($page["mapPage"])
        echo "<body onload='initialize()'>";
    else
        echo "<body>";
    if (strlen($page["importEditorElements"]))
        import_editor($page["importEditorElements"]);
    echo "<div id='holder'>";
?>