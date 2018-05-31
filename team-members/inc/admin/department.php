<?php
if( !class_exists('tm_mbr_db') ){
	require_once( plugin_dir_path( __FILE__ ).'classes/class-db.php');
}
global $wpdb;
$dept_tbl = $wpdb->prefix.'tm_mbr_department';

if( !get_option('tm_mbr_dept_page_item') ){
	$tm_mbr_page_item = 10;
	add_option('tm_mbr_dept_page_item', $tm_mbr_page_item );
}else{
	$tm_mbr_page_item = get_option('tm_mbr_dept_page_item');
}

if( isset($_POST['total_page_item_submit']) && $_POST['tm_mbr_page_item']>0 ){
	update_option('tm_mbr_dept_page_item', $_POST['tm_mbr_page_item']);
}

$tm_mbr_page_item = get_option('tm_mbr_dept_page_item') ? get_option('tm_mbr_dept_page_item') : 10;

$page_num = isset($_GET['page_num']) && !empty($_GET['page_num']) ? $_GET['page_num'] : 1;
$limit = $tm_mbr_page_item;
$offset = ($page_num-1)*$limit;

$dept_all = $wpdb->get_results("SELECT COUNT(ID) AS total FROM $dept_tbl ORDER BY ID DESC" , ARRAY_A);
?>
<div class="tm_mbr_main" data-page='tm_mbr_create_department'>
	<div class="tm_mbr_left">
		<p class="err_msg"></p>
		<h2>CREATE NEW DEPARTMENT</h2>
		<form>
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
		</form>
	</div>


	<div id='department_table' class="tm_mbr_right">
		<p class="err_msg"></p>
		<p class="dept-search-box">
			<input type="text" placeholder="Type Department Name To Search">
			<span class="dept_ajax_mail"></span>
		</p>
		<form id='set_page_item_form' method="post" action="">
			<p class="search-box">
					<label for="total_page_item">Number Of Members For Per Page:</label>
					<input type="text" id='total_page_item' value="<?php echo $tm_mbr_page_item; ?>" name='tm_mbr_page_item'>
					<input type="submit" class="button action" value="Submit" name="total_page_item_submit">
			</p>
		</form>	
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
				
				$dept_info = $wpdb->get_results("SELECT * FROM $dept_tbl ORDER BY ID DESC LIMIT $offset,$limit" , ARRAY_A);


				if( !empty($dept_info) ){
					foreach( $dept_info as $dept ){
			?>
					<tr data-id='<?php echo $dept['ID']; ?>'>
						<td>
							<a href='#' class="dept_name" ><?php echo $dept['NAME']; ?></a>
							<div class="inline_options">
								<a href="<?php echo $dept['ID']; ?>" class="edit">Edit</a>
								<a href="<?php echo $dept['ID']; ?>" class="delete">Delete</a>
							</div>
						</td>
						<?php
							$user_table = $wpdb->prefix.'tm_mbr_team_members';
							$dept_head = $wpdb->get_var( "SELECT NAME FROM $user_table WHERE DEPARTMENT = '".$dept['ID']."' AND ROLE='head' " );
							$user_id = $wpdb->get_var( "SELECT ID FROM $user_table WHERE DEPARTMENT = '".$dept['ID']."' AND ROLE='head' " );
							$user_mail = $wpdb->get_var( "SELECT EMAIL FROM $user_table WHERE DEPARTMENT = '".$dept['ID']."' AND ROLE='head' " );
						?>
						<td class="dept_head_name" data-user_mail='<?php echo $user_mail; ?>' data-user_id = '<?php echo $user_id; ?>' ><?php echo $dept_head; ?></td>
					</tr>
			<?php } }else{ ?>
					<tr>
						<td class='no_dept'>No Departments Found</td>
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
					<a href="?page=tm_mbr_create_department&page_num=<?php echo $i; ?>"><?php echo $i; ?></a>
		<?php } } ?>	
		</div>
	</div>
</div>
