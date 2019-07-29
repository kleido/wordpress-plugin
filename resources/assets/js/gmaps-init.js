if (typeof google === 'object' && typeof google.maps === 'object') {
    console.log('google places is loaded');
} else {
	console.log('google places is going to be loaded');
	var appKey = jQuery('#googleKey').val();
	if (appKey == '') {
		appKey = 'AIzaSyCH2aMLtEaJUC81UK4M8H_Vb4ePt8iti9c';
	} 
    var script = document.createElement("script");
    script.type = "text/javascript";
    script.sync= true;
    script.src = "https://maps.googleapis.com/maps/api/js?key="+appKey+"&v=3.exp&libraries=places&callback=mapsCallback";
    document.head.appendChild(script);
    console.log('google places loaded!');
}

var map;
var place;



function getLocation() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(getPosition);
    }
}

function getPosition(position) {
	place = {
		lat: position.coords.latitude,
		lng: position.coords.longitude
	};
	initMap();
}

function initMap() {

	map = new google.maps.Map(document.getElementById('wp-rengine-places-map'), {
		center: place,
		zoom: 17
	});

	var service = new google.maps.places.PlacesService(map);
	service.nearbySearch({
		location: place,
		radius: 500,
		type: ['store']
	}, processResults);
}

function processResults(results, status, pagination) {
	if (status !== google.maps.places.PlacesServiceStatus.OK) {
		return;
	} else {
		createMarkers(results);

		if (pagination.hasNextPage) {
			var moreButton = document.getElementById('more');

			moreButton.disabled = false;

			moreButton.addEventListener('click', function() {
				moreButton.disabled = true;
				pagination.nextPage();
			});
		}
	}
}

function createMarkers(places) {
	var bounds = new google.maps.LatLngBounds();
	var placesList = document.getElementById('places');

	for (var i = 0, place; place = places[i]; i++) {
		var image = {
			url: place.icon,
			size: new google.maps.Size(71, 71),
			origin: new google.maps.Point(0, 0),
			anchor: new google.maps.Point(17, 34),
			scaledSize: new google.maps.Size(25, 25)
		};

		var marker = new google.maps.Marker({
			map: map,
			icon: image,
			title: place.name,
			position: place.geometry.location
		});

		placesList.innerHTML += '<li>' + place.name + '</li>';

		bounds.extend(place.geometry.location);
	}
	map.fitBounds(bounds);
}