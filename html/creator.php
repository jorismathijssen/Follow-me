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

            /**
             * Init the website.
             */
             function init() {

                //parse the markers from the URL. And use the locations for the points
                if (window.location.hash) {
                    parts = window.location.hash.substr(1).split(',');
                    start_point = new google.maps.LatLng(parts[0], parts[1]);
                    lookat_point = new google.maps.LatLng(parts[2], parts[3]);
                    end_point = new google.maps.LatLng(parts[4], parts[5]);
                    _elevation = parts[6] || 0;
                }

                /* Map */
                /**
                 * Fucntion to make markers snap to roads easy
                 * @param  {Marker}   point    Marker for the point
                 * @param  {dropped} callback callback to check if the marker is dropped
                 * @return {position}            New position of the marker
                 */
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

                /**
                 * Change the url based on the postion of the markers.
                 * @return {string} string and placed in the url.
                 */
                 function changeHash() {
                    window.location.hash = start_pin.getPosition().lat() + ',' + start_pin.getPosition().lng() + ',' + pivot_pin.getPosition().lat() + ',' + pivot_pin.getPosition().lng() + ',' + end_pin.getPosition().lat() + ',' + end_pin.getPosition().lng() + ',' + _elevation;
                }

                //creating a road maps
                var mapOpt = {
                    mapTypeId: google.maps.MapTypeId.ROADMAP,
                    center: start_point,
                    zoom: 15
                };

                //init images for the markers
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

                /**
                 * Find location based on search
                 * @param  {string} address location, street, P.O.I.
                 * @return {Location}         Location where the search is
                 */
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


                /* Hyperlapse */

                var pano = document.getElementById('pano');
                var is_moving = false;
                var px, py;
                var onPointerDownPointerX = 0,
                onPointerDownPointerY = 0;

                var hyperlapse = new Hyperlapse(pano, {
                    lookat: lookat_point,
                    fov: 80,
                    millis: 50,
                    width: window.innerWidth,
                    height: window.innerHeight,
                    zoom: 2,
                    use_lookat: true,
                    distance_between_points: 10,
                    max_points: 300,
                    elevation: _elevation
                });



                hyperlapse.onError = function (e) {
                    console.log("ERROR: " + e.message);
                };

                hyperlapse.onRouteProgress = function (e) {
                    _route_markers.push(new google.maps.Marker({
                        position: e.point.location,
                        draggable: false,
                        icon: "dot_marker.png",
                        map: map
                    }));
                };

                hyperlapse.onRouteComplete = function (e) {
                    directions_renderer.setDirections(e.response);
                    console.log("Number of Points: " + hyperlapse.length());
                    hyperlapse.load();
                };

                hyperlapse.onLoadProgress = function (e) {
                    console.log("Loading: " + (e.position + 1) + " of " + hyperlapse.length());
                };

                hyperlapse.onLoadComplete = function (e) {
                    console.log("" +
                        "Start: " + start_pin.getPosition().toString() +
                        "<br>End: " + end_pin.getPosition().toString() +
                        "<br>Lookat: " + pivot_pin.getPosition().toString() +
                        "<br>Ready.");
                };

                hyperlapse.onFrame = function (e) {
                    console.log("" +
                        "Start: " + start_pin.getPosition().toString() +
                        "<br>End: " + end_pin.getPosition().toString() +
                        "<br>Lookat: " + pivot_pin.getPosition().toString() +
                        "<br>Position: " + (e.position + 1) + " of " + hyperlapse.length());
                    camera_pin.setPosition(e.point.location);
                };

                pano.addEventListener('mousedown', function (e) {
                    e.preventDefault();

                    is_moving = true;

                    onPointerDownPointerX = e.clientX;
                    onPointerDownPointerY = e.clientY;

                    px = hyperlapse.position.x;
                    py = hyperlapse.position.y;

                }, false);

                pano.addEventListener('mousemove', function (e) {
                    e.preventDefault();
                    var f = hyperlapse.fov() / 500;

                    if (is_moving) {
                        var dx = (onPointerDownPointerX - e.clientX) * f;
                        var dy = (e.clientY - onPointerDownPointerY) * f;
                        hyperlapse.position.x = px + dx; // reversed dragging direction (thanks @mrdoob!)
                        hyperlapse.position.y = py + dy;

                        o.position_x = hyperlapse.position.x;
                        o.position_y = hyperlapse.position.y;
                    }

                }, false);

                pano.addEventListener('mouseup', function () {
                    is_moving = false;

                    hyperlapse.position.x = px;
                    //hyperlapse.position.y = py;
                }, false);

                var myBtn = document.getElementById('myBtn');

                tester.addEventListener('click', function(event) {
                    $.ajax({
                        url: 'getcords.php',
                        data: "dataString",
                        dataType: 'html',
                        success: function(data)
                        {
                            $('#output').html();

                            $('#output').html(data);
                        }
                    });
                });

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

                routesave.addEventListener('click', function(event) {
                    var looklat = pivot_pin.getPosition().lat();
                    var looklng = pivot_pin.getPosition().lng();
                    var startlat = start_pin.getPosition().lat();
                    var startlng = start_pin.getPosition().lng();
                    var stoplat = end_pin.getPosition().lat();
                    var stoplng = end_pin.getPosition().lng();
                    //use an AJAX function to save the lat/lng to the data base
                    $.ajax({
                        type: 'POST',
                        data: {
                            looklat: looklat,
                            looklng: looklng,
                            startlat: startlat,
                            startlng: startlng,
                            stoplat: stoplat,
                            stoplng: stoplng
                        },
                        url: "saveroute.php",
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

window.addEventListener('resize', function () {
    hyperlapse.setSize(window.innerWidth, window.innerHeight);
    o.screen_width = window.innerWidth;
    o.screen_height = window.innerHeight;
}, false);

var show_ui = true;
document.addEventListener('keydown', onKeyDown, false);

function onKeyDown(event) {

    switch (event.keyCode) {
        case 72:
        /* H */
        show_ui = !show_ui;
        document.getElementById("controls").style.opacity = (show_ui) ? 1 : 0;
        break;

        case 190:
        /* > */
        hyperlapse.next();
        break;

        case 188:
        /* < */
        hyperlapse.prev();
        break;
    }

};

o.generate();
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
