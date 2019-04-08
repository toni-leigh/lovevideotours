<?php
    if ($page["mapPage"])
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
            <!-- <script type="text/javascript"
                src="http://maps.google.com/maps/api/js?sensor=false">
            </script> -->
            <script async defer src="https://maps.googleapis.com/maps/api/js?key=API_KEY_HERE&callback=initialize" type="text/javascript"></script>

            <script type="text/javascript">
                function initialize()
                {
                    <?php
                        //set initial latitude and longitude values
                        if (is_array($item))
                            echo "var latitude=".$item["itemLatitude"].";\nvar longitude=".$item["itemLongitude"].";\n";
                        else
                            echo "var latitude=".$latitude.";\nvar longitude=".$longitude.";\n";
                        echo "var latlng = new google.maps.LatLng(latitude,longitude);";
                        //set map options
                        echo "var myOptions = {";
                        echo    "zoom: 9,";
                        echo    "scrollwheel: false,";
                        echo    "center: latlng,";
                        echo    "mapTypeId: google.maps.MapTypeId.ROADMAP";
                        echo "};";
                        //create map object
                        echo "var map = new google.maps.Map(document.getElementById('item_map'),myOptions);";// Recenter Map and add Coords by clicking the map
                        //add the central marker to the map
                        if (is_array($item))
                        {
                            echo create_marker($item);
                        }
                        else
                        {
                            $default_item["latitude"]=$latitude;
                            $default_item["longitude"]=$longitude;
                            $default_item["itemName"]="Default Centre Point";
                            echo create_marker($default_item);
                        }
                        //we have to add all the markers in the $map_items query to the map, if they exist
                        if (isset($map_items))
                            while ($map_item=mysql_fetch_array($map_items))
                                echo create_marker($map_item);
                    ?>
                }
            </script>
        <?php
    }
?>