<?php
?>
    <script type="text/javascript">
    $(document).ready(function () {
        $('img#header_image').imgAreaSelect({
            handles: true
        });
    });
    </script>
<?php
//Only display the javacript if an image has been uploaded
if(strlen($large_photo_exists)>0){
    $current_large_image_width = getWidth($scale_locations["pageScale"]);
    $current_large_image_height = getHeight($scale_locations["pageScale"]);?>
<script type="text/javascript">
function preview(img, selection) {
    var scaleX = <?php echo $square_sizes["mediumSquare"];?> / selection.width;
    var scaleY = <?php echo $square_sizes["mediumSquare"];?> / selection.height;

    $('#thumbnail + div > img').css({
        width: Math.round(scaleX * <?php echo $current_large_image_width;?>) + 'px',
        height: Math.round(scaleY * <?php echo $current_large_image_height;?>) + 'px',
        marginLeft: '-' + Math.round(scaleX * selection.x1) + 'px',
        marginTop: '-' + Math.round(scaleY * selection.y1) + 'px'
    });
    $('#x1').val(selection.x1);
    $('#y1').val(selection.y1);
    $('#x2').val(selection.x2);
    $('#y2').val(selection.y2);
    $('#w').val(selection.width);
    $('#h').val(selection.height);
}

$(document).ready(function () {
    $('#save_thumb').click(function() {
        var x1 = $('#x1').val();
        var y1 = $('#y1').val();
        var x2 = $('#x2').val();
        var y2 = $('#y2').val();
        var w = $('#w').val();
        var h = $('#h').val();
        if(x1=="" || y1=="" || x2=="" || y2=="" || w=="" || h==""){
            alert("You must make a selection first");
            return false;
        }else{
            return true;
        }
    });
});

$(window).load(function () {
    $('#thumbnail').imgAreaSelect({ aspectRatio: '1:<?php echo $square_sizes["mediumSquare"]/$square_sizes["mediumSquare"];?>', onSelectChange: preview });
});

</script>
<?php }
    if(strlen($error)>0)
    {
        echo "<ul><li><strong>Error!</strong></li><li>".$error."</li></ul>";
    }
    if(strlen($large_photo_exists)>0 && strlen($thumb_photo_exists)>0)
    {
    }
    else
    {
        if(strlen($large_photo_exists)>0)
        {
            //get the submitted image name if present
            if (strlen($_POST["imageName"])>0)
                $item_name=$_POST["imageName"];
            echo "<h4>Crop '".$item_name."' Image</h4>";
            echo "<div align='center'>";
            echo "<span id='image_message'>The larger the area selected below the better the image will look on the site.</span>";
            echo "<img src='/".$scale_locations["pageScale"]."' style='float: left; margin-right: 10px;' id='thumbnail' alt='Create Thumbnail' />";
            echo    "<br style='clear:both;'/>";
            echo    "<form name='thumbnail' action='".$action_reload_URL."' method='post'>";
            echo        "<input type='hidden' name='x1' value='' id='x1' />";
            echo        "<input type='hidden' name='y1' value='' id='y1' />";
            echo        "<input type='hidden' name='x2' value='' id='x2' />";
            echo        "<input type='hidden' name='y2' value='' id='y2' />";
            echo        "<input type='hidden' name='w' value='' id='w' />";
            echo        "<input type='hidden' name='h' value='' id='h' />";
            echo        "<input type='submit' name='upload_thumbnail' value='Save Image' id='save_thumb' />";
            echo        "<input type='text' name='image_name'/>";
            echo    "</form>";
            echo "</div>";
            echo "<h4>Wrong Image?</h4>";
            echo "<p><a href='".$action_reload_URL."/remove'>Choose Another Image</a></p>";
        }
        else
        {
            echo "<h4>'".$item_name."' Images</h4>";
            $item_images=get_images($entity_type,$item_ID);
            $image_count=mysql_num_rows($item_images);
            $counter=0;
            echo "<form method='post' action='".$action_reload_URL."'>";
            echo    "<input type='hidden' name='save_main'/>";
            while ($item_image=mysql_fetch_array($item_images))
            {
                if ($counter%3==0) echo "<span class='item-row'>";
                echo "<span class='item-panel'>";
                echo    "<input type='radio' name='main' value='".$item_image["imageID"]."' ";
                if ($item_image["main"]==1)
                    echo " checked ";
                echo    "> Main";
                echo    "<img src='/".$item_image["mediumSquarePath"]."' alt='' width='198' height='198'/>";
                echo    "<input type='checkbox' name='".$item_image["imageID"]."remove'/> Remove";
                echo "</span>";
                if ($counter%3<2) echo "<span class='item-panel-spacer'></span>";
                if ($counter%3==2||$counter+1==$image_count) echo "</span>";
                $counter=$counter+1;
            }
            echo    "<input type='submit' name='submit' value='Save mains / Delete'/>";
            echo "</form>";
            echo "<h4>Upload New '".$item_name."' Image</h4>";
            echo "<form name='photo' enctype='multipart/form-data' action='".$action_reload_URL."' method='post'>";
            echo     "Browse for new ".$item_name." Image File<br/>Select File: <input type='file' name='image' size='30' />";
            //image name field shown if loose image, other wise the image name is the item name
            if ($loose_image)
                echo "<br/>Name Image: <input type='text' name='imageName'/> (recommended)<br/>";
            echo     "<input type='submit' name='upload' value='Upload' />";
            echo "</form>";
            echo "<br/>";
        }
    }
?>
