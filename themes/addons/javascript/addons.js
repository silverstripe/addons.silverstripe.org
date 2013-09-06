jQuery(function($) {
	$("#readme-toggle").on("click", function() {
		$(this).parents("#readme").toggleClass("collapsed");
		return false;
	});

	$(document).ready(function() {
		// Show 2.4 specific notice if box is ticked
		$('#legacy-search-notice').hide();
		$('input[name="compatibility[]"').click(function(e) {
			var doShow = $('input[name="compatibility[]"][value="2.4"]').is(':checked');
			$('#legacy-search-notice')[doShow ? 'show' : 'hide']();
		});
	});
});
