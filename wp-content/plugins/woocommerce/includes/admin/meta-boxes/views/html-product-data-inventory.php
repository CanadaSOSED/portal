<div id="inventory_product_data" class="panel woocommerce_options_panel hidden">

	<div class="options_group">
		<?php

			/* --- COMMENTED OUT SKU OPTION - Ryan Woo --- */
			/* if ( wc_product_sku_enabled() ) {
				woocommerce_wp_text_input( array(
					'id'          => '_sku',
					'value'       => $product_object->get_sku( 'edit' ),
					'label'       => '<abbr title="' . __( 'Stock Keeping Unit', 'woocommerce' ) . '">' . __( 'SKU', 'woocommerce' ) . '</abbr>',
					'desc_tip'    => true,
					'description' => __( 'SKU refers to a Stock-keeping unit, a unique identifier for each distinct product and service that can be purchased.', 'woocommerce' ),
				) );
			}

			do_action( 'woocommerce_product_options_sku' ); */

			if ( 'yes' === get_option( 'woocommerce_manage_stock' ) ) {

				woocommerce_wp_checkbox( array(
					'id'            => '_manage_stock',
					'value'         => $product_object->get_manage_stock( 'edit' ) ? 'yes' : 'no',
					'wrapper_class' => 'show_if_simple show_if_variable',
					'label'         => __( 'Maximum Capacity?', 'woocommerce' ),
					'description'   => __( 'Restrict registration to a maximum number - use for room capacity.', 'woocommerce' ),
				) );

				do_action( 'woocommerce_product_options_stock' );

				echo '<div class="stock_fields show_if_simple show_if_variable">';

				woocommerce_wp_text_input( array(
					'id'                => '_stock',
					'value'             => $product_object->get_stock_quantity( 'edit' ),
					'label'             => __( 'Maximum Seats', 'woocommerce' ),
					'desc_tip'          => true,
					'description'       => __( 'Maximum seats. For Take-Home Package and DEAs do not use unless you want to restrict how many purchases you want to allow.', 'woocommerce' ),
					'type'              => 'number',
					'custom_attributes' => array(
						'step'          => 'any',
					),
					'data_type'         => 'stock',
				) );

				/* --- COMMENTED OUT THE BACKORDERS OPTION --- Ryan Woo */
				/* woocommerce_wp_select( array(
					'id'          => '_backorders',
					'value'       => $product_object->get_backorders( 'edit' ),
					'label'       => __( 'Allow backorders?', 'woocommerce' ),
					'options'     => wc_get_product_backorder_options(),
					'desc_tip'    => true,
					'description' => __( 'If managing stock, this controls whether or not backorders are allowed. If enabled, stock quantity can go below 0.', 'woocommerce' ),
				) ); */

				do_action( 'woocommerce_product_options_stock_fields' );

				echo '</div>';
			}


			woocommerce_wp_select( array(
				'id'             => '_stock_status',
				'value'          => $product_object->get_stock_status( 'edit' ),
				'wrapper_class'  => 'hide_if_variable hide_if_external',
				'label'          => __( 'Product Status', 'woocommerce' ),
				'options'        => wc_get_product_stock_status_options(),
				'desc_tip'       => true,
				'description'    => __( 'Controls whether product is still "For Sale" or "No Longer Available".', 'woocommerce' ),
			) );

			do_action( 'woocommerce_product_options_stock_status' );
		?>
	</div>

	<div class="options_group show_if_simple show_if_variable">
		<?php
			woocommerce_wp_checkbox( array(
				'id'            => '_sold_individually',
				'value'         => $product_object->get_sold_individually( 'edit' ) ? 'yes' : 'no',
				'wrapper_class' => 'show_if_simple show_if_variable',
				'label'         => __( 'Sold individually', 'woocommerce' ),
				'description'   => __( 'Enable this to only allow one of this item to be bought in a single order', 'woocommerce' ),
			) );

			do_action( 'woocommerce_product_options_sold_individually' );
		?>
	</div>

	<?php do_action( 'woocommerce_product_options_inventory_product_data' ); ?>
</div>
