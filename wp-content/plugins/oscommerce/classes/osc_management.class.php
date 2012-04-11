<?php
if(!class_exists('osc_management')) :
class osc_management extends osc_db // DASHBOARD MANAGEMENT AND WIDGET CONTROL
{
    function osc_management()
    {
        //MAKE SURE PARENT VARIABLES AND FUNCTIONS ARE INITIALISED
        osc_db::osc_db();
    }

    function display()
    {
        //			fbDebugBacktrace();
        if($_SERVER['REQUEST_METHOD'] == 'POST')
        {
            switch($_POST['form_name'])
            {
                case 'frm_osc_add':
                    $confirm = $this->osc_add($_POST);

                    if(!$confirm['ok'])
                    {
                        $_GET['message'] = $confirm['message'];
                        $this->osc_add_form();
                    }
                    else
                    {
                        wp_redirect('?page=osCommerce&message='. urlencode($confirm['message']));
                        exit();
                    }
                    break;

                case 'frm_osc_edit':
                    $confirm = $this->osc_edit($_POST);

                    if(!$confirm['ok'])
                    {
                        $_GET = $_POST;
                        $_GET['message'] = $confirm['message'];
                        $this->osc_edit_form();
                    }
                    else
                    {
                        wp_redirect('?page=osCommerce&message='. urlencode($confirm['message']));
                        exit();
                    }
                    break;
            }
        }
        //			else
        //				$this->osc_listing();
    }

    // DISPLAY THE LIST OF SHOPS
    function osc_listing()
    {
        //			fbDebugBacktrace();
        // DYNAMICALLY BUILD THE SQL STATEMENT FROM INFO RETRIEVED IN osc_db
        $sql = 'SELECT '.
        $this->table_id_field .', ';

        $sql_cols = '';

        for($i = 0 ; $i < $this->db_col_count ; $i++)
        {
            if($this->oStruc['inlist'][$i] === true)
            $sql_cols.= $this->oStruc['colnam'][$i] .', ';
        }

        $sql_cols = substr($sql_cols, 0, (strlen($sql_cols) - 2));

        $sql.= $sql_cols;

        $sql.= ' FROM '.
        $this->table_name;

        $order = '';

        if(isset($_GET['sort_col']) && !empty($_GET['sort_col']))
        $col = $_GET['sort_col'];
        else
        $col = $this->default_sort_col;

        if($_GET['osc_action'] == 'sorting')
        {
            if(strpos($_GET['order'], $col) !== false)
            {
                if(strpos($_GET['order'], 'ASC') !== false)
                $ord = 'DESC';
                else
                $ord = 'ASC';
            }
            else
            $ord = $this->default_sort_ord;
        }
        else
        {
            if(isset($_GET['sort_ord']) && !empty($_GET['sort_ord']))
            $ord = $_GET['sort_ord'];
            else
            $ord = $this->default_sort_ord;
        }

        $order = $col .' '. $ord;

        $sql = sprintf('%s ORDER BY %s ', $sql, $order);

        $this->record_count = $this->query($sql);

        if($this->record_count > 0)
        {
            $max_page = ceil($this->record_count/$this->records_per_page);

            if($_GET['paged'] > $max_page) $paged = $max_page;

            $firstRecord = $this->records_per_page * ($_GET['paged'] - 1);

            if(!empty($_GET['paged']))
            $sql = sprintf('%s LIMIT %d, %d', $sql, $firstRecord, $this->records_per_page);

            $this->query($sql);
            ?>
            <?php if(isset($_GET['message'])) : ?>
<div id="message" class="updated fade">
  <p>
  <?php echo $_GET['message']; ?>
  </p>
</div>
  <?php $_SERVER['REQUEST_URI'] = remove_query_arg(array('message'), $_SERVER['REQUEST_URI']);
  endif;
  //fb('record_count  / records_per_page'. $this->record_count . $this->records_per_page);
  if($this->record_count > $this->records_per_page)
  $page_links = paginate_links(array('base'    => add_query_arg(array('paged' => '%#%', 'osc_action' => 'paging', 'order' => urlencode($order), 'sort_col' => $col, 'sort_ord' => $ord)),
								   'format'  => '',
								   'total'   => $max_page,
								   'current' => $_GET['paged']));

  if($page_links)
  echo '<div class="tablenav-pages">'. $page_links .'</div>';
  ?>
<table class="widefat">
  <thead class="osc-table-header">
    <tr>
    <?php
    for($i = 0 ; $i < $this->db_col_count ; $i++)
    {
        if($this->oStruc['inlist'][$i] === true)
        {
            ?>
      <th scope="col"><a
        href="?page=osCommerce&paged=<?php echo$_GET['paged'];?>&osc_action=sorting&order=<?php echo$order;?>&sort_col=<?php echo $this->oStruc['colnam'][$i];?>&sort_ord=<?php echo $ord;?>"
        title="<?php echo esc_attr($this->oStruc['nicenam'][$i]);?>"
      ><?php echo $this->oStruc['nicenam'][$i];?> </a></th>
      <?php
        }
    }
    ?>
      <th scope="col">Action</th>
    </tr>
  </thead>
  <tbody>
  <?php
  $res_arr = $this->last_result;

  for($i = 0 ; $i < count($res_arr) ; $i++)
  {
      $$table_id_field = $this->table_id_field;
      $intID = $res_arr[$i]->$$table_id_field;
      $class = 'alternate' == $class ? '' : 'alternate';
      ?>
    <tr class="<?php echo$class;?>">
    <?php
    for($j = 0 ; $j < $this->db_col_count ; $j++)
    {
        if($this->oStruc['inlist'][$j] === true)
        {
            $colnam = $this->oStruc['colnam'][$j];

            if($colnam == 'vchShopName')
            {
                ?>
      <td><strong><?php if(current_user_can('edit_post', $intID)){ ?><a class="row-title"
          href="?page=osCommerce-edit-form&osc_action=osc_edit&amp;intID=<?php echo$intID;?>"
          title="<?php echo esc_attr(sprintf(__('Edit "%s"'), $res_arr[$i]->$colnam)); ?>"
        ><?php echo$res_arr[$i]->$colnam;?> </a> <?php } else { echo $res_arr[$i]->$colnam; } ?> </strong></td>
        <?php
            }
            else
            {
                ?>
      <td><?php echo$res_arr[$i]->$colnam;?></td>
      <?php
            }
        }
    }
    ?>
      <td><?php if(current_user_can('delete_post', $intID)){ echo '<a href="javascript:void(0);" onClick="javascrip: confirm_delete(\''. wp_nonce_url('?page=osCommerce&osc_action=osc_delete&amp;intID='. $intID .'&amp;paged='. $_GET['paged'] .'&amp;order='. $order .'&amp;sort_col='. $col .'&amp;sort_ord='. $ord, 'delete-post_'. $intID) .'\');" title="Delete" style="position:relative; float:left;"><img src="'. OSCOMMERCEIMAGESURL .'/cross.png" alt="Delete" title="Delete" border="0" style="position:relative; float:left; margin-top:2px; margin-right:3px;"> '. __('Delete') .'</a>'; } ?>
      </td>
    </tr>
    <?php
  }
  ?>
  </tbody>
</table>
  <?php
        }
        else
        {
            ?>
<table class="widefat">
  <tbody>
    <tr>
      <td><?php _e('No shops found.'); ?></td>
    </tr>
  </tbody>
</table>
            <?php
        }
    }

