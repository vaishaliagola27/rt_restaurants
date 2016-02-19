/* tooltip for close day preview */
jQuery(document).ready(function ($) {
	$('.info-timing').tooltipster({
		content: $('<div> <strong>Display on site!</strong><br /><img src="'+url.theme_url+'/assets/images/tooltip_timing.png" /></div>'),
		animation: 'fade',
		trigger: 'hover'
	});
});