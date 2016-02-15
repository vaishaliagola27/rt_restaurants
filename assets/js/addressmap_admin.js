jQuery(document).ready(function () {
	initialize();
	var geocoder;
	var map;
	var marker;
	var infowindow = new google.maps.InfoWindow({size: new google.maps.Size(150, 50)});
	function initialize() {
		geocoder = new google.maps.Geocoder();
		var latlng = new google.maps.LatLng(-34.397, 150.644);
		var mapOptions = {
			zoom: 15,
			center: latlng,
			mapTypeId: google.maps.MapTypeId.ROADMAP
		}
		map = new google.maps.Map(document.getElementById('map'), mapOptions);
		google.maps.event.addListener(map, 'click', function () {
			infowindow.close();
		});
		codeAddress(geocoder, map);
	}

	function clone(obj) {
		if (obj == null || typeof (obj) != 'object')
			return obj;
		var temp = new obj.constructor();
		for (var key in obj)
			temp[key] = clone(obj[key]);
		return temp;
	}


	function geocodePosition(pos) {
		geocoder.geocode({
			latLng: pos
		}, function (responses) {
			if (responses && responses.length > 0) {
				marker.formatted_address = responses[0].formatted_address;
			} else {
				marker.formatted_address = 'Cannot determine address at this location.';
			}
			infowindow.setContent(marker.formatted_address + "<br>coordinates: " + marker.getPosition().toUrlValue(6));
			infowindow.open(map, marker);
			var address = ["streetAddress", "addressLocality", "addressRegion", "postalCode", "addressCountry"];
			console.log(responses);
			
			var add = [
				"route",
				"sublocality_level_3",
				"sublocality_level_2",
				"sublocality_level_1",
				"administrative_area_level_2",
				"administrative_area_level_1",
				"country",
				"postal_code"
			];
			for (var i = 0; i < address.length; i++) {
				var value_add = "";
				if (address[i] === "streetAddress") {
					for (var j = 0; j < responses[0].address_components.length; j++) {
						if (responses[0].address_components[j].types[0] === add[0]
							|| responses[0].address_components[j].types[0] === add[1]
							|| responses[0].address_components[j].types[0] === add[2]) {
							value_add = value_add + responses[0].address_components[j].long_name + ', ';
						}
					}
				}

				if (address[i] === "addressLocality") {
					for (var j = 0; j < responses[0].address_components.length; j++) {
						if (responses[0].address_components[j].types[0] === add[3]
							|| responses[0].address_components[j].types[0] === add[4]) {
							value_add = value_add + responses[0].address_components[j].long_name + ', ';
						}
					}
				}

				if (address[i] === "addressRegion") {
					for (var j = 0; j < responses[0].address_components.length; j++) {
						if (responses[0].address_components[j].types[0] === add[5]) {
							value_add = responses[0].address_components[j].long_name;
						}
					}
				}
				if (address[i] === "postalCode") {
					for (var j = 0; j < responses[0].address_components.length; j++) {
						if (responses[0].address_components[j].types[0] === add[7]) {
							value_add = responses[0].address_components[j].long_name;
						}
					}
				}
				if (address[i] === "addressCountry") {
					for (var j = 0; j < responses[0].address_components.length; j++) {
						if (responses[0].address_components[j].types[0] === add[6]) {
							value_add = responses[0].address_components[j].long_name;
						}
					}
				}

				jQuery('input[name="restaurant_add[' + address[i] + ']"]').val(value_add);
			}
		});
	}

	function codeAddress() {
		var address = document.getElementById('map_address').value;
		geocoder.geocode({'address': address}, function (results, status) {
			if (status == google.maps.GeocoderStatus.OK) {
				map.setCenter(results[0].geometry.location);
				if (marker) {
					marker.setMap(null);
					if (infowindow)
						infowindow.close();
				}
				marker = new google.maps.Marker({
					map: map,
					draggable: true,
					position: results[0].geometry.location
				});
				google.maps.event.addListener(marker, 'dragend', function () {
					// updateMarkerStatus('Drag ended');
					geocodePosition(marker.getPosition());
				});
				google.maps.event.addListener(marker, 'click', function () {
					if (marker.formatted_address) {
						infowindow.setContent(marker.formatted_address + "<br>coordinates: " + marker.getPosition().toUrlValue(6));
					} else {
						infowindow.setContent(address + "<br>coordinates: " + marker.getPosition().toUrlValue(6));
					}
					infowindow.open(map, marker);
				});
				google.maps.event.trigger(marker, 'click');
			} else {
				alert('Geocode was not successful for the following reason: ' + status);
			}
		});
	}

});
