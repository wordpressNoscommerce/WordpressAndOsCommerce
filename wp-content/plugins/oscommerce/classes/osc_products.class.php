<?php
if(!class_exists('osc_products')) :
require_once(OSCOMMERCECLASSPATH .'/osc_product_templates.class.php');
require_once(OSCOMMERCECLASSPATH .'/osc_currencies.class.php');
//require_once ( ABSPATH . WPINC . '/cache.php' );
//	require_once(OSCOMMERCEPATH .'/osc_product_template.php');

/** osCommerce product handling using the DB connection stored in the database **/
class osc_products extends osc_product_templates
{
  var $records_per_page;
  var $product_count;
  var $cart_url;      // php page to handle OSC cart
  var $img_url;       // base url of images
  var $shop_url;      //
  var $result;
  var $sql;
  var $osc_db;        // handle to wp_oscommerce table in wordpress DB
  var $osc_sid;       // session id to communicate with shop system NEEDS WORK
  var $shop_db;       // handle to oscommerce shopdb DB
  var $shop_id;
  var $label_id;
  var $artist_id;
  var $format;
  var $paged;
  var $max_page;
  var $release_formats;

  function osc_products()
  {
    $this->osc_db = new osc_db();
    $this->shop_id = $_GET['shopId'];
    $this->artist_id = $_GET['artistId'];
    $this->format = $_GET['format'];
    $this->paged = $_GET['paged'];
    $this->json = $_GET['json'];
    $this->records_per_page = $_GET['pagesize'];
    $this->format = $_GET['format'];
    $this->paged = $_GET['paged'];
    // products_id is ignored here... maybe late inject data for this also
    if (empty($this->shop_id)) $this->shop_id = 1;     	// fix shop id
    if (empty($this->records_per_page)) $this->records_per_page = 9;
    if (empty($this->shop_id)) $this->shop_id = 1;     	// fix shop id
    if (empty($this->format)) $this->format = 'vinyl'; // fix default format
    if (empty($this->paged)) $this->paged = 1;	// default is first page
    $this->release_formats = array('Vinyl','CD','MP3','LP','EP','DVD');
    $this->osc_db = new osc_db();
  }

  /////////////////////////////////////////////////////////////////////////////
  // count total available products in shop
  function osc_count_products($db)
  {
    // p.products_quantity is 0 for master products without parent
    $sql = 'SELECT COUNT(p.products_id) AS cnt
				FROM products p
				INNER JOIN products_to_categories p2c ON p.products_id = pc.products_id';
    $sql.= ' WHERE p.products_quantity > 0 AND p.products_parent = "" ';

    $this->product_count = $db->get_var($sql);
    fb("prodCountCatId($cat_id):$sql:".$this->product_count);
  }

  /////////////////////////////////////////////////////////////////////////////
  // limit counting to same conditions as display but without paging
  function osc_count_products_from($from)
  {
    //			fbDebugBacktrace();
    $select = 'SELECT COUNT(p.products_id) AS cnt ';
    $sql = $select . $from;
    $this->product_count = $this->shop_db->get_var($sql);
    if (empty($this->product_count)) {
      fb("prodCountFrom($sql): IS EMPTY!!!!!!!!!!!");
      //			    fb(json_encode($this->shop_db));
      //			    fb(json_encode($this->wp_db));
    } else
    fb("prodCountFrom($sql):".$this->product_count);
  }

  /////////////////////////////////////////////////////////////////////////////////////
  // list products according to parms set in oscProducts object
  function osc_show_tabbed_products_page() {
    $this->result = $this->osc_query_products();        // result as a member var?
    if (empty($this->result)) {
      $msg ='No Releases found!';
      fb($msg);
      echo "<h1>$msg</h1>";
    } else {
      $this->osc_inject_product_list_json();    // first page of data
      $this->osc_inject_all_product_templates();
      // show the tabs for releases
      $current_tab = $this->osc_show_format_tabs_ajax();
    }
  }

