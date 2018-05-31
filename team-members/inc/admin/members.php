<?php
if( !class_exists('tm_mbr_db') ){
	require_once( plugin_dir_path( __FILE__ ).'classes/class-db.php');
}
global $wpdb;

if( !get_option('tm_mbr_page_item') ){
	$tm_mbr_page_item = 10;
	add_option('tm_mbr_page_item', $tm_mbr_page_item );
}else{
	$tm_mbr_page_item = get_option('tm_mbr_page_item');
}

if( isset($_POST['total_page_item_submit']) && $_POST['tm_mbr_page_item']>0 ){
	update_option('tm_mbr_page_item', $_POST['tm_mbr_page_item']);
}

$tm_mbr_page_item = get_option('tm_mbr_page_item') ? get_option('tm_mbr_page_item') : 10;

$u_type = isset($_GET['user_type']) && !empty( $_GET['user_type'] ) ? $_GET['user_type'] : 'all';
$page_num = isset($_GET['page_num']) && !empty($_GET['page_num']) ? $_GET['page_num'] : 1;
$limit = $tm_mbr_page_item;
$offset = ($page_num-1)*$limit;

?>
<div class="tm_mbr_main" data-page='tm_mbr_team_members'>

<div class="tm_mbr_left">
	<p class="err_msg"></p>
	<h2>CREATE NEW MEMBERS</h2>
	<form action="" id='admin_member_add'>
		<table>
			<tr>
				<td><label for='user_name'>MEMBER NAME:</label></td>
				<td >
					<input id='user_name' type="text" class="tm_mbr_form_field" name="user_name" />
					<p class="err_msg"></p>
				</td>
			</tr>
			<tr>
				<td><label for='pwd'>PASSWORD:</label></td>
				<td >
					<input id='pwd' type="password" class="tm_mbr_form_field" name="pwd" />
					<p class="err_msg"></p>
				</td>
			</tr>
			<tr>
				<td><label for='user_email'>Email:</label></td>
				<td >
					<input id='user_email' type="text" class="tm_mbr_form_field" name="email" />
					<p class="err_msg"></p>
				</td>
			</tr>
			<tr>
				<td><label for='user_role'>Role:</label></td>
				<td >
					<select id='user_role' class="tm_mbr_form_field" name="role" />
						<option value="">Select Role</option>
						<option value="head">Department Head</option>
						<option value="employee">Employee</option>
					</select>
					<p class="err_msg"></p>
				</td>
			</tr>
			<tr>
				<td><label for='user_dept'>Department:</label></td>
				<td >
					<select id='user_dept' class="tm_mbr_form_field" name="department" />
				<?php
					$dept_tbl = $wpdb->prefix.'tm_mbr_department';
					$departments = new tm_mbr_db(
										$dept_tbl
									);
					$department_list = $departments->get_all_rows();
					if( !empty($department_list)){
				?>
						<option value="">Select Department</option>
				<?php
					foreach( $department_list as $dept_name => $dept_val){
				?>
						<option value="<?php echo  $dept_val['ID']; ?>"><?php echo $dept_val['NAME']; ?></option>
				<?php  } }else{ ?>
						<option value="">No Departments Found</option>
				<?php } ?>
					</select>
					<p class="err_msg"></p>
				</td>
			</tr>
			<tr>
				<td></td>
				<td><input type="submit" class="button button-primary button-large" id='save_member' name='submit_dept' value="Add User" /></td>
			</tr>
		</table>
	</form>
</div>

