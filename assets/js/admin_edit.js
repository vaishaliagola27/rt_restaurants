(function($) {

	// we create a copy of the WP inline edit post function
	var $wp_inline_edit = inlineEditPost.edit;

	// and then we overwrite the function with our own code
	inlineEditPost.edit = function( id ) {

		// "call" the original WP edit function
		// we don't want to leave WordPress hanging
		$wp_inline_edit.apply( this, arguments );

		// now we take care of our business

		// get the post ID
		var $post_id = 0;
		if ( typeof( id ) == 'object' ) {
			$post_id = parseInt( this.getId( id ) );
		}

		if ( $post_id > 0 ) {
			// define the edit row
			var $edit_row = $( '#edit-' + $post_id );
			var $post_row = $( '#post-' + $post_id );

			// address data display
			var $address = $( '.column-address', $post_row ).find('span');
			$address.each(function(){
				var attribute = $(this).attr('itemprop');
				var val = $(this).text();
				
				$( 'input[name="restaurant_add[' + attribute +']"]', $edit_row ).val( val );
			});
			
			//contact data display
			var $contact = $( '.column-contactno', $post_row ).text();
			if($contact == ""){
				$( ':input[name="restaurant_contact_no"]', $edit_row ).val( "" );				
			}else{
				$( ':input[name="restaurant_contact_no"]', $edit_row ).val( $contact );				
			}

			//timing table display
//			var $time = $( '.column-timing', $post_row ).find('span');
//			var days_key = new Array("mon","tue", "wed", "thu", "fri", "sat", "sun");
//			var i=0,a=0,p=0;
//			$time.each(function(days_key){
//				var val = $(this).text();
//				if(i%2 == 0){
//					var attr_am =$(this).attr('id');//days_key[a]+"-am");
//					
//					$( 'input[name="time[mon][]"]', $edit_row ).val( val );
//					a++;
//				}
//				else{
//					var attr_pm =$(this).attr(days_key[p]+"-pm");
//					$( 'input[name="time[mon][]"]', $edit_row ).val( val );
//					p++;
//				}
//				i++;
//			});
		}
	};

})(jQuery);