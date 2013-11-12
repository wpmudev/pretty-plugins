<table id="inlineedit" style="display: none">
<tbody>
	<tr style="display:none" id="plugin-edit" class="inline-edit-row inline-edit-row-post inline-edit-post quick-edit-row quick-edit-row-post inline-edit-post alternate inline-editor">
		<td colspan="4" class="colspanchange">

			<fieldset class="inline-edit-col-left">
			<div class="inline-edit-col">
				<h4><?php _e('Edit Plugin Details', 'wmd_prettyplugins'); ?></h4>

				<label>
					<span class="title" id="name_tooltip"><?php _e('Name', 'wmd_prettyplugins');?></span>
					<span class="input-text-wrap">
					<input type="text" name="plugin_name" class="plugin_name" value="">
					</span>
				</label>

				<label class="setting-disabled">
					<span class="title"><?php _e('Orginal Name', 'wmd_prettyplugins');?></span>
					<span class="input-text-wrap">
					<input type="text" name="plugin_name_orginal" class="plugin_name_orginal" value="" disabled>
					</span>
				</label>

				<label>
					<span class="title" id="custom_url_tooltip"><?php _e('Custom URL', 'wmd_prettyplugins');?></span>
					<span class="input-text-wrap"><input type="text" class="plugin_custom_url" name="plugin_custom_url" value=""></span>
				</label>

				<label>
					<span class="title" id="image_url_tooltip"><?php _e('Image URL', 'wmd_prettyplugins');?></span>
					<span class="input-text-wrap">
						<input type="text" class="plugin_image_url" name="plugin_image_url" value="">
						<input type="hidden" class="plugin_image_id" name="plugin_image_id" value="">
						<a class="plugin_image_upload_button button target" href="#"><?php _e('Choose Image', 'wmd_prettyplugins');?></a>
						<a class="plugin_image_edit_button button" href="#" target="_blank"><?php _e('Edit Image', 'wmd_prettyplugins');?></a>
					</span>
				</label>
			</div>
			</fieldset>

			<fieldset class="inline-edit-col-center inline-edit-categories">
			<div class="inline-edit-col">
				<span class="title inline-edit-categories-label" id="categories_tooltip"><?php _e('Categories', 'wmd_prettyplugins');?></span>
				<ul class="plugin-categories-checklist cat-checklist category-checklist">
					<?php

					foreach ($this->plugins_categories as $key => $value) {
					?>
						<li class="category-<?php echo $key; ?>">
							<label class="selectit">
								<input value="<?php echo $key; ?>" type="checkbox" name="plugin_category[]">
								<span class="category-name"><?php echo $value; ?></span>
									<a href="#<?php echo $key; ?>" class="edit-category-show-form"> <small>(<?php _e('edit', 'wmd_prettyplugins');?>)</small></a>
							</label>
						</li>
					<?php
					}

					foreach ($this->plugins_categories_config as $key => $value) {
					?>
						<li class="category-<?php echo $key; ?>">
							<label class="selectit">
								<input value="<?php echo $key; ?>" type="checkbox" name="plugin_category[]" disabled>
								<span class="category-name"><?php echo $value; ?></span>
							</label>
						</li>
					<?php
						}
					?>

				</ul>

				<span class="title inline-edit-categories-label">
					<a class="add-category-show-form" href="#"><?php _e('New category', 'wmd_prettyplugins');?></a>
					<span class="edit-category" style="display:none;"><?php _e('Edit category', 'wmd_prettyplugins');?> <span class="edit-category-name"></span></span>
				</span>
				<div class="plugin-category-add-edit-holder" style="display:none;">
					<input type="hidden" name="plugin_edit_category_key" class="plugin-edit-category-key" value="0">
					<p><input type="text" name="plugin_new_edit_category" class="plugin-new-edit-category" value=""></p>

					<a href="#" title="<?php _e('Add category', 'wmd_prettyplugins');?>" class="button-secondary category-button add-category-button alignright target"><?php _e('Add', 'wmd_prettyplugins');?></a>
					<a href="#" title="<?php _e('Edit category', 'wmd_prettyplugins');?>" class="button-secondary category-button edit-category-save-button alignright target" style="display:none;"><?php _e('Save', 'wmd_prettyplugins');?> </a>
					<a href="#" title="<?php _e('Cancel', 'wmd_prettyplugins');?>" class="button-secondary category-cancel-button alignright"><?php _e('Cancel', 'wmd_prettyplugins');?></a>

					<span class="spinner spinner-add-category"></span>
				</div>
			</div>
			</fieldset>


			<fieldset class="inline-edit-col-right">
			<div class="inline-edit-col">
				<label class="inline-edit-tags">
					<span class="title" id="description_tooltip"><?php _e('Description', 'wmd_prettyplugins');?></span>
					<textarea cols="22" rows="1" name="plugin_description" class="plugin_description" autocomplete="off"></textarea>
				</label>
				<label class="inline-edit-tags setting-disabled">
					<span class="title"><?php _e('Orginal Description', 'wmd_prettyplugins');?></span>
					<textarea cols="22" rows="1" name="plugin_description" class="plugin_description_orginal" autocomplete="off" disabled></textarea>
				</label>
			</div>
			</fieldset>

			<p class="submit inline-edit-save">
				<?php wp_nonce_field( 'wmd_prettyplugins_edit_plugin_details', '_wpnonce' ) ?>
				<a accesskey="c" href="#" title="Cancel" class="button-secondary plugin-cancel alignleft target"><?php _e('Cancel', 'wmd_prettyplugins');?></a>
				<a accesskey="s" href="#" title="Update" class="button-primary plugin-save alignright target"><?php _e('Update', 'wmd_prettyplugins');?></a>
				<span class="spinner spinner-save"></span>
				<span class="error" style="display:none"></span>
				<br class="clear">
			</p>
		</td>
	</tr>
</tbody>
</table>