<?php
/** this is our entry point for dealing with the oscommerce shopping cart
 * **/
require('wp-setupFramework.php');

////////////////////////////////////////////////////////////////////////////////////////////////
// setup Products_id & action
////////////////////////////////////////////////////////////////////////////////////////////////
// be flexible about where our vars come from
$action = $HTTP_GET_VARS['action'];
if (!isset($action))
  $action = $HTTP_POST_VARS['action'];
if (!isset($action)) throw new Exception('required action not provided!');

// be flexible about where our vars come from
$products_id = $HTTP_POST_VARS['products_id'];
if (!isset($products_id))
  $products_id = $HTTP_GET_VARS['products_id'];
if (!isset($products_id) && $action != 'return_cart') 
	throw new Exception('required product ID not provided!');

////////////////////////////////////////////////////////////////////////////////////////////////
// Shopping cart actions
if (isset($action)) {
  // redirect the customer to a friendly cookie-must-be-enabled page if cookies are disabled
  if ($session_started == false) {
    tep_redirect(tep_href_link(FILENAME_COOKIE_USAGE));
  }

  if (DISPLAY_CART == 'true') {
    $goto =  FILENAME_SHOPPING_CART;
    $parameters = array('action', 'cPath', 'products_id', 'pid');
  } else {
    $goto = basename($PHP_SELF);
    if ($action == 'buy_now') {
      $parameters = array('action', 'pid', 'products_id');
    } else {
      $parameters = array('action', 'pid');
    }
  }
  switch ($action) {

  	// customer adds a product from the products page
    case 'add_product' :
      if (isset($products_id) && is_numeric($products_id)) {
      	//small hack for salesbundles ????
      if($_POST['sb_productscount'] && $_POST['sb_productscount'] > 0){
        $sbct = $_POST['sb_productscount'];
        $salesbundle = $_POST['sb_productsids'.$sbct];
      }
      //end small salesbundlehack
      $cart->add_cart($products_id, $cart->get_quantity(tep_get_uprid($products_id, $HTTP_POST_VARS['id']))+1, $HTTP_POST_VARS['id'],true,$salesbundle);
    }
    break;

    // customer wants to decrement quantity for product in the shopping cart
    case 'remove_product' :
   		$qty = $cart->get_quantity($products_id);
//    		if ($qty == 1)
//    			$cart->remove($products_id);
//    		else
   			$cart->update_quantity($products_id, $qty - 1);
    break;

    // customer wants to remove a product completely from their shopping cart
    case 'delete_product' :
   		$cart->remove($products_id);
    break;
    case 'return_cart' :
    	// nothing to do
  }
}

/*

  require(DIR_WS_LANGUAGES . $language . '/' . FILENAME_PRODUCT_INFO);


  $sql = "select count(*) as total from products p, PRODUCTS_DESCRIPTION  pd where p.products_status = '1' and p.products_id = '$products_id' and pd.products_id = p.products_id and pd.language_id = '2'";
  $product_check_query = tep_db_query($sql);
  $product_check = tep_db_fetch_array($product_check_query);

  if ($product_check['total'] < 1) {
      echo "nothing found for ".$products_id;
  } else {
    // fetch the product...
    $sql = "select p.products_id, pd.products_name, pd.products_description, p.products_model, p.products_quantity, p.products_image_lrg, pd.products_url, p.products_price, p.products_tax_class_id, p.products_date_added, p.products_date_available, p.manufacturers_id from " . TABLE_PRODUCTS . " p, " . TABLE_PRODUCTS_DESCRIPTION . " pd where p.products_status = '1' and p.products_id = '" . (int)$products_id . "' and pd.products_id = p.products_id and pd.language_id = '2'";
    $product_info_query = tep_db_query($sql);
    $product_info = tep_db_fetch_array($product_info_query);

    // update the product counter when adding to cart...... seems OK  TODO check this
    tep_db_query("update " . TABLE_PRODUCTS_DESCRIPTION . " set products_viewed = products_viewed+1 where products_id = '" . (int)$products_id . "' and language_id = '2'");

    if ($new_price = tep_get_products_special_price($products_id)) {
      $products_price = '<s>' . $currencies->display_price($product_info['products_price'], tep_get_tax_rate($product_info['products_tax_class_id'])) . '</s> <span class="productSpecialPrice">' . $currencies->display_price($new_price, tep_get_tax_rate($product_info['products_tax_class_id'])) . '</span>';
    } else {
      $products_price = $currencies->display_price($product_info['products_price'], tep_get_tax_rate($product_info['products_tax_class_id']));
    }

    // complete the product_info Array
    $product_info['products_label'] = tep_get_products_label($products_id);
    $product_info['products_price'] = $products_price;

  }
  */
  header('Cache-Control: no-cache, must-revalidate');
  header('Content-type: application/json');
// collect things in result
  $result->customer_id = $_SESSION['customer_id'];
  $result->osCsid = tep_session_id();

  $cart->calculate();
  $result->cart = $cart;
    // and return them
  echo json_encode($result);
?>
