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
  // Handler for .ready() called.
   jQuery( "#multi-7208-0" ).hide();
   
   jQuery( "#multi-7208-0 .close-btn" ).click(function() {
	  jQuery( "#multi-7208-0" ).hide();
	});

   jQuery( ".upload_gallary" ).click(function() {
	  jQuery( "#multi-7208-0" ).show();
	});


    jQuery( "#multi-7208-0 .close-btn" ).click(function() {
    	
    	var image_gallery = [];
    	jQuery('.preview').each(function(){
	   		image_gallery.push(jQuery(this).attr('link'));
		});
		image_gallery_c = image_gallery.join();
		jQuery('#file_dealer').html(image_gallery_c)
	});

});