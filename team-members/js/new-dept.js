jQuery(document).ready(function(){

	var table_name = jQuery('.tm_mbr_right table').data('table');
	jQuery('.tm_mbr_left table tr td .err_msg').hide();//hide add user validate messages
	jQuery('.ajax_mail').hide();


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
		var role = jQuery('#user_role');
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
					jQuery('.tm_mbr_right table .no_dept').parents('tr').remove();
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

//Department Table Update option open function
	jQuery(document).on('click', '#department_table table#all_department tbody .inline_options .edit', function(e){
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

			current_tr.append( "<div class='update' ><div class='clear'></div><div class='update_inn'></div><div class='clear'></div></div>" );
			jQuery('.update_inn').html( "<form class='update_form'><div class='inline-edit-col'><div class='update_inn_content'><label for='update_dept_name'><span class='title'>Name:</span><span class='input-text-wrap'><input type='text' id='update_dept_name' autocomplete='off'><p></span></label></div><div class='update_inn_content'><label for='update_dept_head'><span class='title'>Head EMAIL:</span><span class='input-text-wrap'><input type='text' id='update_dept_head' autocomplete='off'><p></p></span></label><div class='ajax_mail'></div></div></div><p class='update_info'><input type='submit' id='cancel_update_dept' class='cancel button alignleft' value='Cancel'><input type='submit' id='submit_update_dept' class='button button-primary alignright' value='Update'><div class='clear'></div></p></form>" );
		}

		current_tr.find('#update_dept_name').val( current_tr.find('.dept_name').text() );
		current_tr.find('#update_dept_head').val( current_tr.find('.dept_head_name').data('user_mail') );
	});

//Cancel Update operation For Department Table
	jQuery(document).on('click', '#cancel_update_dept', function(e){
		e.preventDefault();
		
		var current_tr = jQuery(this).parents('tr.no_display');
		if( jQuery(this).parents('tr').hasClass('no_display') ){
			jQuery('.tm_mbr_right table tbody tr').find('.inline_options').show();
			jQuery('.update').remove();
			current_tr.removeClass('no_display');
		}
	});

//Update operation for Department Table
	jQuery(document).on('click', '#department_table #submit_update_dept', function(e){
		e.preventDefault();
		var dept_name = jQuery(this).parents('.update_inn').find('.update_inn_content #update_dept_name');
		var head_name = jQuery(this).parents('.update_inn').find('.update_inn_content #update_dept_head');
		var id = jQuery(this).parents('.no_display').data('id');

		if( dept_name.val() ==''){
			dept_name.next().addClass('err_msg');
			dept_name.next().text("This Field Can't be empty" );
		}else{
			dept_name.next().removeClass('err_msg');
			dept_name.next().text("" );
		}


		if( jQuery('.update_inn_content .err_msg').length == 0 ){
			jQuery.ajax({
				url: new_dept_ajax.ajaxurl,
				type: 'post',
				data: {
					action: 'update_dept',
					table: table_name,
					dept: dept_name.val(),
					head: head_name.val(),
					id: id
				},
				success: function(result){
					if(result == 1){
						jQuery('#department_table .err_msg').html("<span class='success_mssg'>Updated</span>");
						
					}else{
						jQuery('#department_table .err_msg').text(result);
					}
				}
			});
		}
	});

	jQuery(document).on('keyup', '#department_table #update_dept_head, #member_table .user-search-box input[type=text]', function(){
		var text = jQuery(this).val();
		jQuery.ajax({
			url: new_dept_ajax.ajaxurl,
			type: 'post',
			data: {
				action: 'name_suggest',
				table: 'tm_mbr_team_members',
				name: text
			},
			success: function(result){
				jQuery('.ajax_mail').show();
				jQuery('.ajax_mail').empty().html(result);
			}
		});
	});

	jQuery(document).on('click', '.ajax_mail a', function(e){
		e.preventDefault();
		var mail_addr = jQuery(this).text();

		jQuery(this).parents('.update_inn_content').find('#update_dept_head').val(mail_addr);
		jQuery(this).parents('.user-search-box').find('input[type=text]').val(mail_addr);
		jQuery('.ajax_mail').hide();
	});

	jQuery(document).on('click',function(){
		jQuery('.ajax_mail').hide();
	});


	//User Table Update option open function
	jQuery(document).on('click', '#member_table table#all_department tbody .inline_options .edit', function(e){
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

			current_tr.append( "<div class='update' ><div class='clear'></div><div class='update_inn'></div><div class='clear'></div></div>" );
			jQuery('.update_inn').html( "<form class='update_form'><div class='inline-edit-col'><div class='update_inn_content'><label for='update_user_name'><span class='title'>Name:</span><span class='input-text-wrap'><input type='text' id='update_user_name' autocomplete='off'><p class='err_msg'></p></span></label></div><div class='update_inn_content'><label for='update_user_email'><span class='title'>EMAIL:</span><span class='input-text-wrap'><input type='text' id='update_user_email' autocomplete='off'><p class='err_msg'></p></span></label></div>   <div class='update_inn_content'><label for='update_user_role'><span class='title'>ROLE:</span><span class='input-text-wrap'><select id='update_user_role'><option value='head' >HEAD</option><option value='employee' >EMPLOYEE</option></select><p class='err_msg'></p></span></label></div>    <div class='update_inn_content'><label for='update_user_department'><span class='title'>DEPARTMENT:</span><span class='input-text-wrap'><select id='update_user_department'></select><p class='err_msg'></p></span></label></div>     <div class='update_inn_content'><label for='update_user_pwd'><span class='title'>PASSWORD:</span><span class='input-text-wrap'><input id='update_user_pwd' type='password'><p class='err_msg'></p></span></label></div>      </div>  <div class='clear'></div>  <p class='update_info'><input type='submit' id='cancel_update_dept' class='cancel button alignleft' value='Cancel'><input type='submit' id='submit_update_dept' class='button button-primary alignright' value='Update'></p><div class='clear'></div></form>" );
		}

		var count = 0;
		jQuery('#user_dept option').each(function(){
			if( count == 0 ){ count++; return; }
			var val = jQuery(this).val();
			var text = jQuery(this).text();
			jQuery('<option>').val(val).text(text).appendTo('#update_user_department');
		});

		jQuery('.no_display .err_msg').hide();
		current_tr.find('#update_user_name').val( current_tr.find('.user_name').text() );
		current_tr.find('#update_user_email').val( current_tr.find('.user_email').data('user_email') );
		current_tr.find('#update_user_role').val( current_tr.find('.user_role').data('user_role') );
		current_tr.find('#update_user_department').val( current_tr.find('.user_dept').data('user_dept') );
		current_tr.find('#update_user_pwd').val( current_tr.find('.user_pwd').data('user_pwd') );
	});


	//User Update operation for User Table
	jQuery(document).on('click', '#member_table #submit_update_dept', function(e){
		e.preventDefault();
		var id = jQuery(this).parents('.no_display').data('id');
		var u_name = jQuery(this).parents('.update_inn').find('.update_inn_content #update_user_name');
		var mail = jQuery(this).parents('.update_inn').find('.update_inn_content #update_user_email');
		var role = jQuery(this).parents('.update_inn').find('.update_inn_content #update_user_role');
		var dept = jQuery(this).parents('.update_inn').find('.update_inn_content #update_user_department');
		var pass = jQuery(this).parents('.update_inn').find('.update_inn_content #update_user_pwd');

		var current_tr = jQuery(this).parents('tr.no_display');
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


		if( jQuery(this).parents('.update_inn').find('.update_inn_content .err').length > 0 ){
			jQuery('#member_table .main_err.err_msg').text("Please Fix All Errors.");
		}else{
			jQuery.ajax({
				url: new_dept_ajax.ajaxurl,
				type: 'post',
				data: {
					action: 'update_admin_user',
					table: table_name,
					u_name: u_name.val(),
					mail: mail.val(),
					role: role.val(),
					dept: dept.val(),
					pass: pass.val(),
					id: id
				},
				success: function(result){
					jQuery('#member_table .main_err.err_msg').html(result);
					var result_flag = jQuery('.result_flag').text();
					if(result_flag == 1){
						jQuery('#member_table .main_err.err_msg').html("<span class='success_mssg'>Updated</span>");
					}else{
						//jQuery('#member_table .main_err.err_msg').text('Sorry Not Updated!');
					}
				},
				error: function(){
					jQuery('#member_table .main_err.err_msg').text("sorry! Ajax Request Cant' Process");
				}
			});
		}
	});

	var page_name = jQuery('.tm_mbr_main').data('page');
	var user_type = jQuery('.tm_mbr_main .subsubsub li a.current').parents('li').attr('class');
	var last_page_number = jQuery('.tm_mbr_main .paginate a').last().index();
	var prev_page_number = jQuery('.tm_mbr_main .paginate .current').text()-1;
	var last_number = last_page_number+1;
	var next_page_number = parseInt(jQuery('.tm_mbr_main .paginate .current').text())+1;

	jQuery('.tm_mbr_main .paginate .current').prev().addClass('page_number_show').prev().addClass('page_number_show');
	jQuery('.tm_mbr_main .paginate .current').next().addClass('page_number_show').next().addClass('page_number_show');

	if( jQuery('.tm_mbr_main .paginate a').index() < 0 ){
		jQuery('.tm_mbr_main .paginate .current').remove();
	}

	if( jQuery('.tm_mbr_main .paginate .current').index() > 0 ){
		jQuery('.tm_mbr_main .paginate a').eq(0).before("<a class='first' href='?page="+page_name+"&user_type="+user_type+"&page_num=1'><<</a>");
		jQuery('.tm_mbr_main .paginate a.first').after("<a class='prev' href='?page="+page_name+"&user_type="+user_type+"&page_num="+prev_page_number+"'><</a>");
	}else{
		jQuery('.tm_mbr_main .paginate a.first').remove();
	}

	if( jQuery('.tm_mbr_main .paginate .current').index() < last_page_number+2 ){
		jQuery('.tm_mbr_main .paginate a').last().after("<a class='last' href='?page="+page_name+"&user_type="+user_type+"&page_num="+last_number+"'>>></a>");
		jQuery('.tm_mbr_main .paginate a.last').before("<a class='next' href='?page="+page_name+"&user_type="+user_type+"&page_num="+next_page_number+"'>></a>");
	}else{
		jQuery('.tm_mbr_main .paginate a.last').remove();
	}

	jQuery(document).on('click','#member_table .ajax_mail a',function(){
		var email = jQuery('#member_table .user-search-box input[type=text]').val();
		var id = jQuery(this).attr('href');

		jQuery.ajax({
			url: new_dept_ajax.ajaxurl,
			type: 'post',
			data: {
				action: 'search_user_info',
				mail: email,
				id: id
			},
			success: function(result){
				jQuery('#member_table table tbody').empty().html(result);
				jQuery('#member_table .paginate').remove();
			},
			error: function(result){
				alert('error');
			}
		});
	});

	jQuery('.dept_ajax_mail').hide();
	jQuery(document).on('click',function(){
		jQuery('.dept_ajax_mail').hide();
	});
	jQuery(document).on('keypress','.dept-search-box input[type=text]',function(){
		var dept_name = jQuery(this).val();
		jQuery.ajax({
			url: new_dept_ajax.ajaxurl,
			type: 'post',
			data: {
				action: 'suggest_dept',
				dept_name: dept_name
			},
			success: function(result){
				jQuery('.dept_ajax_mail').show();
				jQuery('.dept_ajax_mail').empty().html(result);
			},
		})
	});

	jQuery(document).on('click','.dept_ajax_mail a',function(e){
		e.preventDefault();
		var dept_ID = jQuery(this).attr("href");
		var dept_name = jQuery(this).text(); 
		jQuery.ajax({
			url: new_dept_ajax.ajaxurl,
			type: 'post',
			data: {
				action: 'replace_with_search_dept',
				ID: dept_ID,
				Name: dept_name,
			},
			success: function(result){
				jQuery('#all_department tbody').empty().html(result);
				jQuery('.tm_mbr_right .paginate').remove();
			},
		});
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
});