<?php 

class WMD_PrettyPluginsFEShowcase extends WMD_PrettyPlugins_Functions {
	var $plugin;

	function __construct() {
		global $wmd_prettyplugins;
		$this->plugin = $wmd_prettyplugins;

		add_shortcode('wmd-plugins-showcase', array($this,'display_plugins_showcase'));
	}

	function display_plugins_showcase($atts) {
		global $post;

		$atts = shortcode_atts(array( 'plugins' => false, 'hide_interface' => false, 'category' => ''), $atts, 'wmd-plugins-showcase');

		$this->plugin->init_vars();
		$this->plugin->set_custom_plugin_data();

		wp_enqueue_style('wmd-prettyplugins-fe-theme', $this->plugin->plugin_dir_url.'includes/frontend-showcase-files/style.css', array(), 6);
		wp_enqueue_script('wmd-prettyplugins-fe-theme', $this->plugin->plugin_dir_url.'includes/frontend-showcase-files/theme.js', array('jquery', 'backbone', 'wp-backbone'), 7);

		if($atts['plugins']) {
			$plugins = explode(',', str_replace(' ', '' , $atts['plugins']));
		}
		if(!isset($plugins) || !$plugins)
			$plugins = false;
		
		$this->plugin->enqueue_plugin_showcase_script_data('wmd-prettyplugins-fe-theme', parse_url( get_permalink($post->ID), PHP_URL_PATH ), true, $plugins, true);

		ob_start();
		include($this->plugin->plugin_dir.'includes/frontend-showcase-files/plugin_list.php');
		return ob_get_clean();
	}
}

global $wmd_prettyplugins_fe_showcase;
$wmd_prettyplugins_fe_showcase = new WMD_PrettyPluginsFEShowcase;