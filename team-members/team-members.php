<?php
/*
Plugin Name: Wordpress Team Member
Plugin URI: #
Description: Wordpress Team Member Plugins
Author: Mobashir
Version: 6
Author URI: #
Text Domain: wordpress-team-member
*/
?>
<?php

session_start();

//Create Essential Tables On Install Plugin
function tm_mbr_create_table(){
	global $wpdb;
	$charset_collation = $wpdb->get_charset_collate();
	$table_name = $wpdb->prefix.'tm_mbr_department';

	if( $wpdb->get_var("show tables like '$table_name'") != $table_name ){
		$sql = "CREATE TABLE $table_name (
					ID 			INT(9) 				NOT NULL AUTO_INCREMENT,
					NAME 		VARCHAR(40) 				NOT NULL,
					PRIMARY KEY  (ID)
				) $charset_collation;";
		
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta($sql);
	}

	$table_name = $wpdb->prefix.'tm_mbr_team_members';
	if( $wpdb->get_var("show tables like '$table_name'") != $table_name){
		$sql = "CREATE TABLE $table_name (
					ID 				INT(9)								NOT NULL AUTO_INCREMENT,
					NAME 			VARCHAR(40)							NOT NULL,
					PASSWORD 		VARCHAR(40)							NOT NULL,		
					EMAIL			VARCHAR(40)							NOT NULL,
					ROLE 			VARCHAR(20)		DEFAULT 'employee'	NOT NULL,
					DEPARTMENT 		INT(9)								NOT NULL,
					PROFILE_PICTURE	VARCHAR(60)							NOT NULL,	
					PRIMARY KEY  (ID)
				) $charset_collation;";

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta($sql);
	}

	$upload = wp_upload_dir();
	$upload_dir = $upload['basedir'];
	$upload_dir = $upload_dir . '/team-member-images';
	if (! is_dir($upload_dir)) {
	   mkdir( $upload_dir );
	}

}

register_activation_hook( __FILE__, 'tm_mbr_create_table' );

//Delete Plugins data on Delete Plugin
function tm_mbr_delete_table(){
	global $wpdb;
	$charset_collation = $wpdb->get_charset_collate();
	$table_name = $wpdb->prefix.'tm_mbr_department';
	$sql = "DROP TABLE IF EXISTS $table_name;";
    $wpdb->query($sql);

    $table_name = $wpdb->prefix.'tm_mbr_team_members';
    $sql = "DROP TABLE IF EXISTS $table_name;";
    $wpdb->query($sql);

    $home_page = get_option('tm_mbr_home');
    $reg_page = get_option('tm_mbr_reg');
    wp_delete_post($home_page, true);
    wp_delete_post($reg_page, true);
    delete_option('tm_mbr_home');
    delete_option('tm_mbr_reg');
}
register_uninstall_hook(__FILE__, 'tm_mbr_delete_table');


if( !class_exists('tm_mbr_menus') ){
	require_once( plugin_dir_path( __FILE__ ).'/inc/admin/classes/class-tm_mbr_menu.php' );
}
if( !class_exists('tm_mbr_db') ){
	require_once( plugin_dir_path( __FILE__ ).'/inc/admin/classes/class-db.php' );
}
if( !class_exists('PageTemplater') ){
	require_once( plugin_dir_path( __FILE__ ).'/inc/admin/classes/class-page-templater.php' );
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
	(new tm_mbr_menus())->tm_mbr_page_main_content('MEMBERS', 'tm_mbr_create_employee');
}
add_action( 'admin_menu', 'tm_mbr_menu_page_create' );

//Create Submenu Page for Add Department
$department = new tm_mbr_menus('tm_mbr_team_members', 'DEPARTMENT', 'tm_mbr_create_department', 'tm_mbr_create_dept');

// Create Page for Add New Member
//$head = new tm_mbr_menus('', 'EMPLOYEE', 'tm_mbr_create_employee', '');

// Create Page for Add New Department
//$dept = new tm_mbr_menus('', 'DEPARTMENTS', 'tm_mbr_create_dept', '');

//Enqueue Scripts and Styles

