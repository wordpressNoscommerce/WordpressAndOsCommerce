<?php
if(!class_exists('osc_widget')) :
require_once(OSCOMMERCECLASSPATH .'/osc_products.class.php');
require_once(OSCOMMERCECLASSPATH .'/osc_categories.class.php');
// add new osc_manufacturers
require_once(OSCOMMERCECLASSPATH .'/osc_manufacturers.class.php');

class osc_widget extends osc_db // DISPLAY OSC WIDGET
{
    function osc_widget()
    {
        //MAKE SURE PARENT VARIABLES AND FUNCTIONS ARE INITIALISED
        osc_db::osc_db();
    }

    // show shopname and number of products
    function display($args)
    {
        //			fbDebugBacktrace();
        extract($args);

        echo $before_widget;

        $options = get_option('widget_osCommerce');

        $sql = 'SELECT '.
        $this->table_id_field .',
						vchShopName,
						vchUrl,
						vchUsername,
						vchPassword,
						vchDbName,
						vchHost
					FROM '.
        $this->table_name.
				'	WHERE intShopId = 1'; // TODO eliminate query.... only get the first one now!

        $res_arr = $this->get_results($sql);

        if($this->num_rows > 0)
        {
            if(isset($options['title']) && !empty($options['title']))
            echo $before_title . $options['title'] .'('. $this->num_rows .')'. $after_title;

            $osc_products   = new osc_products();
            $osc_categories = new osc_categories();

            echo '<ul class="categories">';

            for($i = 0 ; $i < count($res_arr) ; $i++) // iterate over SHOPS here
            {
                $$table_id_field = $this->table_id_field;
                $intID = $res_arr[$i]->$$table_id_field;

                $shop_db = new wpdb($res_arr[$i]->vchUsername, $res_arr[$i]->vchPassword, $res_arr[$i]->vchDbName, $res_arr[$i]->vchHost);

                $osc_products->osc_count_products($shop_db);

                if($osc_products->record_count > MIN_RECORDS)
                {
                    $osc_categories->osc_count_categories($shop_db);

                    if($osc_categories->record_count > MIN_RECORDS)
                    {
                        $linkname = $res_arr[$i]->vchShopName .'&nbsp;c('. $osc_categories->record_count .')&nbsp;p('. $osc_products->record_count .')';
                        echo '<li class="widget widget_categories"><h3>
							<a name="'. $res_arr[$i]->vchShopName .'"></a>
							<a href="'. wp_nonce_url('?shopID='. $intID .'#'. $res_arr[$i]->vchShopName) .'"
							title="'. attribute_escape($linkname).'">'.$linkname.
							 '</a></h3>';

                        $osc_categories->osc_list_categories($shop_db, $intID, $_GET['shopID'], $_GET['catID'], 0);

                        echo '</li>';
                    }
                }

                unset($shop_db);
            }

            echo '</ul>';

            unset($osc_products);
            unset($osc_categories);
        }
        else
        _e('There are currently no shops.', 'osCommerce');

        echo $after_widget;
  		}
}
endif;
?>