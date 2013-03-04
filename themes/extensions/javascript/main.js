jQuery(function ($) {
	$("input[data-filter]").on("keyup", function () {
		var filter = this.value.toLowerCase();
		var target = $($(this).data("filter"));

		target.children().each(function () {
			if (this.textContent.toLowerCase().indexOf(filter) !== -1) {
				this.style.display = "block";
			} else {
				this.style.display = "none";
			}
		});
	});

	$(".readme .toggle").on("click", function () {
		$(this).parents(".readme").toggleClass("collapsed");
		return false;
	});
});
