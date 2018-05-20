<?php
if( !class_exists('tm_mbr_db') ){
	require_once( plugin_dir_path( __FILE__ ).'classes/class-db.php');
}
global $wpdb;

function tm_mbr_team_member($atts){
	$shortcode = "<form id='login_form' action='".home_url('/')."member-home' method='post' >";
	$shortcode .= "<h2>Log In</h2><p class='form_result err_msg'></p>";
	$shortcode .= "<label for='mail'>EMAIL:</label> <input type='text' name='mail' id='mail'> <span class='err_msg'></span> <br>";
	$shortcode .= "<label for='pwd'>Password:</label> <input type='password' name='pwd' id='pwd'> <span class='err_msg'></span> <br>";
	$shortcode .= "<input type='hidden' id='u_id' name='id' >";
	$shortcode .= "<input type='submit' value='Log In' id='login' name='tm_mbr_login'> ";
	$shortcode .= "<a href='".home_url('/')."member-registration' class='tm_mbr_register'>click here to register</a>";
	$shortcode .= "</form>";
	return $shortcode;
}
add_shortcode('team_member_login', 'tm_mbr_team_member');

function tm_mbr_registration_function($atts){
	global $wpdb;
	$dept_tbl = $wpdb->prefix.'tm_mbr_department';
	$departments = new tm_mbr_db(
							$dept_tbl
						);
	$department_list = $departments->get_all_rows();

	$shortcode  = "<div class='tm_mbr_front'>";
	$shortcode .= "<form id='reg_form' action='' method='post' >";
	$shortcode .= "<h2>REGISTRATION</h2><p class='err_msg'></p>";
	$shortcode .= "<p>Username: <input type='text' name='uname' id='user_name'><span class='err_msg'></span></p>";
	$shortcode .= "<p>Password: <input type='password' name='pwd' id='pwd'> <span class='err_msg'></span></p>";
	$shortcode .= "<p>EMAIL: <input type='text' name='mail' id='user_email'><span class='err_msg'></span></p> ";
	$shortcode .= "<p>DEPARTMENT:<select id='user_dept' class='tm_mbr_form_field' name='department' /></p>";
	$shortcode .= "<option value=''>Select Department</option>";
	if( !empty($department_list)){
		foreach( $department_list as $dept_name => $dept_val){
			$shortcode .= "<option value= $dept_val[ID] > $dept_val[NAME]</option>";
		}
	}else{ 
			$shortcode .= "<option value= ''> No Departments Found </option>";				
		} 
	$shortcode .= "</select><span class='err_msg'></span></p>";
	$shortcode .= "<p><input type='submit' id='register_user' value='SUBMIT' name='tm_mbr_login'></p> ";
	$shortcode .= "</form>";
	$shortcode .= "</div>";
	return $shortcode;
}
add_shortcode('tm_mbr_registration', 'tm_mbr_registration_function');

function tm_mbr_home_function($atts){
	require_once(plugin_dir_path( __FILE__ ).'../../tm_mbr_member-page-template.php');
}
add_shortcode('tm_mbr_home', 'tm_mbr_home_function');


