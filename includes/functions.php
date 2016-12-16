<?php
class WMD_PrettyPlugins_Functions {


	//Helpers


	//check if page that requires data is displayed
	function is_prettyplugin_data_required() {
		global $pagenow;

		if(
			(
				$pagenow == 'settings.php' &&
				isset($_REQUEST['page']) &&
				$_REQUEST['page'] == 'pretty-plugins.php'
			)
			||
			(
				$pagenow == 'admin.php' &&
				isset($_REQUEST['page']) &&
				$_REQUEST['page'] == 'pretty-plugins.php'
			)
			||
			(
				$pagenow == 'plugins.php' &&
				is_network_admin()
			)
		)
			return true;
		else
			return false;
	}
    //get theme array for select option
    function get_themes() {
    	$themes_dirs = $themes = array();
    	$themes_dirs_paths = array(
    			'standard' => $this->plugin_dir.'themes/',
    			'custom' => $this->plugin_dir_custom.'themes/'
    		);
    	if(is_dir($themes_dirs_paths['standard']))
			$themes_dirs['standard'] = scandir($themes_dirs_paths['standard']);

		foreach ($themes_dirs as $type => $themes_dir)
			foreach ($themes_dir as $theme_dir) {
				$theme_dir = str_replace('.', '', $theme_dir);
				if(!empty($theme_dir))
				    if (is_dir($themes_dirs_paths[$type].'/'.$theme_dir))
				        if(file_exists($themes_dirs_paths[$type].'/'.$theme_dir.'/index.php')) {
				        	$theme_dir_name = ucwords(str_replace('-', ' ', $theme_dir));
				        	$type_name = ($type == 'custom') ? __( ' (Custom)', 'wmd_prettyplugins' ) : '';
				        	$themes[$type.'/'.$theme_dir] = $theme_dir_name.$type_name;
				    	}
			}

		return $themes;
    }

    function get_current_theme_details() {
    	$theme = array('url' => '', 'dir' => '');
    	$theme_details = explode('/', $this->options['theme']);
    	if($theme_details[0] == 'standard') {
    		$theme['dir_url'] = $this->plugin_dir_url.'themes/'.$theme_details[1].'/';
    		$theme['dir'] = $this->plugin_dir.'themes/'.$theme_details[1].'/';
    		$theme['type'] = 'standard';
    	}
    	elseif($theme_details[0] == 'custom' && !empty($this->plugin_dir_url_custom)) {
    		$theme['dir_url'] = $this->plugin_dir_url_custom.'themes/'.$theme_details[1].'/';
    		$theme['dir'] = $this->plugin_dir_custom.'themes/'.$theme_details[1].'/';
    		$theme['type'] = 'custom';
    	}

    	return $theme;
    }

    function get_screenshot_url($screenshot_value, $plugin_path, $get_default = 1) {
    	$plugin_path_slug = str_replace('.php', '', str_replace('/', '-', $plugin_path));

		if(!empty($screenshot_value) && count(explode('/', $screenshot_value)) == 1 && file_exists($this->plugin_dir_custom.'screenshots/'.$screenshot_value))
			$screenshot_value = $this->plugin_dir_url_custom.'screenshots/'.$screenshot_value;
		elseif(empty($screenshot_value) && $this->options['plugins_auto_screenshots_by_name'] && file_exists($this->plugin_dir_custom.'screenshots/'.$plugin_path_slug.'.png'))
			$screenshot_value = $this->plugin_dir_url_custom.'screenshots/'.$plugin_path_slug.'.png';
		elseif(empty($screenshot_value) && $this->options['plugins_auto_screenshots'] && file_exists(plugin_dir_path(WP_PLUGIN_DIR.'/'.$plugin_path).'screenshot-1.png'))
			$screenshot_value = plugins_url('screenshot-1.png', $plugin_path);
		elseif($get_default && (empty($screenshot_value) || (!empty($screenshot_value) && count(explode('/', $screenshot_value)) == 1 && !file_exists($this->plugin_dir_custom.'screenshots/'.$screenshot_value)))) {
			if($this->options['plugins_auto_screenshots_wp']) {
				$plugin_path_parts = explode("/", $plugin_path);
				$screenshot_value = '//ps.w.org/'.$plugin_path_parts[0].'/assets/icon-128x128.png';
			}
			else {
				global $wp_version;
				if($wp_version < 3.8 && $this->current_theme_details['type'] == 'standard' )
					$screenshot_value = $this->current_theme_details['dir_url'].'images/default_screenshot_classic.png';
				else
					$screenshot_value = $this->current_theme_details['dir_url'].'images/default_screenshot.png';
			}
		}

    	return (is_ssl()) ? str_replace('http://', 'https://', $screenshot_value) : $screenshot_value;
    }

