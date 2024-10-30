jQuery(document).ready(function($) {
	$( document ).ajaxComplete(function( event, request, settings ) {
		
		if(settings.data) { // filter post calls.
			// check if et is calling for shortcode rendering
			var shortcode_rendering = settings.data.indexOf('action=et_fb_ajax_render_shortcode') !== -1; 
			//check if its an instagram call.
			var is_instagram_call = settings.data.indexOf('=et_pb_df_instagram_feed') !== -1;

			// console.log('shortcode', shortcode_rendering);
			// console.log('instagram', is_instagram_call);

			if(shortcode_rendering && is_instagram_call){
				df_sbi_init();
			}
		}
	});
});

function df_sbi_init(){
//	console.log('rendering instagram feed. Please wait ...');
	sbi_init(function(imagesArr,transientName) {
		sbi_cache_all(imagesArr,transientName);
	});
}
