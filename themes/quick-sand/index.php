<div class="wrap">
	<?php screen_icon('plugins'); ?>
	<h2><?php echo stripslashes($this->options['plugins_page_title']); ?></h2>
	<p><?php echo stripslashes($this->options['plugins_page_description']); ?></p>

	<div id="current-theme" class="plugins-categories">
		<p class="search-box">
			<span><?php _e('Search:', 'wmd_prettyplugins'); ?> </span>
			<input type="search" id="theme-search-input" class="plugin-search-input" name="s" placeholder="<?php _e('Start typing to search...', 'wmd_prettyplugins'); ?>" value="">
		</p>
		<div class="theme-options plugin-options">
			<div class="type categories">
				<span><?php _e('Choose category to display:', 'wmd_prettyplugins'); ?></span>
				<ul id="plugin-categories-list">
					<li><a href="#" class="all"><?php _e('All', 'wmd_prettyplugins'); ?></a></li>
					<?php
					foreach($plugins_categories as $plugins_category_id => $plugins_category)
						echo '<li><a href="#" class="'.$plugins_category_id.'">'.$plugins_category.'</a></li>';
					?>
				</ul>
			</div>
			<div class="type sort">
				<span><?php _e('Sort by:', 'wmd_prettyplugins'); ?></span>
				<ul id="plugin-status-list">
					<li><a href="#" class="all"><?php _e('All', 'wmd_prettyplugins'); ?></a></li>
					<li><a href="#" class="active"><?php _e('Active', 'wmd_prettyplugins'); ?></a></li>
					<li><a href="#" class="inactive"><?php _e('Inactive', 'wmd_prettyplugins'); ?></a></li>
				</ul>
			</div>
		</div>
	</div>
	<div id="availableplugins">
		<?php
		foreach($plugins as $plugin_path => $plugin) {
		?>

		<div data-id="id-<?php echo $plugin['ListID']; ?>" data-type="<?php echo (isset($plugin['Categories'])) ? implode(' ', $plugin['Categories']) : 'all'; echo ($plugin['isActive'] == 1) ? ' active' : ' inactive'; ?>" class="available-theme available-plugin<?php echo ($plugin['isActive'] == 1) ? ' active-plugin' : ' inactive-plugin'; ?>">
			<div class="available-plugin-inner">
				<a href="<?php echo $plugin['ActionLink']; ?>" class="screenshot">
					<img src="<?php echo $plugin['ScreenShot']; ?>" alt="<?php echo $plugin['Name']; ?>">
					<div class="toggle activate">
						<h4>
							<?php echo $plugin['ActionLinkText']; ?>
						</h4>
					</div>
					<?php echo $plugin['Ribbon']; ?>
				</a>

				<h3><?php echo $plugin['Name']; ?></h3>
				<div class="action-links">
					<ul>
						<li>
							<a href="<?php echo $plugin['ActionLink']; ?>" class="<?php echo $plugin['ActionLinkClass']; ?> activate-deactivate" title="<?php echo $plugin['ActionLinkText'];?>"><?php echo $plugin['ActionLinkText'];?></a>
						</li>

						<?php if(isset($plugin['PluginLink'])) { ?>
							<li>
								<a href="<?php echo $plugin['PluginLink']; ?>" target="_blank" title="<?php _e('Learn more about the plugin', 'wmd_prettyplugins') ?>"><?php echo stripslashes($this->options['plugins_link_label']); ?></a>
							</li>
						<?php } ?>

						<?php if($this->options['plugins_hide_descriptions']) { ?>
							<li>
								<a href="#" class="theme-detail plugin-details"><?php _e('Details', 'wmd_prettyplugins') ?></a>
							</li>
						<?php } ?>

						<?php
						foreach ($plugin['Actions'] as $action)
							echo '<li>'.$action.'</li>';
						?>
					</ul>
				</div>

				<div class="themedetaildiv"<?php if($this->options['plugins_hide_descriptions']) echo 'style="display:none;"'; ?>>
					<p><?php echo $plugin['Description']; ?></p>
					<?php if(isset($plugin['CategoriesNames'])) { ?>
						<p><strong><?php _e('Categories', 'wmd_prettyplugins') ?>: </strong> <?php echo implode(', ', $plugin['CategoriesNames']); ?></p>
					<?php } ?>
				</div>
			</div>
		</div>
		<?php
		}
		?>
	</div>
	<div class="no-plugins-found" style="display:none;"><p><?php _e('No plugins found.', 'wmd_prettyplugins'); ?></p></div>
</div>