    function osc_add_form()
    {
        ?>
        <?php if(isset($_GET['message'])) : ?>
<div id="message" class="updated fade">
  <p>
  <?php echo $_GET['message']; ?>
  </p>
</div>
  <?php $_SERVER['REQUEST_URI'] = remove_query_arg(array('message'), $_SERVER['REQUEST_URI']);
  endif; ?>
<div id="form">
  <fieldset>
    <legend>
    <?php _e('Add Shop','osCommerce'); ?>
    </legend>
    <div class="row">
      <font color="red" style="font-weight: bold;">*</font> Marks required fields
    </div>
    <form name="frm_add" action="?page=osCommerce" method="post" onSubmit="javascript: return frm_validate(this);">
      <div class="row">
        <label for="vchShopName"><font color="red">*</font> <?php _e('Shop Name','osCommerce'); ?> </label> <input type="text"
          name="vchShopName" id="vchShopName" value="<?php echo$_POST['vchShopName'];?>" class="text"
        >
      </div>
      <div class="row">
        <label for="vchUrl"><font color="red">*</font> <?php _e('Url','osCommerce'); ?> </label> <input type="text" name="vchUrl"
          id="vchUrl" value="<?php echo$_POST['vchUrl'];?>" class="text"
        >
      </div>
      <div class="row">
        <label for="vchUsername"><font color="red">*</font> <?php _e('Username','osCommerce'); ?> </label> <input type="text"
          name="vchUsername" id="vchUsername" value="<?php echo$_POST['vchUsername'];?>" class="text"
        >
      </div>
      <div class="row">
        <label for="vchPassword"><font color="red">*</font> <?php _e('Password','osCommerce'); ?> </label> <input type="text"
          name="vchPassword" id="vchPassword" value="<?php echo$_POST['vchPassword'];?>" class="text"
        >
      </div>
      <div class="row">
        <label for="vchDbName"><font color="red">*</font> <?php _e('DB Name','osCommerce'); ?> </label> <input type="text" name="vchDbName"
          id="vchDbName" value="<?php echo$_POST['vchDbName'];?>" class="text"
        >
      </div>
      <div class="row">
        <label for="vchHost"><font color="red">*</font> <?php _e('Host','osCommerce'); ?> </label> <input type="text" name="vchHost"
          id="vchHost" value="<?php echo$_POST['vchHost'];?>" class="text"
        >
      </div>
      <p class="submit">
        <input type="submit" name="submit" value="<?php _e('Add Shop','osCommrece'); ?> &raquo;">
      </p>
      <input type="hidden" name="form_name" id="form_name" value="frm_osc_add">
    </form>
  </fieldset>
</div>
    <?php
    }

