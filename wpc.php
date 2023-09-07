<?php

/**
 * Plugin Name: WooCommerce Prevent Completion
 * Description: Prevent order from completed
 * Version: 1.0.0
 *
 * License: GNU General Public License v3.0
 * License URI: https://www.gnu.org/licenses/gpl-3.0.html
 */

add_action( 'woocommerce_pre_payment_complete', 'wpc_check_chip_payment_status', 10, 2 );
function wpc_check_chip_payment_status( $order_id, $chip_purchase_id ) {
  $order = new WC_Order( $order_id );

  $gateway_id = $order->get_payment_method();

  $purchase = $order->get_meta( '_' . $gateway_id . '_purchase', true );
  
  // not chip
  if (empty($purchase)) {
    return;
  }

  // chip for woocommerce not available
  if (!class_exists('Chip_WooCommerce')){
    return;
  }

  $wc_gateway_chip = Chip_Woocommerce::get_chip_gateway_class( $gateway_id );

  $chip = $wc_gateway_chip->api();

  $payment = $chip->get_payment( $chip_purchase_id );

  if ( $payment['status'] == 'paid' ) {
    return; // all ok
  }

  throw new Exception('Purchase is not in paid state cannot be completed!');
}