  /////////////////////////////////////////////////////////////////////////////////////
  /** answer AJAX request for tab content and show either the grid with products as HTML
   *  or return the data as json (get_product_page.php) **/
  function osc_get_products_page() {

    $this->result = $this->osc_query_products();        // result as a member var?
    if (empty($this->result)) {
      $now = date(DATE_RFC822);
      $msg ="No Records found for prod_query_result ($now): $this->sql";
      fb($msg);
      if (!$this->json)	echo "<h3>$msg</h3>";
      else 				echo $msg;
    } else {
      $this->osc_fix_product_list_json();    // add extra fields to list items
      if ($this->json) {
        $retval = array($this->records_per_page,$this->product_count, $this->max_page, $this->result,$this->release_formats);
        // encode tuple of pagesize, count and result
        echo json_encode($retval);
      } else {
        // show product grid as HTML
        $this->osc_show_products_in_grid();
      }
    }
  }

  /////////////////////////////////////////////////////////////////////////////////////
  // insert the product data as jquery array using JSON in the header
  // var $product[format] = [{"products_id":"3493","products_image":"vhr-45-009_kl.jpg","products_name":"Long Distance Calling \/ Satellite Bay","products_model":"vhr-45-009","products_weight":"0.00","products_quantity":"0","products_price":"0.0000","manufacturers_id":"302","manufacturers_name":"Long Distance Calling","manufacturers_image":null,"products_tax_class_id":"0","specials_new_products_price":null,"final_price":"0.0000","products_description":"info coming soon<br \/>"},{"products_id":"3489","products_image":"vhr-45-001_kl.jpg","products_name":"Troum \/ Nargis","products_model":"vhr-45-002","products_weight":"0.00","products_quantity":"0","products_price":"0.0000","manufacturers_id":"301","manufacturers_name":"Troum","manufacturers_image":null,"products_tax_class_id":"0","specials_new_products_price":null,"final_price":"0.0000","products_description":"This is part one of the &quot;Landscapes Single Series&quot;<br \/>"}];
  function osc_inject_product_list_json() {
    $this->osc_fix_product_list_json();
    // TODO script injection not working yet
    wp_localize_script( 'products', 'oscProductList', $this->result);
    ?>

<script type="text/javascript">// initial JSON injection including count, next ones get loaded by AJAX
  var productsCount = <?php echo $this->product_count ?>;
  var productsPageSize = <?php echo $this->records_per_page ?>;
  var productsReleaseFormats = <?php echo $this->release_formats ?>;
  var products = {};
  products['<?php echo strtolower($this->format); ?>'] = <?php echo json_encode($this->result); ?>;
</script>
    <?php
    /*        $ids = array();
     foreach ($this->result as $product) $ids->push($product->products_id);

     fb('injected '.count($this->result).' products for '.$this->format.' using JSON: '. implode("-",$ids));
     */
  }

  /////////////////////////////////////////////////////////////////////////////////////
  // add missing fields to product list
  function osc_fix_product_list_json() {
    $i=0; // add missing fields to products array before jsoning it
    foreach ($this->result as $product) {
      $product->products_index = $i++;
      if ($product->products_image) {
        $product->products_image_url = rtrim($this->shop_url, '/') ."/images/". $product->products_image;
      } else // empty image link
      $product->products_image_url = "/wp-content/plugins/oscommerce/images/no_image.gif";

      if ($product->products_image_lrg){
        $product->products_image_lrg_url = rtrim($this->shop_url, '/') ."/images/". $product->products_image_lrg;
      } else // empty image link
      $product->products_image_lrg_url = "/wp-content/plugins/oscommerce/images/no_image.gif";
    }
  }

