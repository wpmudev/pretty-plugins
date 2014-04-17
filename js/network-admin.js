jQuery(document).ready(function() {
	plugins_details_array = prettyplugins_object_to_array(wmd_pl_na.plugin_details);
	plugins_categories_array = prettyplugins_object_to_array(wmd_pl_na.plugin_categories);

	table = jQuery('table.plugins'); table.show();
	table_plugin_edit = jQuery('table#inlineedit tr');
	plugin_edit_wpnonce = table_plugin_edit.find('#_wpnonce').val();

	//add column for image
	table.find('thead tr, tfoot tr').append( '<th scope="col" id="image" class="manage-column column-image">'+wmd_pl_na.image+'</th>' );

	plugins_table_array = [];

	//Prepare and add data for each plugin
	table.find('tbody > tr:not(.plugin-update-tr)').each(function() {
		plugin_table = jQuery(this);
		plugin_path = plugin_table.find('th.check-column input').val();
		if(plugin_path) {
			plugin_plugin_column = plugin_table.find('td.plugin-title');
			plugin_description_column = plugin_table.find('td.column-description');
			if(jQuery.inArray(plugin_path,wmd_pl_na.network_only_plugins) == -1) {
				plugin_name = plugin_plugin_column.find('strong').text();
				plugin_description = plugin_description_column.find('p').html();

				plugins_table_array[plugin_path] = {
				    table : plugin_table,
				    name : plugin_name,
				    description : plugin_description,
				    plugin_column : plugin_plugin_column,
				    description_column : plugin_description_column
				};
				plugin_table.append( '<td class="column-image desc"><img class="plugin-image" width="100" height="75" src="'+wmd_pl_na.theme_url+'images/default_screenshot.png" alt="'+name+'"/></td>' );

				prettyplugins_plugin_add_data(plugin_path);
			}
			else {
				plugin_plugin_column.find('div.row-actions-visible .edit a').text(wmd_pl_na.edit_code);
				plugin_table.append( '<td class="column-image desc"></td>' );
			}
		}
	});

	//get edit plugin details screen on click
	jQuery('a.edit_details').click(function(event) {
		event.preventDefault();

		plugin_path = jQuery(this).attr('href').substring(1);
		plugin_edit_row = table_plugin_edit.clone();
		plugins_table_array[plugin_path].edit_row = plugin_edit_row;
		plugin = plugins_table_array[plugin_path];

		plugin.table.hide();
		plugin.table.after(plugin_edit_row.show());
		if(!!plugins_details_array[plugin_path]) {
			if(plugins_details_array[plugin_path].name != null)
				plugin.edit_row.find('.plugin_name').val(plugins_details_array[plugin_path].name);
			if(plugins_details_array[plugin_path].description != null)
				plugin.edit_row.find('.plugin_description').val(plugins_details_array[plugin_path].description);
			if(plugins_details_array[plugin_path].custom_url != null)
				plugin.edit_row.find('.plugin_custom_url').val(plugins_details_array[plugin_path].custom_url);
			if(plugins_details_array[plugin_path].image_url != null)
				plugin.edit_row.find('.plugin_image_url').val(plugins_details_array[plugin_path].image_url);
			prettyplugins_handle_image_id(plugin.edit_row, plugins_details_array[plugin_path].image_id);

			if(plugins_details_array[plugin_path].categories != null) {
				jQuery.each(plugins_details_array[plugin_path].categories, function( index, value ) {
						plugin.edit_row.find('.category-'+value+' input').attr('checked', 'checked');
					});
			}
		}
		plugin.edit_row.find('.plugin_name_orginal').val(plugin.name);
		plugin.edit_row.find('.plugin_description_orginal').val(plugin.description);
		plugin.edit_row.find('.target').attr('href','#'+plugin_path);

		return false;
	});

	//show interface to add category on click
	table.on( 'click', 'a.add-category-show-form', function(event) {
		event.preventDefault();

		jQuery(this).parent().siblings( ".plugin-category-add-edit-holder" ).toggle().find('.plugin_new_category').focus();

		return false;
	});

	//makes adding category work
	table.on( 'click', 'a.add-category-button', function(event) {
		event.preventDefault();

		plugin_path = jQuery(this).attr('href').substring(1);
		plugin = plugins_table_array[plugin_path];

		plugin_new_category = plugin.edit_row.find('.plugin-new-edit-category').val();

		if(plugin_new_category != '') {
			plugin.edit_row.find('.spinner-add-category').show();

			var data = { //looks for and sets all variables used for export
				action: 'prettyplugins_add_category_ajax',
				_wpnonce: plugin_edit_wpnonce,
				plugin_new_category: plugin_new_category
			};

			jQuery.post(wmd_pl_na.ajax_url, data, function(data){ //post data to specified action trough special WP ajax page
				data = jQuery.parseJSON(data);
				if(data.error == 0)
					if(data.name == plugin_new_category) {
						var plugin_categories_checklist = plugin.edit_row.find('.plugin-categories-checklist');
					    plugin_categories_checklist.animate({"scrollTop": plugin_categories_checklist[0].scrollHeight}, "slow");

						jQuery('.plugin-categories-checklist').append('<li  class="category-'+data.id+'"><label class="selectit"><input value="'+data.id+'" type="checkbox" name="plugin_category[]"> <span class="category-name">'+data.name+'</span> <a href="#'+data.id+'" class="edit-category-show-form"> <small>('+wmd_pl_na.edit+')</small></a></label></li>');
						plugin_categories_checklist.find('.category-'+data.id+' input').attr("checked","checked");

						plugins_categories_array[data.id] = data.name;

						prettyplugins_hide_new_edit_form(plugin.edit_row);
					}
			});
		}

		return false;
	});

	//show interface for editing category
	table.on( 'click', 'a.edit-category-show-form', function(event) {
		event.preventDefault();

		category_id = jQuery(this).attr('href').substring(1);
		plugin_category_div = jQuery(this).parents('.inline-edit-col');
		category_name = jQuery(this).parent().find('.category-name').text();

		plugin_category_div.find('.add-category-show-form, .add-category-button').hide();

		plugin_category_div.find('.edit-category, .edit-category-save-button').show();
		plugin_category_div.find('.edit-category-name').text('"'+category_name+'"');
		plugin_category_div.find('.plugin-category-add-edit-holder').show().find('.plugin-new-edit-category').val(category_name).focus();

		plugin_category_div.find('.plugin-edit-category-key').val(category_id);

		return false;
	});

	//makes adding category work
	table.on( 'click', 'a.edit-category-save-button', function(event) {
		event.preventDefault();

		plugin_path = jQuery(this).attr('href').substring(1);
		plugin = plugins_table_array[plugin_path];

		plugin_edit_category = plugin.edit_row.find('.plugin-new-edit-category').val();
		plugin_edit_category_key = plugin.edit_row.find('.plugin-edit-category-key').val();

		if(plugin_edit_category != '') {
			plugin.edit_row.find('.spinner-add-category').show();

			var data = { //looks for and sets all variables used for export
				action: 'prettyplugins_save_category_ajax',
				_wpnonce: plugin_edit_wpnonce,
				plugin_edit_category: plugin_edit_category,
				plugin_edit_category_key: plugin_edit_category_key
			};

			jQuery.post(wmd_pl_na.ajax_url, data, function(data){ //post data to specified action trough special WP ajax page
				data = jQuery.parseJSON(data);
				if(data.error == 0)
					if(data.name == plugin_edit_category) {
						jQuery('.plugin-categories-checklist').find('.category-'+data.id+' .category-name').text(data.name);

						plugins_categories_array[data.id] = data.name;

						prettyplugins_hide_new_edit_form(plugin.edit_row);
					}
			});
		}

		return false;
	});

	//cancel plugin editing/adding
	table.on( 'click', 'a.category-cancel-button', function(event) {
		event.preventDefault();

		plugin_category_div = jQuery(this).parents('.inline-edit-col');

		prettyplugins_hide_new_edit_form(plugin_category_div);

		return false;
	});

	//get edit plugin details screen on click
	table.on( 'click', 'a.plugin-save', function(event) {
		event.preventDefault();

		plugin_path = jQuery(this).attr('href').substring(1);
		plugin = plugins_table_array[plugin_path];

		var plugin_categories_ready = new Array();
		jQuery.each(plugin.edit_row.find( 'input[name="plugin_category[]"]:checked' ), function() {
			plugin_categories_ready.push(jQuery(this).val());
		});

		plugin.edit_row.find('.spinner-save').show();

		var data = {
			action: 'prettyplugins_save_plugin_details_ajax',
			_wpnonce: plugin_edit_wpnonce,
			plugin_path: plugin_path,
			plugin_name: plugin.edit_row.find( 'input.plugin_name' ).val(),
			plugin_custom_url: plugin.edit_row.find( 'input.plugin_custom_url' ).val(),
			plugin_image_url: plugin.edit_row.find( 'input.plugin_image_url' ).val(),
			plugin_image_id: plugin.edit_row.find( 'input.plugin_image_id' ).val(),
			plugin_description: plugin.edit_row.find( 'textarea.plugin_description' ).val(),
			plugin_categories: plugin_categories_ready
		};

		jQuery.post(wmd_pl_na.ajax_url, data, function(data){
			data = jQuery.parseJSON(data);
			if(data.error == 0) {
				plugins_details_array[data.new_details.path] = data.new_details;

				jQuery.each(data.remove_categories, function(index, key) {
					jQuery('.plugin-categories-checklist li.category-'+key).remove();
				});
				prettyplugins_plugin_add_data(plugin_path);

				plugin.table.show();
				plugin.edit_row.remove();
			}
		});

		return false;
	});

	//cancel plugin editing
	table.on( 'click', 'a.plugin-cancel', function(event) {
		event.preventDefault();

		plugin_path = jQuery(this).attr('href').substring(1);
		plugin = plugins_table_array[plugin_path];

		if(plugin) {
			plugin.table.show();
			plugin.edit_row.remove();
		}

		return false;
	});

	//handle enter pressing while editing details
	table.on( 'keyup keypress', '#plugin-edit', function(event) {
		var code = event.keyCode || event.which;
		if (code  == 13) {
			event.preventDefault();
			if(jQuery('.plugin-new-edit-category:focus').size() == 1)
			    jQuery('.category-button:visible').click();
			else
				jQuery('.plugin-save').click();

			return false;
		}
	});

    var image_uploader;
    table.on( 'click', '.plugin_image_upload_button', function(event) {
        event.preventDefault();

		plugin_path = jQuery(this).attr('href').substring(1);
		plugin = plugins_table_array[plugin_path];

        if (image_uploader) {
            image_uploader.open();
            return;
        }

        image_uploader = wp.media.frames.file_frame = wp.media({
            title: wmd_pl_na.choose_screenshot,
            button: {
                text: wmd_pl_na.select_image
            },
            multiple: false
        });

        image_uploader.on('select', function() {
            attachment = image_uploader.state().get('selection').first().toJSON();
            plugin.edit_row.find('.plugin_image_url').val(attachment.url);
            prettyplugins_handle_image_id(plugin.edit_row, attachment.id);
        });

        image_uploader.open();
    });

	table.on( 'change', '.plugin_image_url', function(event) {
		prettyplugins_hide_image_id_edit_button(jQuery(this).parent())
	});

	//change colspan for plugin update message
	table.find('tbody > tr.plugin-update-tr').each(function() {
		jQuery(this).find('td').attr('colspan', '4');
	});

});

