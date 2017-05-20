/* Customize from here downwards */
jQuery(document).ready( function($) {
	
	
	// Processing Login, Remember, Register and Logout form.  
 	$(document).on('submit', 'form.sl-form, form.sl-logout, form.sl-remember, form.sl-register', function(event){
		//Stop event, add loading pic...
 		event.preventDefault();
 		var form = $(this);
 		var statusElement = form.find('.sl-status');
 		if( statusElement.length == 0 ){
 			statusElement = $('<span class="sl-status"></span>');
 			form.prepend(statusElement);
 		}
 		var ajaxFlag = form.find('.sl-ajax');
 		if( ajaxFlag.length == 0 ){
 			ajaxFlag = $('<input class="sl-ajax" name="sl" type="hidden" value="1" />');
 			form.prepend(ajaxFlag);
 		}
		$('<div class="sl-loading"></div>').prependTo(form);
		//Make Ajax Call
		var form_action = form.attr('action');
		if( typeof SL !== 'undefined' ) form_action = SL.ajaxurl;
		$.ajax({
			type : 'POST',
			url : form_action,
			data : form.serialize(),
			success : function(data){
				slAjax( data, statusElement );
				$(document).trigger('sl_' + data.action, [data, form]);
			},
			error : function(){ slAjax({}, statusElement); },
			dataType : 'jsonp'
		});
		//trigger event
	});
 	
	
 	//Catch login actions
 	$(document).on('sl_login', function(event, data, form){
		if(data.result === true){
			//Login Successful - Extra stuff to do
			if( data.widget != null ){
				$.get( data.widget, function(widget_result) {
					var newWidget = $(widget_result); 
					form.parent('.sl').replaceWith(newWidget);
				});
			}
		}
 	});
	
	
	//Catch logout actions
 	$(document).on('sl_logout', function(event, data, form){
		if(data.result === true){
			if( data.widget != null ){
				$.get( data.widget, function(widget_result) {
					var newWidget = $(widget_result);  
					form.parent('.sl').replaceWith(newWidget);
				});
			}
		}
 	});
	
	
	// Show register form when clicking on Lost your password
	$(document).on('click', '.sl-links-register', function(event){
		var register_form = $(this).parents('.sl').find('.sl-register');
		if( register_form.length > 0 ){
			event.preventDefault();
			register_form.show();
			$(this).parents('.sl').find('.sl-remember').hide();
			$(this).parents('.sl').find('form.sl-form').hide();
		}
	});
	
	
	// Show remember form when clicking on Lost your password
	$(document).on('click', '.sl-links-remember', function(event){
		var remember_form = $(this).parents('.sl').find('.sl-remember'); 
		if( remember_form.length > 0 ){ 
			event.preventDefault();
			remember_form.show();
			$(this).parents('.sl').find('.sl-register').hide();
			$(this).parents('.sl').find('form.sl-form').hide();	
		}
	});
	
	
	// Show initial login screen again
	$(document).on('click', '.sl-links-remember-cancel', function(event){
		event.preventDefault();
		$(this).parents('.sl-remember').hide();
		$(this).parents('.sl').find('form.sl-form').show();
	});
	
	
	// Show initial login screen again
	$(document).on('click', '.sl-links-register-cancel', function(event){
		event.preventDefault();
		$(this).parents('.sl-register').hide();
		$(this).parents('.sl').find('form.sl-form').show();
	});
	
	
	//Handle a AJAX call for Login, RememberMe or Registration
	function slAjax( data, statusElement ){
		$('.sl-loading').remove();
		statusElement = $(statusElement);
		if(data.result === true){
			//Login Successful
			statusElement.attr('class','sl-status sl-status-confirm').html(data.message); //modify status content
		}else if( data.result === false ){
			//Login Failed
			statusElement.attr('class','sl-status sl-status-invalid').html(data.error); //modify status content
			//We assume a link in the status message is for a forgotten password
			statusElement.find('a').click( function(event){
				var remember_form = $(this).parents('.sl').find('form.sl-remember');
				if( remember_form.length > 0 ){
					event.preventDefault();
					remember_form.show();
					$(this).parents('.sl').find('form.sl-register').hide();
			        $(this).parents('.sl').find('form.sl-form').hide();	
				}
			});
		}else{	
			//If there already is an error element, replace text contents, otherwise create a new one and insert it
			statusElement.attr('class','sl-status sl-status-invalid').html('An error has occured. Please try again.'); //modify status content
		}
	}
	
	
	// Clear input fields after refresh and after loading page
	$('input[type="text"]').val('');
	
	

});