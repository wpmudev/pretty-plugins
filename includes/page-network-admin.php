<div class="wrap">

	<?php screen_icon('tools'); ?>
	<h2><?php _e('Pretty Plugins', 'wmd_prettyplugins') ?></h2>
	<p><?php printf(__('This page lets you control Pretty Plugins. You can configure custom plugin data for each plugin in <a href="%s">"Plugins" network page</a> by clicking "Edit details". PrettyPlugins will improve plugins page look for all sites in your network. <a href="%s">Here</a> you can see how it looks like for main site.', 'wmd_prettyplugins'), admin_url('network/plugins.php'), admin_url('admin.php?page=pretty-plugins.php')) ?></p>
	<form action="settings.php?page=pretty-plugins.php" method="post" enctype="multipart/form-data">

		<?php
		settings_fields('wmd_prettyplugins_options');
		?>

		<h3><?php _e('General Settings', 'wmd_prettyplugins') ?></h3>
		<p>
			<?php _e('Manage Pretty Plugins Options.', 'wmd_prettyplugins') ?>
		</p>

		<table class="form-table">
			<tr valign="top">
				<th scope="row">
					<label for="wmd_prettyplugins_options[setup_mode]"><?php _e('Enable Setup Mode:', 'wmd_prettyplugins') ?></label>
				</th>

				<td>
					<select name="wmd_prettyplugins_options[setup_mode]">
						<?php $this->the_select_options(array(), $this->options['setup_mode']); ?>
					</select>
					<p class="description"<?php echo ($this->options['setup_mode'] == 1) ? ' style="color:red;"' : ''; ?>><?php _e('When enabled, new plugin page will be visible only on main site. This mode is useful to configure plugin details before showing new plugin page for all network sites admin panel.', 'wmd_prettyplugins') ?></p>
				</td>
			</tr>

			<tr valign="top">
				<th scope="row">
					<label for="wmd_prettyplugins_options[theme]"><?php _e('Select theme for plugin page:', 'wmd_prettyplugins') ?></label>
				</th>

				<td>
					<?php
					$select_options = $this->get_themes();
					?>
					<select name="wmd_prettyplugins_options[theme]">
						<?php $this->the_select_options($select_options, $this->options['theme']); ?>
					</select>
					<p class="description"><?php _e('Choose theme that you want to use to display your plugin page. You can add your own themes into "wp-content/upload/prettyplugins/your-theme-/" folder (Tip: duplicate one of ours from "wp-content/plugins/prettyplugins/themes/" to get you started).', 'wmd_prettyplugins') ?></p>
				</td>
			</tr>

		</table>

		<h3><?php _e('Theme Setting', 'wmd_prettyplugins') ?></h3>
		<p><?php _e('Adjust appearance and behavior for plugins page.', 'wmd_prettyplugins') ?></p>

		<table class="form-table">

			<tr valign="top">
				<th scope="row">
					<label for="wmd_prettyplugins_options[plugins_links]"><?php _e('Select where plugin link points to:', 'wmd_prettyplugins') ?></label>
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
					<label for="wmd_prettyplugins_options[plugins_auto_screenshots]"><?php _e('Auto load first screenshot:', 'wmd_prettyplugins') ?></label>
				</th>

				<td>
					<select name="wmd_prettyplugins_options[plugins_auto_screenshots]">
						<?php $this->the_select_options(array(), $this->options['plugins_auto_screenshots']); ?>
					</select>
					<p class="description"><?php _e('If image representing plugin has not been set, first available screenshot in plugin folder will be loaded(screenshot-1.png).', 'wmd_prettyplugins') ?></p>
				</td>
			</tr>

			<tr valign="top">
				<th scope="row">
					<label for="wmd_prettyplugins_options[plugins_auto_screenshots_by_name]"><?php _e('Auto load screenshot with correct name:', 'wmd_prettyplugins') ?></label>
				</th>

				<td>
					<select name="wmd_prettyplugins_options[plugins_auto_screenshots_by_name]">
						<?php $this->the_select_options(array(), $this->options['plugins_auto_screenshots_by_name']); ?>
					</select>
					<p class="description"><?php _e('If image representing plugin has not been set and image located in "wp-content/upload/prettyplugins/screenshots/" with correct name (example: plugin location - "wp-content/plugins/akismet/akismet.php", image file - "akismet-akismet.png".) exists, it will autoload. Only PNG files will work in this method', 'wmd_prettyplugins') ?></p>
				</td>
			</tr>

			<tr valign="top">
				<th scope="row">
					<label for="wmd_prettyplugins_options[plugins_links]"><?php _e('Hide descriptions', 'wmd_prettyplugins') ?></label>
				</th>

				<td>
					<select name="wmd_prettyplugins_options[plugins_hide_descriptions]">
						<?php $this->the_select_options(array(), $this->options['plugins_hide_descriptions']); ?>
					</select>
					<p class="description"><?php _e('Plugin description will be hidden by default and it will appear after clicking special button.', 'wmd_prettyplugins') ?></p>
				</td>
			</tr>
		</table>

		<h3><?php _e('Labels', 'wmd_prettyplugins') ?></h3>
		<p><?php _e('Manage Labels For Plugin Page.', 'wmd_prettyplugins') ?></p>

		<table class="form-table">
			<tr valign="top">
				<th scope="row">
					<label for="wmd_prettyplugins_options[plugins_link_label]"><?php _e('Plugin Page Title:', 'wmd_prettyplugins') ?></label>
				</th>

				<td>
					<input type="text" class="regular-text" name="wmd_prettyplugins_options[plugins_page_title]" value="<?php echo esc_attr($this->options['plugins_page_title']); ?>"/>
					<p class="description"><?php _e('This text will be visible as menu item and header for plugin page.', 'wmd_prettyplugins') ?></p>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">
					<label for="wmd_prettyplugins_options[plugins_link_label]"><?php _e('Plugin Page Description:', 'wmd_prettyplugins') ?></label>
				</th>

				<td>
					<input type="text" class="regular-text" style="width:95%;" name="wmd_prettyplugins_options[plugins_page_description]" value="<?php echo esc_attr($this->options['plugins_page_description']); ?>"/>
					<p class="description"><?php _e('This text will be visible as description for plugins page.', 'wmd_prettyplugins') ?></p>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row">
					<label for="wmd_prettyplugins_options[plugins_link_label]"><?php _e('Custom Link Label:', 'wmd_prettyplugins') ?></label>
				</th>

				<td>
					<input type="text" class="regular-text" name="wmd_prettyplugins_options[plugins_link_label]" value="<?php echo esc_attr($this->options['plugins_link_label']); ?>"/>
					<p class="description"><?php _e('This text will link to plugin link for each of them.', 'wmd_prettyplugins') ?></p>
				</td>
			</tr>
		</table>

		<p class="submit">
			<input type="submit" name="save_settings" class="button-primary" value="<?php _e('Save Changes', 'wmd_prettyplugins') ?>" />
		</p>

		<h3><?php _e('Tools', 'wmd_prettyplugins') ?></h3>
		<p><?php _e('Manage plugin data and settings.', 'wmd_prettyplugins') ?></p>

		<table class="form-table">
			<tr valign="top">
				<th scope="row">
					<label><?php _e('Export:', 'wmd_prettyplugins') ?></label>
				</th>

				<td>
					<a href="<?php echo add_query_arg(array('prettyplugins_action' => 'export', '_wpnonce' => wp_create_nonce('wmd_prettyplugins_options'))); ?>" class="button"><?php _e('Download Export File', 'wmd_prettyplugins') ?></a>
					<p class="description">
						<?php _e('Export data and settings to import or use as config file. You can put exported file named "config.xml" into "wp-content/upload/prettyplugins/" folder for data and settings to autoload.', 'wmd_prettyplugins') ?> <small><?php _e('Keep in mind that data from current config file (if exists) will also be exported.', 'wmd_prettyplugins') ?></small>
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
					<p class="description"><?php _e('Choose proper XML file to import data and settings. This action will replace current data and settings if they already exists or add new ones.', 'wmd_prettyplugins') ?></p>
				</td>
			</tr>

			<tr valign="top">
				<th scope="row">
					<label><?php _e('Reset:', 'wmd_prettyplugins') ?></label>
				</th>

				<td>
					<a onclick="return confirm('<?php _e('Are you sure?', 'wmd_prettyplugins'); ?>')" href="<?php echo add_query_arg(array('prettyplugins_action' => 'delete_custom_data', '_wpnonce' => wp_create_nonce('wmd_prettyplugins_options'))); ?>" class="button"><?php _e('Delete all custom plugin data', 'wmd_prettyplugins') ?></a>
					<p class="description">
						<?php _e('This action will delete all custom plugin data forever.', 'wmd_prettyplugins') ?></br>
					</p>
				</td>
			</tr>
		</table>
	</form>

</div>