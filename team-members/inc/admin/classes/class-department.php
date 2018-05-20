<?php
//class for department
class Department{
	//public $table_name;
	function __construct(){
		//$this->table_name = $wpdb->prefix.'tm_mbr_department';
	}

	function get_dept(){
		global $wpdb;
		//$table_name = $wpdb->prefix.'tm_mbr_department';
		$this->table_name = $wpdb->prefix.'tm_mbr_department';
		$wpdb->show_errors( true );
		$dept = $wpdb->get_results("SELECT * FROM $this->table_name ", ARRAY_A);
		return $dept;
	}
}