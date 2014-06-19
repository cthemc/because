// JavaScript Document
jQuery(document).ready(function($){
  var _custom_media = true,
      _orig_send_attachment = wp.media.editor.send.attachment;

  $(document).on('click', '.meta-image-button_upload', function(e) {
	 
	//Custom data attribute added onto each upload button. 
	var $target = $(this).attr('data-img_target');  
	  
    var send_attachment_bkp = wp.media.editor.send.attachment;
    var button = $(this);
    var id = button.attr('id').replace('_button', '');
    _custom_media = true;
   
   
    wp.media.editor.send.attachment = function(props, attachment){
      if ( _custom_media ) {
       var image_id = attachment.id;
	   var thumb_url = attachment.sizes.thumbnail.url;
	   
	   
	   //Add Image ID to hidden input to screen.
	   $("#"+$target+'_id').val(image_id);
	   
	   //Add Selected image to screen.
       $("#"+$target+'_image').attr('src', thumb_url).addClass('active_img');	
	  	console.log(attachment);
	  
	  
	  } else {
        return _orig_send_attachment.apply( this, [props, attachment] );
      };
    }

    wp.media.editor.open(button);
    return false;
  });

  $('.add_media').on('click', function(){
	  alert('its working');
    _custom_media = false;
  });
  
  
  
  
 //Remove Image Button.	  
 $(document).on('click', '.remove_polling_img', function(){
	 var $target = $(this).attr('data-img_target');
	 $('input[type=hidden][data-img_target = '+$target+']').val('');  
	 $('img[data-img_target = '+$target+']').attr('src', '').removeClass('active_img'); 
	 $(this).remove();
 
 
 
 })
  
  
  
  
  
  
});//Doc Ready

/*
jQuery(document).ready(function($){
 
    // Instantiates the variable that holds the media library frame.
    var meta_image_frame;
 
    // Runs when the image button is clicked.
    $('#xmeta-image-button').click(function(e){

        // Prevents the default action from occuring.
        e.preventDefault();
 
        // If the frame already exists, re-open it.
        if ( meta_image_frame ) {
            wp.media.editor.open();
            return;
        }
 		
        // Sets up the media library frame
        var meta_image_frame = wp.media.frames.meta_image_frame = wp.media({
			className: 'media-frame tgm-media-frame',
			frame: 'select',
			multiple: false,
            title: meta_image.title,
            button: { text:  meta_image.button },
            library: { type: 'image' }
        });
		
		
		console.log(meta_image_frame.options.library);
		
			
        // Runs when an image is selected.
        meta_image_frame.on('select', function(){
			
			// Grabs the attachment selection and creates a JSON representation of the model.
            var media_attachment = meta_image_frame.state().get('selection').first().toJSON();
 
            // Sends the attachment URL to our custom image input field.
            $('#meta-image').val(media_attachment.url);
       
	   	
	   
	   
	    });
 
        // Opens the media library frame.
        wp.media.editor.open();
    });
});*/