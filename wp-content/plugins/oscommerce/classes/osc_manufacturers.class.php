<?php
// TODO write manufacturer class and list method

if(!class_exists('osc_manufacturers')) :
require_once(OSCOMMERCECLASSPATH .'/osc_currencies.class.php');
require_once(OSCOMMERCECLASSPATH .'/osc_manufacturer_templates.class.php');

class osc_manufacturers extends osc_manufacturer_templates // DISPLAY OSC manufacturers
{
  var $records_per_page;
  var $cart_url;      // php page to handle OSC cart
  var $img_url;       // base url of images
  var $shop_url;      //
  var $result;		// query result
  var $osc_db;        // handle to wp_oscommerce table in wordpress DB
  var $osc_sid;       // session id to communicate with shop system NEEDS WORK
  var $shop_db;       // handle to oscommerce shopdb DB
  var $shop_id;
  var $sql;			// keep query for debugging
  var $label_id;
  var $artist_id;
  var $artist_set;	// which manufacturers to show
  var $artist_sets;
  var $artist_count;  // total
  var $format;
  var $paged;
  var $max_page;

  // url parameters use artist not manufacturer
  function osc_manufacturers()
  {
    $this->osc_db = new osc_db();
    $this->shop_id = $_GET['shopID'];
    $this->label_id = $_GET['labelId'];
    $this->artist_id = $_GET['artistId'];
    $this->artist_set = $_GET['artistSet'];
    $this->format = $_GET['format'];
    $this->paged = $_GET['paged'];
    $this->json = $_GET['json'];
    $this->records_per_page = $_GET['pagesize'];
    $this->format = $_GET['format'];
    $this->paged = $_GET['paged'];
    // defaults
    if (empty($this->shop_id)) $this->shop_id = 1;     	// fix shop id
    if (empty($this->records_per_page)) $this->records_per_page = 9;
    if (empty($this->format)) $this->format = 'Vinyl';
    if (empty($this->artist_set)) $this->artist_set = 'main';
    if (empty($this->paged)) $this->paged = 1;	// default is first page
    $this->artist_sets = array('Main','Alumni');
  }

  // limit counting to same conditions as display
  function osc_count_manufacturers_from($from)
  {
    //			fbDebugBacktrace();
    $select = 'SELECT COUNT(DISTINCT m.manufacturers_id) AS cnt ';
    $sql = $select . $from;
    $this->artist_count = $this->shop_db->get_var($sql);
    fb("manuCountFrom({$sql}):".$this->artist_count);
  }

  /////////////////////////////////////////////////////////////////////////////////////
  // list manufacturers according to parms set in oscManufacturers object
  /** entrypoint called from osCommerce.php **/
  /** method called from osCommerce.php **/
  function osc_show_tabbed_manufacturers_page() {
    $this->result = $this->osc_query_manufacturers();        // result as a member var?
    if (empty($this->result)) {
      $now = date(DATE_RFC822);
      $msg ="No Artists found !! ($now)";
      fb($msg + $this->sql);
      echo "<h3>$msg</h3><pre>" . $this->sql . "</pre>";
    } else {
      fb('read '. $this->shop_db->num_rows . ' records from ' .$this->artist_count. ' manufacturers in DB');
      $this->osc_inject_manufacturer_list_json();    // first page of data
      $this->osc_inject_all_manufacturer_templates();
      // show the tabs for releases
      $current_tab = $this->osc_show_artist_set_tabs_ajax();
    }
  }

