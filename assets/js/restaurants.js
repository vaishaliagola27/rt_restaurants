/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


jQuery(document).ready(function () {

<<<<<<< Updated upstream
=======
//Slick for slideshow
	jQuery('.related_restaurants_slide').slick({
		infinite: true,
		slidesToShow: 3,
		slidesToScroll: 1,
		adaptiveHeight: true,
		arrows:false,
		dots:true,
		autoplay: true,
		speed: 500,
	});

>>>>>>> Stashed changes
	//Slick for slideshow
	jQuery('.image-gallery').slick({
		dots: true,
		infinite: true,
		speed: 500,
		fade: true,
		cssEase: 'linear',
	});

	//Address display by Google Map
	initMap();
	function initMap() {
		var map = new google.maps.Map(document.getElementById('map'), {
			zoom: 8,
			center: {lat: -34.397, lng: 150.644}
		});
		var geocoder = new google.maps.Geocoder();
		geocodeAddress(geocoder, map);
	}
	//Loads address into map
	function geocodeAddress(geocoder, resultsMap) {
		var address = document.getElementById('address_value').value;
		geocoder.geocode({'address': address}, function (results, status) {
			if (status === google.maps.GeocoderStatus.OK) {
				resultsMap.setCenter(results[0].geometry.location);
				var marker = new google.maps.Marker({
					map: resultsMap,
					position: results[0].geometry.location
				});
			} else {
				alert('Geocode was not successful for the following reason: ' + status);
			}
		});
	}
});
