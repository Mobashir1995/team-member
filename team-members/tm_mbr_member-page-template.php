<?php
session_start();
echo "<div class='tm_mbr_front'>";
if( $_SESSION['ID'] ){
	global $wpdb;
	$table = $wpdb->prefix.'tm_mbr_team_members';
	$dept_table = $wpdb->prefix.'tm_mbr_department';
	$user_data = $wpdb->get_results("SELECT * FROM $table WHERE ID='".$_SESSION['ID']."' ", ARRAY_A);

	foreach ($user_data as $key => $value) {
		$user_info = $value;
	}
	$count=1;
	foreach ($user_info as $user_key => $user_val){
		if( $count == 1){
			$count++;
			continue;
		}
		$_SESSION[$user_key] = $user_val;
	}
	$dept = $wpdb->get_results( "SELECT * FROM $dept_table WHERE ID='".$_SESSION['DEPARTMENT']."'", ARRAY_A );

	$dept_head = $wpdb->get_results( "SELECT * FROM $table WHERE DEPARTMENT='".$_SESSION['DEPARTMENT']."' AND ROLE='head'", ARRAY_A );

	$upload = wp_upload_dir();
	$upload_dir = $upload['baseurl'];
	$upload_dir = $upload_dir . '/team-member-images/';

	?>
	
	<div class="user_info">
		<div class="profile_pic">
			<form id='form_img_upload' method="post" enctype="multipart/form-data">
				<img id='profile_img' src="<?php echo $upload_dir.$_SESSION['PROFILE_PICTURE']; ?>" width="" height="" alt="">
				<p class="image_upload_text"></p>
				<input type="file" name="fileToUpload" id="fileToUpload">
				<input type="submit" id='img_upload' value="Change Profile Picture" name="imgUpload">
			</form>
		</div>

<!-- 		<div class="multi_profile_pic">
			<form id='multi__img_upload' method="post" enctype="multipart/form-data">
				<img id='multi_img' src="<?php //echo $upload_dir.$_SESSION['PROFILE_PICTURE']; ?>" width="" height="" alt="">
				<p class="multi_image_upload_text"></p>
				<input type="file" name="multiFileToUpload[]" id="multiFileToUpload" multiple="multiple">
				<input type="submit" id='multi_img_upload' value="Upload Multiple Image" name="multiImgUpload">
			</form>
		</div> -->

	<h2>HELLO <?php echo $user_info['NAME']; ?></h2>
	<?php
		if( strtolower( $_SESSION['ROLE'] ) == strtolower('employee') ){
	?>
	<p style="display: block !important;">Your Department Head Name is: <?php if( !empty($dept_head) ){ echo $dept_head[0]['NAME']; }else{ echo "No Head Assigned for Your Department."; } ?></p>
	<?php } ?>
	<p class="name">NAME: <span><?php echo $_SESSION['NAME']; ?></span></p>
	<p class="mail">EMAIL: <span><?php echo $_SESSION['EMAIL']; ?></span></p>
	<p class="department">DEPARTMENT: <span><?php echo $dept[0]['NAME'] ?></span></p>
	<p class="role">ROLE: <span><?php echo $_SESSION['ROLE']; ?></span></p>
	<p class="pwd" style="display: none;">PASSWORD: <span><?php echo $_SESSION['PASSWORD']; ?></span></p>
	<a href="#" class="edit_info">Edit Info</a>
	<a href="#" class="cancel">Cancel Update</a>
	<?php
		$dept_user = $wpdb->get_results( "SELECT * FROM $table WHERE DEPARTMENT='".$_SESSION['DEPARTMENT']."' AND ROLE='employee'", ARRAY_A );

		if( strtolower( $_SESSION['ROLE'] ) == strtolower('head') ){
			if( empty($dept_user) ){
				echo "<p style='margin-top: 15px;'>No Users Found For Your Department. </p>";
			}else{
				echo "<p style='margin-top: 15px;'>Here Is Your Employee List: </p>";
				echo "<table>
						<tr>
						    <th>NAME</th>
						    <th>EMAIL</th>
						</tr>";

				for( $i=0; $i<count($dept_user); $i++){
					//$new_arr = $dept_user[$i];
					echo "<tr>
							<td>".$dept_user[$i][NAME]."</td>
							<td>".$dept_user[$i][EMAIL]."</td>
						  </tr>";
				}
				echo "</table>";
			}
		echo "<span class='head_log_out'>";
		}
	?>
	<a href="#" class="log_out">LOG OUT</a>
	<?php
	echo "</span>";
	echo "</div></div>";
}else{
	echo "<h2> Sorry! Your Session Has expired.</h2></div></div>";
}
?>