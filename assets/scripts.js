jQuery(function ($) {

	$(".tagselect-wrap").each(function () {
		
		var select = $(".tagselect-select:first", this);
		
		select.chosen({"allow_single_deselect" : true});
		
		if ($(".tagselect-add-wrap:first").length) {
			
			var add_button = $(".tagselect-add-button:first", this),
				add_text = $(".tagselect-add-text:first", this);
				
			add_button.click(add_terms);
			
			add_text.keypress(function(event) {
				if (event.which == 13)
					return add_terms();
			});
			
			function add_terms () {
				var add_values = add_text.val().split(",");
				add_text.val("");
				if (add_values.length) {
					for (var key = 0, val; key < add_values.length; key++)
						if (val = add_values[key].replace(/(^\s*|\s*$)/, ""))
							select.append( $("<option>", {"selected":"selected"}).text(val) );
					select.trigger("liszt:updated");
				}
				return false;
			}
			
		}
		
	});

});