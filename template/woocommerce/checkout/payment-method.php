<?php
/**
 * Output a single payment method
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/payment-method.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see         https://docs.woocommerce.com/document/template-structure/
 * @package     WooCommerce/Templates
 * @version     3.5.0
 */

if (!defined('ABSPATH')) {
  exit();
} ?>
<li class="wc_payment_method payment_method_<?php
echo esc_attr($gateway->id);
echo WC_Consor_Finanz::is_consor_finanz($gateway->id)
  ? ' wp-consor-finanz-box'
  : '';
?>">
	<input id="payment_method_<?php echo esc_attr(
   $gateway->id
 ); ?>" type="radio" class="input-radio" name="payment_method" value="<?php echo esc_attr(
  $gateway->id
); ?>" <?php checked(
  $gateway->chosen,
  true
); ?> data-order_button_text="<?php echo esc_attr(
   $gateway->order_button_text
 ); ?>" />

	<label  for="payment_method_<?php echo esc_attr($gateway->id); ?>">
		<?php echo !WC_Consor_Finanz::is_consor_finanz($gateway->id)
    ? $gateway->get_title()
    : ''; ?> <?php echo $gateway->get_icon(); ?>
	</label>
	<?php if ($gateway->has_fields() || $gateway->get_description()): ?>
		<div class="payment_box payment_method_<?php echo esc_attr(
    $gateway->id
  ); ?>" <?php if (!$gateway->chosen): ?>style="display:none;"<?php endif; ?>>
      <?php
      global $woocommerce;
      echo WC_Consor_Finanz::is_consor_finanz($gateway->id)
        ? WC_Consor_Finanz::price_after_text(
          $woocommerce->cart->get_cart_total()
        )
        : $gateway->payment_fields();
      ?>
		</div>
	<?php endif; ?>
</li>
