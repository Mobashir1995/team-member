<?php
/*
Plugin Name: Wordpress Team Member
Plugin URI: #
Description: Wordpress Team Member Plugins
Author: baseonesolutions
Version: 6
Author URI: http://baseonesolutions.com/
Text Domain: wordpress-team-member
*/
?>
<?php

//Create Essential Tables
function tm_mbr_create_table(){
	global $wpdb;
	$charset_collation = $wpdb->get_charset_collate();
	$table_name = $wpdb->prefix.'tm_mbr_department';

	if( $wpdb->get_var("show tables like '$table_name'") != $table_name ){
		$sql = "CREATE TABLE $table_name (
					ID 			INT(9) 				NOT NULL AUTO_INCREMENT,
					NAME 		VARCHAR(40) 				NOT NULL,
					DEPT_HEAD	VARCHAR(80)		DEFAULT ''	NOT NULL,
					PRIMARY KEY  (ID)
				) $charset_collation;";
		
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta($sql);
	}

	$table_name = $wpdb->prefix.'tm_mbr_team_members';
	if( $wpdb->get_var("show tables like '$table_name'") != $table_name){
		$sql = "CREATE TABLE $table_name (
					ID 			INT(9)								NOT NULL AUTO_INCREMENT,
					NAME 		VARCHAR(40)							NOT NULL,
					PASSWORD 	VARCHAR(40)							NOT NULL,		
					EMAIL		VARCHAR(40)							NOT NULL,
					ROLE 		VARCHAR(20)		DEFAULT 'employee'	NOT NULL,
					DEPARTMENT 	INT(9)								NOT NULL,
					PRIMARY KEY  (ID)
				) $charset_collation;";

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta($sql);

	}
}

register_activation_hook( __FILE__, 'tm_mbr_create_table' );




if( !class_exists(tm_mbr_menus) ){
	require_once( plugin_dir_path( __FILE__ ).'/inc/admin/classes/class-tm_mbr_menu.php' );
}

//Create Menu Page 
function tm_mbr_menu_page_create(){

	if ( empty ( $GLOBALS['admin_page_hooks']['tm_mbr_team_members'] ) ){
		add_menu_page(
			__( 'TEAM MEMBER' , 'wordpress-team-member' ),
			'TEAM MEMBER',
			'manage_options',
			'tm_mbr_team_members',
			'tm_mbr_create_menupage',
			'dashicons-groups',
			70
		);

		add_submenu_page(
			'tm_mbr_team_members',
			__( 'ALL MEMBERS' , 'wordpress-team-member' ),
			'ALL MEMBERS',
			'manage_options',
			'tm_mbr_team_members',
			'tm_mbr_create_menupage'
		);
	}

}

//Callback function for Add Menu Page
function tm_mbr_create_menupage(){
	tm_mbr_menus::tm_mbr_page_main_content('MEMBERS', 'tm_mbr_create_employee');
}
add_action( 'admin_menu', 'tm_mbr_menu_page_create' );

//Create Submenu Page for Add Department
$department = new tm_mbr_menus('tm_mbr_team_members', 'DEPARTMENT', 'tm_mbr_create_department', 'tm_mbr_create_dept');

// Create Page for Add New Member
//$head = new tm_mbr_menus('', 'EMPLOYEE', 'tm_mbr_create_employee', '');

// Create Page for Add New Department
//$dept = new tm_mbr_menus('', 'DEPARTMENTS', 'tm_mbr_create_dept', '');

//Enqueue Scripts and Styles
if(is_admin()){
	function tm_mbr_add_jquery(){
		wp_enqueue_style('stylesheet', plugin_dir_url( __FILE__ ).'css/style.css', array(), 1.0 );

		wp_enqueue_script( 'jquery' );
		//wp_enqueue_script( 'custom', plugin_dir_url( __FILE__ ).'js/custom.js', array(), 1.0, true );
		wp_enqueue_script( 'new-department', plugin_dir_url( __FILE__ ).'js/new-dept.js', array(), 1.0, true );
		wp_localize_script( 'new-department', 'new_dept_ajax', array(
			'ajaxurl'=>admin_url( 'admin-ajax.php' ),
		));
	}
	add_action('init', 'tm_mbr_add_jquery');
}


//Ajax Function for Create New Department 
function tm_mbr_new_dept_process_request(){
	$dept_name = $_POST['dept'];

	global $wpdb;

	$dept_table = $wpdb->prefix.'tm_mbr_department';
	$dept_in_db = $wpdb->get_var( "SELECT NAME FROM $dept_table WHERE NAME = '$dept_name' " );

	if( !class_exists(tm_mbr_db) ){
		require_once( plugin_dir_path( __FILE__ ).'/inc/admin/classes/class-db.php' );
	}
	if( strtolower( $dept_in_db ) == strtolower($dept_name) ){
		$select_name = $wpdb->get_results("SELECT NAME FROM $dept_table WHERE NAME LIKE '".$dept_name."%'  ORDER BY ID DESC" , ARRAY_A);
		$count = count($select_name);
		$select_name2 = $wpdb->get_results("SELECT NAME FROM $dept_table WHERE NAME LIKE '".$dept_name."-$count%'  ORDER BY ID DESC" , ARRAY_A);
		if(count($select_name2) > 0){
			$count++;
			$dept_name .= '-'.$count;
		}else{
			$dept_name .= '-'.$count;
		}
		goto a;

	}else{
		a:{
			$insert_dept = new tm_mbr_db( 
								$dept_table,
								array(),
								array(
									'NAME' 		=> $dept_name,
								),
								array(
									'%s',
									'%s'
								)
							);

			if( $insert_dept->insert_row() ){
				echo "<span class='success_mssg'>Department Created Successfully</span>";
				echo "<span id='insert_id' style='display: none;'>".$wpdb->insert_id."</span>";

			}else{
				echo "Sorry Something Went Wrong";
			}
		}
	}
	wp_die();
}
add_action( 'wp_ajax_nopriv_new_dept', 'tm_mbr_new_dept_process_request' );
add_action('wp_ajax_new_dept', 'tm_mbr_new_dept_process_request');



