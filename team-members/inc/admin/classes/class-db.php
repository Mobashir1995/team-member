<?php
/*class for Database
*
*Acceptable Arguments are Table-Name='String', Query_data='Indexed_Array() For Select Values/Associative_Arrar For Update Values', Data_array='Multidimensional_Associative_array() For Where Clause OR Insert', Data-Format=Indexed_array();
*
*/
class tm_mbr_db{
	public $table_name, $query_data, $data_array, $data_format;
	function __construct( $table='', $data_query=array(), $array_data= array(), $format_data= array() ){
		global $wpdb;
		$this->table_name = $table;
		$this->query_data = $data_query;
		$this->data_array = $array_data;
		$this->data_format = $format_data;
	}

	//Insert Single Row
	function insert_row(){
		global $wpdb;
		$wpdb->show_errors( true );
		$insert_info = $wpdb->insert( $this->table_name, $this->data_array, $this->data_format);
		return $insert_info;
	}

	//delete Row
	function delete_rows(){
		global $wpdb;
		$wpdb->show_errors( true );

		$delete_row = $wpdb->delete( $this->table_name, $this->data_array, $this->data_format);
		return $delete_row;
	}
	
	//get all Rows
	function get_all_rows(){
		global $wpdb;
		$wpdb->show_errors( true );

		if( !empty($this->query_data) ){
			foreach( $this->query_data as $query_key => $query_val){
				$select_data .=  $query_val;
			}
		}else{
			$select_data = '*';
		}

		if( !empty($this->data_array) ){
			foreach( $this->data_array as $key => $val){
				if( strtolower($key) == strtolower('relation') ){
					$where_data .= $val." ";
					continue;
				}
				foreach($val as $arg => $data){
					if( $data == end($val) ){
						$where_data .= "$arg = "."'".$data."' ";
					}else{
						$where_data .= "$arg = "."'".$data."' AND ";
					}
				}

			}
			
			$get_all_info = $wpdb->get_results("SELECT $select_data FROM $this->table_name WHERE $where_data ORDER BY ID DESC" , ARRAY_A);
		}else{
			$get_all_info = $wpdb->get_results("SELECT $select_data FROM $this->table_name ORDER BY ID DESC" , ARRAY_A);
		}
		return $get_all_info;
	}

	//update Row----Expect One Parameter For Where Format
	function update_row($where_format){
		global $wpdb;
		$wpdb->show_errors( true );

		$update = $wpdb->update($this->table_name, $this->query_data, $this->data_array, $this->data_format, $where_format);
		return $update;
	}

}