<?php
/**
 * Plugin Name: WooCommerce Consors Finanz Extension
 * Plugin URI: https://dayan.one
 * Description: Consors Finanz  Plugin for WooCommerce.
 * Version: 1.0.0
 * Author: Mücahid Dayan
 * Author URI: https://mücahiddayan.com/
 * Developer: Mücahid Dayan
 * Developer URI: https://mücahiddayan.com/
 * Text Domain: wc-consor-finanz
 * Domain Path: /languages
 *
 *
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */

defined('ABSPATH') or die('No script kiddies please!');

/*
 * This action hook registers our PHP class as a WooCommerce payment gateway
 */
add_filter('woocommerce_payment_gateways', 'wc_consor_finanz_add_gateway');
function wc_consor_finanz_add_gateway($gateways)
{
  $gateways[] = 'WC_Corsor_Finanz';
  return $gateways;
}

/*
 * The class itself, please note that it is inside plugins_loaded action hook
 */
add_action('plugins_loaded', 'wc_consor_finanz_init_gateway_class');
function wc_consor_finanz_init_gateway_class()
{
  class WC_Corsor_Finanz extends WC_Payment_Gateway
  {
    /**
     * Class constructor, more about it in Step 3
     */
    public function __construct()
    {
      $this->id = 'wc_consor_finanz'; // payment gateway plugin ID
      $this->icon = ''; // URL of the icon that will be displayed on checkout page near your gateway name
      $this->has_fields = false; // in case you need a custom credit card form
      $this->method_title = 'WooCommerce Consor Finanz Extension';
      $this->method_description =
        'Description of WooCommerce Consor Finanz payment gateway'; // will be displayed on the options page

      // gateways can support subscriptions, refunds, saved payment methods,
      // but in this tutorial we begin with simple payments
      $this->supports = array('products');

      // Method with all the options fields
      $this->init_form_fields();

      // Load the settings.
      $this->init_settings();
      $this->title = $this->get_option('title');
      $this->vendorId = $this->get_option('vendor_id');
      $this->description = $this->get_option('description');
      $this->enabled = $this->get_option('enabled');
      $this->testmode = 'yes' === $this->get_option('testmode');
      $this->apiUrl = $this->get_option('api_url');
      $this->icon = $this->get_option('icon_url');
      // This action hook saves the settings
      add_action(
        'woocommerce_update_options_payment_gateways_' . $this->id,
        array($this, 'process_admin_options')
      );

      // You can also register a webhook here
      add_action('woocommerce_api_wc_consor_finanz_result', array(
        $this,
        'webhook'
      ));
    }

    /**
     * Plugin options, we deal with it in Step 3 too
     */
    public function init_form_fields()
    {
      $this->form_fields = array(
        'enabled' => array(
          'title' => 'Enable/Disable',
          'label' => 'Enable Consor Finanz',
          'type' => 'checkbox',
          'description' => '',
          'default' => 'no'
        ),
        'vendor_id' => array(
          'title' => 'Handler Id',
          'type' => 'text',
          'description' => 'Haendler Id fuer Consor Finanz',
          'default' => '',
          'desc_tip' => true
        ),
        'defaultduration' => array(
          'title' => 'Anzahl der Raten',
          'type' => 'text',
          'description' => 'Standardwert fuer Anzahl der Raten',
          'default' => '12',
          'desc_tip' => true
        ),

        'api_url' => array(
          'title' => 'Consor Finanz Api Url',
          'type' => 'text',
          'description' => 'Consor Finanz Api Url',
          'default' =>
            'https://finanzieren.consorsfinanz.de/web/ecommerce/gewuenschte-rate',
          'desc_tip' => true
        ),
        'title' => array(
          'title' => 'Title',
          'type' => 'text',
          'description' => 'This title will be shown in checkout page',
          'default' => 'Consor Finanz Ratenzahlung',
          'desc_tip' => true
        ),
        'description' => array(
          'title' => 'Description',
          'type' => 'textarea',
          'description' =>
            'This controls the description which the user sees during checkout.',
          'default' => 'Pay with Consor Finanz'
        ),
        'icon_url' => array(
          'title' => 'Icon URL',
          'type' => 'text',
          'description' => 'Icon Url',
          'default' => '',
          'desc_tip' => true
        ),
        'testmode' => array(
          'title' => 'Test mode',
          'label' => 'Enable Test Mode',
          'type' => 'checkbox',
          'description' =>
            'Place the payment gateway in test mode using test API keys.',
          'default' => 'yes',
          'desc_tip' => true
        )
      );
    }

    public function process_payment($order_id)
    {
      global $woocommerce;

      // we need it to get any order detailes
      $order = wc_get_order($order_id);

      $data = array(
        'vendorid' => $this->vendorId,
        'order_amount' => $woocommerce->cart->total,
        'order_id' => $order_id,
        'successURL' => urlencode($this->get_return_url($order)),
        'cancelURL' => '',
        'failureURL' => ''
      );

      $query = http_build_query($data);
      return array(
        'redirect' => $this->apiUrl . '?' . $query,
        'result' => 'success'
      );
    }

    /*
     * In case you need a webhook, like PayPal IPN etc
     */
    public function webhook()
    {
    }
  }
}