//Ajax Function for Add New Row On Successfully Add New RECORD 
function tm_mbr_new_record_add_row(){
	$last_insert_id = $_POST['last_id'];
	global $wpdb;

	if( !class_exists(tm_mbr_db) ){
		require_once( plugin_dir_path( __FILE__ ).'/inc/admin/classes/class-db.php' );
	}

	$dept_table = $wpdb->prefix.$_POST['table'];
	$select_name = new tm_mbr_db(
						$dept_table,
						'',
						array(
							array('ID' => $last_insert_id)
						)
					);
	$i=0;
	$all_data = $select_name->get_all_rows();
	foreach( $all_data as $key => $val){
		foreach( $val as $data_key => $data){
			$i++;
			echo "<span id=rgt_info-$i style='display: none'>$data</span>";
		}
		if($_POST['table'] == 'tm_mbr_team_members'){
			$select_dept_name = $wpdb->get_var( "SELECT NAME FROM ".$wpdb->prefix."tm_mbr_department WHERE ID = '".$val['DEPARTMENT']."' " );
			echo "<span id='department_name' style='display: none'>$select_dept_name</span>";
		}
	}
	wp_die();
}
add_action( 'wp_ajax_nopriv_newly_added_row', 'tm_mbr_new_record_add_row' );
add_action('wp_ajax_newly_added_row', 'tm_mbr_new_record_add_row');



//Ajax Function for Delete data from table
function tm_mbr_ajax_delete_data(){
	$id = $_POST['id'];
	global $wpdb;

	$dept_table = $wpdb->prefix.$_POST['table'];
	if( !class_exists(tm_mbr_db) ){
		require_once( plugin_dir_path( __FILE__ ).'/inc/admin/classes/class-db.php' );
	}

	$delete = new tm_mbr_db(
					$dept_table,
					'',
					array( 'ID' => $id),
					array('%d')
				);
	if($delete->delete_rows()){
		echo '1';
	}else{
		echo 'Sorry! not deleted';
	}
	wp_die();
}
add_action( 'wp_ajax_nopriv_ajax_delete_data', 'tm_mbr_ajax_delete_data' );
add_action('wp_ajax_ajax_delete_data', 'tm_mbr_ajax_delete_data');



//Ajax Function For Add New User
function tm_mbr_ajax_new_user(){
	$user_data = $_POST['user_info'];
	$user_mail = $user_data['mail'];
	global $wpdb;
	$user_table = $wpdb->prefix.'tm_mbr_team_members';
	if( !class_exists(tm_mbr_db) ){
		require_once( plugin_dir_path( __FILE__ ).'/inc/admin/classes/class-db.php' );
	}

	$user_mail_in_db = $wpdb->get_var( "SELECT EMAIL FROM $user_table WHERE EMAIL = '$user_mail' " );
	$pre_head = $wpdb->get_var( "SELECT ROLE FROM $user_table WHERE ROLE = 'head' AND DEPARTMENT='".$user_data['dept']."' " );
	//$pre_dept = $wpdb->get_var( "SELECT ROLE FROM $user_table WHERE ROLE = 'head' " );

	if( strtolower($user_mail_in_db) == strtolower($user_data['mail']) ){
		echo 'Sorry! A User With this Email has already been exist. Use A different Email.';
	}elseif( strtolower($pre_head) == $user_data['role'] ){
		echo "You can't add more than 1 head for a department";
	}else{
		$new_user = new tm_mbr_db(
							$user_table,
							'',
							array(
								'NAME' 		=> $user_data['name'],
								'PASSWORD'	=> $user_data['pwd'],
								'EMAIL'		=> $user_data['mail'],
								'DEPARTMENT'=> $user_data['dept'],
								'ROLE'		=> $user_data['role']
							),
							array(
								'%s',
								'%s',
								'%s',
								'%s',
								'%s'
							)
						);

		$new_user_insert = $new_user->insert_row();
		if($new_user_insert){
			echo "<span class='success_mssg'>User Added Successfully</span>";
			echo "<span id='insert_id' style='display: none;'>".$wpdb->insert_id."</span>";
		}else{
			echo "Sorry! Something Went Wrong. User Not Added";
		}
	}
	wp_die();
}
add_action( 'wp_ajax_nopriv_ajax_new_user', 'tm_mbr_ajax_new_user' );
add_action('wp_ajax_new_user', 'tm_mbr_ajax_new_user');
?>