	function get_resized_attachment_url($attachment_id, $width = '600', $height = '600', $crop = true, $suffix = "-plugin-screenshot") {
		$attachment_url = wp_get_attachment_url($attachment_id);
		if($attachment_url) {
			$attachment_meta = wp_get_attachment_metadata($attachment_id);
			if($attachment_meta['width'] > $width || $attachment_meta['height'] > $height) {
				$old_image_details = array('path' => get_attached_file($attachment_id), 'url' => $attachment_url);
				foreach ($old_image_details as $type => $address) {
					$path_parts = pathinfo($address);
					$filename = $path_parts['filename'];
					$new_filename = $filename.$suffix.'.'.$path_parts['extension'];
					$new_detail = $path_parts['dirname'].'/'.$new_filename;

					$new_image_details[$type] = $new_detail;
				}

				if(!file_exists($new_image_details['path'])) {
					$image = wp_get_image_editor($old_image_details['path']);
					if (!is_wp_error($image)) {
					    $image->resize($width, $height, $crop);
					    $image->save($new_image_details['path']);
					}
				}

				if(file_exists($new_image_details['path']))
					return $new_image_details['url'];
				else
					return false;
			}
			else
				return  $attachment_url;
		}
		else
			return false;
	}

	function get_merged_plugins_categories() {
		if(!isset($this->plugins_categories_config) || !is_array($this->plugins_categories_config))
			$this->plugins_categories_config = array();
		if(!isset($this->plugins_categories) || !is_array($this->plugins_categories))
			$this->plugins_categories = array();

		$categories = array_merge($this->plugins_categories_config, $this->plugins_categories);
		asort($categories);

		return $categories;
	}

	function get_merged_plugins_custom_data() {
		if(!isset($this->plugins_custom_data_config) || !is_array($this->plugins_custom_data_config))
			$this->plugins_custom_data_config = array();
		if(!isset($this->plugins_custom_data) || !is_array($this->plugins_custom_data))
			$this->plugins_custom_data = array();

		$plugins = array_replace_recursive($this->plugins_custom_data_config, $this->plugins_custom_data);

		//properly merge config categories
		foreach ($plugins as $path => $values) {
			$categories = (isset($this->plugins_custom_data[$path]['Categories']) && is_array($this->plugins_custom_data[$path]['Categories'])) ? $this->plugins_custom_data[$path]['Categories'] : array();
			$config_categories = (isset($this->plugins_custom_data_config[$path]['Categories'])) ? $this->plugins_custom_data_config[$path]['Categories'] : array();
			if(count($categories) || count($config_categories))
			$plugins[$path]['Categories'] = array_merge($categories, $config_categories);
		}

		ksort($plugins);

		return $plugins;
	}

	function get_last_category_id() {
		if($this->plugins_categories) {
			end($this->plugins_categories);
			$last_category = key($this->plugins_categories);
			return substr($last_category, 8);
		}
		else
			return 0;
	}

