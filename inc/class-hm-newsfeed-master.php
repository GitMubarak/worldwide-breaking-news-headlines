<?php
/**
 * HM Newsfeed Widget: main plugin class
*/
class HMNFW_Master {

	protected $hmnfw_loader;
	protected $hmnfw_version;
	
	/**
	 * Class Constructor
	*/
	public function __construct() {
		$this->hmnfw_version = HMNFW_VERSION;
		$this->hmnfw_load_dependencies();
		$this->hmnfw_trigger_widget_hooks();
	}
	
	/**
	 * Loading Dependencies likes included, required fies 
	 * and their objects
	*/
	private function hmnfw_load_dependencies() {

		require_once HMNFW_PATH . 'widget/' . HMNFW_CLASSPREFIX . 'widget-activater.php';
	}
	
	/**
	 * Calling the widget section widget child class
	*/
	private function hmnfw_trigger_widget_hooks() {

		new HmNewsFeedWidgetActivater();
		add_action( 'widgets_init', function(){ register_widget( 'HmNewsFeedWidgetActivater' ); });
	}
	
	/**
	 * Controlling the version
	*/
	public function hmnfw_version() {
		return $this->hmnfw_version;
	}
}