<div id='member_table' class="tm_mbr_right">
	<p class="main_err err_msg"></p>
	<p class="user-search-box">
		<input type="text" placeholder="Type User Email To Search">
		<span class="ajax_mail"></span>
	</p>
	<ul class="subsubsub">
		<li class="all">
			<a <?php if($u_type == 'all'){ ?> class='current'<?php } ?> href="?page=tm_mbr_team_members&user_type=all">All</a>
		</li>
		<li class="head">
			<a <?php if($u_type == 'head'){ ?> class='current'<?php } ?> href="?page=tm_mbr_team_members&user_type=head">Heads</a>
		</li>
		<li class="employee">
			<a <?php if($u_type == 'employee'){ ?> class='current'<?php } ?> href="?page=tm_mbr_team_members&user_type=employee">Employees</a>
		</li>
	</ul>
	<form id='set_page_item_form' method="post" action="">
		<p class="search-box">
				<label for="total_page_item">Number Of Members For Per Page:</label>
				<input type="text" id='total_page_item' value="<?php echo $tm_mbr_page_item; ?>" name='tm_mbr_page_item'>
				<input type="submit" class="button action" value="Submit" name="total_page_item_submit">
		</p>
	</form>

	<table id='all_department' data-table="tm_mbr_team_members">
		<thead>
			<tr>
				<th>Name</th>
				<th>Email</th>
				<th>Role</th>
				<th>Assigned Department</th>
			</tr>
		</thead>
		<tbody>
		<?php
			$dept_tbl = $wpdb->prefix.'tm_mbr_team_members';
			if( $u_type == 'all' ){
				// $dept_info = new tm_mbr_db(
				// 					$dept_tbl
				// 				);
				$dept_all = $wpdb->get_results("SELECT COUNT(ID) AS total FROM $dept_tbl ORDER BY ID DESC" , ARRAY_A);
				$dept_info = $wpdb->get_results("SELECT * FROM $dept_tbl ORDER BY ID DESC LIMIT $offset,$limit" , ARRAY_A);
			}else{
				// $dept_info = new tm_mbr_db(
				// 					$dept_tbl,
				// 					'',
				// 					array( array('ROLE' => $u_type." LIMIT 4 ") )
				// 				);
				$dept_all = $wpdb->get_results("SELECT COUNT(ID) AS total FROM $dept_tbl WHERE ROLE='$u_type' ORDER BY ID DESC" , ARRAY_A);
				$dept_info = $wpdb->get_results("SELECT * FROM $dept_tbl WHERE ROLE='$u_type' ORDER BY ID DESC LIMIT $offset,$limit" , ARRAY_A);
			}


			if( !empty($dept_info) ){
				foreach( $dept_info as $dept ){
		?>
				<tr data-id='<?php echo $dept['ID']; ?>' >
					<td >
						<a class="user_name" href='#'><?php echo $dept['NAME']; ?></a>
						<div class="inline_options">
							<a href="<?php echo $dept['ID']; ?>" class="edit">Edit</a>
							<a href="<?php echo $dept['ID']; ?>" class="delete">Delete</a>
						</div>
					</td>
					<td class="user_email" data-user_email="<?php echo $dept['EMAIL']; ?>"><?php echo $dept['EMAIL']; ?></td>
					<td class="user_role" data-user_role="<?php echo $dept['ROLE']; ?>"><?php echo $dept['ROLE']; ?></td>
					<?php $select_dept_name = $wpdb->get_var( "SELECT NAME FROM ".$wpdb->prefix."tm_mbr_department WHERE ID = '".$dept['DEPARTMENT']."' " ); ?>
					<td class="user_dept" data-user_dept="<?php echo $dept['DEPARTMENT']; ?>" >
						<?php echo $select_dept_name; ?>	
					</td>
					<td class="user_pwd" data-user_pwd="<?php echo $dept['PASSWORD']; ?>" style='display: none; '>
						<?php echo $dept['PASSWORD']; ?>	
					</td>
				</tr>
		<?php } }else{ ?>
				<tr>
					<td class='no_dept'>No Members Found</td>
				</tr>
		<?php } ?>
		</tbody>
	</table>
	<div class="paginate">
	<?php
		foreach($dept_all as $key=>$val){
			$total = $val['total'];
		}
		$total_page = ceil($total/$limit);
		for($i=1; $i<$total_page+1;$i++){
			if($i==$page_num){
	?>
				<span class="current"><?php echo $i; ?></span>
	<?php }else{ ?>
				<a href="?page=tm_mbr_team_members&user_type=<?php echo $u_type; ?>&page_num=<?php echo $i; ?>"><?php echo $i; ?></a>
	<?php } } ?>	
	</div>
</div>
<div class="clear">
</div>