  //////////////////////////////////////////////////////////////////////////////////////
  // cat_id == labelID .... man_id == artistID... format == productFormat .... paged pageNumber
  function osc_query_products()
  {
    //		    fbDebugBacktrace();
    $this->osc_get_shop_db ();    // get handle for shop database
    if (empty($this->shop_db)) {
      fb('could not get handle for SHOP DB');
      throw new Exception('empty SHOP DB');
    }
    $select = 'SELECT	p.products_id,
						p.products_image,
						p.products_image_lrg,
						pd.products_name,
						p.products_model,
						p.products_weight,
						p.products_quantity,
						p.products_price,
						p.manufacturers_id,
						m.manufacturers_name,
						m.manufacturers_image,
						p.products_tax_class_id,
						IF(s.status, s.specials_new_products_price, NULL) AS specials_new_products_price,
						IF(s.status, s.specials_new_products_price, p.products_price) AS final_price,
						pd.products_description,
						pd.products_format,
						pd.products_viewed,
						pd.products_head_keywords_tag
						';
    $order = "ORDER BY p.products_model desc";

    //	TODO HACK: use literal table names cos cannot include other virthost installation dirs on production server
    //			require OSCOMMERCE_DOC_ROOT."/includes/database_tables.php"; from shopkatapult/index.php:#141
    $from ='FROM products p
			LEFT JOIN products_description pd ON pd.products_id = p.products_id
			LEFT JOIN manufacturers m ON m.manufacturers_id = p.manufacturers_id
			LEFT JOIN specials s ON p.products_id = s.products_id
			LEFT JOIN products_to_categories p2c ON p.products_id = p2c.products_id ';
    // condition
    $where = "WHERE p.products_status = '1' and p.products_parent = '' ";
    // show the products of a specified manufacturer
    if ($this->artist_id) {
      // We show the ones for the given manufacturer
      $where .= "AND m.manufacturers_id = '" . (int)$this->artist_id . "' ";
    }
    if ($this->label_id) {
      // show the products in a given categorie
      $where .= "AND p2c.categories_id = '" . (int)$this->label_id . "' ";
    }
    if ($this->format && $this->format <> 'All') {
      // show the products having a specific format (keyword)
      $where .= "AND pd.products_head_keywords_tag LIKE '%$this->format%' ";
    }
    $sql = $select . $from . $where . $order;

    /* CALCULATE PAGING */
    // GET TOTAL AMOUNT OF PRODUCTS TODO try to story this value somehow
    $this->osc_count_products_from($from.$where);
    fb('counted '.$this->product_count.' products');
    if($this->product_count > 0)
    {
      $this->max_page = ceil($this->product_count/$this->records_per_page);

      if($this->paged > $this->max_page) $this->paged = $this->max_page;

      $firstRecordOfPage = $this->records_per_page * ($this->paged - 1);

      if(!empty($this->paged))
      $sql = sprintf('%s LIMIT %d, %d', $sql, $firstRecordOfPage, $this->records_per_page);
    }
    /////////////// QUERY
    $prod_query_results = $this->shop_db->get_results($sql); // returns array of objects
    fb('SQL:'.$sql.' found Records:'.$this->product_count);
    fb("osc_query_products(shop_db, $this->shop_id, $this->cat_id):". $this->shop_db->num_rows);
    $this->sql = $sql;
    $this->result = $prod_query_results;
    return $prod_query_results;
  }

  ////////////////////////////////////////////////////////////////////////////////////////////////
  // make HTML from the query OBSOLETE
  function osc_show_products_in_grid() {
    if(count($this->result) > 0)
    {
      /* DISPLAY PAGING LINKS */
      if($this->product_count > $this->records_per_page)
      $page_links = paginate_links(array('base'    => add_query_arg(array('paged' => '%#%')),
													   'format'  => '',
													   'total'   => $this->max_page,
													   'current' => $this->paged));
      $currencies = new osc_currencies($this->shop_db);
      if ($this->format == "") $this->format ="All Format";
      echo '<div id="loop" class="grid clear">';
      if (! $this->bare)
      echo "<span class=\"recordCount\">$this->product_count items</span>";
      /*	leave it to jquery
       // use grid formatting similar to category page with posts (however in a different hierarchy)
       for($j = 0 ; $j < count($this->result) ; $j++)
       {
       $this->show_product_box ($j, $this->result[$j],$this->shop_url);
       }
       */
      echo '</div>';  // finish loop
      // page links at bottom
      if($page_links)
      echo '<div class="tablenav-pages pagination">'. $page_links .'</div>';

      unset($currencies);
    }

    //			unset($shop_db);    // TODO check the database connections
  }

