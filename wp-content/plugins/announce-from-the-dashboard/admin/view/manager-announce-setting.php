<?php

global $locale;

$settings_data = $this->get_data();

$announce_types = $Afd->Helper->get_announce_types();
$date_periods = $Afd->Helper->get_date_periods();
$multisite_show_standard = $Afd->Helper->get_multisite_show_standard();
$all_user_roles = $Afd->Helper->get_all_user_roles();

?>
<div class="wrap <?php echo $Afd->ltd; ?>">

	<h2>
		<?php echo $this->page_title; ?>
		<a href="javascript:void(0);" class="add-new-h2" id="add-new-announce"><?php echo esc_html_x( 'Add New' , 'post' ); ?></a>
	</h2>
	
	<div class="metabox-holder columns-1">
	
		<div id="postbox-container" class="postbox-container">
		
			<div id="add-announce-form">
			
				<form id="<?php echo $Afd->ltd; ?>_add_<?php echo $this->name; ?>" class="<?php echo $Afd->ltd; ?>_form" method="post" action="<?php echo esc_url( $Afd->Helper->get_action_link() ); ?>">
				
					<input type="hidden" name="<?php echo $Afd->Form->field; ?>" value="Y">
					<?php wp_nonce_field( $Afd->Form->nonce . 'add_' . $this->name , $Afd->Form->nonce . 'add_' . $this->name ); ?>
					
					<?php $announce = $this->MainModel->get_default_data(); ?>
					<?php $announce_id = false; ?>
					
					<div class="announce-edit">

						<table class="form-table">
							<tbody>
								<tr>
									<th>
										<?php _e( 'Announce title' , $Afd->ltd ); ?>
									</th>
									<td>
										<?php $this->print_form_fields( 'title' ); ?>
									</td>
								</tr>
								<tr>
									<th>
										<?php _e( 'Announce content' , $Afd->ltd ); ?>
									</th>
									<td>
										<?php $this->print_form_fields( 'content' ); ?>
									</td>
								</tr>
								<tr>
									<th>
										* <?php _e( 'Announce type' , $Afd->ltd ); ?>
									</th>
									<td>
										<?php $this->print_form_fields( 'type' ); ?>
									</td>
								</tr>
								<tr>
									<th>
										<?php _e( 'Date Range' , $Afd->ltd ); ?>
									</th>
									<td>
										<?php $this->print_form_fields( 'date_range' ); ?>
									</td>
								</tr>
								<tr>
									<th>
										<?php _e( 'User Roles' ); ?>
									</th>
									<td>
										<?php $this->print_form_fields( 'user_role' ); ?>
									</td>
								</tr>
						
								<?php if( $Afd->Site->is_multisite ): ?>
						
									<tr>
										<th>
											* <?php _e( 'Default show for announce of Child-sites' , $Afd->ltd ); ?>
										</th>
										<td>
											<?php $this->print_form_fields( 'show_standard' ); ?>
										</td>
									</tr>
									<tr>
										<th>
											<?php _e( 'Select the sub-sites' , $Afd->ltd ); ?>
										</th>
										<td>
											<?php $this->print_form_fields( 'subsites' ); ?>
										</td>
									</tr>
						
								<?php endif; ?>
						
							</tbody>
						</table>
						
					</div>
					
					<p>
						<span class="spinner"></span>
						<input type="button" class="button button-primary" id="do-add-announce" value="<?php _e( 'Save' ); ?>" />
					</p>
				
				</form>
			
			</div>

			<p><?php _e( 'It is will show in order from the top.' , $Afd->ltd ); ?></p>

			<div class="tablenav top">
				<select class="<?php echo $Afd->ltd; ?>-action">
					<option value=""><?php _e( 'Bulk Actions' ); ?></option>
					<option value="delete"><?php _e( 'Delete' ); ?></option>
				</select>
				<input type="button" class="button-secondary action bulk" value="<?php _e( 'Apply' ); ?>" />
			</div>

			<table class="wp-list-table widefat fixed" id="announces">

				<?php $arr = array( 'thead' , 'tfoot' ); ?>

				<?php foreach( $arr as $tag ) : ?>
				
					<<?php echo $tag; ?>>
					
						<tr>
							<th class="manage-column column-cb check-column">
								<input type="checkbox" />
							</th>
							<th class="manage-column column-title">
								<?php _e( 'Announce title' , $Afd->ltd ); ?> / 
								<?php _e( 'Announce type' , $Afd->ltd ); ?>
							</th>
							<th class="manage-column column-content">
								<?php if( $Afd->Site->is_multisite ): ?>
									<?php _e( 'Child-sites' , $Afd->ltd ); ?> /
								<?php endif; ?>
								<?php _e( 'Announce content' , $Afd->ltd ); ?>
							</th>
							<th class="manage-column column-role">
								<?php _e( 'User Roles' ); ?>
							</th>
							<th class="manage-column column-operation">
								&nbsp;
							</th>
						</tr>
					
					</<?php echo $tag; ?>>
					
				<?php endforeach; ?>

				<tbody>
					
					<?php if( empty( $settings_data ) ) : ?>
						
						<td colspan="5">
							
							<p><strong><?php _e( 'Not created announce.' , $Afd->ltd ); ?></strong></p>
							
						</td>
						
					<?php else : ?>

						<?php foreach( $settings_data as $announce_id => $announce ) : ?>
						
							<?php include( $this->elements_dir . 'announce-list.php' ); ?>
	
						<?php endforeach; ?>
							
					<?php endif; ?>

				</tbody>

			</table>

			<div class="tablenav top">
				<select class="<?php echo $Afd->ltd; ?>-action">
					<option value=""><?php _e( 'Bulk Actions' ); ?></option>
					<option value="delete"><?php _e( 'Delete' ); ?></option>
				</select>
				<input type="button" class="button-secondary action bulk" value="<?php _e( 'Apply' ); ?>" />
			</div>

		</div>
		
		<div class="clear"></div>
	
	</div>

