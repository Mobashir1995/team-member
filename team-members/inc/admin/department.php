<?php
if( !class_exists(tm_mbr_db) ){
	require_once( plugin_dir_path( __FILE__ ).'classes/class-db.php');
}
global $wpdb;
?>
<div class="tm_mbr_left">
	<p class="err_msg"></p>
	<table>
		<tr>
			<td><label for='dept_name'>DEPARTMENT NAME:</label></td>
			<td ><input type="text" id='dept_name' class="tm_mbr_form_field" name="dept_name" /></td>
		</tr>
		<tr>
			<td></td>
			<td><input type="submit" class="button button-primary button-large" id='save_dept' name='submit_dept' value="Save Department" /></td>
		</tr>
	</table>
</div>

<div class="tm_mbr_right">
	<p class="err_msg"></p>	
	<table id='all_department' data-table="tm_mbr_department">
		<thead>
			<tr>
				<th>Department Name</th>
				<th>Department Head</th>
			</tr>
		</thead>
		<tbody>
		<?php
			$dept_tbl = $wpdb->prefix.'tm_mbr_department';
			$dept_info = new tm_mbr_db(
								$dept_tbl
							);


			if( !empty($dept_info->get_all_rows()) ){
				foreach( $dept_info->get_all_rows() as $dept ){
		?>
				<tr>
					<td>
						<a href='#' class="dept_name"><?php echo $dept['NAME']; ?></a>
						<div class="inline_options">
							<a href="<?php echo $dept['ID']; ?>" class="edit">Edit</a>
							<a href="<?php echo $dept['ID']; ?>" class="delete">Delete</a>
						</div>
					</td>
					<td class="dept_head_name">
						<?php
							$user_table = $wpdb->prefix.'tm_mbr_team_members';
							$dept_head = $wpdb->get_var( "SELECT NAME FROM $user_table WHERE DEPARTMENT = '".$dept['ID']."' " );
							echo $dept_head;
						?>
					</td>
				</tr>
		<?php } }else{ ?>
				<tr>
					<td class='no_dept'>No Departments Found</td>
				</tr>
		<?php } ?>
		</tbody>
	</table>
</div>
