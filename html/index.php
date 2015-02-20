<!DOCTYPE html>
<html>

    <head>
        <meta http-equiv="content-type" content="text/html; charset=UTF-8" />
        <title>Follow Me - Joris Mathijssen</title>

        <script src="https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false" type="text/javascript"></script>
        <script src="http://jqueryjs.googlecode.com/files/jquery-1.3.2.min.js" type="text/javascript"></script>
        <script src="js/dat.gui.min.js"></script>
        <script src="js/three.min.js"></script>
        <script src="js/GSVPano.js"></script>
        <script src="../js/Hyperlapse.js"></script>
        <script>
            //creating start up vars.
            var start_point = new google.maps.LatLng(48.85877000000001, 2.293130000000019);
            var end_point = new google.maps.LatLng(48.859260000000006, 2.2938300000000003);
            var lookat_point = new google.maps.LatLng(48.858228924927595, 2.294524215344154);
            var map, directions_renderer, directions_service, streetview_service, geocoder;
            var start_pin, end_pin, pivot_pin, camera_pin;
            var _elevation = 0;
            var _route_markers = [];

            function init() {

                if (window.location.hash) {
                    parts = window.location.hash.substr(1).split(',');
                    start_point = new google.maps.LatLng(parts[0], parts[1]);
                    lookat_point = new google.maps.LatLng(parts[2], parts[3]);
                    end_point = new google.maps.LatLng(parts[4], parts[5]);
                    _elevation = parts[6] || 0;
                }

                /* Map */

                function snapToRoad(point, callback) {
                    var request = {
                        origin: point,
                        destination: point,
                        travelMode: google.maps.TravelMode["DRIVING"]
                    };
                    directions_service.route(request, function (response, status) {
                        if (status == "OK") callback(response.routes[0].overview_path[0]);
                        else callback(null);
                    });
                }

                function changeHash() {
                    window.location.hash = start_pin.getPosition().lat() + ',' + start_pin.getPosition().lng() + ',' + pivot_pin.getPosition().lat() + ',' + pivot_pin.getPosition().lng() + ',' + end_pin.getPosition().lat() + ',' + end_pin.getPosition().lng() + ',' + _elevation;
                }

                var mapOpt = {
                    mapTypeId: google.maps.MapTypeId.ROADMAP,
                    center: start_point,
                    zoom: 15
                };

                var start = 'img/start.png';
                var looks = 'img/eye.png';
                var eind = 'img/eind.png';

                map = new google.maps.Map(document.getElementById("map"), mapOpt);
                geocoder = new google.maps.Geocoder();

                var overlay = new google.maps.StreetViewCoverageLayer();
                overlay.setMap(map);

                directions_service = new google.maps.DirectionsService();
                directions_renderer = new google.maps.DirectionsRenderer({
                    draggable: false,
                    markerOptions: {
                        visible: false
                    }
                });
                directions_renderer.setMap(map);
                directions_renderer.setOptions({
                    preserveViewport: true
                });

                camera_pin = new google.maps.Marker({
                    position: start_point,
                    map: map,
                    visible: false
                });

                start_pin = new google.maps.Marker({
                    position: start_point,
                    draggable: true,
                    map: map,
                    icon: start
                });

                google.maps.event.addListener(start_pin, 'dragend', function (event) {
                    snapToRoad(start_pin.getPosition(), function (result) {
                        start_pin.setPosition(result);
                        start_point = result;
                        changeHash();
                    });
                });

                end_pin = new google.maps.Marker({
                    position: end_point,
                    draggable: true,
                    map: map,
                    icon: eind
                });

                google.maps.event.addListener(end_pin, 'dragend', function (event) {
                    snapToRoad(end_pin.getPosition(), function (result) {
                        end_pin.setPosition(result);
                        end_point = result;
                        changeHash();
                    });
                });

                pivot_pin = new google.maps.Marker({
                    position: lookat_point,
                    draggable: true,
                    map: map,
                    icon: looks
                });

                google.maps.event.addListener(pivot_pin, 'dragend', function (event) {
                    hyperlapse.setLookat(pivot_pin.getPosition());
                    changeHash();
                });

                function findAddress(address) {
                    geocoder.geocode({
                        'address': address
                    }, function (results, status) {
                        if (status == google.maps.GeocoderStatus.OK) {
                            map.setCenter(results[0].geometry.location);
                            o.drop_pins();
                        } else {
                            console.log("Geocode was not successful for the following reason: " + status);
                        }
                    });
                }

                var search = document.getElementById('searchButton');
                search.addEventListener('click', function (event) {
                    event.preventDefault();
                    findAddress(document.getElementById("address").value);
                }, false);

                //Save cords function
                save.addEventListener('click', function(event) {
                    var lat = pivot_pin.getPosition().lat();
                    var lng = pivot_pin.getPosition().lng();
                    //use an AJAX function to save the lat/lng to the data base
                    $.ajax({
                        type: 'POST',
                        data: {
                            latitude: lat,
                            longitude: lng
                        },
                        url: "savecords.php",
                        success: function(response) {
                            if (response == "success") {
                                console.log(response);
                            } else {
                                console.log(response);
                            }
                        },
                        error: function() {
                            alert('An error occured');
                        }
                    });
                });
            }

            window.onload = init;
        </script>
    </head>

    <body>
        <div id="pano" style="position: absolute; left: 0; top: 0; right: 0; bottom: 0; z-index:-1;"></div>
        <div id="controls">
            <div id="map" style="width: 800px; height: 600px; float: left; padding: 0;"></div>
            <div id="controls" style="">
                <form id="map_form">
                    <input type="text" name="address" id="address" />
                    <button type="submit" id="searchButton">Search</button>
                    <button id="save">Sla punten op</button>
                </form>
            </div>
            <div style="float:right;">
                <input type="text" name="routename" id="routename" placeholder="route name"/>
                <button id="routesave">Sla route op</button>
            </div>
            <textarea rows="30" cols="65" id="output"></textarea>
            <button id="tester">test</button>
        </div>

    </body>

</html>