	function get_validated_options($input) {
		if(is_array($input)) {
			if(isset($input['plugins_links']) && in_array($input['plugins_links'], array('plugin_url', 'plugin_cutom_url', 'plugin_url_or_cutom_url', 'disable')))
				$this->options['plugins_links'] = $input['plugins_links'];
			else
				$this->options['plugins_links'] = 'plugin_cutom_url';

			$possible_themes = $this->get_themes();
			if(isset($input['theme']) && array_key_exists($input['theme'], $possible_themes))
				$this->options['theme'] = $input['theme'];
			else
				$this->options['theme'] = 'standard/quick-sand';

			$standard_options = array('plugins_link_label' => 'strip_tags', 'plugins_page_title' => 'strip_tags', 'plugins_page_description' => '', 'plugins_auto_screenshots' => '', 'plugins_auto_screenshots_wp' => '', 'setup_mode' => '', 'plugins_hide_descriptions' => '', 'plugins_auto_screenshots_by_name' => '');
			foreach ($standard_options as $option => $action) {
				if(isset($input[$option])) {
					if($action == 'strip_tags')
						$input[$option] = strip_tags($input[$option]);
					$this->options[$option] = $input[$option];
				}
				elseif(!isset($this->options[$option]))
					$this->options[$option] = $this->default_options[$option];
			}
		}

		return $this->options;
	}

	function get_converted_plugins_data_for_js($plugins_custom_data_source = array()) {
		$plugins_custom_data_ready = array();
		foreach ($plugins_custom_data_source as $path => $details) {
			$possible_data = array('Name', 'Description', 'Categories', 'CustomLink', 'ScreenShot', 'ScreenShotID');
			foreach ($possible_data as $possible_data_name)
				$details[$possible_data_name] = (isset($details[$possible_data_name]) && !empty($details[$possible_data_name])) ? $details[$possible_data_name] : null;

			$details['ScreenShotPreview'] = $this->get_screenshot_url($details['ScreenShot'], $path, 0);

			$plugins_custom_data_ready[$path] = array(
					'path' => $path,
					'name' => $details['Name'],
					'description' => stripslashes($details['Description']),
					'categories' => $details['Categories'],
					'custom_url' => $details['CustomLink'],
					'image_url' => $details['ScreenShot'],
					'image_url_preview' => $details['ScreenShotPreview'],
					'image_id' => $details['ScreenShotID']
				);
		}
		//set up screenshot preview for plugins without any image url
		foreach (apply_filters('all_plugins', get_plugins()) as $path => $value) {
			if(!isset($plugins_custom_data_ready[$path])) {
				$screenshot = $this->get_screenshot_url('', $path, 0);
				if(!empty($screenshot))
					$plugins_custom_data_ready[$path]['image_url_preview'] = $screenshot;
			}
		}

		return $plugins_custom_data_ready;
	}

	//Converts array to xml
	function get_array_as_xml($array, $node_name = 'item') {
		$xml = "\n";

		if (is_array($array) || is_object($array)) {
			foreach ($array as $key => $value) {
				if (is_numeric($key)) {
					$key = $node_name;
				}
				$xml .= '<'.$key.'>'.$this->get_array_as_xml($value).'</'.$key.'>'."\n";
			}
		} else {
			$xml = "\n".htmlspecialchars($array, ENT_QUOTES) . "\n";
		}

		return $xml;
	}

	//used to sort plugins by name
	function compare_by_name($a, $b) {
		return strtolower($a['Name']) > strtolower($b['Name']);
	}

	function the_select_options($array, $current) {
		if(empty($array))
			$array = array( 1 => 'True', 0 => 'False' );

		foreach( $array as $name => $label ) {
			$selected = selected( $current, $name, false );
			echo '<option value="'.$name.'" '.$selected.'>'.$label.'</option>';
		}
	}


	//Actions