function prettyplugins_handle_image_id(target, image_id) {
	if(image_id != null) {
		target.find('.plugin_image_id').val(image_id);
		target.find('.plugin_image_edit_button').attr('style', 'display: inline-block;').attr('href', wmd_pl_na.admin_url+'/post.php?post='+image_id+'&action=edit&image-editor');
	}
	else
		prettyplugins_hide_image_id_edit_button(target)
}
function prettyplugins_hide_image_id_edit_button(target) {
	target.find('.plugin_image_id').val('');
	target.find('.plugin_image_edit_button').hide().attr('href', '#');
}
function prettyplugins_hide_new_edit_form(target) {
	target.find('.spinner-add-category').hide();

	target.find('.add-category-show-form, .add-category-button').show();
	target.find('.edit-category, .edit-category-save-button').hide();

	target.find('.plugin-category-add-edit-holder').hide().find('.plugin_new_category').val('');
}

function prettyplugins_plugin_add_data(plugin_path) {
	plugin_image_url = '';
	if(!!plugins_details_array[plugin_path]) {
		plugin = plugins_table_array[plugin_path];
		plugin_details = plugins_details_array[plugin_path];

		if(plugin_details.name != null) {
			var plugin_name_custom = plugin.plugin_column.find('.plugin-name-custom small');
			if(plugin_name_custom.length)
				plugin_name_custom.text(plugin_details.name);
			else
				plugin.plugin_column.find('strong').after('<strong class="plugin-name-custom"><small>'+plugin_details.name+'</small></strong>');
		}
		else
			plugin.plugin_column.find('.plugin-name-custom').remove();

		if(plugin_details.description != null) {
			var plugin_description_custom = plugin.description_column.find('.plugin-description-custom');
			if(plugin_description_custom.length)
				plugin_description_custom.html('<small>'+plugin_details.description+'</small>');
			else
				plugin.description_column.find('p').after('<p class="plugin-description-custom"><small>'+plugin_details.description+'</small></p>');
		}
		else
			plugin.description_column.find('.plugin-description-custom').remove();

		if(plugin_details.custom_url != null) {
			var plugin_custom_url = plugin.description_column.find('.plugin-help-url');
			if(plugin_custom_url.length)
				plugin_custom_url.attr('href', plugin_details.custom_url);
			else
				plugin.description_column.find('.plugin-version-author-uri:not(.plugin-categories-holder)').append( '<span class="plugin-help-holder"> | <a class="plugin-help-url" href="'+plugin_details.custom_url+'" title="'+wmd_pl_na.visit_help+'">'+wmd_pl_na.visit_help+'</a></span>' );
		}
		else
			plugin.description_column.find('.plugin-help-holder').remove();

		if(plugin_details.image_url_preview != null)
			plugin.table.find('.plugin-image').attr('src', plugin_details.image_url_preview);

		if(plugin_details.categories != null) {
			plugin_categories_names = [];
			jQuery.each(plugin_details.categories, function( index, value ) {
					plugin_categories_names.push(plugins_categories_array[value]);
				});
			var plugin_categories = plugin.description_column.find('.plugin-categories-list');
			if(plugin_categories.length)
				plugin_categories.text(plugin_categories_names.join(', '));
			else
				plugin.description_column.find('.plugin-version-author-uri').before( '<div class="update second plugin-version-author-uri plugin-categories-holder">'+wmd_pl_na.categories+': <span class="plugin-categories-list">'+plugin_categories_names.join(', ')+'</span></div>' );
		}
		else
			plugin.description_column.find('.plugin-categories-holder').remove();
	}
}


function prettyplugins_object_to_array(object) {
	array = [];
	jQuery.each(object, function( index, value ) {
		array[index] = value;
	});

	return array;
}