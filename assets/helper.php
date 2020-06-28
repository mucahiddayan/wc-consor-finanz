<?php

class WP_Consor_Finanz_Helper
{
  public static function get_parameter_from_request($param)
  {
    if (isset($_GET[$param])) {
      return $_GET[$param];
    }
    if (isset($_POST[$param])) {
      return $_POST[$param];
    }
  }

  public static function remove_query_parameter_from_url($url, $param_to_remove)
  {
    $url_data = parse_url($url);
    parse_str($url_data['query'], $query_data);
    unset($query_data[$param_to_remove]);
    $url_data['query'] = http_build_query($query_data);
    return self::unparse_url($url_data);
  }

  public static function priceToFloat($s)
  {
    // convert "," to "."
    $s = str_replace(',', '.', $s);

    // remove everything except numbers and dot "."
    $s = preg_replace("/[^0-9\.]/", "", $s);

    // remove all seperators from first part and keep the end
    $s = str_replace('.', '', substr($s, 0, -3)) . substr($s, -3);

    // return float
    return (float) $s;
  }

  public static function get_month($price)
  {
    $month = 36;
    while ($price / 9 < $month) {
      $month -= 6;
    }
    return $month;
  }

  public static function get_custom_logo_url()
  {
    $custom_logo_id = get_theme_mod('custom_logo');
    $url = wp_get_attachment_url($custom_logo_id);
    return $url;
  }

  public static function equal($str1, $str2)
  {
    return strcmp($str1, $str2) === 0;
  }

  public static function unparse_url($parsed_url, $ommit = array())
  {
    //From Open Web Analytics owa_lib.php
    $url = '';
    $p = array();
    $p['scheme'] = isset($parsed_url['scheme'])
      ? $parsed_url['scheme'] . '://'
      : '';
    $p['host'] = isset($parsed_url['host']) ? $parsed_url['host'] : '';
    $p['port'] = isset($parsed_url['port']) ? ':' . $parsed_url['port'] : '';
    $p['user'] = isset($parsed_url['user']) ? $parsed_url['user'] : '';
    $p['pass'] = isset($parsed_url['pass']) ? ':' . $parsed_url['pass'] : '';
    $p['pass'] = $p['user'] || $p['pass'] ? $p['pass'] . "@" : '';
    $p['path'] = isset($parsed_url['path']) ? $parsed_url['path'] : '';
    $p['query'] = isset($parsed_url['query']) ? '?' . $parsed_url['query'] : '';
    $p['fragment'] = isset($parsed_url['fragment'])
      ? '#' . $parsed_url['fragment']
      : '';

    if ($ommit) {
      foreach ($ommit as $key) {
        if (isset($p[$key])) {
          $p[$key] = '';
        }
      }
    }

    return $p['scheme'] .
      $p['user'] .
      $p['pass'] .
      $p['host'] .
      $p['port'] .
      $p['path'] .
      $p['query'] .
      $p['fragment'];
  }

  public static function override_woocommerce_templates(
    $template,
    $template_name,
    $template_path
  ) {
    global $woocommerce;
    $_template = $template;
    if (!$template_path) {
      $template_path = $woocommerce->template_url;
    }

    $plugin_path =
      untrailingslashit(plugin_dir_path(__FILE__)) . '/template/woocommerce/';

    // Look within passed path within the theme - this is priority
    $template = locate_template(array(
      $template_path . $template_name,
      $template_name
    ));

    if (!$template && file_exists($plugin_path . $template_name)) {
      $template = $plugin_path . $template_name;
    }

    if (!$template) {
      $template = $_template;
    }

    return $template;
  }
}
