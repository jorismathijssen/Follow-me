<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>Street View service</title>
        <link rel="stylesheet" type="text/css" href="css/style.css">
        <script src="https://maps.googleapis.com/maps/api/js?v=3.exp&signed_in=true"></script>
        <script>
            function initialize() {
                var fenway = new google.maps.LatLng(42.345573, -71.098326);
                var mapOptions = {
                    center: fenway,
                    zoom: 14
                };
                var map = new google.maps.Map(
                    document.getElementById('map-canvas'), mapOptions);
                var panoramaOptions = {
                    position: fenway,
                    pov: {
                        heading: 34,
                        pitch: 10
                    }
                };
                var panorama = new google.maps.StreetViewPanorama(document.getElementById('pano'), panoramaOptions);
                map.setStreetView(panorama);

                //add buttons to field.

                var leftControlDiv = document.createElement('div');
                var leftControl = new leftControl(leftControlDiv, map);

                var rightControlDiv = document.createElement('div');
                var rightControl = new rightControl(rightControlDiv, map);

                leftControlDiv.index = 1;
                panorama.controls[google.maps.ControlPosition.BOTTOM].push(leftControlDiv);

                rightControlDiv.index = 1;
                panorama.controls[google.maps.ControlPosition.BOTTOM].push(rightControlDiv);
            }

            function leftControl(controlDiv, map) {

                // Set CSS for the control border
                var controlUI = document.createElement('div');
                controlUI.style.backgroundColor = '#fff';
                controlUI.style.border = '2px solid #fff';
                controlUI.style.borderRadius = '3px';
                controlUI.style.boxShadow = '0 2px 6px rgba(0,0,0,.3)';
                controlUI.style.cursor = 'pointer';
                controlUI.style.marginBottom = '22px';
                controlUI.style.marginRight = '22px';
                controlUI.style.textAlign = 'center';
                controlUI.title = 'Click to recenter the map';
                controlDiv.appendChild(controlUI);

                // Set CSS for the control interior
                var controlText = document.createElement('div');
                controlText.style.color = 'rgb(25,25,25)';
                controlText.style.fontFamily = 'Roboto,Arial,sans-serif';
                controlText.style.fontSize = '16px';
                controlText.style.lineHeight = '38px';
                controlText.style.paddingLeft = '5px';
                controlText.style.paddingRight = '5px';
                controlText.innerHTML = '<';
                controlUI.appendChild(controlText);

                // Setup the click event listeners: simply set the map to
                // Chicago
                google.maps.event.addDomListener(controlUI, 'click', function() {
                    console.log('LEFT');
                });

            }

            function rightControl(controlDiv, map) {

                // Set CSS for the control border
                var controlUI1 = document.createElement('div');
                controlUI1.style.backgroundColor = '#fff';
                controlUI1.style.border = '2px solid #fff';
                controlUI1.style.borderRadius = '3px';
                controlUI1.style.boxShadow = '0 2px 6px rgba(0,0,0,.3)';
                controlUI1.style.cursor = 'pointer';
                controlUI1.style.marginBottom = '22px';
                controlUI1.style.textAlign = 'center';
                controlUI1.title = 'Click to recenter the map';
                controlDiv.appendChild(controlUI1);

                // Set CSS for the control interior
                var controlText1 = document.createElement('div');
                controlText1.style.color = 'rgb(25,25,25)';
                controlText1.style.fontFamily = 'Roboto,Arial,sans-serif';
                controlText1.style.fontSize = '16px';
                controlText1.style.lineHeight = '38px';
                controlText1.style.paddingLeft = '5px';
                controlText1.style.paddingRight = '5px';
                controlText1.innerHTML = '>';
                controlUI1.appendChild(controlText1);

                // Setup the click event listeners: simply set the map to
                // Chicago
                google.maps.event.addDomListener(controlUI1, 'click', function() {
                    console.log('RIGHT');
                });

            }

            google.maps.event.addDomListener(window, 'load', initialize);

        </script>
    </head>
    <body>
        <div id="pano">
            <div id="map-canvas">
            </div>
        </div>
    </body>
</html>

