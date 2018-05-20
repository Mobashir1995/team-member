<?php
//class for create menu
Class tm_mbr_menus{

	private $parent_menu, $title, $slug, $refer_page;
	function __construct($parent_menu_title='', $sub_menu_title='', $sub_menu_slug='', $new_page=''){
		$this->parent_menu = $parent_menu_title;
		$this->title = $sub_menu_title;
		$this->slug = $sub_menu_slug;
		$this->refer_page = $new_page;
		add_action( 'admin_menu', array( $this, 'tm_mbr_sub_menu_page_create') );
	}

	//Create Sub Menu Page 
	function tm_mbr_sub_menu_page_create(){

		add_submenu_page(
			$this->parent_menu,
			$this->title,
			$this->title,
			'manage_options',
			$this->slug,
			array( $this, 'tm_mbr_page_main_content')
		);

	}

	//Callback Function for Menu Page
	function tm_mbr_page_main_content($main_title, $new_page=''){
		$main_title = ($main_title ? $main_title : $this->title );
		$new_page = ($new_page ? $new_page : $this->refer_page);
?>
			<div class='wrap'>
				<h2><?php echo $main_title; ?></h2>
				<form action='' method="post">
			      <?php if( strtoupper( $main_title ) == strtoupper( 'DEPARTMENT' ) ){ ?>
			        <?php require_once( plugin_dir_path(__FILE__).'../department.php'); ?>	
			        
			      <?php }elseif( strtoupper( $main_title ) == strtoupper( 'MEMBERS' ) ){ ?>
			        	<?php require_once( plugin_dir_path(__FILE__).'../members.php'); } ?>
		        </form>
			</div>
<?php
	}

}