  /////////////////////////////////////////////////////////////////////////////////////
  // get handle to shop db using data from wp db
  // db is member and injected to avoid dependency
  // returns shop_db
  function osc_get_shop_db () {
    $db = $this->osc_db;
    // TODO try to fix shop_id to save queries
    $shopSql = 'SELECT vchUrl, vchUsername, vchPassword, vchDbName, vchHost
						FROM wp_oscommerce WHERE intShopId = '. $this->shop_id;
    //  			$res_arr  = $this->osc_db->get_results($shopSql);
    $res_arr  = $db->get_results($shopSql);

    $shop_url = $res_arr[0]->vchUrl;
    if (preg_match('/http:/', $shop_url))
    $this->shop_url = $res_arr[0]->vchUrl;
    else
    $this->shop_url = 'http://'.$res_arr[0]->vchUrl;

    $this->img_url = rtrim($this->shop_url, '/') ."/images/";

    $this->cart_url = OSCOMMERCEURL.'/catalog/handle_cart.php';

    $this->osc_sid = 0;    // this will be set after we had a shopping cart or login action


    if (!is_array($res_arr)|| count($res_arr) == 0) {
      $now = time();
      $now = date('d.m. H:M',$now);
      $msg = "($now) DATABASE CONNECTION FAILED for shop_id=$this->shop_id maybe too many connections?".json_encode($res_arr);
      fb($msg);
      throw new Exception($msg);
    }
    // show connection data
    //  		    if (sizeof($res_arr) > 0) {
    //  		        fb("ShopDb_data:".sizeof($res_arr).":$shop_id:{$res_arr[0]->vchUrl},{$res_arr[0]->vchUsername}, {$res_arr[0]->vchPassword}, {$res_arr[0]->vchDbName}, {$res_arr[0]->vchHost}");
    //  		    }
    $this->shop_db  = new wpdb($res_arr[0]->vchUsername, $res_arr[0]->vchPassword, $res_arr[0]->vchDbName, $res_arr[0]->vchHost);
    if (empty($this->shop_db))
    fb('SHOPDB_Object is EMPTY !!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!');
    //		    else     // fails with json warning
    //  				fb('SHOPDB_Object:'.json_encode($this->shop_db));
    return $this->shop_db;
  }

  /////////////////////////////////////////////////////////////////////////////////////
  // show product box content as HTML
  function show_product_box ($j, $product, $shop_url) { ?>
<div class="product-box post hentry type-post status-publish format-standard">
  <div class="product-thumb">
    <a href="?pid=<?php echo $product->products_id;?>#infotext" class="product-thumb"> <img src="<?php echo $product->products_image_url;?>"
      alt="<?php echo $product->products_name; ?>" title="<?php echo $product->products_name; ?>"
    /> </a>
  </div>
  <div class="product-info clear">
    <span class="product-model"><?php echo $product->products_model;?> </span><br /> <span class="product-title"><?php echo $product->products_name;?>
    </span> <span class="product-id"><?php echo $product->products_id;?> </span>
  </div>
</div>
<!-- end product-box -->
  <?php
  } // EOF show_product_box

  /////////////////////////////////////////////////////////////////////////////////////
  // write the tabs for the formats
  function osc_show_format_tabs() {
    if (empty($this->format)) $this->format = "Vinyl"; // debugging
    ?>
<div
  class="wp-tabs wpui-light wpui-styles"
>
  <!--  the product details DIV are right below the format tab header-->
  <div id="product-detail" class="product-detail"></div>
  <?php 	    foreach ($this->release_formats as $format) { ?>
  <div id="tab-<?php echo $format; ?>" class="ui-tabs-panel ui-widget-content ui-corner-bottom">
    <h3 class="wp-tab-title" style="display: none;">
    <?php echo $format; // no WS here!! ?>
    </h3>
    <div class="wp-tab-content">
    <?php
    if ($this->format == $format) {    // this format is the value from the query
      $this->osc_show_products_in_grid();
    }
    ?>
    </div>
  </div>
  <!-- wp-tab-content ui-tabs-panel -->
  <?php 	    } // foreach ?>
</div>
<!-- wp-tabs -->
  <?php
  } // EOF osc_show_format_tabs

