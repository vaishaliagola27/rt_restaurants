(function ($) {

	var $wp_inline_edit = inlineEditPost.edit;

	inlineEditPost.edit = function (id) {

		$wp_inline_edit.apply(this, arguments);

		// get the post ID
		var $post_id = 0;
		if (typeof (id) == 'object') {
			$post_id = parseInt(this.getId(id));
		}

		if ($post_id > 0) {
			// define the edit row
			var $edit_row = $('#edit-' + $post_id);
			var $post_row = $('#post-' + $post_id);

			// address data display
			var $address = $('.column-address', $post_row).find('span');
			$address.each(function () {
				var attribute = $(this).attr('itemprop');
				var val = $(this).text();

				$('input[name="restaurant_add[' + attribute + ']"]', $edit_row).val(val);
			});

			//contact data display
			var $contact = $('.column-contactno', $post_row).text();
			if ($contact == "") {
				$(':input[name="restaurant_contact_no"]', $edit_row).val(null);
			} else {
				$(':input[name="restaurant_contact_no"]', $edit_row).val($contact);
			}

			//timing table display
			var $time = $('.column-timing', $post_row).find('span');

			var i = 0, loop = 1;
			$time.each(function () {
				var days_key = ["mon", "tue", "wed", "thu", "fri", "sat", "sun"];
				var val = $(this).text();
				var span_id = $(this).attr('id');
				var str = span_id.slice(0, 3);

				if (1 === loop) {
					if (str.localeCompare(days_key[i]) === 0) {
						$('input[name="time[' + days_key[i] + '][am]"]', $edit_row).val(val);
					} else {
						//set value of close day
						$('input[name="time[' + days_key[i] + '][am]"]', $edit_row).val(null);
						$('input[name="time[' + days_key[i] + '][pm]"]', $edit_row).val(null);

						i++;
						$('input[name="time[' + days_key[i] + '][am]"]', $edit_row).val(val);
					}

				}
				if (loop === 2)
				{
					$('input[name="time[' + days_key[i] + '][pm]"]', $edit_row).val(val);
				}
				if (loop === 2) {
					loop = 0;
					i++;
				}
				loop++;
			});
			//if sunday is close day
			if (i === 6) {
				$('input[name="time[sun][am]"]', $edit_row).val(null);
				$('input[name="time[sun][pm]"]', $edit_row).val(null);
			}
		}
	};

})(jQuery);