</div>

<div id="remove-announces">

	<form id="<?php echo $Afd->ltd; ?>_remove_<?php echo $this->name; ?>" class="<?php echo $Afd->ltd; ?>_form" method="post" action="<?php echo esc_url( $Afd->Helper->get_action_link() ); ?>">
	
		<input type="hidden" name="<?php echo $Afd->Form->field; ?>" value="Y">
		<?php wp_nonce_field( $Afd->Form->nonce . 'remove_' . $this->name , $Afd->Form->nonce . 'remove_' . $this->name ); ?>
		
		<div class="add-fields-inner"></div>
	
	</form>

</div>

<?php $Afd->Helper->includes( 'admin/view/elements/information.php' ); ?>

<style>
#add-announce-form {
    display: none;
    margin-bottom: 50px;
}
#add-announce-form .spinner {
    float: left;
}

table#announces {}
table#announces thead th, table#announces tfoot th {
	font-size: 12px;
}
table#announces th.check-column {
	width: 2.8em;
}
table#announces th.check-column .spinner {
    margin-top: 20px;
}
table#announces th.column-title {
	width: 20%;
}
table#announces th.column-role {
	width: 14%;
}
table#announces th.column-operation {
	width: 12%;
}
table#announces tbody tr.list:hover {
	outline: 1px solid #999;
}
table#announces tbody th.check-column {
	background: #eee url(<?php echo $this->assets_url; ?>images/icon-sortable.png) no-repeat center 32px;
}
table#announces tbody th.check-column:hover {
	cursor: move;
}
table#announces tbody tr.list.type-normal td.column-content {
	border-left: 4px solid #A6A6A6;
}
table#announces tbody tr.list.type-error td.column-content {
	border-left: 4px solid #CC0000;
}
table#announces tbody tr.list.type-updated td.column-content {
	border-left: 4px solid #7ad03a;
}
table#announces tbody tr.list.type-metabox td.column-content {
	background-color: #f5f5f5;
}
table#announces tbody tr.sorting .column-title,
table#announces tbody tr.sorting .column-content,
table#announces tbody tr.sorting .column-role,
table#announces tbody tr.sorting .column-operation {
    opacity: 0.6;
}
table#announces tbody tr.sorted {
	background: rgba(255, 255, 200, 0.6);
}
table#announces tbody tr.widget-placeholder {
	outline: 1px dashed #bbb;
	height: 100px;
	margin: 0 auto 20px auto;
	width: 100%;
	box-sizing: border-box;
	background: rgba(255, 255, 200, 0.4);
}
table#announces tbody tr.ui-sortable-helper {
	background: rgba(255, 255, 200, 0.8);
}
table#announces tbody tr td.column-operation ul {
	margin: 0;
    padding: 0;
}
table#announces tbody tr td.column-operation ul li {
	display: block;
    margin-bottom: 10px;
}
table#announces tbody tr.list .inline,
table#announces tbody tr.inline-edit .show {
    display: none;
}
table#announces tbody tr.inline-edit {
    background-color: #f9f9f9;
}

