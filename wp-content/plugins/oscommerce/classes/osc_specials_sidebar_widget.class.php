<?php
if(!class_exists('Osc_specials_widget')) :
require_once(OSCOMMERCECLASSPATH .'/osc_db.class.php');
require_once(OSCOMMERCECLASSPATH .'/osc_products.class.php');

class Osc_specials_widget extends WP_Widget
{

	function Osc_specials_widget()
	{
		$widget_opts = array('description' => __("Special Offers", "oscommerce") );
		$this->WP_Widget('oscommercespecials', 'OsCommerceSpecials', $widget_opts);
	}

	function widget($args, $instance)
	{
		extract($args, EXTR_SKIP);

		echo $before_widget;
		if (!empty($instance['title'])) echo $before_title . $instance['title'] . $after_title;
		echo oscommerce_sidebar($instance);
		echo $after_widget;
	}

	function update($new_instance, $old_instance)
	{
		$instance = array();
		$allowed = array(
				'title',
				'limit',
		);

		foreach($new_instance as $option => $value)
		{
			if(in_array($option, $allowed)) {
				if($option == 'limit' && (!is_numeric($value) || $value === 0)) {
					$instance['limit'] = 5;
				} else {
					$instance[$option] = gigpress_db_in($value);
				}
			}
		}
		return $instance;
	}

	// form for parametrization
	function form($instance)
	{
		$defaults = array(
				'title' => 'Special Offers',
				'limit' => 5,
		);

		$instance = wp_parse_args($instance, $defaults);
		extract($instance);

		?>
<p>
	<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title'); ?>: <input class="widefat"
		id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text"
		value="<?php echo $title; ?>" /> </label>
</p>

<p>
	<label for="<?php echo $this->get_field_id('limit'); ?>"><?php _e('Number of shows to list', 'gigpress'); ?>: <input
		style="width: 25px; text-align: center;" id="<?php echo $this->get_field_id('limit'); ?>"
		name="<?php echo $this->get_field_name('limit'); ?>" type="text" value="<?php echo $limit; ?>" /> </label>
</p>

<?php }

}

// Register the widget
function oscommerce_load_widgets() {
	register_widget('Osc_specials_widget');
}


function oscommerce_sidebar($filter = null) {

	global $gpo;
	$further_where = '';

	$options = get_option('widget_osCommerce_Specials');

	// Number of shows to list (per artist if grouping by artist)
	$limit = (isset($filter['limit']) && is_numeric($filter['limit'])) ? $filter['limit'] : 5;

	$osc_products = new osc_products();
	$special_query_results = $osc_products->osc_get_special_offers($limit);

	ob_start();

	// If we're grouping by artist, we'll unfortunately have to first get all artists
	// Then  make a query for each one. Looking for a better way to do this.
	$count = sizeof($special_query_results);
	if($count > 1) {
		$idx=0;
		foreach ($special_query_results as $record) {
			//			print_r($record);
			// prepare record fields 
			$record->products_link = get_site_url() . '/releases?products_id='.$record->products_id;
			$record->image_url = $osc_products->get_img_url($record->products_image);
			$nameslash = strpos($record->products_name,'/');
			if (empty($record->products_format)) {
				if ($nameslash > 0)
					$record->products_format =  substr ($record->products_name,0,$nameslash - 1);
			}
			if ($nameslash > 0)
					$record->products_name =  substr ($record->products_name,$nameslash + 1);
			if ($record->products_price > 0) {
				$record->products_price_gross = round($record->products_price * 119)/100;
				$record->products_price_gross = '€'.$record->products_price_gross;
			}
			/// TODO NASTY HACK
			if (empty($record->manufacturers_name)) $record->manufacturers_name = "Shitkatapult";
			/// END NASTY HACK
			
			
			include oscommerce_template('sidebar-special-offers');

			// generate separating lines
			$idx++;
			if ($idx < $count) {
				echo '<hr/>';
			}
		}
	}

	return ob_get_clean();
}
endif;
?>