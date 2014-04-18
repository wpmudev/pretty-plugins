<?php
/*
Plugin Name: Pretty Plugins
Plugin URI: http://premium.wpmudev.org/project/pretty-plugins/
Description: Give your plugin page the look of an app store, with featured images, categories, and amazing search.
Version: 1.0.4
Network: true
Text Domain: wmd_prettyplugins
Author: WPMUDEV
Author URI: http://premium.wpmudev.org/
WDP ID: 852474
*/

/*
Copyright Incsub (http://incsub.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License (Version 2 - GPLv2) as published by
the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

define( 'PLUGLOOK_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

include_once(PLUGLOOK_PLUGIN_DIR.'includes/functions.php');

class WMD_PrettyPlugins extends WMD_PrettyPlugins_Functions {

	var $plugin_main_file;
	var $plugin_dir_url;
	var $plugin_dir;
	var $plugin_basename;
	var $plugin_rel;
	var $plugin_dir_custom;
	var $plugin_dir_url_custom;

	var $blog_id;
	var $pro_site_plugin_active;
	var $pro_site_settings;

	var $plugins_custom_data;
	var $plugins_custom_data_config;
	var $plugins_categories;
	var $plugins_categories_config;

	var $default_options;
	var $options;
	var $current_theme_details;

	function __construct() {
		//loads dashboard stuff
		global $wpmudev_notices;
		$wpmudev_notices[] = array( 'id'=> 852474, 'name'=> 'Pretty Plugins', 'screens' => array( 'toplevel_page_pretty-plugins', 'settings_page_pretty-plugins-network' ) );
		include_once(PLUGLOOK_PLUGIN_DIR.'external/dash-notice/wpmudev-dash-notification.php');

		//plugin only works on admin
		if(is_admin()) {
			$this->init_vars();

			register_activation_hook($this->plugin_main_file, array($this, 'do_activation'));

			//if in setup mode, disable everything for other sites then main.
			if( isset($this->options['setup_mode']) && ($this->options['setup_mode'] == 0 || ($this->blog_id == 1 && $this->options['setup_mode'] == 1)) ) {
				add_action('plugins_loaded', array($this,'plugins_loaded'));

				add_action('admin_enqueue_scripts', array($this,'register_scripts_styles_admin'));

				add_action('admin_init', array($this,'admin_init'));
				add_action('init', array($this,'init'));

				add_action('admin_menu', array($this,'admin_page'), 20);
				add_action('network_admin_menu', array($this,'network_admin_page'), 20);
				add_action('contextual_help', array($this,'network_plugins_help'), 10, 2);
				add_action('network_admin_notices', array($this,'options_page_validate_save_notices'));
				add_action('all_admin_notices', array($this,'plugin_page_notice'), 11);
				add_filter('network_admin_plugin_action_links', array($this,'network_admin_plugin_action_links'), 10, 3);

				add_action('admin_footer-plugins.php', array($this,'prettyplugins_edit_html'));

				add_action('wp_ajax_prettyplugins_add_category_ajax', array($this,'add_category_ajax'));
				add_action('wp_ajax_prettyplugins_save_category_ajax', array($this,'save_category_ajax'));
				add_action('wp_ajax_prettyplugins_save_plugin_details_ajax', array($this,'save_plugin_details_ajax'));
			}
		}
	}

    function init_vars() {
    	$this->blog_id = get_current_blog_id();

		$this->plugin_main_file = __FILE__;
		$this->plugin_dir = PLUGLOOK_PLUGIN_DIR;
		$this->plugin_dir_url = plugin_dir_url($this->plugin_main_file);
		$this->plugin_basename = plugin_basename($this->plugin_main_file);
		$this->plugin_rel = dirname($this->plugin_basename).'/';

		$wp_upload_dir = wp_upload_dir();
		if($this->blog_id != 1)
			foreach ($wp_upload_dir as $type => $value)
				if($type == 'basedir' || $type == 'baseurl') {
					$parts = explode('/', $value);
					if(is_numeric(end($parts))) {
						array_pop($parts);
						array_pop($parts);
						$wp_upload_dir[$type] = implode('/', $parts);
					}
				}

		$this->plugin_dir_custom = $wp_upload_dir['basedir'].'/prettyplugins/';
		$this->plugin_dir_url_custom = $wp_upload_dir['baseurl'].'/prettyplugins/';

		$this->default_options = array(
			'setup_mode' => '1',
			'theme' => 'standard/quick-sand',
			'plugins_links' => 'plugin_cutom_url',
			'plugins_auto_screenshots' => '0',
			'plugins_auto_screenshots_by_name' => '0',
			'plugins_hide_descriptions' => '0',
			'plugins_page_title' => __('Plugins', 'wmd_prettyplugins'),
			'plugins_page_description' => __('Plugins lets you enable additional functionality on your site! Here is a list of what we have available for you.', 'wmd_prettyplugins'),
			'plugins_link_label' => __('Learn more', 'wmd_prettyplugins')
		);

		//load options
		$this->options = get_site_option('wmd_prettyplugins_options');
    }

    function do_activation() {
    	if(!is_multisite())
    		trigger_error(sprintf(__('Pretty Plugins only works in multisite configuration. You can read more about it <a href="%s" target="_blank">here</a>.', 'wmd_prettyplugins'), 'http://codex.wordpress.org/Create_A_Network'),E_USER_ERROR);
    	else {
	        //create folder for custom themes
	        if (!is_dir($this->plugin_dir_custom)) {
	            mkdir($this->plugin_dir_custom);

	            if (!is_dir($this->plugin_dir_custom.'themes/'))
	            	mkdir($this->plugin_dir_custom.'themes/');
	        	if (!is_dir($this->plugin_dir_custom).'screenshots/')
	            	mkdir($this->plugin_dir_custom.'screenshots/');
	        }

	        //save default options
			if(get_site_option('wmd_prettyplugins_options', 0) == 0)
				update_site_option('wmd_prettyplugins_options', $this->default_options);
		}
    }

	function plugins_loaded() {
		//delete_site_option( 'wmd_prettyplugins_options');
		load_plugin_textdomain( 'wmd_prettyplugins', false, $this->plugin_rel.'languages/' );
	}

	function init(){
		global $pagenow;

		//load stuff when on correct page
		if($this->is_prettyplugin_data_required()) {
			$this->plugins_custom_data = get_site_option('wmd_prettyplugins_plugins_custom_data', array());
			$this->current_theme_details = $this->get_current_theme_details();
		}

		//we need categories also on all admin pages because it is used in menu
		if(!is_network_admin() || $this->is_prettyplugin_data_required()) {
			//Check if prosite and plugin module is active
			$this->pro_site_settings = get_site_option( 'psts_settings' );
			if(function_exists('is_pro_site') && isset($this->pro_site_settings['modules_enabled']) && in_array('ProSites_Module_Plugins', $this->pro_site_settings['modules_enabled']))
				$this->pro_site_plugin_active = true;
			else {
				$this->pro_site_plugin_active = false;
				$this->pro_site_settings = false;
			}

			//load config file if exists
			if(file_exists($this->plugin_dir_custom.'config.xml')) {
				//check last modified time, load file if new
				$config_file_m_time = filemtime($this->plugin_dir_custom.'config.xml');
				if($config_file_m_time != get_site_option('wmd_prettyplugins_last_config_file_m_time', 0)) {
					update_site_option('wmd_prettyplugins_last_config_file_m_time', $config_file_m_time);
					$this->import_xml_data_setting_file($this->plugin_dir_custom.'config.xml', 1);
				}
				else {
					$this->plugins_categories_config = get_site_option('wmd_prettyplugins_plugins_categories_config', array());
					$this->plugins_custom_data_config = get_site_option('wmd_prettyplugins_plugins_custom_data_config', array());
				}
			}
			else {
				$this->plugins_categories_config = array();
				$this->plugins_custom_data_config = array();
			}
			//load data
			$this->plugins_categories = get_site_option('wmd_prettyplugins_plugins_categories', array());
		}

		//controlls welcome/setup notice
		if(current_user_can('manage_network_options') && (!isset($_POST['wmd_prettyplugins_options']['setup_mode']) && $this->options['setup_mode'] == 1 || (isset($_POST['wmd_prettyplugins_options']['setup_mode']) && $_POST['wmd_prettyplugins_options']['setup_mode'] != 0)) && $this->is_prettyplugin_data_required())
			add_action( 'all_admin_notices', array( $this, 'setup_mode_welcome_notice' ), 12 );

		//check if stuff are being exported
		if(isset($_REQUEST['prettyplugins_action']) && $_REQUEST['prettyplugins_action'] == 'export')
			add_action('wp_loaded', array($this,'export_data_settings'), 1);
	}

	function admin_init(){
		global $pagenow;

		//controls activate/deactivate actions for plugins
		$prettyplugins = isset($_REQUEST['page']) ? $_REQUEST['page'] : 0;
		$plugin = isset($_REQUEST['plugin']) ? $_REQUEST['plugin'] : 0;
		$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : 0;
		if ( $action && $prettyplugins == 'pretty-plugins.php' && $pagenow == 'admin.php' && $plugin ) {
			switch ( $action ) {
				case 'activate':
					if ( ! current_user_can('activate_plugins') )
						wp_die(__('You do not have sufficient permissions to activate plugins for this site.'));

					if ( is_multisite() && ! is_network_admin() && is_network_only_plugin( $plugin ) ) {
						wp_redirect( self_admin_url("admin.php?page=pretty-plugins.php&plugin_status=$status") );
						exit;
					}

					check_admin_referer('activate-plugin_'.$plugin);

					$result = activate_plugin($plugin, self_admin_url('admin.php?page=pretty-plugins.php&activate=true&plugin=' . $plugin), is_network_admin() );
					if ( is_wp_error( $result ) ) {
						if ( 'unexpected_output' == $result->get_error_code() ) {
							$redirect = self_admin_url('admin.php?page=pretty-plugins.php&error=true&charsout=' . strlen($result->get_error_data()) . '&plugin=' . $plugin );
							wp_redirect(add_query_arg('_error_nonce', wp_create_nonce('plugin-activation-error_' . $plugin), $redirect));
							exit;
						} else {
							wp_die($result);
						}
					}

					if ( ! is_network_admin() ) {
						$recent = (array) get_option( 'recently_activated' );
						unset( $recent[ $plugin ] );
						update_option( 'recently_activated', $recent );
					}

					exit;
					break;
				case 'deactivate':
					if ( ! current_user_can('activate_plugins') )
						wp_die(__('You do not have sufficient permissions to deactivate plugins for this site.'));

					check_admin_referer('deactivate-plugin_'.$plugin);

					if ( ! is_network_admin() && is_plugin_active_for_network( $plugin ) ) {
						wp_redirect( self_admin_url("admin.php?page=pretty-plugins.php") );
						exit;
					}

					deactivate_plugins( $plugin, false, is_network_admin() );
					if ( ! is_network_admin() )
						update_option( 'recently_activated', array( $plugin => time() ) + (array) get_option( 'recently_activated' ) );
					if ( headers_sent() )
						echo "<meta http-equiv='refresh' content='" . esc_attr( "0;url=admin.php?page=pretty-plugins.php&deactivate=true" ) . "' />";
					else
						wp_redirect( self_admin_url("admin.php?page=pretty-plugins.php&deactivate=true") );
					exit;
					break;
			}
		}

		if($this->pro_site_plugin_active) {
			global $plugin_page;
			if ( isset($plugin_page) && $plugin_page == 'pretty-premium-plugins' )
				wp_redirect( admin_url('admin.php?page=pretty-plugins.php') );
		}
	}

	function register_scripts_styles_admin($hook) {
		global $wp_version;

		//register scripts and styles for plugin page
		if( $hook == 'toplevel_page_pretty-plugins' ) {
			wp_register_style('wmd-prettyplugins-theme', $this->current_theme_details['dir_url'].'style.css', array(), '2');
			wp_enqueue_style('wmd-prettyplugins-theme');

			wp_register_script('wmd-prettyplugins-theme', $this->current_theme_details['dir_url'].'theme.js', array('jquery'), false, true);
			wp_enqueue_script('wmd-prettyplugins-theme');

			if(isset($_REQUEST['category']))
				$show_category = $_REQUEST['category'];
			else
				$show_category = 'all';
			$params = array(
				'show_category' => $show_category,
				'show_status' => 'all'
			);
			wp_localize_script( 'wmd-prettyplugins-theme', 'wmd_pl_a', $params );
		}
		//register scripts and styles for network plugin page
		elseif($hook == 'plugins.php' && is_network_admin() && (!isset($_GET['plugin_status']) || (isset($_GET['plugin_status']) && $_GET['plugin_status'] != 'mustuse' && $_GET['plugin_status'] != 'dropins'))) {
			wp_register_style('wmd-prettyplugins-network-admin', $this->plugin_dir_url.'css/network-admin.css');
			wp_enqueue_style('wmd-prettyplugins-network-admin');

			wp_register_script('wmd-prettyplugins-network-admin', $this->plugin_dir_url.'js/network-admin.js', false, true);
			wp_enqueue_script('wmd-prettyplugins-network-admin');

			$network_only_plugins = array();
			$plugins = apply_filters('all_plugins', get_plugins());
			foreach ($plugins as $path => $plugin)
				if($plugin['Network'])
					$network_only_plugins[] = $path;

			$plugins_custom_data_ready = $this->get_converted_plugins_data_for_js($this->get_merged_plugins_custom_data());

			$plugins_categories_ready = $this->get_merged_plugins_categories();
			$protocol = isset( $_SERVER["HTTPS"] ) ? 'https://' : 'http://'; //This is used to set correct adress if secure protocol is used so ajax calls are working
			$params = array(
				'ajax_url' => admin_url( 'admin-ajax.php', $protocol ),
				'admin_url' => admin_url( '', $protocol ),
				'prettyplugins_url' => $this->plugin_dir_url,
				'theme_url' => $this->current_theme_details['dir_url'],
				'image' => __('Image', 'wmd_prettyplugins'),
				'edit_code' => __('Edit Code', 'wmd_prettyplugins'),
				'orginal_description' => __('Show/hide orginal description', 'wmd_prettyplugins'),
				'edit_details' => __('Edit Details', 'wmd_prettyplugins'),
				'edit_details_a_title' => __('Edit plugin details like title, discription, image and categories', 'wmd_prettyplugins'),
				'visit_help' => __('Visit plugin help site', 'wmd_prettyplugins'),
				'categories' => __('Categories', 'wmd_prettyplugins'),
				'choose_screenshot' => __('Choose image for plugin screenshot (recommended size: 300px on 225px)', 'wmd_prettyplugins'),
				'select_image' => __('Select Image', 'wmd_prettyplugins'),
				'edit' => __('Edit', 'wmd_prettyplugins'),
				'plugin_details' => $plugins_custom_data_ready,
				'plugin_categories' => $plugins_categories_ready,
				'network_only_plugins' => $network_only_plugins
			);
			wp_localize_script( 'wmd-prettyplugins-network-admin', 'wmd_pl_na', $params );

			wp_enqueue_media();
		}

        //mp6 icon load
        if ( $wp_version >= 3.8 ) {
            wp_register_style( 'wmd-prettyplugins-mp6', $this->plugin_dir_url . 'css/mp6.css');
            wp_enqueue_style('wmd-prettyplugins-mp6');
        }
	}

	//Replaces plugins page with custom
	function admin_page() {
		remove_menu_page('plugins.php');
		add_menu_page(stripslashes($this->options['plugins_page_title']), stripslashes($this->options['plugins_page_title']), 'activate_plugins', basename($this->plugin_main_file), array($this,'new_plugin_page'), $this->plugin_dir_url.'/images/icon.png', 65);

		if($this->pro_site_plugin_active) {
			remove_submenu_page('psts-checkout', 'premium-plugins');
			add_submenu_page('psts-checkout', $this->pro_site_settings['pp_name'], $this->pro_site_settings['pp_name'], 'activate_plugins', 'pretty-premium-plugins', array($this,'new_plugin_page') );
		}

		add_submenu_page( 'pretty-plugins.php', __('Plugins', 'wmd_prettyplugins'), __('All', 'wmd_prettyplugins'), 'activate_plugins', basename($this->plugin_main_file), array($this,'new_plugin_page') );

		$plugins_categories_ready = $this->get_merged_plugins_categories();
		foreach ($plugins_categories_ready as $plugins_category_key => $plugins_category)
			add_submenu_page( 'pretty-plugins.php', $plugins_category, $plugins_category, 'activate_plugins', basename($this->plugin_main_file).'&category='.$plugins_category_key, array($this,'new_plugin_page') );
	}

	function network_plugins_help($contextual_help, $screen_id) {
		if($screen_id == 'plugins-network') {
			//Adds new help tab
			$screen = get_current_screen();
		    $screen->add_help_tab( array(
		        'id'	=> 'edit_details',
		        'title'	=> __('Editing Plugin Details', 'wmd_prettyplugins'),
		        'content'	=> '
		        	<p>'.sprintf(__( 'You can edit plugin details for each plugin by clicking "Edit Details". All new details will be visible on <a href="%s">the plugins page</a> available for all network sites. It is also possible to control aditional settings on <a href="%s">this site</a>.','wmd_prettyplugins'),  admin_url('admin.php?page=pretty-plugins.php'), admin_url('network/settings.php?page=pretty-plugins.php')).'</p>
		        	<p>'.__( '<strong>Name</strong> - Replace the name of the plugin with one of your choice. Leave blank to use the original name.','wmd_prettyplugins').'</p>
		        	<p>'.__( '<strong>Custom URL</strong> - Create an external plugin link to any URL of your choice, for support documentation for example.','wmd_prettyplugins').'</p>
		        	<p>'.__( '<strong>Image URL</strong> - Set the featured image for this plugin. You can choose an image from your media gallery or copy the URL of the file located in "wp-content/uploads/prettyplugins/screenshots/". Alternatively, a file with the correct name will be autoloaded even when this field is empty (example: plugin location - "wp-content/plugins/akismet/akismet.php", image file - "akismet-akismet.png". Only PNG files will work with this method.). "Auto load screenshot with correct name" setting needs to be set to true for it to work. Recommended dimensions are 600px on 450px.','wmd_prettyplugins').'</p>
		        	<p>'.__( '<strong>Categories</strong> - Allows you to set categories that the plugin will be assigned to. Unused categories will be automatically deleted.','wmd_prettyplugins').'</p>
		        	<p>'.__( '<strong>Description</strong> - Replace the original description of the plugin with your own. Leave blank to use the original.','wmd_prettyplugins').'</p>
		        ',
		    ) );


			//load tooltips for admin plugins page
			if(!class_exists('WpmuDev_HelpTooltipsDyn'))
				include($this->plugin_dir.'external/wpmudev-help-tooltips.php');
			$tips = new WpmuDev_HelpTooltipsDyn();
			$tips->set_icon_url($this->plugin_dir_url.'images/tooltip.png');
			$tips->set_use_notice(false);

			$tips->bind_tip(__('Replace the name of the plugin with one of your choice. Leave blank to use the original name.', 'wmd_prettyplugins'), '#name_tooltip');
			$tips->bind_tip(__('Create an external plugin link to any URL of your choice, for support documentation for example.', 'wmd_prettyplugins'), '#custom_url_tooltip');
			$tips->bind_tip(__('Set the featured image for this plugin. Recommended dimensions are 600px on 450px. Use help tab (top right corner) to get info about advanced usage.', 'wmd_prettyplugins'), '#image_url_tooltip');
			$tips->bind_tip(__('Allows you to set categories that the plugin will be assigned to. Unused categories will be automatically deleted.', 'wmd_prettyplugins'), '#categories_tooltip');
			$tips->bind_tip(__('Replace the original description of the plugin with your own. Leave blank to use the original.', 'wmd_prettyplugins'), '#description_tooltip');
		}
	}

	function setup_mode_welcome_notice() {
        echo '<div class="updated fade"><p>'.sprintf(__('Pretty Plugins is currently in "Setup Mode". This reminder will disappear after configuring the plugin and setting Setup Mode to "False" <a href="%s">here</a>. You can modify the details for every plugin in your network <a href="%s">here</a>, and see how the "Plugins" page looks like for all network sites <a href="%s">here</a>.', 'wmd_prettyplugins'), admin_url('network/settings.php?page=pretty-plugins.php'), admin_url('network/plugins.php'), admin_url('admin.php?page=pretty-plugins.php'), add_query_arg('prettyplugins_action', 'remove_notice')).'</a></p></div>';
	}

	function plugin_page_notice() {
		global $pagenow;

		if ( $pagenow == 'admin.php' && isset($_REQUEST['page']) && $_REQUEST['page'] == 'pretty-plugins.php' && is_super_admin() && !is_network_admin() )
			echo '<div class="updated"><p>'.sprintf(__('As a Super Admin you can activate any plugins for this site. Please note that standard plugin page can still be accessed at <a href="%s">this URL</a>.', 'wmd_prettyplugins'), admin_url('plugins.php')).'</p></div>';
	}

	function network_admin_page() {
		add_submenu_page('settings.php', __( 'Pretty Plugins', 'wmd_prettyplugins' ), __( 'Pretty Plugins', 'wmd_prettyplugins' ), 'manage_network_options', basename($this->plugin_main_file), array($this,'network_option_page'));
	}

	function network_option_page() {
		include($this->plugin_dir.'includes/page-network-admin.php');
	}

	function network_admin_plugin_action_links($actions, $plugin_file, $plugin_data) {
		//adds edit details link
		if(((isset($plugin_data['Network']) && !$plugin_data['Network']) || !isset($plugin_data['Network'])) && (!isset($_GET['plugin_status']) || (isset($_GET['plugin_status']) && $_GET['plugin_status'] != 'mustuse' && $_GET['plugin_status'] != 'dropins')))
			array_splice($actions, 1, 0, '<a href="#'.$plugin_file.'" title="'.__('Edit plugin details like title, discription, image and categories', 'wmd_prettyplugins').'" class="edit_details">'.__('Edit Details', 'wmd_prettyplugins').'</a>');

		//changes edit link to edit code for clarity
		if(isset($actions['edit']))
			$actions['edit'] = str_replace(__('Edit'), __( 'Edit Code', 'wmd_prettyplugins' ), $actions['edit']);

		if($plugin_file == 'pretty-plugins/pretty-plugins.php')
			$actions['settings'] = '<a href="'.admin_url('network/settings.php?page=pretty-plugins.php').'" title="'.__('Go to the Pretty Plugins settings page', 'wmd_prettyplugins').'">'.__('Settings', 'wmd_prettyplugins').'</a>';

		return $actions;
	}

	function options_page_validate_save_notices() {
		//default save
		if(isset($_POST['option_page']) && $_POST['option_page'] == 'wmd_prettyplugins_options' && isset($_POST['save_settings']) && wp_verify_nonce($_POST['_wpnonce'], 'wmd_prettyplugins_options-options')) {
			$validated = $this->get_validated_options($_POST['wmd_prettyplugins_options']);

			update_site_option( 'wmd_prettyplugins_options', $validated );

			echo '<div id="message" class="updated"><p>'.__( 'Successfully saved', 'wmd_prettyplugins' ).'</p></div>';
		}
		elseif(isset($_REQUEST['prettyplugins_action'], $_REQUEST['_wpnonce']) && wp_verify_nonce($_REQUEST['_wpnonce'], 'wmd_prettyplugins_options')) {
			//delete custom data
			if($_REQUEST['prettyplugins_action'] == 'delete_custom_data') {
				echo '<div id="message" class="updated"><p>'.__( 'All custom plugin data deleted sucessfully.', 'wmd_prettyplugins' ).'</p></div>';
				delete_site_option('wmd_prettyplugins_plugins_custom_data');
				delete_site_option('wmd_prettyplugins_plugins_custom_data_config');
				delete_site_option('wmd_prettyplugins_plugins_categories');
				delete_site_option('wmd_prettyplugins_plugins_categories_config');
				delete_site_option('wmd_prettyplugins_last_config_file_m_time');
			}
		}
		//try to import config file
		elseif(isset($_POST['option_page'], $_POST['import_config'], $_POST['_wpnonce']) && $_POST['option_page'] == 'wmd_prettyplugins_options' && wp_verify_nonce($_POST['_wpnonce'], 'wmd_prettyplugins_options-options')) {
			if (!isset($_FILES['config_file']) && $_FILES['config_file']['error'] > 0) {
				echo '<div id="message" class="error"><p>'.__( 'There was a problem while uploading file.', 'wmd_prettyplugins' ).'</p></div>';
			}
			else {
				$this->import_xml_data_setting_file($_FILES['config_file']["tmp_name"]);
				echo '<div id="message" class="updated"><p>'.__( 'Plugins data and settings imported successfully.', 'wmd_prettyplugins' ).'</p></div>';
			}
		}
	}

	function export_data_settings() {
		$this->export_xml_data_setting_file();
	}

	function prettyplugins_edit_html() {
		include($this->plugin_dir.'includes/element-edit-plugin-details.php');
	}

	function add_category_ajax() {
		error_reporting(0);
		$error = 0;

		//loads variables for ajax call
		$this->plugins_categories = get_site_option('wmd_prettyplugins_plugins_categories', array());

		if(wp_verify_nonce($_POST['_wpnonce'], 'wmd_prettyplugins_edit_plugin_details')) {
			$last_category = $this->get_last_category_id();
			$last_category++;
			$new_key = 'category'.$last_category;

			$this->plugins_categories[$new_key] = $_POST['plugin_new_category'];

			if(!empty($this->plugins_categories[$new_key]) && !empty($_POST['plugin_new_category']))
				update_site_option('wmd_prettyplugins_plugins_categories', $this->plugins_categories);
			else
				$error = 1;
		}
		else
			$error = 1;

		echo json_encode(array('id' => $new_key, 'name' => $_POST['plugin_new_category'], 'error' => $error));
		die();
	}

	function save_category_ajax() {
		error_reporting(0);
		$error = 0;

		//loads variables for ajax call
		$this->plugins_categories = get_site_option('wmd_prettyplugins_plugins_categories', array());

		if(wp_verify_nonce($_POST['_wpnonce'], 'wmd_prettyplugins_edit_plugin_details')) {
			if(isset($this->plugins_categories[$_POST['plugin_edit_category_key']]) && !empty($_POST['plugin_edit_category']) && $_POST['plugin_edit_category_key']) {
				$this->plugins_categories[$_POST['plugin_edit_category_key']] = $_POST['plugin_edit_category'];
				update_site_option('wmd_prettyplugins_plugins_categories', $this->plugins_categories);
			}
			else
				$error = 1;
		}
		else
			$error = 1;

		echo json_encode(array('id' => $_POST['plugin_edit_category_key'], 'name' => $_POST['plugin_edit_category'], 'error' => $error));
		die();
	}

	function save_plugin_details_ajax() {
		error_reporting(0);
		$error = 0;

		//loads variables for ajax call
		$this->plugins_categories = get_site_option('wmd_prettyplugins_plugins_categories', array());
		$this->plugins_custom_data = get_site_option('wmd_prettyplugins_plugins_custom_data', array());
		$this->plugins_custom_data_config = get_site_option('wmd_prettyplugins_plugins_custom_data_config', array());

		if(wp_verify_nonce($_POST['_wpnonce'], 'wmd_prettyplugins_edit_plugin_details')) {
			if(is_numeric($_POST['plugin_image_id']))
				$_POST['plugin_image_url'] = $this->get_resized_attachment_url( $_POST['plugin_image_id'] );

			foreach($_POST['plugin_categories'] as $key => $category)
				if(strpos($category, 'config') !== false)
					unset($_POST['plugin_categories'][$key]);

			if(!isset($this->plugins_custom_data[$_POST['plugin_path']]))
				$this->plugins_custom_data[$_POST['plugin_path']] = array();

			$data = array(
				'Categories' => $_POST['plugin_categories'],
				'ScreenShot' => $_POST['plugin_image_url'],
				'ScreenShotID' => $_POST['plugin_image_id'],
				'CustomLink' => $_POST['plugin_custom_url'],
				'Description' => $_POST['plugin_description'],
				'Name' => $_POST['plugin_name'],
			);
			foreach ($data as $name => $value)
				if(!empty($data[$name]))
					$this->plugins_custom_data[$_POST['plugin_path']][$name] = $value;
				else
					unset($this->plugins_custom_data[$_POST['plugin_path']][$name]);

			//empty categories fix
			if(count($this->plugins_custom_data[$_POST['plugin_path']]['Categories']) < 1)
				unset($this->plugins_custom_data[$_POST['plugin_path']]['Categories']);

			//adds http to custom link
			if(isset($this->plugins_custom_data[$_POST['plugin_path']]['CustomLink']))
				if (strpos($this->plugins_custom_data[$_POST['plugin_path']]['CustomLink'], '://') === false  && count(explode('/', $this->themes_custom_data[$_POST['plugin_path']]['CustomLink'])) > 1)
					$this->plugins_custom_data[$_POST['plugin_path']]['CustomLink'] = 'http://'.$this->plugins_custom_data[$_POST['plugin_path']]['CustomLink'];

			//remove unused and categories if necessary
			$removed_categories = $all_used_categories = array();
			foreach ($this->plugins_custom_data as $path => $data)
				if(isset($data['Categories']))
					$all_used_categories = array_merge($all_used_categories, $data['Categories']);
			foreach ($this->plugins_categories as $key => $category_name)
				if(!in_array($key, $all_used_categories)) {
					$update_categories = 1;
					unset($this->plugins_categories[$key]);
					$removed_categories[] = $key;
				}
			if(isset($update_categories))
				update_site_option('wmd_prettyplugins_plugins_categories', $this->plugins_categories);

			$plugins_custom_data_ready = $this->get_converted_plugins_data_for_js($this->get_merged_plugins_custom_data());
			if(empty($plugins_custom_data_ready[$_POST['plugin_path']]))
				$error = 1;

			if(empty($this->plugins_custom_data[$_POST['plugin_path']]))
				unset($this->plugins_custom_data[$_POST['plugin_path']]);

			ksort($this->plugins_custom_data);
			update_site_option('wmd_prettyplugins_plugins_custom_data', $this->plugins_custom_data);
		}
		else
			$error = 1;

		echo json_encode(array('new_details' => $plugins_custom_data_ready[$_POST['plugin_path']], 'remove_categories' => $removed_categories, 'error' => $error));
		die();
	}

	function new_plugin_page() {
		if ( ! current_user_can('activate_plugins') )
			wp_die( __( 'You do not have sufficient permissions to manage plugins for this site.' ) );

		$plugins_categories = $this->get_merged_plugins_categories();

		$plugins_default_data = apply_filters('all_plugins', get_plugins());
		$plugins_custom_data = $this->get_merged_plugins_custom_data();

		//remove details for plugins that do not exists
		foreach($plugins_custom_data as $plugin_path => $plugin)
			if(!array_key_exists($plugin_path, $plugins_default_data))
				unset($plugins_custom_data[$plugin_path]);

		$plugins_orginal = array_replace_recursive($plugins_default_data, $plugins_custom_data);

		$count = 0;
		$plugins = array();
		foreach($plugins_orginal as $plugin_path => $plugin) {
			if( (isset($plugin['Network']) && $plugin['Network']) || $plugin_path == 'pretty-plugins/pretty-plugins.php' || !isset($plugin['Name']) || is_plugin_active_for_network($plugin_path) )
				continue;

			$plugin_prepare = $plugin;

			$plugin_prepare['Actions'] = array();

			$count++;
			$plugin_prepare['ListID'] = $count;

			$plugin_prepare['isActive'] = is_plugin_active( $plugin_path );

			//set correct screenshot
			$plugin_prepare['ScreenShot'] = $this->get_screenshot_url(isset($plugin_prepare['ScreenShot']) ? $plugin_prepare['ScreenShot'] : '', $plugin_path);

			$plugin_prepare['isAvailable'] = ($this->pro_site_plugin_active) ? $this->prosite_plugin_available($plugin_path) : true;

			if(!$plugin_prepare['isAvailable']) {
				$plugin_prepare['ActionLinkText'] = sprintf( __('Upgrade to %s!', 'wmd_prettyplugins'), $this->prosite_plugin_required_level_name($plugin_path));
				$checkout_url = isset($this->pro_site_settings['checkout_url']) ? $this->pro_site_settings['checkout_url'] : '';
			    if(apply_filters('psts_force_ssl', false))
			    	$checkout_url = str_replace('http://', 'https://', $checkout_url);
				$checkout_url = add_query_arg('bid', $this->blog_id, $checkout_url);
				$plugin_prepare['ActionLink'] = $checkout_url;
				$plugin_prepare['ActionLinkClass'] = 'upgrade';
			}
			elseif($plugin_prepare['isActive']) {
				$plugin_prepare['ActionLinkText'] = __('Deactivate', 'wmd_prettyplugins');
				$plugin_prepare['ActionLink'] = wp_nonce_url('admin.php?page=pretty-plugins.php&amp;action=deactivate&amp;plugin='.$plugin_path, 'deactivate-plugin_'.$plugin_path);
				$plugin_prepare['ActionLinkClass'] = 'deactivate';
			}
			else {
				$plugin_prepare['ActionLinkText'] = __('Activate', 'wmd_prettyplugins');
				$plugin_prepare['ActionLink'] = wp_nonce_url('admin.php?page=pretty-plugins.php&amp;action=activate&amp;plugin='.$plugin_path, 'activate-plugin_'.$plugin_path);
				$plugin_prepare['ActionLinkClass'] = 'activate';
			}

			//set up ribbon
			if($plugin_prepare['isActive'])
				$plugin_prepare['Ribbon'] = '<div class="ribbon"><img src="'.$this->current_theme_details['dir_url'].'images/ribbon_active.png'.'" alt="'.__('Active', 'wmd_prettyplugins').'"></div>';
			elseif($this->pro_site_plugin_active)
				if($plugin_prepare['isAvailable'])
					$plugin_prepare['Ribbon'] = '<div class="ribbon"><img src="'.$this->current_theme_details['dir_url'].'images/ribbon_available.png'.'" alt="'.__('Free', 'wmd_prettyplugins').'"></div>';
				else
					$plugin_prepare['Ribbon'] = '<div class="ribbon"><img src="'.$this->current_theme_details['dir_url'].'images/ribbon_upgrade.png'.'" alt="'.__('Pro Only', 'wmd_prettyplugins').'"></div>';
			else
				$plugin_prepare['Ribbon'] = '';

			if(isset($plugin_prepare['Description']))
				$plugin_prepare['Description'] = stripslashes($plugin_prepare['Description']);

			if(isset($plugin_prepare['Categories']) && count($plugin_prepare['Categories']) > 0)
				foreach ($plugin_prepare['Categories'] as $plugin_category_key)
					$plugin_prepare['CategoriesNames'][] = $plugins_categories[$plugin_category_key];

			//Set up Plugin Link according to options settings
			if($this->options['plugins_links'] == 'plugin_url' && isset($plugin_prepare['PluginURI']) && !empty($plugin_prepare['PluginURI']))
				$plugin_prepare['PluginLink'] = $plugin_prepare['PluginURI'];
			elseif($this->options['plugins_links'] == 'plugin_cutom_url' && isset($plugin_prepare['CustomLink']) && !empty($plugin_prepare['CustomLink']))
				$plugin_prepare['PluginLink'] = $plugin_prepare['CustomLink'];
			elseif($this->options['plugins_links'] == 'plugin_url_or_cutom_url')
				if(isset($plugin_prepare['CustomLink']) && !empty($plugin_prepare['CustomLink']))
					$plugin_prepare['PluginLink'] = $plugin_prepare['CustomLink'];
				elseif(isset($plugin_prepare['PluginURI']) && !empty($plugin_prepare['PluginURI']))
					$plugin_prepare['PluginLink'] = $plugin_prepare['PluginURI'];

			//setup action links by plugins for plugins
			$plugin_prepare['Actions'] = apply_filters('plugin_action_links', array(), $plugin_path, $plugin_prepare, '');
			$plugin_prepare['Actions'] = apply_filters("plugin_action_links_$plugin_path", $plugin_prepare['Actions'], $plugin_path, $plugin_prepare, '');
			//remove link added by prosites because we have better one:)
			if(isset($checkout_url))
				foreach($plugin_prepare['Actions'] as $key => $value)
					if(strpos($value, $checkout_url) !== false)
						unset($plugin_prepare['Actions'][$key]);

			$plugins[$plugin_path] = $plugin_prepare;
		}

		uasort($plugins, array($this,'compare_by_name'));
		?>
		<?php if ( isset($_GET['error']) ) :

			if ( isset( $_GET['main'] ) )
				$errmsg = __( 'You cannot delete an plugin while it is active on the main site.' );
			elseif ( isset($_GET['charsout']) )
				$errmsg = sprintf(__('The plugin generated %d characters of <strong>unexpected output</strong> during activation. If you notice &#8220;headers already sent&#8221; messages, problems with syndication feeds or other issues, try deactivating or removing this plugin.'), $_GET['charsout']);
			else
				$errmsg = __('plugin could not be activated because it triggered a <strong>fatal error</strong>.');
			?>
			<div id="message" class="updated"><p><?php echo $errmsg; ?></p>
			<?php
				if ( !isset( $_GET['main'] ) && !isset($_GET['charsout']) && wp_verify_nonce($_GET['_error_nonce'], 'plugin-activation-error_' . $plugin) ) { ?>
			<iframe style="border:0" width="100%" height="70px" src="<?php echo 'plugins.php?action=error_scrape&amp;plugin=' . esc_attr($plugin) . '&amp;_wpnonce=' . esc_attr($_GET['_error_nonce']); ?>"></iframe>
			<?php
				}
			?>
			</div>
		<?php elseif ( isset($_GET['activate']) ) : ?>
			<div id="message" class="updated"><p><?php _e('Plugin <strong>activated</strong>.') ?></p></div>
		<?php elseif ( isset($_GET['deactivate']) ) : ?>
			<div id="message" class="updated"><p><?php _e('Plugin <strong>deactivated</strong>.') ?></p></div>
		<?php endif;

		include($this->current_theme_details['dir'].'index.php');
	}
}

global $wmd_prettyplugins;
$wmd_prettyplugins = new WMD_PrettyPlugins;