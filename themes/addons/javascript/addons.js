jQuery(function($) {
	$('#readme-toggle').on('click', function() {
		$(this).parents('#readme').toggleClass('collapsed');
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

	$('[data-copies-field]').on('click', function(e) {
	    var button = $(e.target);
	    var message = button.siblings('.copy-field__confirmation');
	    var fields = $('[data-copiable-field=' + button.data('copies-field') + ']');

	    if (fields.length === 1) {
            fields[0].select();
            document.execCommand('copy');

            // Notify the user visually / audibly that the copy succeeded
            button.addClass('copy-field__button--triggered');
            message.attr('aria-hidden', 'false');
            message.text('Copied!');
            setTimeout(function() {
                this.removeClass('copy-field__button--triggered');
                message.attr('aria-hidden', 'true');
            }.bind(button), 1000);
        }
    });
});