	function import_xml_data_setting_file($file_path, $config = 0) {
	    $xml = simplexml_load_string(str_replace("\n", "", file_get_contents($file_path) ));
	    $xml_json = json_encode($xml);
	    $xml_import_data = json_decode($xml_json,TRUE);

		if(isset($xml_import_data['Categories'])) {
			$plugins_categories_to_import = array();

			//replace names for config categories
			if($config) {
				//rename categories so they have "config" at the beginning
				foreach ($xml_import_data['Categories'] as $key => $value) {
					$new_key = str_replace('category', 'configcategory', $key);

					$plugins_categories_replace[$key] = $new_key;
					$plugins_categories_to_import[$new_key] = $value;
				}
			}
			//looks for different keyes with same value and creates new key for them
			elseif(!empty($this->plugins_categories)) {
				$plugins_categories_replace = array();
				$last_category = 0;
				foreach ($xml_import_data['Categories'] as $key => $value) {
					$category_key = array_search($value, $this->plugins_categories);
					if(isset($category_key) && $category_key)
						$plugins_categories_replace[$key] = $category_key;
					elseif(isset($this->plugins_categories[$key]) && $this->plugins_categories[$key] != $value) {
						if(!$last_category) {
							end($this->plugins_categories);
							$last_category = key($this->plugins_categories);
							$last_category = substr($last_category, 8);
						}

						$last_category ++;
						$new_last_category = 'category'.$last_category;
						$plugins_categories_replace[$key] = $new_last_category;
						$plugins_categories_to_import[$new_last_category] = $value;
					}
					else
						$plugins_categories_to_import[$key] = $value;
				}
			}
			else
				$plugins_categories_to_import = $xml_import_data['Categories'];

			if($config) {
				$this->plugins_categories_config = $plugins_categories_to_import;
				update_site_option('wmd_prettyplugins_plugins_categories_config', $this->plugins_categories_config);
			}
			else {
				$this->plugins_categories = array_replace_recursive($this->plugins_categories, $plugins_categories_to_import);
				update_site_option('wmd_prettyplugins_plugins_categories', $this->plugins_categories);
			}
		}

		if(isset($xml_import_data['Plugins']['Plugin'])) {
			//fix for single plugin in xml
			if(!isset($xml_import_data['Plugins']['Plugin'][0]))
				$xml_import_data['Plugins']['Plugin'] = array(0 => $xml_import_data['Plugins']['Plugin']);

			$plugin_custom_data_to_import = array();
			foreach($xml_import_data['Plugins']['Plugin'] as $key => $value) {
				if(isset($value['Path'])) {
					$path = $value['Path'];
					unset($value['Path']);
					unset($value['ScreenShotID']);

					//fix for single plugin category in xml
					if(isset($value['Categories']['item']) && !is_array($value['Categories']['item']))
						$value['Categories']['item'] = array(0 => $value['Categories']['item']);

					//Merges old categories with new one
					if(isset($value['Categories']['item']) && isset($plugins_categories_replace) && $plugins_categories_replace) {
						//configure plugin categories
						$new_categories = array();
						foreach ($value['Categories']['item'] as $id => $category) {
							if(array_key_exists($category, $plugins_categories_replace))
								$new_categories[] = $plugins_categories_replace[$category];
							else
								$new_categories[] = $category;
						}

						if(!$config && isset($this->plugins_custom_data[$path]['Categories'])) {
							$value['Categories'] = array_merge_recursive($this->plugins_custom_data[$path]['Categories'], $new_categories);
							$value['Categories'] = array_unique($value['Categories']);
						}
						else
							$value['Categories'] = $new_categories;
					}
					elseif(isset($value['Categories']['item']))
						$value['Categories'] = $value['Categories']['item'];
					else
						unset($value['Categories']);

					if(!empty($value))
						$plugin_custom_data_to_import[$path] = $value;
				}
			}
			if(!empty($plugin_custom_data_to_import)) {
				if($config) {
					$this->plugins_custom_data_config = $plugin_custom_data_to_import;
					update_site_option('wmd_prettyplugins_plugins_custom_data_config', $this->plugins_custom_data_config);
				}
				else {
					$this->plugins_custom_data = array_replace_recursive($this->plugins_custom_data, $plugin_custom_data_to_import);
					ksort($this->plugins_custom_data);
					update_site_option('wmd_prettyplugins_plugins_custom_data', $this->plugins_custom_data);
				}
			}
		}

		if(isset($xml_import_data['Options'])) {
			$validated = $this->get_validated_options($xml_import_data['Options']);

			update_site_option( 'wmd_prettyplugins_options', $validated );
		}
	}