  /////////////////////////////////////////////////////////////////////////////////////
  /** answer AJAX request for tab content and show either the grid with manufacturers as HTML
   *  or return the data as json (get_product_page.php) **/
  function osc_get_manufacturers_page() {
    $this->result = $this->osc_query_manufacturers();        // result as a member var?
    if (empty($this->result)) {
      $now = date(DATE_RFC822);
      $msg ="No Records found for manu_query_result ($now): $this->sql";
      fb($msg);
      if (!$this->json)	echo "<h3>$msg</h3>";
      else 				echo $msg;
    } else {
      fb('read '. $this->shop_db->num_rows . ' records from ' .$this->artist_count. ' manufacturers in DB');
      $this->osc_fix_manufacturer_list_json();    // add extra fields to result list items
      if ($this->json) {
        $retval = array($this->records_per_page,$this->artist_count, $this->max_page, $this->result);
        // encode tuple of pagesize, count and result
        echo json_encode($retval);
      } else {
        // show product grid as HTML
        $this->osc_show_manufacturers_in_grid();
      }
    }
  }

  /////////////////////////////////////////////////////////////////////////////////////
  // insert the product data as jquery array using JSON in the header
  // var $product[format] = [{"products_id":"3493","products_image":"vhr-45-009_kl.jpg","products_name":"Long Distance Calling \/ Satellite Bay","products_model":"vhr-45-009","products_weight":"0.00","products_quantity":"0","products_price":"0.0000","manufacturers_id":"302","manufacturers_name":"Long Distance Calling","manufacturers_image":null,"products_tax_class_id":"0","specials_new_products_price":null,"final_price":"0.0000","products_description":"info coming soon<br \/>"},{"products_id":"3489","products_image":"vhr-45-001_kl.jpg","products_name":"Troum \/ Nargis","products_model":"vhr-45-002","products_weight":"0.00","products_quantity":"0","products_price":"0.0000","manufacturers_id":"301","manufacturers_name":"Troum","manufacturers_image":null,"products_tax_class_id":"0","specials_new_products_price":null,"final_price":"0.0000","products_description":"This is part one of the &quot;Landscapes Single Series&quot;<br \/>"}];
  function osc_inject_manufacturer_list_json() {
    $this->osc_fix_manufacturer_list_json();
    // TODO script injection not working yet
    wp_localize_script( 'manufacturer', 'oscArtistList', $this->result);
    ?>

<script type="text/javascript">// initial JSON injection, next ones get loaded by AJAX
//  var products = {}; // for robustness as
	var oscShopUrl = "<?php echo $this->shop_url ?>";  
  var manufacturersCount = <?php echo $this->artist_count ?>;
  var manufacturersPageSize = <?php echo $this->records_per_page ?>;
  var manufacturersSets = <?php echo $this->artist_sets ?>;
  var manufacturers = {};
  manufacturers['<?php echo strtolower($this->artist_set) ?>'] = <?php echo json_encode($this->result); ?>;
</script>
    <?php
    /*        $ids = array();
     foreach ($this->result as $product) $ids->push($product->products_id);

     fb('injected '.count($this->result).' products for '.$this->format.' using JSON: '. implode("-",$ids));
     */
  }

  /////////////////////////////////////////////////////////////////////////////////////
  // add missing fields to manufacturer list
  function osc_fix_manufacturer_list_json() {
    $i=0; // add missing fields to manufacturers array before jsoning it
    foreach ($this->result as $manufacturer) {
      $manufacturer->manufacturers_index = $i++;
      if ($manufacturer->manufacturers_image) {
        $manufacturer->manufacturers_image_url = rtrim($this->shop_url, '/') ."/images/". $manufacturer->manufacturers_image;
      } else // empty image link
      $manufacturer->manufacturers_image_url = "/wp-content/plugins/oscommerce/images/no_image.gif";

      if ($manufacturer->manufacturers_image_lrg){
        $manufacturer->manufacturers_image_lrg_url = rtrim($this->shop_url, '/') ."/images/". $manufacturer->manufacturers_image_lrg;
      } else // empty image link
      $manufacturer->manufacturers_image_lrg_url = "/wp-content/plugins/oscommerce/images/no_image.gif";
    }
  }

