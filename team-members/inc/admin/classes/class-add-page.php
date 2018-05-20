<?php
//Class For Create New Page
Class tm_mbr_new_page{
	private $type, $title, $content, $status; 

	function __construct($post_type='page',$page_title='', $page_content='', $page_status='' ){
		$this->type = $post_type;
		$this->title = $page_title;
		$this->content = $page_content;
		$this->status = $page_status;
	}

	function create_page(){
		$new_page = array(
						'post_type'		=> $this->type,
					    'post_title'    => $this->title,
					    'post_content'  	=> $this->content,
					    'post_status'		=> $this->status
					);
		return wp_insert_post( $new_page, true );
	}
}