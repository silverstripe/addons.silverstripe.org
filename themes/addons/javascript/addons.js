jQuery(function($) {
	$("#readme-toggle").on("click", function() {
		$(this).parents("#readme").toggleClass("collapsed");
		return false;
	});
});
