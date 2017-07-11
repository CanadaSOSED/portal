<?php
if ( !defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
$active_tab = isset( $_REQUEST[ 'tab' ] ) ? $_REQUEST[ 'tab' ] : 'export';
?>
<?php if ( isset( $_REQUEST['save'] ) ): ?>
<div class="update-nag" style="color: #008000; border-left: 4px solid green; display: block; width: 70%;"><?php _e( 'Settings saved', 'woocommerce-order-export' ) ?></div>
<?php endif; ?>
<h2 class="nav-tab-wrapper" id="tabs">
    <a class="nav-tab <?php echo $active_tab == 'export' ? 'nav-tab-active' : '' ?>" href="<?php echo admin_url( 'admin.php?page=wc-order-export&tab=export' ) ?>"><?php _e( 'Export', 'woocommerce-order-export' ) ?></a>
	<a class="nav-tab <?php echo $active_tab == 'profiles' ? 'nav-tab-active' : '' ?>" href="<?php echo admin_url( 'admin.php?page=wc-order-export&tab=profiles' ) ?>"><?php _e( 'Profiles', 'woocommerce-order-export' ) ?></a>
	<a class="nav-tab <?php echo $active_tab == 'order_actions' ? 'nav-tab-active' : '' ?>" href="<?php echo admin_url( 'admin.php?page=wc-order-export&tab=order_actions' ) ?>"><?php _e( 'Order Change', 'woocommerce-order-export' ) ?></a>
	<a class="nav-tab <?php echo $active_tab == 'schedules' ? 'nav-tab-active' : '' ?>" href="<?php echo admin_url( 'admin.php?page=wc-order-export&tab=schedules' ) ?>"><?php _e( 'Scheduled Exports', 'woocommerce-order-export' ) ?></a>
    <a class="nav-tab <?php echo $active_tab == 'tools' ? 'nav-tab-active' : '' ?>" href="<?php echo admin_url( 'admin.php?page=wc-order-export&tab=tools' ) ?>"><?php _e( 'Tools', 'woocommerce-order-export' ) ?></a>
    <a class="nav-tab <?php echo $active_tab == 'help' ? 'nav-tab-active' : '' ?>" href="<?php echo admin_url( 'admin.php?page=wc-order-export&tab=help' ) ?>"><?php _e( 'Help', 'woocommerce-order-export' ) ?></a>
</h2>

<script>
	var ajaxurl = "<?php echo $ajaxurl ?>"
</script>