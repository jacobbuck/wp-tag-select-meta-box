jQuery(function ($) {

	$(".tagselect-wrap").each(function () {
		
		var select = $(".tagselect-select:first", this),
			is_mobile = $("body").hasClass("mobile");
		
		if (! is_mobile)
			select.chosen({allow_single_deselect : true});
		
		if ($(".tagselect-add-wrap:first", this).length) {
			
			function add_terms () {
				var add_values = add_text.val().split(",");
				add_text.val("");
				if (add_values.length) {
					for (var key = 0, val; key < add_values.length; key++)
						if (val = add_values[key].replace(/(^\s*|\s*$)/, ""))
							select.prepend( $("<option>", {"selected":"selected"}).text(val) );
					if (! is_mobile)
						select.trigger("liszt:updated");
				}
				return false;
			}
			
			var add_button = $(".tagselect-add-button:first", this),
				add_text = $(".tagselect-add-text:first", this);
				
			add_button.click(add_terms);
			
			add_text.keypress(function(event) {
				if (event.which == 13)
					return add_terms(event);
			});
			
		}
		
	});

});