	function export_xml_data_setting_file() {
		if(wp_verify_nonce($_REQUEST['_wpnonce'], 'wmd_prettyplugins_options')) {
			//rename categories to remove "config" part and merges
			$plugins_categories_xml = array();
			$plugins_categories_config_ready = array();
			$plugins_categories_replace = array();
			$last_category = 0;
			foreach ($this->plugins_categories_config as $key => $value) {
				if(!empty($this->plugins_categories))
					$category_key = array_search($value, $this->plugins_categories);
				if(isset($category_key) && $category_key)
					$plugins_categories_replace[$key] = $category_key;
				else {
					if(!$last_category)
						$last_category = $this->get_last_category_id();
					$last_category ++;
					$new_last_category = 'category'.$last_category;
					$plugins_categories_replace[$key] = $new_last_category;
					$plugins_categories_config_ready[$new_last_category] = $value;
				}
			}
			$plugins_categories_xml = array_merge($plugins_categories_config_ready, $this->plugins_categories);

			$plugins_custom_data_xml = array();
			foreach ($this->get_merged_plugins_custom_data() as $path => $value) {
				//replace plugins categories keys to match new ones(without config in name)
				if(isset($value['Categories']) && $plugins_categories_replace) {
					$new_categories = array();
					foreach ($value['Categories'] as $id => $category) {
						if(array_key_exists($category, $plugins_categories_replace))
							$new_categories[] = $plugins_categories_replace[$category];
						else
							$new_categories[] = $category;
					}
					if(isset($this->plugins_custom_data[$path]['Categories'])) {
						$value['Categories'] = array_merge_recursive($this->plugins_custom_data[$path]['Categories'], $new_categories);
						$value['Categories'] = array_unique($value['Categories']);
					}
					else
						$value['Categories'] = $new_categories;
				}

				//move path from key to array
				$plugins_custom_data_xml[] = array_merge(array('Path' => $path), $value);
			}

			$filename = 'config.xml';

			header( 'Content-Description: File Transfer' );
			header( 'Content-Disposition: attachment; filename=' . $filename );
			header( 'Content-Type: text/plain; charset=' . get_option( 'blog_charset' ), true );

			$xml = '<?xml version="1.0" encoding="UTF-8" ?>'."\n";

			$xml .= '<Plugins-data-settings>'."\n";
				$xml .= '<Plugins>';
					$xml .= $this->get_array_as_xml($plugins_custom_data_xml, 'Plugin');
				$xml .= '</Plugins>'."\n";

				$xml .= '<Categories>';
					$xml .= $this->get_array_as_xml($plugins_categories_xml);
				$xml .= '</Categories>'."\n";

				$xml .= '<Options>';
					$xml .= $this->get_array_as_xml($this->options);
				$xml .= '</Options>'."\n";
			$xml .= '</Plugins-data-settings>';

			echo $xml;

			die();
		}
	}


	//Plugins integration


	function prosite_plugin_available($plugin_file) {
		$psts_plugins = $this->pro_site_settings['pp_plugins'];

		if(isset($psts_plugins[$plugin_file]['level']) && $psts_plugins[$plugin_file]['level'] != 0 && is_numeric($psts_plugins[$plugin_file]['level']) && !is_super_admin())
			if((function_exists('is_pro_site') && is_pro_site($this->blog_id, $psts_plugins[$plugin_file]['level'])) ||
				(function_exists('psts_show_ads') && !psts_show_ads($this->blog_id)))
				return true;
			else
				return false;
		else
			return true;
	}

	function prosite_plugin_required_level_name($plugin_file) {
		global $psts;
		$psts_plugins = $this->pro_site_settings['pp_plugins'];

		if(isset($psts_plugins[$plugin_file]['level']) && $psts_plugins[$plugin_file]['level'] != 0 && is_numeric($psts_plugins[$plugin_file]['level'])) {
			return $psts->get_level_setting($psts_plugins[$plugin_file]['level'], 'name');
		}
		else
			return true;
	}
}

//Compatibility with older PHP
if (!function_exists('array_replace_recursive')) {
	function array_replace_recursive() {
	    $arrays = func_get_args();

	    $original = array_shift($arrays);

	    foreach ($arrays as $array) {
	        foreach ($array as $key => $value) {
	            if (is_array($value)) {
	                $original[$key] = array_replace_recursive($original[$key], $array[$key]);
        }

	            else {
	                $original[$key] = $value;
      }
    }
	    }

	    return $original;
  }
}