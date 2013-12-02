<div class="wrap">

	<?php screen_icon('tools'); ?>
	<h2><?php _e('Pretty Plugins Settings', 'wmd_prettyplugins') ?></h2>
	<p><?php printf(__('This page lets you control Pretty Plugins. You can configure custom plugin data for each plugin in <a href="%s">"Plugins" network page</a> by clicking "Edit details". Click <a href="%s">here</a> to see how it currently looks like on the main site.', 'wmd_prettyplugins'), admin_url('network/plugins.php'), admin_url('admin.php?page=pretty-plugins.php')) ?></p>
	<form action="settings.php?page=pretty-plugins.php" method="post" enctype="multipart/form-data">

		<?php
		settings_fields('wmd_prettyplugins_options');
		?>

		<h3><?php _e('General Settings', 'wmd_prettyplugins') ?></h3>

		<table class="form-table">
			<tr valign="top">
				<th scope="row">
					<label for="wmd_prettyplugins_options[setup_mode]"><?php _e('Enable Setup Mode', 'wmd_prettyplugins') ?></label>
				</th>

				<td>
					<select name="wmd_prettyplugins_options[setup_mode]">
						<?php $this->the_select_options(array(), $this->options['setup_mode']); ?>
					</select>
					<p class="description"<?php echo ($this->options['setup_mode'] == 1) ? ' style="color:red;"' : ''; ?>><?php _e('When set to "true", the Pretty Plugins plugin page will be visible only on the main site. This mode is useful for configuring plugin details before enabling the Pretty Plugins features on all sites in the network.', 'wmd_prettyplugins') ?></p>
				</td>
			</tr>

			<tr valign="top">
				<th scope="row">
					<label for="wmd_prettyplugins_options[theme]"><?php _e('Select Theme For Plugin Page', 'wmd_prettyplugins') ?></label>
				</th>

				<td>
					<?php
					$select_options = $this->get_themes();
					?>
					<select name="wmd_prettyplugins_options[theme]">
						<?php $this->the_select_options($select_options, $this->options['theme']); ?>
					</select>
					<p class="description"><?php _e('Choose the theme that you want to use to display your plugin page. You can add your own themes into "wp-content/upload/prettyplugins/your-theme-/" folder. (Tip: duplicate the default theme from "wp-content/plugins/prettyplugins/themes/" to get started.)', 'wmd_prettyplugins') ?></p>
				</td>
			</tr>

		</table>

		<h3><?php _e('Theme and Appearance Settings', 'wmd_prettyplugins') ?></h3>

		<table class="form-table">

			<tr valign="top">
				<th scope="row">
					<label for="wmd_prettyplugins_options[plugins_links]"><?php _e('Select Where Plugin Link Points To', 'wmd_prettyplugins') ?></label>
				</th>

				<td>
					<?php
					$select_options = array( 'plugin_url' => 'Plugin orginal URL', 'plugin_cutom_url' => 'Plugin custom URL', 'plugin_url_or_cutom_url' => 'Plugin custom URL or if cutom does not exists, orginal URL', 'disable' => 'Disable' );
					?>
					<select name="wmd_prettyplugins_options[plugins_links]">
						<?php $this->the_select_options($select_options, $this->options['plugins_links']); ?>
					</select>
				</td>
			</tr>

			<tr valign="top">
				<th scope="row">
					<label for="wmd_prettyplugins_options[plugins_auto_screenshots]"><?php _e('Auto Load First Screenshot', 'wmd_prettyplugins') ?></label>
				</th>

				<td>
					<select name="wmd_prettyplugins_options[plugins_auto_screenshots]">
						<?php $this->the_select_options(array(), $this->options['plugins_auto_screenshots']); ?>
					</select>
					<p class="description"><?php _e('If a featured image for a plugin has not been set, the first available screenshot in the plugin folder will be loaded (example: screenshot-1.png).', 'wmd_prettyplugins') ?></p>
				</td>
			</tr>

			<tr valign="top">
				<th scope="row">
					<label for="wmd_prettyplugins_options[plugins_auto_screenshots_by_name]"><?php _e('Auto Load Screenshot With Correct Name', 'wmd_prettyplugins') ?></label>
				</th>

				<td>
					<select name="wmd_prettyplugins_options[plugins_auto_screenshots_by_name]">
						<?php $this->the_select_options(array(), $this->options['plugins_auto_screenshots_by_name']); ?>
					</select>
					<p class="description"><?php _e('If the featured image for a plugin has not been set and there is an image located in "wp-content/upload/prettyplugins/screenshots/" with the correct name (example: plugin location - "wp-content/plugins/akismet/akismet.php", image file - "akismet-akismet.png".), the it will autoload. Only PNG files will work in this method', 'wmd_prettyplugins') ?></p>
				</td>
			</tr>

			<tr valign="top">
				<th scope="row">
					<label for="wmd_prettyplugins_options[plugins_links]"><?php _e('Minimize Descriptions', 'wmd_prettyplugins') ?></label>
				</th>

				<td>
					<select name="wmd_prettyplugins_options[plugins_hide_descriptions]">
						<?php $this->the_select_options(array(), $this->options['plugins_hide_descriptions']); ?>
					</select>
					<p class="description"><?php _e('If "True", the plugin "description" will be hidden by default and it will appear after clicking "descriptions" link on the plugins page.', 'wmd_prettyplugins') ?></p>
				</td>
			</tr>
		</table>

		<h3><?php _e('Labels', 'wmd_prettyplugins') ?></h3>

		<table class="form-table">
			<tr valign="top">
				<th scope="row">
					<label for="wmd_prettyplugins_options[plugins_link_label]"><?php _e('Plugin Page Title', 'wmd_prettyplugins') ?></label>
				</th>

				<td>
					<input type="text" class="regular-text" name="wmd_prettyplugins_options[plugins_page_title]" value="<?php echo stripslashes(esc_attr($this->options['plugins_page_title'])); ?>"/>
					<p class="description"><?php _e('This is what you call the "Plugins" menu item. Call it "Plugins", "Apps", "Add-ons" or whatever you\'d like.', 'wmd_prettyplugins') ?></p>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">
					<label for="wmd_prettyplugins_options[plugins_link_label]"><?php _e('Plugin Page Description', 'wmd_prettyplugins') ?></label>
				</th>

				<td>
					<input type="text" class="regular-text" style="width:95%;" name="wmd_prettyplugins_options[plugins_page_description]" value="<?php echo stripslashes(esc_attr($this->options['plugins_page_description'])); ?>"/>
					<p class="description"><?php _e('This text will be visible at the top of the plugins page. Tell your users what you have to offer.', 'wmd_prettyplugins') ?></p>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">
					<label for="wmd_prettyplugins_options[plugins_link_label]"><?php _e('Custom Link Label', 'wmd_prettyplugins') ?></label>
				</th>

				<td>
					<input type="text" class="regular-text" name="wmd_prettyplugins_options[plugins_link_label]" value="<?php echo stripslashes(esc_attr($this->options['plugins_link_label'])); ?>"/>
					<p class="description"><?php _e('Add a link to any URL for each plugin. For example, a link to support documents for the plugin.', 'wmd_prettyplugins') ?></p>
				</td>
			</tr>
		</table>

		<p class="submit">
			<input type="submit" name="save_settings" class="button-primary" value="<?php _e('Save Changes', 'wmd_prettyplugins') ?>" />
		</p>

		<h3><?php _e('Export and Import', 'wmd_prettyplugins') ?></h3>

		<table class="form-table">
			<tr valign="top">
				<th scope="row">
					<label><?php _e('Export', 'wmd_prettyplugins') ?></label>
				</th>

				<td>
					<a href="<?php echo add_query_arg(array('prettyplugins_action' => 'export', '_wpnonce' => wp_create_nonce('wmd_prettyplugins_options'))); ?>" class="button"><?php _e('Download Export File', 'wmd_prettyplugins') ?></a>
					<p class="description">
						<?php _e('Export data and settings for later import or use as a configuration file. You can put exported file named "config.xml" into "wp-content/upload/prettyplugins/" folder to autoload data and settings.', 'wmd_prettyplugins') ?> <small><?php _e('Keep in mind that data from current config file (if exists) will also be exported.', 'wmd_prettyplugins') ?></small>
					</p>
				</td>
			</tr>

			<tr valign="top">
				<th scope="row">
					<label for="wmd_prettyplugins_options[plugins_link_label]"><?php _e('Import:', 'wmd_prettyplugins') ?></label>
				</th>

				<td>
					<input type="file" name="config_file" id="upload" size="25">
					<input type="submit" name="import_config" class="button" value="<?php _e('Upload file and import', 'wmd_prettyplugins'); ?>"/>
					<p class="description"><?php _e('Choose an export file (correctly formatted XML file) to import data and settings. This action will replace any existing data and settings.', 'wmd_prettyplugins') ?></p>
				</td>
			</tr>

			<tr valign="top">
				<th scope="row">
					<label><?php _e('Reset:', 'wmd_prettyplugins') ?></label>
				</th>

				<td>
					<a onclick="return confirm('<?php _e('Are you sure?', 'wmd_prettyplugins'); ?>')" href="<?php echo add_query_arg(array('prettyplugins_action' => 'delete_custom_data', '_wpnonce' => wp_create_nonce('wmd_prettyplugins_options'))); ?>" class="button"><?php _e('Delete all custom plugin data', 'wmd_prettyplugins') ?></a>
					<p class="description">
						<?php _e('This action will permanently delete all existing custom plugin data.', 'wmd_prettyplugins') ?></br>
					</p>
				</td>
			</tr>
		</table>
	</form>

</div>