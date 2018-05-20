jQuery(document).ready(function(){
	jQuery('.image_upload_text').text( jQuery('.image_upload_result').text() );
	jQuery(document).on('click', '#register_user', function(e){
		e.preventDefault();
		var u_name = jQuery('#reg_form #user_name');
		var pass = jQuery('#reg_form #pwd');
		var mail = jQuery('#reg_form #user_email');
		var dept = jQuery('#reg_form #user_dept');
		var err_length = 0;

		if( u_name.val() == '' ){
			u_name.addClass('err');
			u_name.next().show().text("This Field Can't be empty");
		}else{
			u_name.removeClass('err');
			u_name.next().hide();
		}

		if( pass.val() == '' ){
			pass.addClass('err');
			pass.next().show().text("This Field Can't be empty");
		}else{
			pass.removeClass('err');
			pass.next().hide();
		}

		if( !validMail( mail.val() ) ){
			mail.addClass('err');
			mail.next().show().text("Please Enter A Valid Email ");
		}else{
			mail.removeClass('err');
			mail.next().hide();
		}

		if( dept.val() == ''){
			dept.addClass('err');
			dept.next().show().text("Please Choose A Department");
		}else{
			dept.removeClass('err');
			dept.next().hide();
		}
		if( jQuery('.err').length > 0 ){
			return false;
		}else{
			
			var user_info = {
							name: u_name.val(),
							pwd: pass.val(),
							mail: mail.val(),
							role: 'employee',
							dept: dept.val()
						}
			jQuery.ajax({
				url: front_end_ajax.ajaxurl,
				type: 'post',
				data: {
					action: 'new_user',
					user_info: user_info
				},
				success: function(result){

					jQuery('.err_msg').html(result);
					var insert_id = jQuery('#insert_id').text();
				},
				error: function(result){
					jQuery('.err_msg').text("Sorry! Not Inserted!");
				}
			});
		}

	});

	jQuery(document).on('click', '#login', function(e){
		e.preventDefault();
		var mail = jQuery('#login_form #mail');
		var pass = jQuery('#login_form #pwd');

		if( !validMail( mail.val() ) ){
			mail.addClass('err');
			mail.next().show().text("Please Enter A Valid Email ");
		}else{
			mail.removeClass('err');
			mail.next().hide();
		}

		if( pass.val() == '' ){
			pass.addClass('err');
			pass.next().show().text("This Field Can't be empty");
		}else{
			pass.removeClass('err');
			pass.next().hide();
		}

		if( jQuery('.err').length > 0 ){
			return false;
		}else{
			jQuery.ajax({
				async: false,
				url: front_end_ajax.ajaxurl,
				type: 'post',
				data: {
					action: 'log_in',
					mail: mail.val(),
					pwd: pass.val()
				},
				success: function(result){
					jQuery('.form_result.err_msg').html(result);
					var result_flag = jQuery('.success_flag').text();
					var new_loc = jQuery('.home_url').text();
					var u_id = jQuery('.form_result.err_msg .user_id').text();
					if( result_flag == 1){
						jQuery('.form_result.err_msg').html("<span class='success_mssg'>Log In Successfull. Please Wait....");
						jQuery('#u_id').val(u_id);
						jQuery('form').attr('action',new_loc).submit();
					}
				},
				error: function(result){
					jQuery('.err_msg').text("Sorry! Something Went Wrong");
				}
			});
		}

	});

	jQuery(document).on('click','.edit_info', function(e){
		e.preventDefault();
		jQuery('.cancel').css('display','inline-block');
		var name = jQuery(this).parents('.user_info').find('.name span').text();
		var mail = jQuery(this).parents('.user_info').find('.mail span').text();
		var department = jQuery(this).parents('.user_info').find('.department span').text();
		var role = jQuery(this).parents('.user_info').find('.role span').text();
		var pass = jQuery(this).parents('.user_info').find('.pwd span').text();
		jQuery(this).text('Update Info').removeClass('edit_info').addClass('update_info');
		jQuery(this).prevAll('p').children().empty().html("<input type='text'>");
		jQuery(this).prev('.pwd').children().empty().html("<input type='password'>");
		jQuery(this).parents('.user_info').find('.pwd').show();
		jQuery(this).parents('.user_info').find('.name span input').val(name);
		jQuery(this).parents('.user_info').find('.mail span input').val(mail);
		jQuery(this).parents('.user_info').find('.department span input').val(department).attr('disabled', true);
		jQuery(this).parents('.user_info').find('.role span input').val(role).attr('disabled', true);
		jQuery(this).parents('.user_info').find('.pwd span input').val(pass);
		jQuery('.user_info h2').after("<p class='err_msg'></p>");
	});

	jQuery('body').on('click','.update_info', function(e){
		e.preventDefault();
		
		var name = jQuery(this).parents('.user_info').find('.name input').val();
		var mail = jQuery(this).parents('.user_info').find('.mail input').val();
		var department = jQuery(this).parents('.user_info').find('.department input').val();
		var role = jQuery(this).parents('.user_info').find('.role input').val();
		var pass = jQuery(this).parents('.user_info').find('.pwd input').val();
var that = this;
		jQuery.ajax({
			//async: false,
			url: front_end_ajax.ajaxurl,
			type: 'post',
			data: {
				action: 'update_user',
				name: name,
				mail: mail,
				pwd: pass
			},
			success: function(result){
				jQuery('.err_msg').html(result);
				var result_flag = jQuery('.result_flag').text();
				if(result_flag == 1){
					jQuery('.cancel').css('display','none');
					jQuery(that).text('Edit Info').removeClass('update_info').addClass('edit_info');
					jQuery(that).prevAll('p').children().empty().html("");
					jQuery(that).parents('.user_info').find('.pwd').hide();

					jQuery(that).parents('.user_info').find('.name span').text(name);
					jQuery(that).parents('.user_info').find('.mail span').text(mail);
					jQuery(that).parents('.user_info').find('.department span').text(department);
					jQuery(that).parents('.user_info').find('.role span').text(role);
					jQuery(that).parents('.user_info').find('.pwd span').text(pass);
					jQuery('.user_info h2').next(".err_msg").remove();
				}
			},
			error: function(result){
				jQuery('.err_msg').html("Sorry! Something Went Wrong");
			}
		});
	});

	jQuery(document).on('click', '.cancel', function(e){
		e.preventDefault();
		jQuery('.user_info h2').next(".err_msg").remove();
		var name = jQuery(this).parents('.user_info').find('.name input').val();
		var mail = jQuery(this).parents('.user_info').find('.mail input').val();
		var department = jQuery(this).parents('.user_info').find('.department input').val();
		var role = jQuery(this).parents('.user_info').find('.role input').val();
		var pass = jQuery(this).parents('.user_info').find('.pwd input').val();

		jQuery('.update_info').text('Edit Info').removeClass('update_info').addClass('edit_info');
		jQuery(this).css('display', 'none');
		jQuery(this).prevAll('p').children().empty().html("");

		jQuery(this).parents('.user_info').find('.name span').text(name);
		jQuery(this).parents('.user_info').find('.mail span').text(mail);
		jQuery(this).parents('.user_info').find('.department span').text(department);
		jQuery(this).parents('.user_info').find('.role span').text(role);
		jQuery(this).parents('.user_info').find('.pwd span').text(pass);
		jQuery(this).parents('.user_info').find('.pwd').hide();
	});

	jQuery(document).on('click','.log_out',function(e){
		e.preventDefault();
		jQuery.post({
			url: front_end_ajax.ajaxurl,
			type: 'post',
			data: {
				action: 'delete_session',
			},
			success: function(){
				jQuery('.user_info').empty().html("<p>Logged Out Successfully</p>");
			},
		});
	});

	function readURL(input) {
		if (input.files && input.files[0]) {
			var reader = new FileReader();
			reader.onload = function (e) {
				jQuery('#profile_img').attr('src', e.target.result);
			}
			reader.readAsDataURL(input.files[0]);
		}
	}

	jQuery("#fileToUpload").on('change',function(){
		readURL(this);
	});

	jQuery(document).on('submit', '#form_img_upload', function(e){
		e.preventDefault();
		var fd = new FormData(this);
		fd.append('action', 'image_upload');
		jQuery.ajax({
			url: front_end_ajax.ajaxurl,
			type: 'post',
			contentType: false, 
			cache: false,
			processData:false, 
			data: fd,
			success: function(result){
				jQuery('.image_upload_text').html(result);
				var result_text = jQuery('.image_upload_result').text();
				var result_flag = jQuery('.result_flag').text();
				if(result_flag == 1){
					jQuery('.image_upload_text').html("<span class='success_mssg'> "+result_text+"</span>");
				}else{
					jQuery('.image_upload_text').html("<span class='err_msg'> "+result_text+"</span>");
				}
			},
		});
	});

	function validMail(email){
		var filter = /^[a-z][a-zA-Z0-9_.]*(\.[a-zA-Z][a-zA-Z0-9_.]*)?@[a-z][a-zA-Z-0-9]*\.[a-z]+(\.[a-z]+)?$/;
		if(filter.test(email)){
			return true;
		}else{
			return false;
		}
	}
})