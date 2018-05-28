jQuery(document).ready(function($) {
    $('#export-button').click(function(){
    	var post_id = $(this).attr('data-id');
		callAjaxExportFile(post_id);	        
    });

    function callAjaxExportFile(post_id) {
    	jQuery.ajax({
            type: "POST",
            url: ajax_obj.ajax_url,
            data: {
            	post_id: post_id,
                action: 'export_file',
            },
            success: function(response) {
            	if(response.data != null) {
            		window.location.replace(response.data);
            	}
            }
        });
    }
});
 