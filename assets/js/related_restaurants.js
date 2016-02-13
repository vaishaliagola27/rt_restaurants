
jQuery(function () {
	var data = {
		'action': 'related_restaurants',
	};
	var arrRestaurants = [];
	jQuery.post(auto.admin_url, data, function (response) {

		obj = JSON.parse('[' + response + ']');

		for (var i = 0; i < obj[0].length; i++) {
			var restaurants = {};
			restaurants['value'] = obj[0][i].value + '-' + obj[0][i].label;
			restaurants['label'] = obj[0][i].label;
			arrRestaurants.push(restaurants);
		}

	});
	function split(val) {
		return val.split(/,\s*/);
	}
	function extractLast(term) {
		return split(term).pop();
	}

	jQuery("#related_restaurants_tag")
		// don't navigate away from the field on tab when selecting an item
		.on("keydown", function (event) {
			if (event.keyCode === jQuery.ui.keyCode.TAB &&
				jQuery(this).autocomplete("instance").menu.active) {
				event.preventDefault();
			}
		})
		.autocomplete({
			minLength: 0,
			source:
				function (request, response) {
					// delegate back to autocomplete, but extract the last term
					response(jQuery.ui.autocomplete.filter(
						arrRestaurants, extractLast(request.term)));
				}
			,
			focus: function () {
				// prevent value inserted on focus
				return false;
			},
			select: function (event, ui) {
				var terms = split(this.value);
				// remove the current input
				terms.pop();
				// add the selected item
				terms.push(ui.item.value);
				// add placeholder to get the comma-and-space at the end
				terms.push("");
				this.value = terms.join(",");
				return false;
			}

		});

});
