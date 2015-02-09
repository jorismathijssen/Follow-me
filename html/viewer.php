<!DOCTYPE html>
<html> 
<head> 
    <title>[Playing] Follow Me - Joris Mathijssen</title> 

    <script src="https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false" type="text/javascript"></script> 
    <script src="js/three.min.js"></script>
    <script src="js/GSVPano.js"></script>
    <script src="../js/Hyperlapse.js"></script>
    <script> 
        function init() {
            var hyperlapse = new Hyperlapse(document.getElementById('pano'), {
                lookat: new google.maps.LatLng(37.81409525128964,-122.4775045005249),
                zoom: 1,
                use_lookat: true,
                elevation: 80
            });


            hyperlapse.setSize(window.innerWidth, window.innerHeight);

            hyperlapse.onError = function(e) {
                console.log(e);
            };
            
            hyperlapse.onRouteComplete = function(e) {
                hyperlapse.load();
            };

            hyperlapse.onLoadComplete = function(e) {
                hyperlapse.generate();
            }

            // Google Maps API stuff here...
            var directions_service = new google.maps.DirectionsService();
            var route = {
                request:{
                    origin: new google.maps.LatLng(48.85877000000001, 2.293130000000019),
                    destination: new google.maps.LatLng(48.859260000000006, 2.2938300000000003),
                    travelMode: google.maps.DirectionsTravelMode.DRIVING
                }
            };
            directions_service.route(route.request, function(response, status) {
                if (status == google.maps.DirectionsStatus.OK) {
                    hyperlapse.generate( {route:response} );
                    console.log('Generate 1 complete');
                } else {
                    console.log(status);
                }
            });

            window.addEventListener('resize', function () {
                hyperlapse.setSize(window.innerWidth, window.innerHeight);
                o.screen_width = window.innerWidth;
                o.screen_height = window.innerHeight;
            }, false);


            var pressed = false;
            document.addEventListener('keydown', onKeyDown, false);

            function onKeyDown(event) {

                switch (event.keyCode) {
                    case 72:
                    /* H */
                    break;
                }

            };


        }
        window.onload = init;

    </script> 
</head> 
<body> 
    <div id="pano"></div>
</body> 
</html>