  /////////////////////////////////////////////////////////////////////////////////////
  // write the tabs for the formats with UL list which is tabbed by .wptabs() and then filled with content
  // by own jquery code in @link products.js
  function osc_show_format_tabs_ajax() {
    if (empty($this->format)) $this->format = "Vinyl"; // debugging
    //    the product details DIV are right below the format tab header
    ?>
<div id="product-format-tabs"
	class="wp-tabs wpui-light wpui-styles widget">
	<div id="product-detail" class="product-detail"></div>
	<?php 	    foreach ($this->release_formats as $format) { 
	echo '
	<div id="'.$format.'" class="ui-tabs-panel ui-widget-content">
		<h3 class="wp-tab-title" style="display: none;">'.$format.'</h3>
		<div class="wp-tab-content"></div>';
		// show pagination only if more pages left.... (use class instead of id)
		if ($this->product_count > $this->records_per_page && $this->paged < $this->max_page) { ?>
		<div class="pagination">
			<span class="pages">loaded <span class="thispage"><?php echo $this->paged;?>
			</span> out of <span class="maxpage"><?php echo $this->max_page;?> </span>
				pages (<span class="loaded"><?php echo ($this->records_per_page*$this->paged);
				?> </span>/<span class="reccount"><?php echo $this->product_count;
				?> </span>&nbsp;items) </span> <a class="nextpostslink" href="#">LOAD
				MORE</a>
		</div>
		<?php           }; ?>
	</div>
	<?php         }; // foreach ?>
</div>
<!-- product-format-tabs -->
  <?php 	} // EOF osc_show_format_tabs_ajax

  /** return single MP3 tracks to an AJAX request -- get_xsell_products.php **/
  function osc_get_xsell_products($products_id) {
    $sql = "
            SELECT DISTINCT p.products_id          ,
                            p.products_model       ,
                            p.products_prelisten   ,
                            p.products_image       ,
                            p.products_for_free    ,
                            pd.products_name       ,
                            p.products_tax_class_id,
                            products_price
            FROM            products p
            LEFT JOIN 		products_xsell xp ON xp.xsell_id = p.products_id
            LEFT JOIN 		products_description pd ON pd.products_id = p.products_id
            WHERE           xp.products_id    =  '$products_id'
            AND             pd.language_id    = '2'
            AND             p.products_status = '1'
            ORDER BY        pd.products_name ASC
            ";

    $this->osc_get_shop_db ();    // get handle for shop database
    if (empty($this->shop_db)) throw new Exception('empty SHOP DB');

    $prod_xsell_query_results = $this->shop_db->get_results($sql); // returns array of objects
    fb('SQL:'.$sql.' found XSell Records:'.count($prod_xsell_query_results));

    if ($this->json)
    echo json_encode($prod_xsell_query_results);
    else
    return $prod_xsell_query_results;
  }

  /** return product formats to an AJAX request -- get_product_formats.php **/
  function osc_get_product_formats($products_model) {
    $sql = "
                SELECT p.products_id,
                       p.products_tax_class_id,
                       p.products_parent products_model,
                       pd.products_format,
                       IF(s.status, s.specials_new_products_price, p.products_price) AS products_price
                FROM   products p
                       LEFT JOIN specials s ON p.products_id = s.products_id
                       LEFT JOIN products_description pd ON p.products_id = pd.products_id
                WHERE  p.products_status = '1'
                       AND p.products_parent = '".$products_model."'
                       AND p.products_prelisten NOT LIKE '%.mp3%'
                       AND pd.products_format NOT LIKE '%MP3-Single%'
                       AND pd.language_id = '2'
                ORDER  BY pd.products_format ASC
         ";

    $this->osc_get_shop_db ();    // get handle for shop database
    if (empty($this->shop_db)) throw new Exception('empty SHOP DB');

    $prod_format_query_results = $this->shop_db->get_results($sql); // returns array of objects
    fb('SQL:'.$sql.' found Format Records:'. count($prod_format_query_results));
    foreach ($prod_format_query_results as $prod_format) {
      if ($prod_format->products_tax_class_id == 2) {
        $prod_format->products_price_taxed = + round($prod->products_price + ($prod->products_price * 19)/100, 2);
      }
      else if ($prod_format->products_tax_class_id == 1) {
        $prod_format->products_price_taxed = round($prod->products_price  + ($prod->products_price * 7)/100, 2);
      } else
      $prod_format->products_price_taxed = $prod->products_price;

    }
    if ($this->json)
    echo json_encode($prod_format_query_results);
    else
    return $prod_format_query_results;
  }
} // EOC class def
endif;
?>