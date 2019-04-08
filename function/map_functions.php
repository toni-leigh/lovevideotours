<?php
    function create_marker($item)
    {
        if (is_numeric($item["itemID"]))
        {
            $item_images=get_images("item",$item["itemID"]);
            $item_image=mysql_fetch_array($item_images);
        }
        $marker="var point = new google.maps.LatLng(".$item["latitude"].", ".$item["longitude"].");\n";
        $marker.="var marker".$item["itemID"]." = new google.maps.Marker({position:point,map:map,icon:markerImage".$item["categoryID"]."});\n";
        $marker.="marker".$item["itemID"].".setMap(map);\n";
        $info_box_HTML="<span class='map_info_window'>";
        $info_box_HTML.="<span class='map_info_window_image'>";
        $info_box_HTML.="<a href='".build_item_link($item)."'><img src='/".$item_image["tinyThumbnailPath"]."' alt='Thumbnail image of ".$item["itemName"]." on map marker'/></a>";
        $info_box_HTML.="</span>";
        $info_box_HTML.=$item["itemName"];
        $info_box_HTML.="</span>";
        $marker.="var infoWindow".$item["itemID"]." = new google.maps.InfoWindow({content:'".json_sanitise($info_box_HTML)."'});\n";
        $marker.="google.maps.event.addListener(marker".$item["itemID"].", 'click', function () {\n";
        $marker.="infoWindow".$item["itemID"].".open(map,marker".$item["itemID"].");\n";
        $marker.="});";
        return $marker;
    }
    function get_distance($latitude1, $longitude1, $latitude2, $longitude2)
    {
        $theta = $longitude1 - $longitude2;
        $distance = (sin(deg2rad($latitude1)) * sin(deg2rad($latitude2))) +(cos(deg2rad($latitude1)) * cos(deg2rad($latitude2)) *cos(deg2rad($theta)));
        $distance = acos($distance);
        $distance = rad2deg($distance);
        $distance = $distance * 60 * 1.1515;
        return (round($distance,2));
    }
?>