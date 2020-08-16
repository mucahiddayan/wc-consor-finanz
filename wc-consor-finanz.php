<?php
/**
 * Plugin Name: WooCommerce Consors Finanz Extension
 * Plugin URI: https://dayan.one
 * Description: Consors Finanz  Plugin for WooCommerce.
 * Version: 1.0.1
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
require_once 'assets/helper.php';

/*
 * This action hook registers our PHP class as a WooCommerce payment gateway
 */
add_filter('woocommerce_payment_gateways', 'wc_consor_finanz_add_gateway');
function wc_consor_finanz_add_gateway($gateways)
{
  $gateways[] = 'WC_Consor_Finanz';
  return $gateways;
}

/*
 * The class itself, please note that it is inside plugins_loaded action hook
 */
add_action('plugins_loaded', 'wc_consor_finanz_init_gateway_class');
function wc_consor_finanz_init_gateway_class()
{
  class WC_Consor_Finanz extends WC_Payment_Gateway
  {
    /**
     * Class constructor, more about it in Step 3
     */
    public function __construct()
    {
      $this->id = 'wc_consor_finanz'; // payment gateway plugin ID
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
      $this->title = '' /* $this->get_option('title') */;
      $this->vendorId = $this->get_option('vendor_id');
      $this->description = $this->get_option('description');
      $this->enabled = $this->get_option('enabled');
      $this->testmode = 'yes' === $this->get_option('testmode');
      $this->apiUrl = $this->get_option('api_url');
      $this->icon = plugin_dir_url(__FILE__) . 'assets/consors_finanz_logo.jpg';
      $this->defaultduration = $this->get_option('defaultduration');
      $this->hash_key = $this->get_option('hash_key');

      // This action hook saves the settings
      add_action(
        'woocommerce_update_options_payment_gateways_' . $this->id,
        array($this, 'process_admin_options')
      );

      // You can also register a webhook here
      add_action('woocommerce_api_' . $this->id, array(
        $this,
        'notifier_callback'
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
          'type' => 'range',
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
        'hash_key' => array(
          'title' => 'Hash Key',
          'type' => 'text',
          'description' =>
            'Hash Key für sichere Kommunikation. (Dieser Key wird bei der Freigabe von Consors Finanz übersandt)',
          'default' => '',
          'desc_tip' => true
        )
        // 'title' => array(
        //   'title' => 'Title',
        //   'type' => 'text',
        //   'description' => 'This title will be shown in checkout page',
        //   'default' => 'Consor Finanz Ratenzahlung',
        //   'desc_tip' => true
        // ),
        // 'description' => array(
        //   'title' => 'Description',
        //   'type' => 'textarea',
        //   'description' => 'Description',
        //   'default' => 'Finanzierung mit Consor'
        // )
      );
    }

    public function process_payment($order_id)
    {
      global $woocommerce;

      // we need it to get any order detailes
      $order = wc_get_order($order_id);
      $order_data = $order->get_data();
      $billing = $order_data['billing'];

      $data = array(
        'vendorid' => $this->vendorId,
        'order_amount' => $woocommerce->cart->total,
        'order_id' => $order_id,
        'successURL' => urlencode($this->get_return_url($order)),
        'cancelURL' => urlencode($order->get_cancel_order_url_raw()),
        'failureURL' => '',
        'notifyURL' => home_url() . '?wc-api=' . strtoupper($this->id),
        //customer informations
        'salutation' => 'herr',
        'firstname' => $billing['first_name'],
        'lastname' => $billing['last_name'],
        'birthdate' => '',
        'phone' => $billing['phone'],
        'mobil' => '',
        'email' => $billing['email'],
        'street' => $billing['address_1'] . ' ' . $billing['address_2'],
        'zip' => $billing['postcode'],
        'city' => $billing['city'],
        'shopbrandname' => get_bloginfo('name'),
        'shoplogoURL' => WC_Consor_Finanz_Helper::get_custom_logo_url(),
        'defaultduration' => $this->defaultduration
      );

      $url = add_query_arg($data, $this->apiUrl);

      return array(
        'redirect' => $url,
        'result' => 'success'
      );
    }

    public function is_valid_hash($to_check)
    {
      $data = WC_Consor_Finanz_Helper::remove_query_parameter_from_url(
        home_url() . $_SERVER['REQUEST_URI'],
        'hash'
      );
      $hashed_data = strtoupper(hash_hmac('sha512', $data, $this->hash_key));
      return hash_equals($hashed_data, strtoupper($to_check));
    }

    /*
     * In case you need a webhook, like PayPal IPN etc
     */
    public function notifier_callback()
    {
      $hash = WC_Consor_Finanz_Helper::get_parameter_from_request('hash');

      if ($this->is_valid_hash($hash)) {
        $status = WC_Consor_Finanz_Helper::get_parameter_from_request('status');
        $status_detail = WC_Consor_Finanz_Helper::get_parameter_from_request(
          'status_detail'
        );
        $order_id = WC_Consor_Finanz_Helper::get_parameter_from_request(
          'order_id'
        );
        $transaction_id = WC_Consor_Finanz_Helper::get_parameter_from_request(
          'transaction_id'
        );
        $duration = WC_Consor_Finanz_Helper::get_parameter_from_request(
          'duration'
        );
        $creditAmount = WC_Consor_Finanz_Helper::get_parameter_from_request(
          'creditAmount'
        );

        // ignore statuses 'proposal' and 'sucess'
        $order = wc_get_order($order_id);
        if ($order) {
          if (equal($status, 'error')) {
            $order->cancel_order();
          }
          if (equal($status, 'accepted')) {
            $order->payment_complete();
            wc_reduce_stock_levels($order_id);
          }

          // if (
          //   equal($status_detail, 'CANCELLED') ||
          //   equal($status_detail, 'DECLINED')
          // ) {
          //   $order->cancel_order();
          // }
        } else {
          echo 'There is no order with this id ' . $order_id;
        }
      }
    }

    //  functions to extend functionality of wordpress/woocommerce
    public static function load_styles_and_scripts()
    {
      wp_enqueue_style(
        'finanzierung_rechner_style',
        plugin_dir_url(__FILE__) . 'assets/style.css',
        null,
        '1.0.0'
      );
      wp_enqueue_script(
        'finanzierung_rechner_js',
        plugin_dir_url(__FILE__) . 'assets/main.js',
        array('jquery'),
        '1.0.0'
      );
    }

    //  functions to extend functionality of wordpress/woocommerce
    public static function load_admin_styles_and_scripts()
    {
      wp_enqueue_style(
        'finanzierung_rechner_style',
        plugin_dir_url(__FILE__) . 'assets/admin-style.css',
        null,
        '1.0.0'
      );
      wp_enqueue_script(
        'finanzierung_rechner_js',
        plugin_dir_url(__FILE__) . 'assets/admin-main.js',
        array('jquery'),
        '1.0.0'
      );
    }

    public static function is_consor_finanz($gateway)
    {
      return !strcmp($gateway, (new self())->id);
    }

    public static function cw_change_product_price_display($price)
    {
      $price .= self::price_after_text($price);
      return $price;
    }

    public static function price_after_text($price)
    {
      $rawPrice = WC_Consor_Finanz_Helper::priceToFloat($price);
      $month = WC_Consor_Finanz_Helper::get_month($rawPrice);
      $pricePerMonth = number_format((float) ($rawPrice / $month), 2, '.', '');
      return $rawPrice >= 54
        ? "
      <div class=\"consor-finanz__charging-hint\">
      <i><b>Möglicher Finanzierungsplan:</b></i>
      <span>$month Monatsraten à € $pricePerMonth*</span>
      <span class=\"consor-finanz__debit\">(*) 0% Sollzinsen für 36 Monate</span>
      </div>" . PHP_EOL
        : 'Finanzierung ist erst ab einem Einkaufswert von 54 Euro moeglich!';
    }

    public static function consor_finance_tab($tabs)
    {
      // Adds the new tab
      $tabs['consor_finance_tab'] = array(
        'title' => 'Finanzierung',
        'priority' => 50,
        'callback' => array('WC_Consor_Finanz', 'consor_finanz_calculator')
      );
      return $tabs;
    }

    public static function cart_totals_order_total_html($order_total)
    {
      $order_total .= self::price_after_text($order_total);
      return $order_total;
    }

    public static function consor_finanz_calculator()
    {
      ?>
  <h2>Finanzierung</h2>
  <div id="calculator"></div>
  <?php
    }
  }
}

//add custom tab
add_filter('woocommerce_product_tabs', array(
  'WC_Consor_Finanz',
  'consor_finance_tab'
));

//add styles and scripts
add_action('wp_enqueue_scripts', array(
  'WC_Consor_Finanz',
  'load_styles_and_scripts'
));

add_action('admin_enqueue_scripts', array(
  'WC_Consor_Finanz',
  'load_admin_styles_and_scripts'
));

add_filter(
  'woocommerce_locate_template',
  array('WC_Consor_Finanz_Helper', 'override_woocommerce_templates'),
  1,
  3
);

// add_action('woocommerce_after_cart', array(
//   'WC_Consor_Finanz',
//   'consor_finanz_calculator'
// ));

// add_filter('woocommerce_get_price_html', array(
//   'WC_Consor_Finanz',
//   'cw_change_product_price_display'
// ));

// add_filter('woocommerce_cart_item_price', array(
//   'WC_Consor_Finanz',
//   'cw_change_product_price_display'
// ));

// add_filter('woocommerce_cart_totals_order_total_html', array(
//   'WC_Consor_Finanz',
//   'cart_totals_order_total_html'
// ));

// override default templates

require_once 'vendor/plugin-update-checker-4.9/plugin-update-checker.php';
$myUpdateChecker = Puc_v4_Factory::buildUpdateChecker(
  plugin_dir_url(__FILE__) . 'assets/details.json',
  __FILE__, //Full path to the main plugin file or functions.php.
  'wc_consor_finanz'
);
