<?php
/**
 * WCFM plugin views
 *
 * Plugin Custom Field Products Manage Views
 *
 * @author 		WC Lovers
 * @package 	wcfm/views
 * @version   2.3.7
 */
global $wp, $WCFM;

$product_id = 0;

if( isset( $wp->query_vars['wcfm-products-manage'] ) && !empty( $wp->query_vars['wcfm-products-manage'] ) ) {
	$product_id = $wp->query_vars['wcfm-products-manage'];
}

/*if( class_exists( 'Types_Post_Type' ) ) {
	 $Types_Post_Type = new Types_Post_Type( 'product' );
	 $field_groups = $Types_Post_Type->get_field_groups();
	 print_r($field_groups);
	 die;
}*/

?>

<!-- Start Product Custom Fields -->
<?php
$wcfm_product_custom_fields = (array) get_option( 'wcfm_product_custom_fields' );
$wpcf_icons = array( 'snowflake-o', 'ravelry', 'eercast', 'bullseye', 'cloud', 'certificate', 'crosshairs');
if( $wcfm_product_custom_fields && is_array( $wcfm_product_custom_fields ) && !empty( $wcfm_product_custom_fields ) ) {
	foreach( $wcfm_product_custom_fields as $wpcf_index => $wcfm_product_custom_field ) {
		if( !isset( $wcfm_product_custom_field['enable'] ) ) continue;
		?>
		<div class="page_collapsible products_manage_<?php echo sanitize_title( $wcfm_product_custom_field['block_name'] ); ?> simple variable external grouped booking" id="wcfm_products_manage_form_<?php echo sanitize_title( $wcfm_product_custom_field['block_name'] ); ?>_head"><label class="fa fa-<?php echo ($wpcf_icons[$wpcf_index]) ? $wpcf_icons[$wpcf_index] : 'snowflake-o'; ?>"></label><?php echo $wcfm_product_custom_field['block_name']; ?><span></span></div>
		<div class="wcfm-container simple variable external grouped booking">
			<div id="wcfm_products_manage_form_<?php echo sanitize_title( $wcfm_product_custom_field['block_name'] ); ?>_expander" class="wcfm-content">
				<?php
				$is_group = !empty( $wcfm_product_custom_field['group_name'] ) ? 'yes' : 'no';
				$is_group = !empty( $wcfm_product_custom_field['is_group'] ) ? 'yes' : 'no';
				$group_name = $wcfm_product_custom_field['group_name'];
				$group_value = array();
				if( $product_id ) {
					$group_value = (array) get_post_meta( $product_id, $group_name, true );		
				}
				$wcfm_product_custom_block_fields = $wcfm_product_custom_field['wcfm_product_custom_block_fields'];
				if( !empty( $wcfm_product_custom_block_fields ) ) {
					foreach( $wcfm_product_custom_block_fields as $wcfm_product_custom_block_field ) {
						$field_value = '';
						$field_name = $wcfm_product_custom_block_field['name'];
						if( $is_group == 'yes' ) {
							$field_name = $group_name . '[' . $wcfm_product_custom_block_field['name'] . ']';
							if( $product_id ) {
								if( $wcfm_product_custom_block_field['type'] == 'checkbox' ) {
									$field_value = isset( $group_value[$wcfm_product_custom_block_field['name']] ) ? 'yes' : 'no';
								} else {
									if( isset( $group_value[$wcfm_product_custom_block_field['name']] )) {
										$field_value = $group_value[$wcfm_product_custom_block_field['name']];
									}
								}
							}
						} else {
							if( $product_id ) {
								if( $wcfm_product_custom_block_field['type'] == 'checkbox' ) {
									$field_value = get_post_meta( $product_id, $field_name, true ) ? get_post_meta( $product_id, $field_name, true ) : 'no';
								} else {
									$field_value = get_post_meta( $product_id, $field_name, true );
								}
							}
						}
						switch( $wcfm_product_custom_block_field['type'] ) {
							case 'text':
								$WCFM->wcfm_fields->wcfm_generate_form_field(  array( $wcfm_product_custom_block_field['name'] => array( 'label' => $wcfm_product_custom_block_field['label'] , 'name' => $field_name, 'type' => 'text', 'class' => 'wcfm-text wcfm_ele simple variable external grouped booking', 'label_class' => 'wcfm_title', 'value' => $field_value ) ) );
							break;
							
							case 'number':
								$WCFM->wcfm_fields->wcfm_generate_form_field(  array( $wcfm_product_custom_block_field['name'] => array( 'label' => $wcfm_product_custom_block_field['label'] , 'name' => $field_name, 'type' => 'number', 'class' => 'wcfm-text wcfm_ele simple variable external grouped booking', 'label_class' => 'wcfm_title', 'value' => $field_value ) ) );
							break;
							
							case 'textarea':
								$WCFM->wcfm_fields->wcfm_generate_form_field(  array( $wcfm_product_custom_block_field['name'] => array( 'label' => $wcfm_product_custom_block_field['label'] , 'name' => $field_name, 'type' => 'textarea', 'class' => 'wcfm-textarea wcfm_ele simple variable external grouped booking', 'label_class' => 'wcfm_title', 'value' => $field_value ) ) );
							break;
							
							case 'datepicker':
								$WCFM->wcfm_fields->wcfm_generate_form_field(  array( $wcfm_product_custom_block_field['name'] => array( 'label' => $wcfm_product_custom_block_field['label'] , 'name' => $field_name, 'type' => 'text', 'placeholder' => 'YYYY-MM-DD', 'class' => 'wcfm-text wcfm_ele wcfm_datepicker simple variable external grouped booking', 'label_class' => 'wcfm_title', 'value' => $field_value ) ) );
							break;
							
							case 'timepicker':
								$WCFM->wcfm_fields->wcfm_generate_form_field(  array( $wcfm_product_custom_block_field['name'] => array( 'label' => $wcfm_product_custom_block_field['label'] , 'name' => $field_name, 'type' => 'time', 'class' => 'wcfm-text wcfm_ele simple variable external grouped booking', 'label_class' => 'wcfm_title', 'value' => $field_value ) ) );
							break;
							
							case 'checkbox':
								$WCFM->wcfm_fields->wcfm_generate_form_field(  array( $wcfm_product_custom_block_field['name'] => array( 'label' => $wcfm_product_custom_block_field['label'] , 'name' => $field_name, 'type' => 'checkbox', 'class' => 'wcfm-checkbox wcfm_ele simple variable external grouped booking', 'label_class' => 'wcfm_title checkbox-title', 'value' => 'yes', 'dfvalue' => $field_value ) ) );
							break;
							
							case 'select':
								$select_opt_vals = array();
								$select_options = explode( '|', $wcfm_product_custom_block_field['options'] );
								if( !empty ( $select_options ) ) {
									foreach( $select_options as $select_option ) {
										if( $select_option ) {
											$select_opt_vals[$select_option] = ucfirst( str_replace( "-", " " , $select_option ) );
										}
									}
								}
								$WCFM->wcfm_fields->wcfm_generate_form_field(  array( $wcfm_product_custom_block_field['name'] => array( 'label' => $wcfm_product_custom_block_field['label'] , 'name' => $field_name, 'type' => 'select', 'class' => 'wcfm-select wcfm_ele simple variable external grouped booking', 'label_class' => 'wcfm_title', 'options' => $select_opt_vals, 'value' => $field_value ) ) );
							break;
						}
					}
				}
				?>
			</div>
		</div>
		<?php
	}
}
?>
<!-- End Product Custom Fields -->