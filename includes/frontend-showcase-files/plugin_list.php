<div<?php if ( $atts['category'] ) echo ' data-category="'.$atts['category'].'"';?> class="wmd-plugins-showcase plugin-browser<?php if ( $atts['hide_interface'] ) echo ' hide-interface'; ?>">
	<div class="plugins">

		<?php
		foreach ( $this->plugin->plugins_data as $plugin ) :
			$aria_action = esc_attr( $plugin['id'] . '-action' );
			$aria_name   = esc_attr( $plugin['id'] . '-name' );
		?>

		<div class="plugin-card">
			<div class="plugin-content plugin-<?php echo $plugin['ActionLinkClass'];?>">
				<div class="plugin-card-top">
					<div class="plugin-icon"><img src="data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs=" data-src="<?php echo $plugin['ScreenShot'];?>"></div>
					
					<div class="name column-name">
						<h4 class="plugin-name" id="<?php echo $plugin['id'];?>-name"><?php echo $plugin['Name'];?></h4>
					</div>
					<div class="desc column-description">
						<p><?php echo $plugin['Description'];?></p>
					</div>
					<div class="action-links">
						<ul class="plugin-action-buttons">
							<?php if ( isset($plugin['PluginLink']) && $plugin['PluginLink'] ) { ?>
								<li>
									<a<?php echo apply_filters('prettyplugins_action_link_attrs', ''); ?> href="<?php echo $plugin['PluginLink'];?>" target="_blank" title="<?php _e('Learn more about the plugin', 'wmd_prettyplugins') ?>"><?php echo stripslashes($this->plugin->options['plugins_link_label']); ?></a>
								</li>
							<?php } ?>
						</ul>				
					</div>
				</div>
				<?php /* if ( isset($plugin['CategoriesNames']) && $plugin['CategoriesNames'] ) { ?>
					<div class="plugin-card-bottom">
						<strong><?php _e('Categories: ', 'wmd_prettyplugins') ?></strong> <?php echo implode(', ', $plugin['CategoriesNames']);?>
					</div>
				<?php } */?>
			</div>
		</div>

		<?php endforeach; ?>

	</div>

	<br class="clear">
</div>

<script id="tmpl-category" type="text/template">
	<a data-sort="{{ data.Name }}" class="plugin-section plugin-category" href="#">{{ data[0] }}</a>
</script>

<script id="tmpl-plugin" type="text/template">
	<div class="plugin-content plugin-{{ data.ActionLinkClass }}">
		<div class="plugin-card-top">
			<div class="plugin-icon"><img src="{{ data.ScreenShot }}"></div>
			
			<div class="name column-name">
				<h4 class="plugin-name" id="{{ data.id }}-name">{{{ data.Name }}}</h4>
			</div>
			<div class="desc column-description">
				<p class="p-desc">{{{ data.Description }}}</p>
			</div>
			<div class="action-links">
				<ul class="plugin-action-buttons">
					<# if ( data.PluginLink ) { #>
						<li>
							<a<?php echo apply_filters('prettyplugins_action_link_attrs', ''); ?> href="{{ data.PluginLink }}" target="_blank" title="<?php _e('Learn more about the plugin', 'wmd_prettyplugins') ?>"><?php echo stripslashes($this->plugin->options['plugins_link_label']); ?></a>
						</li>
					<# } #>
				</ul>				
			</div>
		</div>
		<?php /*
		<# if ( data.CategoriesNames ) { #>
			<div class="plugin-card-bottom">
				<strong><?php _e('Categories: ', 'wmd_prettyplugins') ?></strong> {{ data.CategoriesNames.join(', ') }}
			</div>
		<# } #> */ ?>
	</div>
</script>