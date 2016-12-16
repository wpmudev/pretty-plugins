
<div class="wrap">
	<h2><?php echo stripslashes( $this->options['plugins_page_title'] ); ?>
		<div class="filter-count">
			<span class="count plugin-count"><?php echo count( $plugins ); ?></span>
		</div>
	</h2>
	<?php if($this->options['plugins_page_description']) { ?>
	<p class="page-description">
		<?php echo stripslashes($this->options['plugins_page_description']); ?>
	</p>
	<?php } ?>

<div class="wp-list-table widefat plugin-install-network plugin-browser">

<?php
foreach ( $plugins as $plugin ) :
	$aria_action = esc_attr( $plugin['id'] . '-action' );
	$aria_name   = esc_attr( $plugin['id'] . '-name' );
	?>

	<div class="plugin-card">
		<div class="plugin-<?php echo $plugin['ActionLinkClass'];?>">
			<div class="plugin-card-top">
				<div class="plugin-icon"><img src="data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs=" data-src="<?php echo $plugin['ScreenShot'];?>"></div>
				
				<div class="name column-name">
					<h4 class="plugin-name" id="<?php echo $plugin['id'];?>-name"><?php echo $plugin['Name'];?></h4>
				</div>
				<div class="action-links">
					<ul class="plugin-action-buttons">
						<li><a class="button<?php if ( !$plugin['isActive'] ) { ?> button-primary<?php } ?>" href="<?php echo $plugin['ActionLink'];?>"><?php echo $plugin['ActionLinkText'];?></a></li>
						<?php if ( isset($plugin['PluginLink']) && $plugin['PluginLink'] ) { ?>
							<li>
								<a href="<?php echo $plugin['PluginLink'];?>" target="_blank" title="<?php _e('Learn more about the plugin', 'wmd_prettyplugins') ?>"><?php echo stripslashes($this->options['plugins_link_label']); ?></a>
							</li>
						<?php } ?>
						<?php if ( isset($plugin['Actions']) && is_array($plugin['Actions']) && count($plugin['Actions']) ) { ?>
							<li><?php echo implode('</li><li>', $plugin['Actions']);?>}</li>
						<?php } ?>
					</ul>				
				</div>
				<div class="desc column-description">
					<p><?php echo $plugin['Description'];?></p>
				</div>
			</div>
			<?php if ( isset($plugin['CategoriesNames']) && $plugin['CategoriesNames'] ) { ?>
				<div class="plugin-card-bottom">
					<strong><?php _e('Categories: ', 'wmd_prettyplugins') ?></strong> <?php echo implode(', ', $plugin['CategoriesNames']);?>
				</div>
			<?php } ?>
		</div>
	</div>

<?php endforeach; ?>

</div>

</div><!-- .wrap -->

<script id="tmpl-category" type="text/template">
	<a data-sort="{{ data.Name }}" class="plugin-section plugin-category" href="#">{{ data[0] }}</a>
</script>

<script id="tmpl-plugin" type="text/template">
	<div class="plugin-content plugin-{{ data.ActionLinkClass }}<# if ( data.ShowMore ) { #> plugin-show-more<# } #>">
		<div class="plugin-card-top">
			<div class="plugin-icon"><img src="{{ data.ScreenShot }}"></div>
			
			<div class="name column-name">
				<h4 class="plugin-name" id="{{ data.id }}-name">{{{ data.Name }}}</h4>
			</div>
			<div class="action-links">
				<ul class="plugin-action-buttons">
					<li><a class="button<# if ( !data.isActive ) { #> button-primary<# } #>" href="{{{ data.ActionLink }}}">{{ data.ActionLinkText }}</a></li>
					<# if ( data.PluginLink ) { #>
						<li>
							<a href="{{ data.PluginLink }}" target="_blank" title="<?php _e('Learn more about the plugin', 'wmd_prettyplugins') ?>"><?php echo stripslashes($this->options['plugins_link_label']); ?></a>
						</li>
					<# } #>
					<# if ( data.ActionsValues ) { #>
						<li>{{{ data.ActionsValues.join('</li><li>') }}}</li>
					<# } #>
				</ul>				
			</div>
			<div class="desc column-description">
				<p class="p-desc">{{{ data.Description }}}</p>
				<a class="show-more-button" href="#"><?php _e('Show more', 'wmd_prettyplugins') ?></a>
			</div>
		</div>
		<?php /*
		<# if ( data.CategoriesNames ) { #>
			<div class="plugin-card-bottom">
				<strong><?php _e('Categories: ', 'wmd_prettyplugins') ?></strong> {{ data.CategoriesNames.join(', ') }}
			</div>
		<# } #> */?>
	</div>
</script>

<script id="tmpl-plugin-single" type="text/template">

</script>

