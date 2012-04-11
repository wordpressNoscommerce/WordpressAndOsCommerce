<?php
if(!class_exists('osc_categories')) :

class osc_categories // DISPLAY OSC CATEGORIES
{
    var $record_count;

    function osc_categories(){}

    function osc_count_categories($db, $level = 0)
    {
        //			fbDebugBacktrace();
        $sql = 'SELECT
						COUNT(c.categories_id) AS cnt
					FROM
						categories c
						INNER JOIN products_to_categories pc ON c.categories_id = pc.categories_id';

        if(!empty($level))
        $sql.= ' WHERE c.parent_id = '. $level;

        $sql.= ' GROUP BY c.categories_id
					HAVING cnt > 0';

        $this->record_count = $db->get_var($sql);
        fb("catCount:".$this->record_count);
  		}

  		// gets called with $level 0 from widget->display() after it recurses
  		function osc_list_categories($db, $shop_id, $sel_shop_id, $sel_cat_id, $level)
  		{
  		    $sql = 'SELECT
						c.categories_id,
						cd.categories_name,
						c.parent_id
					FROM
						categories c
						INNER JOIN categories_description cd ON c.categories_id = cd.categories_id
						INNER JOIN products_to_categories pc ON c.categories_id = pc.categories_id
					WHERE 0 = 0
					AND	c.parent_id = '. $level .'
--					AND cd.language_id = 1   			-- messy hardcoded & WRONG language_id
					GROUP BY
						c.categories_id
					ORDER BY
						c.sort_order,
						cd.categories_name';

  		    $res_cats = $db->get_results($sql);
  		    if($db->num_rows > 0)
  		    {
  		        fbDebugBacktrace('catQuery:'.$db->num_rows); // only if something found
  		        if(($shop_id == $sel_shop_id) && ($level == 0))
  		        $display = 'style="display:block;"';
  		        else
  		        {
  		            if(($shop_id == $sel_shop_id) && ($level == $sel_cat_id))
  		            $display = 'style="display:block;"';
  		            else
  		            $display = 'style="display:none;"';
  		        }

  		        echo '<ul id="categories_'. $shop_id .'_'. $level .'" '. $display .'>';

  		        $osc_products = new osc_products();

  		        for($j = 0 ; $j < count($res_cats) ; $j++)
  		        {
  		            $osc_products->osc_count_products($db, $res_cats[$j]->categories_id);
  		            // 10 prods minimum
  		            if ($osc_products->record_count > 10) {
  		                echo '<li class="cat-item cat-item-1">
						<a name="'. $res_cats[$j]->categories_name .'_'. $shop_id.'"></a>					
						<a href="'
						. wp_nonce_url('products?shopID='. $shop_id
						.'&amp;catID='. $res_cats[$j]->categories_id .'#'
						. $res_cats[$j]->categories_name .'_'. $shop_id)
						.'" title="'. attribute_escape($res_cats[$j]->categories_name
						.' p('. $osc_products->record_count .')') .'">'. $res_cats[$j]->categories_name
						.' p('. $osc_products->record_count .')' .'</a>';
  		            }
  		            $this->osc_list_categories($db, $shop_id, $sel_shop_id, $sel_cat_id, $res_cats[$j]->categories_id);

  		            echo '</li>';
  		        }

  		        unset($osc_products);

  		        echo '</ul>';
  		    }
  		}
}
endif;
?>