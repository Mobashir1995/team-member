jQuery(document).ready(function(){

	var table_name = jQuery('.tm_mbr_right table').data('table');
	jQuery('.tm_mbr_left table tr td .err_msg').hide();//hide add user validate messages

	jQuery(document).on('click', '#save_dept',function(){
		var dept_name = jQuery('#dept_name').val();
		if( dept_name == '' ){
			jQuery('.tm_mbr_left .err_msg').text("Department name can't be empty");
			jQuery('#dept_name').addClass('err');
		}else{
			jQuery('#dept_name').removeClass('err');
			jQuery('.err_msg').empty();
		}
		if( jQuery('.err').length > 0 ){
			return false;
		}else{
			jQuery.ajax({
				url: new_dept_ajax.ajaxurl,
				type: 'post',
				data: {
					action: 'new_dept',
					dept: dept_name
				},
				success: function(result){
					jQuery('.no_dept').remove();
					jQuery('.tm_mbr_left .err_msg').html(result);
					var insert_id = jQuery('#insert_id').text();

					jQuery.ajax({
						url: new_dept_ajax.ajaxurl,
						type: 'post',
						data: {
							action: 'newly_added_row',
							last_id: insert_id,
							table: table_name
						},
						success: function(last_result){
							jQuery('.tm_mbr_right .err_msg').html(last_result);
							var ins_id = jQuery('#rgt_info-1').text();
							var insert_dept = jQuery('#rgt_info-2').text();
							
							jQuery('.tm_mbr_right table tbody').prepend("<tr class='new_ins_row'><td><a class='new_ins_dept'></a><div class='inline_options'> <a href='#' class='edit'>Edit</a><a href='#' class='delete'>Delete</a></div></td></tr>");
							jQuery('.tm_mbr_right table tbody .new_ins_row').eq(0).find('.new_ins_dept').text(insert_dept);
							jQuery('.tm_mbr_right table tbody .new_ins_row').eq(0).find('.inline_options .delete').attr("href", ins_id);
						}
					})

				},
				error: function(result){
					jQuery('.tm_mbr_left .err_msg').text("Sorry! Not Inserted!");
				}
			});
			return false;
		}
	});

	jQuery(document).on('click', '.tm_mbr_right table tbody .inline_options .delete', function(e){
		e.preventDefault();
		var id = jQuery(this).attr('href');
		jQuery(this).addClass('to_be_delete');

		jQuery.ajax({
			url: new_dept_ajax.ajaxurl,
			type: 'post',
			data: {
				action: 'ajax_delete_data',
				table: table_name,
				id: id
			},
			success: function(result){
				if(result == 1){
					jQuery('.to_be_delete').parents('tr').css('background','red');
					jQuery('.to_be_delete').parents('td').css('background','red');
					jQuery('.to_be_delete').parents('td').siblings('td').css('background','red');
					jQuery('.to_be_delete').parents('tr').fadeOut(400,function(){
						jQuery('.to_be_delete').parents('tr').remove();
					});
				}else{
					jQuery('.tm_mbr_right .err_msg').text(result);
				}
			},
			error: function(result){
				jQuery('.tm_mbr_left .err_msg').text(result);
			}
		});
	});

	jQuery(document).on('click', '#save_member', function(e){
		e.preventDefault();
		var u_name = jQuery('#user_name');
		var pass = jQuery('#pwd');
		var mail = jQuery('#user_email');
		var role = jQuery('#user_role')
		var dept = jQuery('#user_dept');
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

		if(role.val() == ''){
			role.addClass('err');
			role.next().show().text("Please Enter A Valid Email ");
		}else{
			role.removeClass('err');
			role.next().hide();
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
							role: role.val(),
							dept: dept.val()
						}
			jQuery.ajax({
				url: new_dept_ajax.ajaxurl,
				type: 'post',
				data: {
					action: 'new_user',
					user_info: user_info
				},
				success: function(result){
					jQuery('.tm_mbr_left .err_msg').html(result);
					var insert_id = jQuery('#insert_id').text();

					jQuery.ajax({
						url: new_dept_ajax.ajaxurl,
						type: 'post',
						data: {
							action: 'newly_added_row',
							table: table_name,
							last_id: insert_id
						},
						success: function(last_result){
							jQuery('.tm_mbr_right .err_msg').html(last_result);
							var ins_id = jQuery('#rgt_info-1').text();
							var insert_name = jQuery('#rgt_info-2').text();
							var insert_mail = jQuery('#rgt_info-4').text();
							var insert_role = jQuery('#rgt_info-5').text();
							var insert_dept = jQuery('#department_name').text();
							if( ins_id ){
								jQuery('.tm_mbr_right table tbody').prepend("<tr class='new_ins_row'><td><a class='new_ins_dept'></a><div class='inline_options'> <a href='#' class='edit'>Edit</a><a href='#' class='delete'>Delete</a></div></td></tr>");
								jQuery('.tm_mbr_right table tbody .new_ins_row').eq(0).append("<td class='email'></td><td class='role'></td><td class='department'></td>");
								jQuery('.tm_mbr_right table tbody .new_ins_row').eq(0).find('.new_ins_dept').text(insert_name);
								jQuery('.tm_mbr_right table tbody .new_ins_row').eq(0).find('.email').text(insert_mail);
								jQuery('.tm_mbr_right table tbody .new_ins_row').eq(0).find('.role').text(insert_role);
								jQuery('.tm_mbr_right table tbody .new_ins_row').eq(0).find('.department').text(insert_dept);
								jQuery('.tm_mbr_right table tbody .new_ins_row').eq(0).find('.inline_options .delete').attr("href", ins_id);
							}
						}
					});

				},
				error: function(result){
					jQuery('.tm_mbr_left .err_msg').text("Sorry! Not Inserted!");
				}
			});
		}

	});

	function validMail(email){
		var filter = /^[a-zA-Z0-9]+[a-zA-Z0-9_.-]+[a-zA-Z0-9_-]+@[a-zA-Z0-9]+[a-zA-Z0-9.-]+[a-zA-Z0-9]+.[a-z]{2,4}$/;
		if(filter.test(email)){
			return true;
		}
		else{
			return false;
		}
	}

	jQuery(document).on('click', '.tm_mbr_right table tbody .inline_options .edit', function(e){
		e.preventDefault();
		
		if( !jQuery(this).parents('tr').hasClass('no_display') ){
			jQuery('.tm_mbr_right table tbody tr').removeClass('no_display');
			jQuery(this).parents('tr').addClass('no_display');
		}
		var current_tr = jQuery(this).parents('tr.no_display');
		if( jQuery(this).parents('tr').hasClass('no_display') ){
			jQuery('.tm_mbr_right table tbody tr').find('.inline_options').show();
			current_tr.find('.inline_options').hide();
			jQuery('.update').remove();

			current_tr.append( "<div class='update' ><div class='clear'></div><div class='update_inn'></div></div>" );
			jQuery('.update_inn').html( "<form class='update_form'><div class='inline-edit-col'><div class='update_inn_content'><label for='update_dept_name'><span class='title'>Name:</span><span class='input-text-wrap'><input type='text' id='update_dept_name'></span></label></div><div class='update_inn_content'><label for='update_dept_head'><span class='title'>Head:</span><span class='input-text-wrap'><input type='text' id='update_dept_head'></span></label></div></div><p class='update_info'><input type='submit' id='cancel_update_dept' class='cancel button alignleft' value='Cancel'><input type='submit' id='submit_update_dept' class='button button-primary alignright' value='Update'></p></form>" );
		}

		current_tr.find('#update_dept_name').val( current_tr.find('.dept_name').text() );
		current_tr.find('#update_dept_head').val( current_tr.find('.dept_head_name').text().trim() );
	});

	jQuery(document).on('click', '#cancel_update_dept', function(e){
		e.preventDefault();
		
		var current_tr = jQuery(this).parents('tr.no_display');
		if( jQuery(this).parents('tr').hasClass('no_display') ){
			jQuery('.tm_mbr_right table tbody tr').find('.inline_options').show();
			jQuery('.update').remove();

		}
	});

});