jQuery( document ).ready(function() {
	console.log("sdsdsdsdsd");

	jQuery('input[name="void_check"]').click(function () {
		
		var radioValue = jQuery("input[name='void_check']:checked").val();	
		console.log(radioValue);

		if ( radioValue == 'approved_file') {

			jQuery(this).closest('.upload_btn_check').find('.upload_buttons').toggle();
		}else{

			
			jQuery(this).closest('.void_check_btn').find('.show_void_text').toggle();
			

		}
	});


});