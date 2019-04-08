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
    $current_large_image_width = getWidth($full_location);
    $current_large_image_height = getHeight($full_location);?>
<script type="text/javascript">
function preview(img, selection) {
    var scaleX = <?php echo $item["thumbnailSize"];?> / selection.width;
    var scaleY = <?php echo $item["thumbnailSize"];?> / selection.height;

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
    $('#thumbnail').imgAreaSelect({ aspectRatio: '1:<?php echo $item["thumbnailSize"]/$item["thumbnailSize"];?>', onSelectChange: preview });
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
            echo "<h4>Crop '".$item["itemName"]."' Image</h4>";
            echo "<div align='center'>";
            echo "<span id='image_message'>The larger the area selected below the better the image will look on the site.</span>";
            echo "<img src='/".$full_location."' style='float: left; margin-right: 10px;' id='thumbnail' alt='Create Thumbnail' />";
            echo    "<br style='clear:both;'/>";
            if ($loose_image)
                echo    "<form name='thumbnail' action='/image-upload/loose-image' method='post'>";
            else
                echo    "<form name='thumbnail' action='/image-upload/".$item["itemType"]."/".$item["itemID"]."' method='post'>";
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
            if ($loose_image)
                echo "<p><a href='/image-upload/loose-image?remove_image'>Choose Another Image</a></p>";
            else
                echo "<p><a href='/image-upload/".$item["itemType"]."/".$item["itemID"]."?remove_image'>Choose Another Image</a></p>";
        }
        else
        {
            if ($loose_image)
                $item_images=get_images("loos",0);
            else
                $item_images=get_images("item",$item["itemID"]);
            $image_count=mysql_num_rows($item_images);
            $counter=0;
            if ($loose_image)
                echo "<form method='post' action='/image-upload/loose-image'>";
            else
                echo "<form method='post' action='/image-upload/".$item["itemType"]."/".$item["itemID"]."'>";
            echo    "<input type='hidden' name='save_main'/>";
            while ($item_image=mysql_fetch_array($item_images))
            {
                if ($counter%3==0) echo "<span class='item-row'>";
                echo "<span class='item-panel'>";
                echo    "<input type='radio' name='main' value='".$item_image["imageID"]."' ";
                if ($item_image["main"]==1)
                    echo " checked ";
                echo    "> Main";
                echo    "<a href='/image-upload/".$item["itemType"]."/".$item["itemID"]."/".$item_image["imageID"]."'><img src='/".$item_image["thumbnailPath"]."' alt='' width='198' height='198'/></a>";
                echo    "<input type='checkbox' name='".$item_image["imageID"]."remove'/> Remove";
                echo "</span>";
                if ($counter%3<2) echo "<span class='item-panel-spacer'></span>";
                if ($counter%3==2||$counter+1==$image_count) echo "</span>";
                $counter=$counter+1;
            }
            echo    "<input type='submit' name='submit' value='Save mains / Delete'/>";
            echo "</form>";
            echo "<h4>Upload New '".$item["itemName"]."' Image</h4>";
            if ($loose_image)
                echo "<form name='photo' enctype='multipart/form-data' action='/image-upload/loose-image' method='post'>";
            else
                echo "<form name='photo' enctype='multipart/form-data' action='/image-upload/".$item["itemType"]."/".$item["itemID"]."' method='post'>";
            echo     "Browse for new ".$item["itemName"]." Picture <input type='file' name='image' size='30' />";
            echo     "<input type='submit' name='upload' value='Upload' />";
            echo "</form>";
            echo "<br/>";
        }
    }
?>