  //////////////////////////////////////////////////////////////////////////////////////
  // cat_id == labelID .... man_id == manufacturerID... format == manufacturerFormat .... paged pageNumber
  function osc_query_manufacturers()
  {
    //		    fbDebugBacktrace();
    $this->osc_get_shop_db ();    // get handle for shop database
    if (empty($this->shop_db)) {
      fb('could not get handle for SHOP DB');
      throw new Exception('empty SHOP DB');
    }

    $select = 'SELECT
						m.manufacturers_id,
						m.manufacturers_name,
						m.manufacturers_label,
						m.manufacturers_image,
						m.manufacturers_image_med,
						m.manufacturers_press_image,
						min.manufacturers_url,
						min.manufacturers_description,
           				p.products_id,
						p.products_image,
						p.products_image_lrg,
 						pd.products_name,
						p.products_model,
						p.products_weight,
						p.products_quantity,
						p.products_price,
						p.manufacturers_id
						';
    $from = ' FROM manufacturers m
				JOIN manufacturers_info min ON m.manufacturers_id = min.manufacturers_id
				JOIN products p ON m.manufacturers_id = p.manufacturers_id
				JOIN products_description pd ON pd.products_id = p.products_id
				';    
    $where = " WHERE m.manufacturers_image <> '' "; // only with images
    // only compare ints with 0 !!!!!
    if(!empty($this->label_id) && $this->label_id != 0) {
      $where .= " AND m.manufacturers_label LIKE '%{$this->label_id}%' ";
    }
    // TODO do selection better than with entry in m.manufacturers_label
    if(!empty($this->artist_set)) {    	
    	$label = ($this->artist_set == 'main')?"'%244%'":"'%245%'";
   		$where .= " AND m.manufacturers_label LIKE $label";
    }
    $group =  " GROUP BY p.manufacturers_id ";

    $order = ' ORDER BY m.manufacturers_id ASC';
    // TODO DO/DONT include database tables from oscommerce installation
    //			require OSCOMMERCE_DOC_ROOT."/includes/database_tables.php";
    // from shopkatapult/index.php:#141
    $sql = $select . $from . $where . $group . $order;

    /* CALCULATE PAGING */
    // GET TOTAL AMOUNT OF PRODUCTS and store it in manufacturer count
    // this only works without the group by....
    $this->osc_count_manufacturers_from($from.$where);

    if($this->artist_count > 0)
    {
      $this->max_page = ceil($this->artist_count/$this->records_per_page);
      if($this->paged > $this->max_page) $this->paged = $this->max_page;
      $firstRecordOfPage = $this->records_per_page * ($this->paged - 1);
      if(!empty($this->paged))
      $sql = sprintf('%s LIMIT %d, %d', $sql, $firstRecordOfPage, $this->records_per_page);
    } else {    
    	//#####################################################################
    	// WORKAROUND for artist_sets when nothing found
    	//#####################################################################
    	if(!empty($this->artist_set)) {
    		$where = " WHERE m.manufacturers_image <> '' "; // only with images
    		if ($this->artist_set == 'main')
    			$where .= " AND (m.manufacturers_label LIKE '%999%' OR m.manufacturers_label LIKE '%32%') ";
    		else
    			$where .= " AND (m.manufacturers_label LIKE '%33%' OR m.manufacturers_label LIKE '%22%') ";
    		$sql = $select . $from . $where . $group . $order;
    		$this->osc_count_manufacturers_from($from.$where);
    		if($this->artist_count > 0)
    		{
    			$this->max_page = ceil($this->artist_count/$this->records_per_page);
    			if($this->paged > $this->max_page) $this->paged = $this->max_page;
    			$firstRecordOfPage = $this->records_per_page * ($this->paged - 1);
    			if(!empty($this->paged))
    				$sql = sprintf('%s LIMIT %d, %d', $sql, $firstRecordOfPage, $this->records_per_page);
    		}
    	}
    	//#####################################################################
    	// EOW
    	//#####################################################################    	
   	}
    /////////////// QUERY
    $query_results = $this->shop_db->get_results($sql); // returns array of objects
    fb('SQL:'.$sql.' read # Records: '.$this->shop_db->num_rows);
    $this->result = $query_results;
    $this->sql = $sql;
    return $query_results;
  }