    function osc_edit_form()
    {
        $intID = $_GET['intID'];
        $shop  = $this->get_shop($intID);
        $shop  = $shop[0];
        ?>
        <?php if(isset($_GET['message'])) : ?>
<div id="message" class="updated fade">
  <p>
  <?php echo $_GET['message']; ?>
  </p>
</div>
  <?php $_SERVER['REQUEST_URI'] = remove_query_arg(array('message'), $_SERVER['REQUEST_URI']);
  endif; ?>
<div id="form">
  <fieldset>
    <legend>
    <?php _e('Edit Shop','osCommerce'); ?>
    </legend>
    <div class="row">
      <font color="red" style="font-weight: bold;">*</font> Marks required fields
    </div>
    <form name="frm_edit" method="post" action="?page=osCommerce" onSubmit="javascript: return frm_validate();">
      <div class="row">
        <label for="vchShopName"><font color="red">*</font> <?php _e('Shop Name','osCommerce'); ?> </label> <input type="text"
          name="vchShopName" id="vchShopName" value="<?php echostripslashes($shop->vchShopName);?>" class="text"
        >
      </div>
      <div class="row">
        <label for="vchUrl"><font color="red">*</font> <?php _e('Url','osCommerce'); ?> </label> <input type="text" name="vchUrl"
          id="vchUrl" value="<?php echostripslashes($shop->vchUrl);?>" class="text"
        >
      </div>
      <div class="row">
        <label for="vchUsername"><font color="red">*</font> <?php _e('Username','osCommerce'); ?> </label> <input type="text"
          name="vchUsername" id="vchUsername" value="<?php echostripslashes($shop->vchUsername);?>" class="text"
        >
      </div>
      <div class="row">
        <label for="vchPassword"><font color="red">*</font> <?php _e('Password','osCommerce'); ?> </label> <input type="text"
          name="vchPassword" id="vchPassword" value="<?php echostripslashes($shop->vchPassword);?>" class="text"
        >
      </div>
      <div class="row">
        <label for="vchDbName"><font color="red">*</font> <?php _e('DB Name','osCommerce'); ?> </label> <input type="text" name="vchDbName"
          id="vchDbName" value="<?php echostripslashes($shop->vchDbName);?>" class="text"
        >
      </div>
      <div class="row">
        <label for="vchHost"><font color="red">*</font> <?php _e('Host','osCommerce'); ?> </label> <input type="text" name="vchHost"
          id="vchHost" value="<?php echostripslashes($shop->vchHost);?>" class="text"
        >
      </div>
      <p class="submit">
        <input type="submit" name="submit" value="<?php _e('Update Shop','osCommerce'); ?> &raquo;">
      </p>
      <input type="hidden" name="form_name" id="form_name" value="frm_osc_edit"> <input type="hidden" name="intID" id="intID"
        value="<?php echo$intID;?>"
      >
    </form>
  </fieldset>
</div>

    <?php
    }

    function widget_control()
    {
        $options = get_option('widget_osCommerce');

        if(!is_array($options))
        {
            $options = array();
            $options['title'] = _e('osCommerce', 'osCommerce');
        }

        if($_POST['oscommerce']['submit'])
        {
            unset($_POST['oscommerce']['submit']);

            foreach($_POST['oscommerce'] as $key => $option)
            {
                $options[$key] = strip_tags(stripslashes($option));
            }

            update_option('widget_osCommerce', $options);
        }

        $title = htmlspecialchars($options['title'], ENT_QUOTES);

        echo '<p style="text-align:center;"><label for="oscommerce-title">'. __('Title', 'osCommerce') .': <input style="width: 200px;" id="oscommerce-title" name="oscommerce[title]" type="text" value="'. $title .'"></label></p>';
        echo '<input type="hidden" id="oscommerce-submit" name="oscommerce[submit]" value="1">';
    }
}
endif;
?>