<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>Profielwerkstuk - Elvira Mathijssen</title>
        <link rel="stylesheet" type="text/css" href="css/normalize.css">
        <link rel="stylesheet" type="text/css" href="css/style.css">
        <link rel="stylesheet" type="text/css" href="css/flexslider.css">
        <script src="js/jquery.min.js"></script>
        <script src="https://maps.googleapis.com/maps/api/js?v=3.exp&signed_in=true"></script>
        <script src="js/jquery.flexslider.js"></script>
        <script>
            //creating the array's before use globaly, is this right?
            var addresses = [], counter = 0;
            var titel = [];
            var desc = [];
            var heading = [];
            var pitch = [];

            function initialize() {
                //Ajax call to get the cordinates
                $.ajax({
                    url: 'getcords.php',
                    data: "dataString",
                    dataType: 'html',
                    success: function(data)
                    {
                        var obj = JSON && JSON.parse(data) || $.parseJSON(data); //Parsing JSON

                        for (var key in obj) { //Put data in the array's

                            addresses.push(new google.maps.LatLng(obj[key]['latitude'], obj[key]['longitude']));
                            titel.push(obj[key]['titel']);
                            desc.push(obj[key]['beschrijving']);
                            heading.push(obj[key]['heading']);
                            pitch.push(obj[key]['pitch']);

                        }

                        var location = addresses[0]; //seting the first location and putting it in a map.
                        var mapOptions = {
                            center: location,
                            zoom: 14
                        };
                        var map = new google.maps.Map(
                            document.getElementById('map-canvas'), mapOptions);

                        var panoramaOptions = { //seting the first Panorama settings.
                            position: location,
                            zoom: 0.2,
                            pov: {
                                heading: parseFloat(heading[0]),
                                pitch: parseFloat(pitch[0])
                            },
                            disableDefaultUI: true
                        };
                        var panorama = new google.maps.StreetViewPanorama(document.getElementById('pano'), panoramaOptions);
                        map.setStreetView(panorama);

                        var leftControlDiv = document.createElement('div'); //Adding left controls
                        var leftControler = new leftControl(leftControlDiv, panorama, map);
                        leftControlDiv.index = 1;
                        panorama.controls[google.maps.ControlPosition.BOTTOM].push(leftControlDiv);

                        var rightControlDiv = document.createElement('div'); //Adding right controls
                        var rightControler = new rightControl(rightControlDiv, panorama, map);
                        rightControlDiv.index = 1;
                        panorama.controls[google.maps.ControlPosition.BOTTOM].push(rightControlDiv);

                        //setting the titel and discription divs
                        var titelElement = document.getElementById("header");
                        titelElement.innerHTML = titel[0];
                        var descElement = document.getElementById("desc");
                        descElement.innerHTML = desc[0];

                        //Creating a listener for the image click.
                        $("#imagebox").click(function(){
                            console.log('Hallo');
                        });

                        $('.flexslider').flexslider({
                            animation: "slide",
                            controlNav: "thumbnails"
                        });
                    }
                });
            }

            function leftControl(controlDiv, map, smallmap) {

                // Set CSS for the control border
                var controlUI = document.createElement('div');
                controlUI.style.backgroundColor = 'rgba(0, 0, 0, 0.7)';
                controlUI.style.border = '2px solid rgba(0, 0, 0, 0.7)';
                controlUI.style.borderRadius = '3px';
                controlUI.style.boxShadow = '0 2px 6px rgba(0, 0, 0, 0.7)';
                controlUI.style.cursor = 'pointer';
                controlUI.style.marginBottom = '22px';
                controlUI.style.marginRight = '22px';
                controlUI.style.textAlign = 'center';
                controlUI.title = 'To the the left';
                controlDiv.appendChild(controlUI);

                // Set CSS for the control interior
                var controlText = document.createElement('div');
                controlText.style.color = 'rgb(244, 235, 235)';
                controlText.style.fontFamily = 'Roboto,Arial,sans-serif';
                controlText.style.fontSize = '16px';
                controlText.style.lineHeight = '38px';
                controlText.style.paddingLeft = '5px';
                controlText.style.paddingRight = '5px';
                controlText.innerHTML = 'Vorige';
                controlUI.appendChild(controlText);

                // Setup the click event listeners
                google.maps.event.addDomListener(controlUI, 'click', function() {
                    if(counter == 0) counter = addresses.length; //check if counter is 0 otherwise set to 0. There is a fix for this...
                    counter = (counter - 1);
                    smallmap.panTo(addresses[counter]);
                    map.setPosition(addresses[counter]);
                    var pov = map.getPov();
                    pov.heading = parseFloat(heading[counter]);
                    pov.pitch = parseFloat(pitch[counter]);
                    map.setPov(pov);
                    var titelElement = document.getElementById("header");
                    titelElement.innerHTML = titel[counter];
                    var descElement = document.getElementById("desc");
                    descElement.innerHTML = desc[counter];
                });

            }

            function rightControl(controlDiv, map, smallmap) {

                // Set CSS for the control border
                var controlUI1 = document.createElement('div');
                controlUI1.style.backgroundColor = 'rgba(0, 0, 0, 0.7)';
                controlUI1.style.border = '2px solid rgba(0, 0, 0, 0.7)';
                controlUI1.style.borderRadius = '3px';
                controlUI1.style.boxShadow = '0 2px 6px rgba(0, 0, 0, 0.7)';
                controlUI1.style.cursor = 'pointer';
                controlUI1.style.marginBottom = '22px';
                controlUI1.style.textAlign = 'center';
                controlUI1.title = 'To the the right';
                controlDiv.appendChild(controlUI1);

                // Set CSS for the control interior
                var controlText1 = document.createElement('div');
                controlText1.style.color = 'rgb(244, 235, 235)';
                controlText1.style.fontFamily = 'Roboto,Arial,sans-serif';
                controlText1.style.fontSize = '16px';
                controlText1.style.lineHeight = '38px';
                controlText1.style.paddingLeft = '5px';
                controlText1.style.paddingRight = '5px';
                controlText1.innerHTML = 'Volgende';
                controlUI1.appendChild(controlText1);

                // Setup the click event listeners
                google.maps.event.addDomListener(controlUI1, 'click', function() {
                    counter = (counter + 1) % addresses.length;
                    smallmap.panTo(addresses[counter]);
                    map.setPosition(addresses[counter]);
                    var pov = map.getPov();
                    pov.heading = parseFloat(heading[counter]);
                    pov.pitch = parseFloat(pitch[counter]);
                    map.setPov(pov);
                    var titelElement = document.getElementById("header");
                    titelElement.innerHTML = titel[counter];
                    var descElement = document.getElementById("desc");
                    descElement.innerHTML = desc[counter];
                });

            }

            google.maps.event.addDomListener(window, 'load', initialize);
        </script>
    </head>
    <body>
        <div id="pano">
            <div id="map-canvas">
            </div>
            <div id="imagebox">
                <div class="flexslider">
                    <ul class="slides">
                        <li data-thumb="img/1.jpg">
                            <img src="img/1.jpg" />
                        </li>
                        <li data-thumb="img/2.jpg">
                            <img src="img/2.jpg" />
                        </li>
                        <li data-thumb="img/3.jpg">
                            <img src="img/3.jpg" />
                        </li>
                        <li data-thumb="img/4.jpg">
                            <img src="img/4.jpg" />
                        </li>
                    </ul>
                </div>
            </div>
            <div>
                <div id="hintbox-top"><h1 id="header"></h1><p id="desc"></p></div>
            </div>
        </div>
    </body>
</html>