  ////////////////////////////////////////////////////////////////////////////////////////////////
  // make HTML from the query OBSOLETE
  function osc_show_manufacturers_in_grid() {
    if(count($this->result) > 0)
    {
      /* DISPLAY PAGING LINKS */
      if($this->artist_count > $this->records_per_page)
      $page_links = paginate_links(array('base'    => add_query_arg(array('paged' => '%#%')),
													   'format'  => '',
													   'total'   => $this->max_page,
													   'current' => $this->paged));
      $currencies = new osc_currencies($this->shop_db);
      if ($this->format == "") $this->format ="All Format";
      echo '<div id="loop" class="grid clear">';
      if (! $this->bare)
      echo "<span class=\"recordCount\">$this->artist_count items</span>";
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
    }// if

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

    $this->img_url = rtrim($this->shop_url, '/ ') ."/images/";

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
    if (sizeof($res_arr) > 0) {
      fb("ShopDb_data:".sizeof($res_arr).":$shop_id:{$res_arr[0]->vchUrl},{$res_arr[0]->vchUsername}, {$res_arr[0]->vchPassword}, {$res_arr[0]->vchDbName}, {$res_arr[0]->vchHost}");
    }
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
  // write the tabs for the artist_sets
  function osc_show_artist_set_tabs() {
    if (empty($this->artist_set)) $this->artist_set = "Main"; // debugging
    ?>
<div
  class="wp-tabs wpui-styles"
>
  <!--  the product details DIV are right below the format tab header-->
  <div id="artist-details" class="product-details"></div>
  <?php 	    foreach ($this->artist_sets as $artist_set) { ?>
  <div id="tab-<?php echo $artist_set; ?>" class="ui-tabs-panel ui-widget-content ui-corner-bottom">
    <h3 class="wp-tab-title" style="display: none;"><?php echo $artist_set; // no WS here!! ?></h3>
    <div class="wp-tab-content"><?php
            if ($this->artist_set == $artist_set) {    // this format is the value from the query
              $this->osc_show_manufacturers_in_grid();
            }
    ?></div>
  </div>
  <!-- wp-tab-content ui-tabs-panel -->
  <?php 	    } // foreach ?>
</div>
<!-- wp-tabs -->
  <?php
  } // EOF osc_show_artist_set_tabs

  /////////////////////////////////////////////////////////////////////////////////////
  // write the tabs for the artist_sets with UL list which is tabbed by .wptabs() and then filled with content
  // by own jquery code in @link products.js
  function osc_show_artist_set_tabs_ajax() {
    if (empty($this->artist_set)) $this->artist_set = "Main"; // debugging
    //    the product details DIV are right below the artist_set tab header
    ?>
<div id="artist-set-tabs" class="wp-tabs wpui-styles widget">
  <div id="artist-detail" class="artist-detail"></div>
  <?php 	    foreach ($this->artist_sets as $artist_set) { ?>
  <div id="<?php echo $artist_set;?>" class="ui-tabs-panel ui-widget-content">
    <h3 class="wp-tab-title" style="display: none;"><?php echo $artist_set;// NO WS around this!! ?></h3>
    <div class="wp-tab-content"></div>
    <?php
    // show pagination only if more pages left.... (use class instead of id)
    if ($this->artist_count > $this->records_per_page && $this->paged < $this->max_page) { ?>
    <div class="pagination">
      <span class="pages">loaded <span class="thispage"><?php echo $this->paged;?> </span> out of <span class="maxpage"><?php echo $this->max_page;?>
      </span> pages (<span class="loaded"><?php echo ($this->records_per_page*$this->paged);
      ?> </span>/<span class="reccount"><?php echo $this->artist_count;
      ?> </span>&nbsp;items) </span> <a class="nextpostslink" href="#">LOAD MORE</a>
    </div>
    <?php           }; ?>
  </div>
  <?php         }; // foreach ?>
</div>
<!-- product-artist_set-tabs-alt -->
  <?php 	} // EOF osc_show_artist_set_tabs_ajax

 function osc_list_manufacturers($db, $shop_id, $label_id)
  {
    fbDebugBacktrace();
    // TODO try to fix shopId to save queries
    $shopSql = 'SELECT vchUrl, vchUsername, vchPassword, vchDbName, vchHost
						FROM wp_oscommerce WHERE intShopId = '. $shop_id;

    $res_arr  = $db->get_results($shopSql);

    $shop_url = $res_arr[0]->vchUrl;
    $this->shop_db  = new wpdb($res_arr[0]->vchUsername, $res_arr[0]->vchPassword, $res_arr[0]->vchDbName, $res_arr[0]->vchHost);
    fb('SHOPDB_Object:'.gettype($this->shop_db));
    //			fb('SHOPDB_Object:'.json_encode($this->shop_db));
    // ================================================================================================
    // TODO adapt query to manufacturers --- add MAIN/ALumni as label_ids?
    // osc plugin select
    $select = 'SELECT
						m.manufacturers_id,
						m.manufacturers_name,
						m.manufacturers_label,
						m.manufacturers_image,
						m.manufacturers_image_med,
						m.manufacturers_press_image,
						min.manufacturers_url,
						min.manufacturers_description ';
    $from = 'FROM manufacturers m
					 JOIN manufacturers_info min ON m.manufacturers_id = min.manufacturers_id ';
    if(!empty($label_id) && $label_id != 0)
    $where = "WHERE m.manufacturers_label LIKE '%{$label_id}%' AND m.manufacturers_image <> ''";
    else
    $where = "WHERE m.manufacturers_image <> ''"; // only with images

    // include database tables from oscommerce installation
    //			require OSCOMMERCE_DOC_ROOT."/includes/database_tables.php";
    // from shopkatapult/index.php:#141
    $sql = $select . $from . $where . $order;

    /* CALCULATE PAGING */
    // GET TOTAL AMOUNT OF manufacturers
    $this->osc_count_manufacturers_from($from.$where);

    if($this->record_count > 0)
    {
      $max_page = ceil($this->record_count/$this->records_per_page);

      if($_GET['paged'] > $max_page) $paged = $max_page;

      $firstRecordOfPage = $this->records_per_page * ($_GET['paged'] - 1);

      if(!empty($_GET['paged']))
      $sql = sprintf('%s LIMIT %d, %d', $sql, $firstRecordOfPage, $this->records_per_page);
    }
    fb('SQL:'.$sql.' recCNT:'.$this->record_count);
    $res_prods = $this->shop_db->get_results($sql); // returns array of objects
    fb("osc_list_manufacturers(shop_db, $shop_id, $label_id):". $this->shop_db->num_rows);
    if($this->shop_db->num_rows > 0)
    {
      $osCsid = md5('product_session');

      /* DISPLAY PAGING LINKS */
      if($this->record_count > $this->records_per_page)
      $page_links = paginate_links(array('base'    => add_query_arg(array('paged' => '%#%')),
													   'format'  => '',
													   'total'   => $max_page,
													   'current' => $_GET['paged']));

      $currencies = new osc_currencies($this->shop_db);
      // this is where the product details are shown
      ?>
<div id="prod-details" class="product-details"></div>
<div class="wp-tabs wpui-styles">
  <div class="ui-tabs ui-widget ui-widget-content ui-corner-all">
    <ul class="ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all">
      <li class="ui-state-default ui-corner-top first-li"><a href="#manufacturers">manufacturers</a></li>
      <li class="ui-state-default ui-corner-top last-li"><a href="#alumni">Alumni Roster</a></li>
    </ul>

    <div class="ui-tabs-panel ui-widget-content ui-corner-bottom ui-tabs-hide" id="manufacturers">
      <div class="wp-tab-content tab-1"></div>
    </div>
    <div class="ui-tabs-panel ui-widget-content ui-corner-bottom ui-tabs-hide" id="alumni">
      <div class="wp-tab-content tab-2"></div>
    </div>
  </div>
  <!-- end div.wp-tab-content -->
</div>
      <?php
      // use grid formatting similar to category page with posts (however in a different hierarchy)
      echo '<div id="loop" class="grid clear">';
      for($j = 0 ; $j < count($res_prods) ; $j++)
      {
        echo '<div class="product-box product-box-'.$j.' post hentry type-post status-publish format-standard">
							<div class="product-thumb">';

        // image url needs http prefix to load from other site
        if(!empty($res_prods[$j]->manufacturers_image)) {
          $imageUrl = "http://".rtrim($shop_url, '/') ."/images/". $res_prods[$j]->manufacturers_image;
          $prodUrl  = "http://".rtrim($shop_url, '/') ."/product_info.php?manufacturers_id=".$res_prods[$j]->manufacturers_id;
          echo '<img src="'. $imageUrl .'" alt="'. $res_prods[$j]->manufacturers_name .'" title="'. $res_prods[$j]->manufacturers_name .'" style="border:none;" onClick="showManufacturer();">';
        } else
        echo '<img src="'. OSCOMMERCEIMAGESURL .'/no_image.gif" alt="No Image" title="No Image" style="border:none;">';

        echo '</div>';
        // short text under image
        echo '<div class="product-info clear">';
        if(!empty($res_prods[$j]->manufacturers_model))
        echo '<span class="product-model">'. $res_prods[$j]->manufacturers_model .'</span><br/>';
        echo '<span class="product-title">'. $res_prods[$j]->manufacturers_name .'</span>
						</div>
					</div>';

        /*
         if(!empty($res_prods[$j]->manufacturers_model))
         echo 'Model: '. $res_prods[$j]->manufacturers_model .'<br>';

         if(!empty($res_prods[$j]->manufacturers_weight))
         echo 'Weight: '. $res_prods[$j]->manufacturers_weight .'<br>';

         echo 'No. Available: '. $res_prods[$j]->manufacturers_quantity .'<br>';

         if(!empty($res_prods[$j]->specials_new_manufacturers_price))
         echo 'Was: <s>'. $currencies->display_price($res_prods[$j]->manufacturers_price, $currencies->get_tax_rate($res_prods[$j]->manufacturers_tax_class_id)) .'</s> Now: <span class="product-special">'. $currencies->display_price($res_prods[$j]->specials_new_manufacturers_price, $currencies->get_tax_rate($res_prods[$j]->manufacturers_tax_class_id)) .'</span><br>';
         else
         echo 'Price: '. $currencies->display_price($res_prods[$j]->manufacturers_price, $currencies->get_tax_rate($res_prods[$j]->manufacturers_tax_class_id)) .'<br>';

         if(!empty($res_prods[$j]->manufacturers_description))
         echo '<br>'. $res_prods[$j]->manufacturers_description;

         echo '</p>
         <div class="bottom"><a href="javascript:void(0);" onClick="javascript: window.open(\''. wp_nonce_url($shop_url. 'checkout_shipping.php/cPath/'. $label_id .'/sort/2a/action/buy_now/manufacturers_id/'. $res_prods[$j]->manufacturers_id .'?osCsid='. $osCsid) .'\', \'cart\', \'menubar=no, resizable=yes, status=no, toolbar=no, scrollbars=yes, top=0, left=0, width=640, height=500\');" title="Buy Now"><img src="'. OSCOMMERCEIMAGESURL .'/button_buy_now.gif" alt="Buy Now" title="Buy Now" style="border:none;"></a></div>
         </div>
         </div>'; // end post
         // no line underneath a row
         //						<div style="clear:both; padding-top:5px; margin-bottom:20px; border-bottom:1px solid #EDEDED;"></div>';
         *
         */
      }
      echo '</div>';  // finish loop
      // page links at bottom
      if($page_links)
      echo '<div class="tablenav-pages pagination">'. $page_links .'</div>';


      unset($currencies);
    }

    unset($this->shop_db);
  }
}
endif;
?>