function tm_mbr_add_jquery(){
	wp_enqueue_style('team-member-style', plugin_dir_url( __FILE__ ).'css/team-member-style.css', array(), 1.0 );

	wp_enqueue_script( 'jquery' );

	if(is_admin()){
		wp_enqueue_script( 'new-department', plugin_dir_url( __FILE__ ).'js/new-dept.js', array(), 1.0, true );
		wp_localize_script( 'new-department', 'new_dept_ajax', array(
			'ajaxurl'=>admin_url( 'admin-ajax.php' ),
		));
	}else{
		wp_enqueue_script( 'team-member-front', plugin_dir_url( __FILE__ ).'js/team-member-front.js', array(), 1.0, true );
		wp_localize_script( 'team-member-front', 'front_end_ajax', array(
			'ajaxurl'=>admin_url( 'admin-ajax.php' ),
		));
	}
}
add_action('wp_enqueue_scripts', 'tm_mbr_add_jquery');
add_action('admin_enqueue_scripts', 'tm_mbr_add_jquery');


//Create Essential Pages
function tm_mbr_create_page(){
	if( !class_exists('tm_mbr_new_page') ){
		require_once( plugin_dir_path( __FILE__ ).'/inc/admin/classes/class-add-page.php' );
	}

	$page_exist = get_page_by_title('MEMBER HOME', 'ARRAY_A', 'page');
	if( empty($page_exist) ){
		$home_page = new tm_mbr_new_page( 'page', 'MEMBER HOME', '[tm_mbr_home]', 'publish'  );
		$create_home_page = $home_page->create_page();
		if($create_home_page){
			add_option('tm_mbr_home',$create_home_page);
			update_post_meta( $create_home_page, '_wp_page_template', 'tm_mbr_member-page-template.php' );
		}
	}

	$page_exist = get_page_by_title('MEMBER REGISTRATION', 'ARRAY_A', 'page');
	if( empty($page_exist) ){
		$reg_page = new tm_mbr_new_page( 'page', 'MEMBER REGISTRATION', '[tm_mbr_registration]', 'publish'  );
		$create_reg_page = $reg_page->create_page();
		if($create_reg_page){
			add_option('tm_mbr_reg',$create_reg_page);
		}
	}
}
add_action( 'init', 'tm_mbr_create_page' );



//Create Add Page Template Option
add_action( 'plugins_loaded', array( 'PageTemplater', 'get_instance' ) );



