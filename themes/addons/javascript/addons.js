jQuery(function($) {
	$("#readme-toggle").on("click", function() {
		$(this).parents("#readme").toggleClass("collapsed");
		return false;
	});
	
	// Bootstrap tooltips
	$('[data-toggle=tooltip]').tooltip();

	$('#popularsort').on('change', function() {
		var selection = $(this).val();
		$.post('/addonSort', {'type': selection}, function(result) {
			if(result.success) {
				$('#popularAddons').html(result.body);
				// We need to re-bind the tooltips
                $('[data-toggle=tooltip]').tooltip();
            }
		}, 'JSON')
	});
});
