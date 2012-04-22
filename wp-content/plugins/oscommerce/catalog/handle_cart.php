<?php
/** this is our entry point for dealing with the oscommerce shopping cart
 * **/
require_once ('debug.php');

foreach($_POST as $key => $value) { fb('$_POST['.$key.']='.$value); }
foreach($_GET as $key => $value) { fb('$_GET['.$key.']='.$value); }

require('includes/setupFramework.php');
fb('id: '. $HTTP_POST_VARS['id']);

////////////////////////////////////////////////////////////////////////////////////////////////
// setup Products_id & action
////////////////////////////////////////////////////////////////////////////////////////////////
// be flexible about where our vars come from
$products_id = $HTTP_POST_VARS['products_id'];
if (!isset($products_id))
  $products_id = $HTTP_GET_VARS['products_id'];
if (!isset($products_id)) throw new Exception('required product ID not provided!');

// be flexible about where our vars come from
$action = $HTTP_GET_VARS['action'];
if (!isset($action))
  $action = $HTTP_POST_VARS['action'];
if (!isset($action)) throw new Exception('required action not provided!');

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
   		fb('add  product ' . $products_id);
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
   		fb('remove_product '.$products_id);
   		$qty = $cart->get_quantity($products_id);
   		$cart->update_quantity($products_id, $qty - 1);
    break;

    // customer wants to remove a product completely from their shopping cart
    case 'delete_product' :
   		fb('delete_product '.$products_id);
   		$cart->remove($products_id);
    break;

    // performed by the 'buy now' button in product listings and review page
    case 'buy_now' :        if (isset($products_id)) {
    	// check if product selection is incomplete (i.e. product needs more attributes)
      if (tep_has_product_attributes($products_id)) {
      	fb('products has attributes');
        tep_redirect(tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $products_id));
      } else {
        $cart->add_cart($products_id, $cart->get_quantity($products_id)+1);
      }
    }
//    tep_redirect(tep_href_link($goto, tep_get_all_get_params($parameters)));
    break;
    case 'notify' :
      if (tep_session_is_registered('customer_id')) {
        if (isset($products_id)) {
          $notify = $products_id;
        } elseif (isset($HTTP_GET_VARS['notify'])) {
          $notify = $HTTP_GET_VARS['notify'];
        } elseif (isset($HTTP_POST_VARS['notify'])) {
          $notify = $HTTP_POST_VARS['notify'];
        } else {
          tep_redirect(tep_href_link(basename($PHP_SELF), tep_get_all_get_params(array('action', 'notify'))));
        }
        if (!is_array($notify)) $notify = array($notify);
        for ($i=0, $n=sizeof($notify); $i<$n; $i++) {
          $check_query = tep_db_query("select count(*) as count from " . TABLE_PRODUCTS_NOTIFICATIONS . " where products_id = '" . $notify[$i] . "' and customers_id = '" . $customer_id . "'");
          $check = tep_db_fetch_array($check_query);
          if ($check['count'] < 1) {
            tep_db_query("insert into " . TABLE_PRODUCTS_NOTIFICATIONS . " (products_id, customers_id, date_added) values ('" . $notify[$i] . "', '" . $customer_id . "', now())");
          }
        }
        tep_redirect(tep_href_link(basename($PHP_SELF), tep_get_all_get_params(array('action', 'notify'))));
      } else {
        //      $navigation->set_snapshot();
        tep_redirect(tep_href_link(FILENAME_LOGIN, '', 'SSL'));
      }
      break;
    case 'notify_remove' :  if (tep_session_is_registered('customer_id') && isset($products_id)) {
      $check_query = tep_db_query("select count(*) as count from " . TABLE_PRODUCTS_NOTIFICATIONS . " where products_id = '" . $products_id . "' and customers_id = '" . $customer_id . "'");
      $check = tep_db_fetch_array($check_query);
      if ($check['count'] > 0) {
        tep_db_query("delete from " . TABLE_PRODUCTS_NOTIFICATIONS . " where products_id = '" . $products_id . "' and customers_id = '" . $customer_id . "'");
      }
      tep_redirect(tep_href_link(basename($PHP_SELF), tep_get_all_get_params(array('action'))));
    } else {
      $navigation->set_snapshot();
      tep_redirect(tep_href_link(FILENAME_LOGIN, '', 'SSL'));
    }
    break;
    case 'cust_order' :     if (tep_session_is_registered('customer_id') && isset($HTTP_GET_VARS['pid'])) {
      if (tep_has_product_attributes($HTTP_GET_VARS['pid'])) {
        tep_redirect(tep_href_link(FILENAME_PRODUCT_INFO, 'products_id=' . $HTTP_GET_VARS['pid']));
      } else {
        $cart->add_cart($HTTP_GET_VARS['pid'], $cart->get_quantity($HTTP_GET_VARS['pid'])+1);
      }
    }
    tep_redirect(tep_href_link($goto, tep_get_all_get_params($parameters)));
    break;
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
  // collect things in result
  $result['osCsid']=tep_session_id();
	$cart->calculate();
  $result['cart']=$cart;
  // and return them
  echo json_encode($result);
?>
