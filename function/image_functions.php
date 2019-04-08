<?php
    function get_images($entity_type,$entity_ID)
    {
        $dev=0;
        $image_string="select * from Image where entityID=".$entity_ID." and entityType='".$entity_type."' and removed=0 order by main desc";
        $images_query=site_query($image_string,"get_images()",$dev);   
        return $images_query;
    }
?>