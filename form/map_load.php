<?php
    if (show_map(array("page"=>$page,"item_type"=>$item_type)))
    {
        if (isset($_SESSION["user"]))
        {
            $latitude=$_SESSION["user"]["userLatitude"];
            $longitude=$_SESSION["user"]["userLongitude"];
        }
        else
        {
            //Wooler mark, my current nearest town :-)
            $latitude="55.54747155125705";
            $longitude="-2.0096560058593695";
        }
        ?>
            <style type="text/css">
                html { height: 100% }
                body { height: 100%; margin: 0px; padding: 0px }
            </style>
            <!-- enable for mobiles -->
            <!-- <meta name="viewport" content="initial-scale=1.0, user-scalable=no" /> -->
            <script async defer src="https://maps.googleapis.com/maps/api/js?key=API_KEY_HERE&callback=initialize" type="text/javascript"></script>
            <script type="text/javascript">
                var markers=new Array();
                var map;
                // main_map = the actual map, that is always there, map = the map to apply the marker to, which may actually be set to null if the marker is not to be shown
                function display_marker(main_map,map,info_open,item_ID,item_name,item_tweet,item_link,lat,lng,thumb,icon,catID,sprOff)
                {
                    if (sprOff==999)
                    {
                        offx=186;
                        if (catID==3) offy=188; else offy=0;
                        sizex=48;
                        sizey=65;
                    }
                    else
                    {
                        if (catID==3)
                        {
                            offx=186;
                            offy=253;
                            sizex=36;
                            sizey=48;
                        }
                        else
                        {
                            offx=25;
                            offy=(sprOff/25)*39;
                            sizex=24;
                            sizey=38;
                        }
                    }
                    var point=new google.maps.LatLng(lat,lng);
                    var marker_image=new google.maps.MarkerImage('/img/icons/sprite.png',new google.maps.Size(sizex,sizey),new google.maps.Point(offx,offy));
                    markers[item_ID]=new Array();
                    markers[item_ID][0]=new google.maps.Marker({ position:point, map:main_map, icon:marker_image });
                    markers[item_ID][0].setMap(map);
                    markers[item_ID][1]=catID;
                    var boxHTML="<span class='mi'>";
                    boxHTML+="<span class='mi_image'>";
                    boxHTML+="<a href='"+item_link+"'><img src='/"+thumb+"' alt='Thumbnail image of "+item_name+" on map marker' width='100' height='100'/></a>";
                    boxHTML+="</span>";
                    boxHTML+="<span class='mi_text'>";
                    boxHTML+="<span class='mi_name'><a href='"+item_link+"'>"+item_name+"</a></span>";
                    boxHTML+="<span class='mi_tweet'>"+item_tweet+"</span>";
                    boxHTML+="</span>";
                    boxHTML+="</span>";
                    markers[item_ID][2]=new google.maps.InfoWindow({ content:boxHTML });
                    google.maps.event.addListener(markers[item_ID][0], 'click', function () { for (x in markers) if (typeof(markers[x][2])!="undefined") markers[x][2].close();markers[item_ID][2].open(main_map,markers[item_ID][0]); });
                    if (info_open==1) markers[item_ID][2].open(main_map,markers[item_ID][0]); // load up with this item info open
                }
                function initialize()
                {
                    <?php
                        //set initial latitude and longitude values
                        if (is_numeric($item["latitude"])&&is_numeric($item["longitude"]))
                            echo "var latitude=".$item["latitude"].";\nvar longitude=".$item["longitude"].";\n";
                        else
                            echo "var latitude=".$latitude.";\nvar longitude=".$longitude.";\n";
                        echo "var latlng = new google.maps.LatLng(latitude,longitude);\n";
                        //set map options
                        echo "var myOptions = {\n";
                        echo    "zoom: 9,\n";
                        echo    "scrollwheel: false,\n";
                        echo    "center: latlng,\n";
                        echo    "mapTypeId: google.maps.MapTypeId.ROADMAP\n";
                        echo "};\n";
                        //create map object
                        if ($page["pageID"]==23||$page["pageID"]==48)
                            echo "var map = new google.maps.Map(document.getElementById('map_canvas_back_office'),myOptions);";// Recenter Map and add Coords by clicking the map
                        else
                            echo "map = new google.maps.Map(document.getElementById('item_map'),myOptions);\n"; // Recenter Map and add Coords by clicking the map
                        //add the central marker to the map
                        if (is_numeric($item["latitude"])&&is_numeric($item["longitude"]))
                        {
                            if ($page["pageID"]==23||$page["pageID"]==48)
                            {
                                echo "var markerImage = new google.maps.MarkerImage('/img/icons/sprite.png',new google.maps.Size(36,48),new google.maps.Point(186,253));";
                            }
                            else
                            {
                                $item_image=mysql_fetch_array($item_images);
                                echo "display_marker(map,map,1,".$item["itemID"].",'".json_sanitise(str_replace("'","",$item["itemName"]))."','".json_sanitise(str_replace("'","",$item["itemTweet"]))."','".json_sanitise(build_item_link($item))."',".$item["latitude"].",".$item["longitude"].",'".$item_image["tinySquarePath"]."','',".$item["categoryID"].",999);\n";
                                mysql_data_seek($item_images,0);
                            }
                        }
                        if ($page["pageID"]==23||$page["pageID"]==48)
                        {

                            //do the stuff to populate the fields in the back office
                            ?>
                                var markerImage = new google.maps.MarkerImage('/img/icons/sprite.png',new google.maps.Size(36,48),new google.maps.Point(186,253));
                                google.maps.event.addListener(map, 'click', function(point)
                                {
                                    if (typeof(marker)!='undefined') marker.setMap(null);
                                    var latLong=""+point.latLng;
                                    var splitValues=latLong.split(',');
                                    document.getElementById("latitude").value=splitValues[0].substr(1);
                                    document.getElementById("longitude").value=splitValues[1].substr(1,splitValues[1].length-2);
                                    marker = new google.maps.Marker({
                                        position: point.latLng,
                                        map: map,
                                        icon: markerImage
                                      });
                                    marker<? echo $item["itemID"]; ?>.setMap(null);
                                    marker.setMap(map);
                                });
                            <?php
                        }
                        else
                        {
                            //we have to add all the markers in the $map_items query to the map, if they exist
                            if (isset($map_items))
                            {
                                while ($mi=mysql_fetch_array($map_items))
                                    $mis[]=$mi["itemID"];
                                // get all items to show on map, we will create but not show some based on the map items query
                                $all_items=get_video_items(array("skip_traverse"=>1));
                                while ($ai=mysql_fetch_array($all_items))
                                {
                                    $map_item_images=get_images("item",$ai["itemID"]);
                                    $map_item_image=mysql_fetch_array($map_item_images);
                                    if ($item["categoryID"]==3&&$ai["categoryID"]==3)
                                    {
                                        // don't place other holiday properties on the map when someone is viewing one holiday property
                                    }
                                    else
                                    {
                                        if (in_array($ai["itemID"],$mis)&&$ai["itemID"]!=$item["itemID"])
                                            echo "display_marker(map,map,0,".$ai["itemID"].",'".json_sanitise(str_replace("'","",$ai["itemName"]))."','".json_sanitise(str_replace("'","",$ai["itemTweet"]))."','".json_sanitise(build_item_link($ai))."',".$ai["latitude"].",".$ai["longitude"].",'".$map_item_image["tinySquarePath"]."','',".$ai["categoryID"].",".$ai["spriteOffset"].");\n";
                                        else
                                            echo "display_marker(map,null,0,".$ai["itemID"].",'".json_sanitise(str_replace("'","",$ai["itemName"]))."','".json_sanitise(str_replace("'","",$ai["itemTweet"]))."','".json_sanitise(build_item_link($ai))."',".$ai["latitude"].",".$ai["longitude"].",'".$map_item_image["tinySquarePath"]."','',".$ai["categoryID"].",".$ai["spriteOffset"].");\n";
                                    }
                                }
                            }
                        }
                    ?>
                }
            </script>
        <?php
    }
?>