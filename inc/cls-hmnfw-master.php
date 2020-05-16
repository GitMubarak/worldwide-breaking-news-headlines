<?php
/**
 * HM Newsfeed Widget: main plugin class
*/
class HMNFW_Master {

	protected $hmnfw_loader;
	protected $hmnfw_version;
	
	public function __construct() {
		$this->hmnfw_version = HMNFW_VERSION;
		$this->hmnfw_load_dependencies();
		$this->hmnfw_trigger_widget_hooks();
	}
	
	function hmnfw_load_dependencies() {

		require_once HMNFW_PATH . 'widget/' . HMNFW_CLASSPREFIX . 'widget.php';
	}
	
	private function hmnfw_trigger_widget_hooks() {

		$hmnfw_widget = new HMNFW_Widget();
		add_action( 'widgets_init', function() { 
			register_widget( 'HMNFW_Widget' ); 
		});
	}
	
	function hmnfw_version() {
		return $this->hmnfw_version;
	}
}