.announce-edit {}
.announce-edit select.multiple-select {
	height: 220px;
}

.announce-edit .show-subsite-descriptions.not .all,
.announce-edit .show-subsite-descriptions.all .not {
    display: none;
}
.announce-edit .show-subsite-descriptions .all,
table#announces tbody tr.list td.column-content .show-subsite-description.all {
	background: rgba( 0 , 116 , 162 , 0.1);
}
.announce-edit .show-subsite-descriptions .not,
table#announces tbody tr.list td.column-content .show-subsite-description.not {
	background: rgba( 204 , 0 , 0 , 0.1);
}
.announce-edit .date_range .date-range-setting {
    display: none;
    margin-bottom: 20px;
}
.announce-edit .date_range.specify .date-range-setting {
    display: block;
}
.announce-edit .date_range_error {
	border-left: 3px solid #f00;
	background: #fff;
	color: #c00;
	padding: 5px 5px 5px 12px;
	display: none;
}
#remove-announce {
    display: none;
}
</style>
<script>
jQuery(document).ready( function($) {

	$('#add-new-announce').on('click', function( ev ) {
		
		$('#add-announce-form').show();
		
	});

	$('.action.bulk').on('click', function( ev ) {

		var bulk_action = $(this).parent().find('.<?php echo $Afd->ltd; ?>-action').val();
		
		if( bulk_action != 'delete' ) {
			
			return false;
			
		}
		
		var delete_flag = false;
	
		var $remove_form = $('#<?php echo $Afd->ltd; ?>_remove_<?php echo $this->name; ?>');
		var $field_inner = $remove_form.find('.add-fields-inner');
		
		$field_inner.html('');

		$(document).find('#announces tbody tr').each(function( tr_index , tr_el ) {
				
			var $tr_el = $(tr_el);
				
			if( $tr_el.find('.announce-id').prop('checked') ) {
					
				add_delete_id_field( $tr_el.find('.announce-id').val() );
				delete_flag = true;
					
			}
				
		});
		
		if( !delete_flag ) {
			
			return false;
			
		}
			
		if( window.confirm( '<?php _e( 'Are you sure you wish to delete this Announce?' , $Afd->ltd ); ?>' ) ) {
	
			$remove_form.submit();
					
		} else {
					
			return false;
					
		}
	
	});
	
	$(document).on('click', 'table#announces .announce-delete', function( ev ) {

		if( window.confirm( '<?php _e( 'Are you sure you wish to delete this Announce?' , $Afd->ltd ); ?>' ) ) {
			
			$(this).parent().parent().parent().parent().find( '.spinner' ).css({ visibility: 'visible' , display: 'inline-block' });

			var $remove_form = $('#<?php echo $Afd->ltd; ?>_remove_<?php echo $this->name; ?>');
			var $field_inner = $remove_form.find('.add-fields-inner');
			
			$field_inner.html('');

			var delete_id = $(this).parent().parent().parent().parent().parent().find('.announce-id').val();
			add_delete_id_field( delete_id );
			
			$remove_form.submit();
			
		} else {
			
			return false;
			
		}

	});
	
	function add_delete_id_field( delete_id ) {
		
		var $remove_form = $('#<?php echo $Afd->ltd; ?>_remove_<?php echo $this->name; ?>');
		var $field_inner = $remove_form.find('.add-fields-inner');

		$field_inner.append( '<input type="hidden" name="data[delete][]" value="' + delete_id + '" />' );
		
	}
	
	$(document).on('click', '.announce-edit-inline', function( ev ) {
		
		var $tr_el = $(this).parent().parent().parent().parent().parent();
		
		$tr_el.removeClass('list').addClass('inline-edit announce-edit');

	});

	$(document).on('click', '.cancel-edit-announce', function( ev ) {
		
		var $tr_el = $(this).parent().parent().parent().parent().parent();
		
		$tr_el.removeClass('inline-edit announce-edit').addClass('list');
		
	});

	$(document).find('table#announces tbody').sortable({
		
		placeholder: 'widget-placeholder',
		handle: '.check-column',
		cursor: 'move',
		distance: 2,

		stop: function( e , ui ) {

			ui.item.find( 'th.check-column .spinner' ).css({ visibility: 'visible' , display: 'inline-block' });
			ui.item.addClass( 'sorting' );
			
			var sort_lists = [];

			ui.item.parent().children('tr').each(function( tr_index , tr_el ) {

				var $tr_el = $(tr_el);
				sort_lists.push( $(tr_el).find('.announce-id').val() );

			});

			var PostData = {
				action: '<?php echo $Afd->ltd; ?>_announce_update_sort',
				'<?php echo $Afd->Form->nonce . 'announce_update_sort'; ?>': '<?php echo wp_create_nonce( $Afd->Form->nonce . 'announce_update_sort' ); ?>',
				sort_lists: sort_lists
			}
			
			$.ajax({
				type: 'post',
				url: ajaxurl,
				data: PostData,
			}).done(function( xhr ) {
				
				ui.item.find( 'th.check-column .spinner' ).css({ visibility: 'hidden' , display: 'none' });
				ui.item.removeClass( 'sorting' );

				if( xhr.success ) {

					ui.item.addClass( 'sorted' );
					
				} else {
					
					errors = xhr.data.errors;
					
					if( errors.not_type ) {
						
						alert( errors.not_type.msg );

					}
	
				}
	
			});

		},

	});

	$(document).on('click', '.change-date-range', function( ev ) {
		
		var checked = $(this).prop('checked');
		var $date_range = $(this).parent().parent().parent();
		
		if( checked ) {
			
			$date_range.addClass( 'specify' );
			
		} else {
			
			$date_range.removeClass( 'specify' );

		}
		
	});

	$(document).on('click', '.change-ymd', function( ev ) {
		
		var $date_range = $(this).parent().parent();
		
		$date_range.find('.date-ymd-field').datepicker({
			
			dateFormat: 'yy-mm-dd',
			//regional: '<?php echo $locale; ?>',
			onSelect: function( date , object ) {
				
				$date_range.find('.date-range-ymd').text( date );
				
				date_field_add( $date_range );
				
			}

		}).datepicker( 'show' );
		
	});

	$(document).on('change', '.change-h, .change-i', function( ev ) {
		
		var $date_range = $(this).parent().parent();
		
		date_field_add( $date_range );
		
	});

	function date_field_add( $date_range ) {
		
		var ymd = $date_range.find('.date-ymd-field').val();

		var h = $date_range.find('.date-h-field').val();

		var i = $date_range.find('.date-i-field').val();

		var ss = ':00'

		var his = h + ':' + i  + ss;
		
		var date = ymd + ' ' + his;
		
		$date_range.find('.date-range-field').val( date );
		
	}

	$(document).on('change', '.change-show-standard', function( ev ) {
		
		var standard = $(this).val();
		
		$(this).parent().parent().parent().find('.show-subsite-descriptions').removeClass('not').removeClass('all').addClass( standard );
		
	});

	$(document).on('click', '#do-add-announce', function( ev ) {
		
		var $form = $(this).parent().parent();
		
		$form.find('.spinner').css({ visibility: 'visible' , display: 'inline-block' });
		
		var data = { 'type': '' , range: { start: 0 , end: 0 } , date: { start: '' , end: '' } };
		
		data.type = $form.find('.type-field').val();
		
		if( $form.find('.date_range.start .change-date-range').prop('checked') ) {
			
			data.range.start = 1;
			data.date.start = $form.find('.date_range.start .date-range-field').val();

		}

		if( $form.find('.date_range.end .change-date-range').prop('checked') ) {
			
			data.range.end = 1;
			data.date.end = $form.find('.date_range.end .date-range-field').val();

		}
		
		var PostData = {
			action: '<?php echo $Afd->ltd; ?>_announce_validate_data',
			'<?php echo $Afd->Form->nonce . 'announce_validate_data'; ?>': '<?php echo wp_create_nonce( $Afd->Form->nonce . 'announce_validate_data' ); ?>',
			data: data
		}
		
		$.ajax({
			type: 'post',
			url: ajaxurl,
			data: PostData,
			beforeSend: function(){
				$form.find('.date_range_error').hide();
			}
		}).done(function( xhr ) {
			
			$form.find('.spinner').css({ visibility: 'hidden' , display: 'none' });

			if( xhr.success ) {

				$form.submit();
					
			} else {
					
				errors = xhr.data.errors;
				
				if( errors.not_type ) {
						
					alert( errors.not_type.msg );

				} else if( errors.not_date ) {
						
					alert( errors.not_date.msg );

				} else if( errors.time_is_compare ) {
					
					$form.find('.date_range_error').show();
					alert( errors.time_is_compare.msg );

				}
	
			}
	
		});
		
	});
	
	$(document).on('click', '.do-edit-announce', function( ev ) {
		
		$(this).parent().parent().parent().parent().find( '.spinner' ).css({ visibility: 'visible' , display: 'inline-block' });

		var $tr_el = $(this).parent().parent().parent().parent().parent();
		var announce_id = $tr_el.find('.announce-id').val();
		var $update_form = $tr_el.find('.update-announce form');
		var $field_inner = $update_form.find('.add-fields-inner');
		
		$field_inner.html('');
		
		var name_field = '';
		var value = '';

		var $title_field = $tr_el.find('.column-title .inline .title-field');
		$field_inner.append( '<input type="hidden" name="' + $title_field.prop('name') + '" value="' + $title_field.val() + '" />' );

		var $type_field = $tr_el.find('.column-title .inline .type-field');
		$field_inner.append( '<input type="hidden" name="' + $type_field.prop('name') + '" value="' + $type_field.val() + '" />' );

		var $range_start_field = $tr_el.find('.column-title .inline .date_range.start .range-field');

		if( $range_start_field.prop('checked') ) {

			$field_inner.append( '<input type="hidden" name="' + $range_start_field.prop('name') + '" value="' + $range_start_field.val() + '" />' );

			var $date_start_field = $tr_el.find('.column-title .inline .date_range.start .date-range-field');
			$field_inner.append( '<input type="hidden" name="' + $date_start_field.prop('name') + '" value="' + $date_start_field.val() + '" />' );

		}

		var $range_end_field = $tr_el.find('.column-title .inline .date_range.end .range-field');

		if( $range_end_field.prop('checked') ) {

			$field_inner.append( '<input type="hidden" name="' + $range_end_field.prop('name') + '" value="' + $range_end_field.val() + '" />' );

			var $date_end_field = $tr_el.find('.column-title .inline .date_range.end .date-range-field');
			$field_inner.append( '<input type="hidden" name="' + $date_end_field.prop('name') + '" value="' + $date_end_field.val() + '" />' );

		}
		
		var $user_role_field = $tr_el.find('.column-role .inline .user-role-field');
		var user_roles = $user_role_field.val();
		
		if( user_roles ) {

			$.each(user_roles, function( i , role ) {
				
				$field_inner.append( '<input type="hidden" name="' + $user_role_field.prop('name') + '" value="' + role + '" />' );

			});
			
		}

		$update_form.submit();
		
	});
	
});
</script>
