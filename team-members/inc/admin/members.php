<?php
if( !class_exists(tm_mbr_db) ){
	require_once( plugin_dir_path( __FILE__ ).'classes/class-db.php');
}
global $wpdb;
?>
<div class="tm_mbr_left">
	<p class="err_msg"></p>
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
	<p class="err_msg"></p>
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
			$dept_info = new tm_mbr_db(
								$dept_tbl
							);


			if( !empty($dept_info->get_all_rows()) ){
				foreach( $dept_info->get_all_rows() as $dept ){
		?>
				<tr>
					<td>
						<a href='#'><?php echo $dept['NAME']; ?></a>
						<div class="inline_options">
							<a href="#" class="edit">Edit</a>
							<a href="<?php echo $dept['ID']; ?>" class="delete">Delete</a>
						</div>
					</td>
					<td><?php echo $dept['EMAIL']; ?></td>
					<td><?php echo $dept['ROLE']; ?></td>
					<td>
						<?php
							$select_dept_name = $wpdb->get_var( "SELECT NAME FROM ".$wpdb->prefix."tm_mbr_department WHERE ID = '".$dept['DEPARTMENT']."' " );
							echo $select_dept_name;
						?>	
					</td>
				</tr>
		<?php } }else{ ?>
				<tr>
					<td class='no_dept'>No Members Found</td>
				</tr>
		<?php } ?>
		</tbody>
	</table>
</div>
<div class="clear">