//Ajax Function for Create New Department 
function tm_mbr_new_dept_process_request(){
	$dept_name = $_POST['dept'];

	global $wpdb;

	$dept_table = $wpdb->prefix.'tm_mbr_department';
	$dept_in_db = $wpdb->get_var( "SELECT NAME FROM $dept_table WHERE NAME = '$dept_name' " );

	if( !class_exists('tm_mbr_db') ){
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

	if( !class_exists('tm_mbr_db') ){
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
	if( !class_exists('tm_mbr_db') ){
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



//Ajax Function for Update Department 
function tm_mbr_update_dept(){
	global $wpdb;

	$dept_table = $wpdb->prefix.$_POST['table'];
	$user_table = $wpdb->prefix.'tm_mbr_team_members';
	$dept_name = $_POST['dept'];
	$head_name = $_POST['head'];
	$id = $_POST['id'];

	$dept_in_db = $wpdb->get_var( "SELECT NAME FROM $dept_table WHERE NAME = '$dept_name' " );

	if( !class_exists('tm_mbr_db') ){
		require_once( plugin_dir_path( __FILE__ ).'/inc/admin/classes/class-db.php' );
	}
	if( strtolower( $dept_in_db ) == strtolower($dept_name) ){
		$select_name = $wpdb->get_results("SELECT NAME FROM $dept_table WHERE ID NOT IN ($id) AND NAME LIKE '".$dept_name."%' ORDER BY ID DESC" , ARRAY_A);
		$count = count($select_name);
		$select_name2 = $wpdb->get_results("SELECT NAME FROM $dept_table WHERE ID NOT IN ($id) AND NAME LIKE '".$dept_name."-$count%' ORDER BY ID DESC" , ARRAY_A);
		
		if( !empty($select_name) || !empty($select_name2) ){
			if(count($select_name2) > 0){
				$count++;
				$dept_name .= '-'.$count;
			}else{
				$dept_name .= '-'.$count;
			}
		}
		goto a;
	}else{
		a:{
			if( !empty($dept_name) ){
				$update_dept = new tm_mbr_db( 
										$dept_table,
										array(
											'NAME' 	=> $dept_name,
										),
										array(
											'ID' => $id
										),
										array(
											'%s'
										)
									);
				if( $update_dept->update_row( array('%d' ) ) || false == $update_dept->update_row( array('%d') ) ){
					$ok = 1;
				}else{
					echo 'Sorry Not Updated';
				}
			}

			if( !empty($head_name) ){
				$select_dept_id = $wpdb->get_var( "SELECT ID FROM ".$wpdb->prefix."tm_mbr_department WHERE NAME = '".$dept_name."' " );
				$head = $wpdb->get_results("SELECT * FROM $user_table WHERE EMAIL='".$head_name."' AND ROLE='head' AND DEPARTMENT NOT LIKE'".$select_dept_id."'" , ARRAY_A);

				$user = $wpdb->get_results("SELECT * FROM $user_table WHERE EMAIL='".$head_name."' AND ROLE='employee' " , ARRAY_A);

				$all_user = $wpdb->get_results("SELECT * FROM $user_table WHERE EMAIL='".$head_name."' " , ARRAY_A);

				if(!empty($user)){
					foreach( $user as $mail_key => $mail_val ){ $user_id = $mail_val; };
				}else{
					$user = $wpdb->get_results("SELECT * FROM $user_table WHERE EMAIL='".$head_name."' AND ROLE='head' " , ARRAY_A);
					foreach( $user as $mail_key => $mail_val ){ $user_id = $mail_val; };
				}

				if( count($head) > 0 ){
					echo "This user has already been assigned for another Department.";
				}elseif( count($all_user) > 0 ){

					$prev_head = $wpdb->get_results("SELECT * FROM $user_table WHERE DEPARTMENT='".$select_dept_id."' AND ROLE = 'head' " , ARRAY_A);

					if( count($prev_head) >0 ){
						foreach( $prev_head as $prev_head_key => $prev_head_val ){
						
							$remove_prev_head = new tm_mbr_db( 
														$user_table,
														array(
															'ROLE'			=> 'employee',
															'DEPARTMENT' 	=> $select_dept_id,
														),
														array(
															'ID' => $prev_head_val['ID']
														),
														array(
															'%s',
															'%d'
														)
													);
							$remove_prev_head->update_row( array('%d') );
						}
					}

					$update_dept = new tm_mbr_db( 
										$user_table,
										array(
											'ROLE'			=> 'head',
											'DEPARTMENT' 	=> $select_dept_id,
										),
										array(
											'ID' => $user_id['ID']
										),
										array(
											'%s'
										)
									);
					$new_head = $update_dept->update_row( array('%d') );
					if( $new_head ){ $ok=1; }
				}elseif( count($all_user) < 1 ){
					echo "Sorry! No User Exist With This Email";
				}
			}else{
				$head = $wpdb->get_results("SELECT * FROM $user_table WHERE DEPARTMENT='".$id."' AND ROLE = 'head' " , ARRAY_A);
				if( !empty($head) ){
					foreach( $head as $key=>$val ){
						$remove_head = new tm_mbr_db( 
													$user_table,
													array(
														'ROLE'			=> 'employee',
														'DEPARTMENT' 	=> $id,
													),
													array(
														'ID' => $val['ID']
													),
													array(
														'%s',
														'%d'
													)
												);
						$remove_head->update_row( array('%d') );
					}
				}
			}

			if( $ok == 1){ echo '1'; }

		}
	}
	wp_die();
}
add_action( 'wp_ajax_nopriv_update_dept', 'tm_mbr_update_dept' );
add_action('wp_ajax_update_dept', 'tm_mbr_update_dept');


//Ajax Function for Update User 
function tm_mbr_ajax_update_admin_user(){
	$table = $_POST['table'];
	$user_name = $_POST['u_name'];
	$user_mail = $_POST['mail'];
	$role = $_POST['role'];
	$dept = $_POST['dept'];
	$pass = $_POST['pass'];
	$id = $_POST['id'];
	global $wpdb;
	$user_table = $wpdb->prefix.$table;

	$user_mail_in_db = $wpdb->get_results( "SELECT EMAIL FROM $user_table WHERE EMAIL = '$user_mail' AND ID NOT LIKE '".$id."' ", ARRAY_A );
	$pre_head = $wpdb->get_results( "SELECT * FROM $user_table WHERE ROLE = 'head' AND DEPARTMENT='".$dept."' ", ARRAY_A );

	if( count($user_mail_in_db) > 0){
		echo "A User with this Email has already been exist. Please Try with a different Email.";
	}else{
		if( strtolower($role) == strtolower('head') ){
			if( !empty($pre_head) ){
				for( $i=0; $i<count($pre_head); $i++){
					$pre_head_id = $pre_head[$i]['ID'];
					$remove_head = new tm_mbr_db( 
												$user_table,
												array(
													'ROLE'			=> 'employee',
													'DEPARTMENT' 	=> $dept,
												),
												array(
													'ID' => $pre_head_id
												),
												array(
													'%s',
													'%d'
												)
											);
					$remove_head->update_row( array('%d') );
				}
				goto update;
			}else{
				goto update;
			}
		}else{
			update:{
				$update_user = $wpdb->query("UPDATE $user_table SET NAME='".$user_name."', EMAIL='".$user_mail."', PASSWORD='".$pass."', ROLE='".$role."', DEPARTMENT='".$dept."'  WHERE ID='".$id."'");
				if($update_user){
					echo "<span class='result_flag' style='display: none;'>1</span>";
				}elseif( 0===$update_user ){
					echo "<span class='result_flag' style='display: none;'>1</span>";
				}else{
					echo "Sorry! User Can't Update";
				}
			}
		}
	}
	//$pre_dept = $wpdb->get_var( "SELECT ROLE FROM $user_table WHERE ROLE = 'head' " );

	wp_die();
}
add_action( 'wp_ajax_nopriv_ajax_update_admin_user', 'tm_mbr_ajax_update_user' );
add_action('wp_ajax_update_admin_user', 'tm_mbr_ajax_update_admin_user');




//Ajax Function For Add New User
function tm_mbr_ajax_new_user(){
	$user_data = $_POST['user_info'];
	$user_mail = $user_data['mail'];
	global $wpdb;
	$user_table = $wpdb->prefix.'tm_mbr_team_members';
	if( !class_exists('tm_mbr_db') ){
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
add_action( 'wp_ajax_nopriv_new_user', 'tm_mbr_ajax_new_user' );
add_action('wp_ajax_new_user', 'tm_mbr_ajax_new_user');


//Ajax Function For Suggest User
function tm_mbr_ajax_suggest(){
	global $wpdb;
	$user_mail = $_POST['name'];
	$table = $wpdb->prefix.$_POST['table'];

	if( !class_exists('tm_mbr_db') ){
		require_once( plugin_dir_path( __FILE__ ).'/inc/admin/classes/class-db.php' );
	}

	$all_mail = $wpdb->get_results("SELECT * FROM $table WHERE EMAIL LIKE '".$user_mail."%' ORDER BY ID DESC" , ARRAY_A);
	foreach($all_mail as $key=>$val){
		echo "<a href=$val[ID]>".$val['EMAIL']."</a>";
	}
	wp_die();
}
add_action( 'wp_ajax_nopriv_name_suggest', 'tm_mbr_ajax_suggest' );
add_action('wp_ajax_name_suggest', 'tm_mbr_ajax_suggest');


//Ajax Function For Match UserName And Password
function tm_mbr_log_in(){

	global $wpdb;
	$mail = $_POST['mail'];
	$pwd = $_POST['pwd'];
	$user_table = $wpdb->prefix.'tm_mbr_team_members';

	$select_users = $wpdb->get_results("SELECT * FROM $user_table WHERE EMAIL='".$mail."' AND PASSWORD='".$pwd."'", ARRAY_A);

	if($select_users){
		$_SESSION['ID'] = $select_users[0]['ID'];
		echo "<span class='success_flag' style='display: none'>1</span><span class='home_url' style='display: none'>".get_permalink(get_page_by_path('member-home')->ID)."</span><span class='user_id' style='display: none'>".$select_users[0]['ID']."</span>";
	}else{
		echo "Sorry! User Name And Password Not Match.";
	}

	wp_die();
}
add_action( 'wp_ajax_nopriv_log_in', 'tm_mbr_log_in' );
add_action('wp_ajax_log_in', 'tm_mbr_log_in');


//Ajax Function For Update Result
function tm_mbr_update_user(){

	global $wpdb;
	$name = $_POST['name'];
	$mail = $_POST['mail'];
	$pwd = $_POST['pwd'];
	$user_table = $wpdb->prefix.'tm_mbr_team_members';

	$check_mail = $wpdb->get_results("SELECT * FROM $user_table WHERE EMAIL='".$mail."' AND ID NOT LIKE '".$_SESSION['ID']."')",ARRAY_A);
	if( count($check_mail) > 0 ){
		echo "This Email has already been used by another user. Try With A Different Email.";
	}else{
		$update = $wpdb->query("UPDATE $user_table SET NAME='".$name."', EMAIL='".$mail."', PASSWORD='".$pwd."' WHERE ID='".$_SESSION['ID']."'");
		if( $update ){
			echo "<span class='result_flag' style='display: none'>1</span>";
		}elseif(0===$update){
			echo "<span class='result_flag' style='display: none'>1</span>";
		}else{
			echo "Sorry! Not Updated";
		}
	}

	wp_die();
}
add_action( 'wp_ajax_nopriv_update_user', 'tm_mbr_update_user' );
add_action('wp_ajax_update_user', 'tm_mbr_update_user');


//Ajax Function For Upload Image
function tm_mbr_image_upload(){
	//print_r($_FILES);
	global $wpdb;
	$upload = wp_upload_dir();
	$upload_dir = $upload['basedir'];
	$upload_dir = $upload_dir . '/team-member-images/';
	$target_file = $upload_dir . basename($_FILES["fileToUpload"]["name"]);

	
	$uploadOk = 1;
	$imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);
	$user_table = $wpdb->prefix.'tm_mbr_team_members';

	$check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
	if($check !== false) {
	    $uploadOk = 1;
	} else {
	    echo "<p class='image_upload_result' style='display: none'>Please Upload an image File.</p>";
	    $uploadOk = 0;
	}

	if (file_exists($target_file)) {
		$count = 0;
	    while( file_exists($target_file) ){
	    	$count++;
	    	$target_file = basename($_FILES['fileToUpload']['name'], ".$imageFileType");
			$target_file = $target_file."-$count.$imageFileType";
	    	$uploadOk = 1;
	    }
	}


	if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif" ){
	    echo "<p class='image_upload_result' style='display: none'>Sorry, only JPG, JPEG, PNG & GIF files are allowed.</p>";
	    $uploadOk = 0;
	}

	if ($_FILES["fileToUpload"]["size"] > 5000000000) {
	    echo "<p class='image_upload_result' style='display: none'>Sorry, your file is too large.</p>";
	    $uploadOk = 0;
	}
	if ($uploadOk == 0) {
	    echo "<p class='image_upload_result' style='display: none'>Sorry, your file was not uploaded.</p>";
	} else {
	    if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
	        $target_file = basename($target_file);
	        $image_insert = $wpdb->query("UPDATE $user_table SET PROFILE_PICTURE='".$target_file."' WHERE ID='".$_SESSION['ID']."' ");
	        if($image_insert){
	        	echo "<span class='result_flag'>1</span><p class='image_upload_result' style='display: none'>Your Profile Picture has been uploaded.</p>";
	        }else{
	        	echo $wpdb->show_errors( true );
	        }
	    } else {
	        echo "<p class='image_upload_result' style='display: none'>Sorry, there was an error uploading your file.</p>";
	    }
	}
	wp_die();
}
add_action( 'wp_ajax_nopriv_image_upload', 'tm_mbr_image_upload' );
add_action('wp_ajax_image_upload', 'tm_mbr_image_upload');

function tm_mbr_search_user_info(){
	$email = $_POST['mail'];
	$u_id = $_POST['id'];
	global $wpdb;
	$user_table = $wpdb->prefix.'tm_mbr_team_members';
	$dept_table = $wpdb->prefix.'tm_mbr_department';
	$select_users = $wpdb->get_results("SELECT *, $user_table.NAME AS u_name FROM $user_table RIGHT JOIN $dept_table ON $dept_table.ID=$user_table.DEPARTMENT WHERE $user_table.EMAIL='".$email."' AND $user_table.ID='".$u_id."'", ARRAY_A);

$select_users_name = $wpdb->get_results("SELECT * FROM $user_table WHERE EMAIL='".$email."' ", ARRAY_A);


	foreach ($select_users as $key => $value) {

		echo "<tr data-id=$value[ID]>
				<td>
					<a class='user_name' href='#'>$value[u_name]</a>
						<div class='inline_options'>
							<a class='edit' href=$value[ID]>Edit</a>
							<a class='delete' href=$value[ID]>Delete</a>
						</div>
				</td>
				<td class='user_email' data-user_email=$value[EMAIL]>$value[EMAIL]</td>
				<td class='user_role' data-user_role=$value[ROLE]>$value[ROLE]</td>
				<td class='user_dept' data-user_dept=$value[DEPARTMENT]> $value[NAME] </td>
				<td class='user_pwd' data-user_pwd=$value[PASSWORD] style='display: none; '> user1 </td>
			  </tr>";
	}

	wp_die();
}
add_action( 'wp_ajax_nopriv_search_user_info', 'tm_mbr_search_user_info' );
add_action('wp_ajax_search_user_info', 'tm_mbr_search_user_info');


//Ajax Function For Suggest Department
function tm_mbr_suggest_dept(){

	global $wpdb;
	$name = $_POST['dept_name'];
	$dept_table = $wpdb->prefix.'tm_mbr_department';

	$select_dept = $wpdb->get_results("SELECT * FROM $dept_table WHERE NAME LIKE '".$name."%'", ARRAY_A);
	foreach($select_dept as $term){
		echo "<a href=$term[ID]>".$term['NAME']."</a>";
	}

	wp_die();
}
add_action( 'wp_ajax_nopriv_suggest_dept', 'tm_mbr_suggest_dept' );
add_action('wp_ajax_suggest_dept', 'tm_mbr_suggest_dept');

function replace_with_search_dept(){
	global $wpdb;
	$dept_id = $_POST['ID'];
	$dept_name = $_POST['Name'];
	$user_table = $wpdb->prefix."tm_mbr_team_members";

	$select_users = $wpdb->get_results("SELECT * FROM $user_table WHERE DEPARTMENT= '".$dept_id."' AND ROLE='HEAD' ", ARRAY_A);
	if(!empty($select_users)){
		foreach ($select_users as $key => $value) {
			echo "<tr data-id=$dept_id>
					<td>
					<a class='dept_name' href='#'>$dept_name</a>
					<div class='inline_options'>
					<a class='edit' href=$dept_id>Edit</a>
					<a class='delete' href=$dept_id>Delete</a>
					</div>
					</td>
					<td class='dept_head_name' data-user_mail=$value[EMAIL] data-user_id=$value[ID]>$value[NAME]</td>
				</tr>";
		}
	}else{
		echo "<tr data-id=$dept_id>
				<td>
				<a class='dept_name' href='#'>$dept_name</a>
				<div class='inline_options'>
				<a class='edit' href=$dept_id>Edit</a>
				<a class='delete' href=$dept_id>Delete</a>
				</div>
				</td>
				<td class='dept_head_name' data-user_mail='' data-user_id=''>$value[NAME]</td>
			</tr>";
	}
	wp_die();
}
add_action( 'wp_ajax_nopriv_replace_with_search_dept', 'replace_with_search_dept' );
add_action('wp_ajax_replace_with_search_dept', 'replace_with_search_dept');

//Ajax Function For Delete Session
function tm_mbr_delete_session(){

	session_unset(); 
	session_destroy(); 

	wp_die();
}
add_action( 'wp_ajax_nopriv_delete_session', 'tm_mbr_delete_session' );
add_action('wp_ajax_delete_session', 'tm_mbr_delete_session');

//Include Shortcodes
require_once( plugin_dir_path( __FILE__ ).'/inc/admin/shortcodes.php' );

?>
