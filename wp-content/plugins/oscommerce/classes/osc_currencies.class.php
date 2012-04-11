<?php
/*
 $Id: currencies.php 1803 2008-01-11 18:16:37Z hpdl $

 osCommerce, Open Source E-Commerce Solutions
 http://www.oscommerce.com

 Copyright (c) 2008 osCommerce

 Released under the GNU General Public License
 */

// Class to handle currencies
// TABLES: currencies
class osc_currencies
{
    var $db;
    var $currencies;

    // class constructor
    function osc_currencies($shop_db)
    {
        fbDebugBacktrace();
        $this->db         = $shop_db;
      		$this->currencies = array();

      		$sql = 'SELECT
						code,
						title,
						symbol_left,
						symbol_right,
						decimal_point,
						thousands_point,
						decimal_places,
						value
					FROM
						currencies';

      		$res_curr = $this->db->get_results($sql);

      		for($i = 0 ; $i < count($res_curr) ; $i++)
      		{
      		    $this->currencies[$res_curr[$i]->code] = array('title'           => $res_curr[$i]->title,
            		                                       	   'symbol_left'     => $res_curr[$i]->symbol_left,
                		                                   	   'symbol_right'    => $res_curr[$i]->symbol_right,
                    		                               	   'decimal_point'   => $res_curr[$i]->decimal_point,
                        		                           	   'thousands_point' => $res_curr[$i]->thousands_point,
                            		                       	   'decimal_places'  => $res_curr[$i]->decimal_places,
                                		                   	   'value'           => $res_curr[$i]->value);
      		}
    }

    // class methods
    // WRAPPER FUNCTION FOR round()
    function osc_round($number, $precision)
    {
        if(strpos($number, '.') && (strlen(substr($number, strpos($number, '.') + 1)) > $precision))
        {
            $number = substr($number, 0, strpos($number, '.') + 1 + $precision + 1);

            if(substr($number, -1) >= 5)
            {
                if($precision > 1)
                $number = substr($number, 0, -1) + ('0.' . str_repeat(0, $precision-1) . '1');
                elseif($precision == 1)
                $number = substr($number, 0, -1) + 0.1;
                else
                $number = substr($number, 0, -1) + 1;
            }
            else
            {
                $number = substr($number, 0, -1);
            }
        }

        return $number;
  		}

  		// Calculates Tax rounding the result
  		function calculate_tax($price, $tax)
  		{
  		    return $this->osc_round($price * $tax / 100, $this->currencies['ZAR']['decimal_places']);
  		}

  		// Calculates Tax rounding the result
  		function add_tax($price, $tax)
  		{
  		    // Will be changed
  		    if((DISPLAY_PRICE_WITH_TAX == 'true') && ($tax > 0))
  		    return $this->osc_round($price, $this->currencies['ZAR']['decimal_places']) + $this->calculate_tax($price, $tax);
  		    else
  		    return $this->osc_round($price, $this->currencies['ZAR']['decimal_places']);
  		}

  		function format($number, $calculate_currency_value = true, $currency_type = '', $currency_value = '')
  		{
      		if(empty($currency_type)) $currency_type = 'ZAR';

      		if($calculate_currency_value === true)
      		{
      		    $rate = !empty($currency_value) ? $currency_value : $this->currencies[$currency_type]['value'];

      		    $format_string = $this->currencies[$currency_type]['symbol_left'] . number_format($this->osc_round($number * $rate, $this->currencies[$currency_type]['decimal_places']), $this->currencies[$currency_type]['decimal_places'], $this->currencies[$currency_type]['decimal_point'], $this->currencies[$currency_type]['thousands_point']) . $this->currencies[$currency_type]['symbol_right'];
      		}
      		else
      		$format_string = $this->currencies[$currency_type]['symbol_left'] . number_format($this->osc_round($number, $this->currencies[$currency_type]['decimal_places']), $this->currencies[$currency_type]['decimal_places'], $this->currencies[$currency_type]['decimal_point'], $this->currencies[$currency_type]['thousands_point']) . $this->currencies[$currency_type]['symbol_right'];

      		return $format_string;
  		}

  		function calculate_price($products_price, $products_tax, $quantity = 1)
  		{
      		return $this->osc_round($this->add_tax($products_price, $products_tax), $this->currencies['ZAR']['decimal_places']) * $quantity;
  		}

  		function is_set($code)
  		{
      		if(isset($this->currencies[$code]) && !empty($this->currencies[$code]))
      		return true;
      		else
      		return false;
  		}

  		function get_value($code)
  		{
      		return $this->currencies[$code]['value'];
  		}

  		function get_decimal_places($code)
  		{
      		return $this->currencies[$code]['decimal_places'];
  		}

  		function display_price($products_price, $products_tax, $quantity = 1)
  		{
  		    fbDebugBacktrace();
      		return $this->format($this->calculate_price($products_price, $products_tax, $quantity));
  		}

  		function get_tax_rate($class_id, $country_id = 193, $zone_id = null)
  		{
  		    $sql = 'SELECT
						SUM(tax_rate) AS tax_rate
					FROM
						tax_rates tr
						LEFT JOIN zones_to_geo_zones za ON tr.tax_zone_id = za.geo_zone_id
						LEFT JOIN geo_zones tz ON tz.geo_zone_id = tr.tax_zone_id
					WHERE
						(za.zone_country_id IS NULL
						OR za.zone_country_id = 0
						OR za.zone_country_id = '. (int)$country_id .')
						AND (za.zone_id IS NULL
						OR za.zone_id = 0
						OR za.zone_id = '. (int)$zone_id . ')
						AND tr.tax_class_id = '. (int)$class_id .'
					GROUP BY
						tr.tax_priority';

  		    $res_rates = $this->db->get_results($sql);

      		if($this->db->num_rows > 0)
      		{
      		    $tax_multiplier = 1.0;

      		    for($i = 0 ; $i < count($res_rates) ; $i++)
      		    $tax_multiplier *= 1.0 + ($res_rates[$i]->tax_rate / 100);

      		    return ($tax_multiplier - 1.0) * 100;
      		}
      		else
      		return 0;
  		}
}
?>