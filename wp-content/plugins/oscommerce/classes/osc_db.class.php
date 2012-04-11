<?php
if(!class_exists('osc_db')) :

class osc_db extends wpdb
{
    var $db_version;
    var $table_name;
    var $table_id_field;
    var $default_sort_col;
    var $default_sort_ord;
    var $db_col_count;
    var $oStruc;
    var $records_per_page;
    var $record_count;
    var $field_index;

    function osc_db()
    {
        //MAKE SURE PARENT VARIABLES AND FUNCTIONS ARE INITIALISED
        wpdb::__construct(DB_USER, DB_PASSWORD, DB_NAME, DB_HOST);

        $this->db_version       = '1.0';

        if(empty($this->prefix))
        $this->prefix       = 'wp_';

        $this->table_name       = $this->prefix . 'oscommerce';
        $this->table_id_field   = 'intShopId';
        $this->default_sort_col = 'vchShopName';
        $this->default_sort_ord = 'ASC';
        $this->records_per_page = 15;

        //fb('TABLE_NAME'.$this->table_name);
        // CHECK IF TABLE EXISTS
        if($this->get_var('SHOW TABLES LIKE \''. $this->table_name .'\'') == $this->table_name)
        {
            // GET TABLES FIELDS
            $this->db_col_count = $this->query('SHOW FIELDS FROM '. $this->table_name);

            $res_arr = $this->last_result;

            // DYNAMICALLY GENERATE FIELD LISTING, INDEX
            for($i = 0 ; $i < count($res_arr) ; $i++)
            {
                $this->oStruc['colnam'][$i] = $res_arr[$i]->Field;
                $this->field_index[$this->oStruc['colnam'][$i]] = $i;

                // DEFINES IF THE FIELD WILL BE INCLUDED IN THE LISTING
                if($this->oStruc['colnam'][$i] == 'vchShopName'
                || $this->oStruc['colnam'][$i] == 'vchUrl')
                $this->oStruc['inlist'][$i] = true;
                else
                $this->oStruc['inlist'][$i] = false;

                // DEFINES THE DISPLAY/READABLE NAME OF THE FIELD
                $tmp_str = substr($this->oStruc['colnam'][$i], 3, strlen($this->oStruc['colnam'][$i]));

                if(isset($cap))
                unset($cap);

                for($j = 0 ; $j <= strlen($tmp_str) ; $j++)
                {
                    $letter = substr($tmp_str, $j, 1);

                    if(ereg('[A-Z]', $letter))
                    {
                        $cap[] = $letter;
                    }
                }

                $tmp = preg_split('/[A-Z]/', $tmp_str, -1, PREG_SPLIT_NO_EMPTY);

                $nice_str = '';

                if(count($tmp) < count($cap))
                {
                    $nice_str_p = $cap[0];

                    $nice_str.= $nice_str_p;

                    for($j = 0 ; $j < count($tmp) ; $j++)
                    {
                        $nice_str_p = $cap[$j + 1].$tmp[$j];

                        $nice_str.= ' '.$nice_str_p;
                    }
                }
                else
                {
                    for($j = 0 ; $j < count($cap) ; $j++)
                    {
                        $nice_str_p = $cap[$j].$tmp[$j];

                        $nice_str.= ' '.$nice_str_p;
                    }
                }

                $this->oStruc['nicenam'][count($this->oStruc['nicenam'])] = __($nice_str, 'osCommerce');

                // DEFINES IF THE FIELD WILL BE INCLUDED IN THE FORM
                if($this->oStruc['colnam'][$i] != $this->table_id_field)
                $this->oStruc['inform'][$i] = true;
                else
                $this->oStruc['inform'][$i] = false;

                // DEFINES WHETHER THIS FIELD IS REQUIRED AND WILL DISPLAY A STAR NEXT TO IT
                $this->oStruc['reqrd'][$i] = true;

                // DEFINES THE FIELD'S DATA TYPE WHICH IS USER DEFINED AND LATER DETERMINS ACTIONS ON FIELD VALUES
                // POSSIBLE OPTIONS (in this plugin):
                /*
                 integer (mostly ignored, or used for sorting in lists)
                 string (for input type text)
                 */

                if($this->oStruc['colnam'][$i] == $this->table_id_field)
                $this->oStruc['dtype'][$i] = 'integer';
                else
                $this->oStruc['dtype'][$i] = 'string';
            }


        }
    }

    function create_tbl()
    {
        if($this->get_var('SHOW TABLES LIKE \''. $this->table_name .'\'') != $this->table_name)
        {
            $sql = 'CREATE TABLE '. $this->table_name . '(
							intShopId int(11) NOT NULL AUTO_INCREMENT,
							vchShopName VARCHAR(255) NOT NULL,
							vchUrl VARCHAR(255) NOT NULL,
							vchUsername VARCHAR(255) NOT NULL,
							vchPassword VARCHAR(255) NOT NULL,
							vchDbName VARCHAR(255) NOT NULL,
							vchHost VARCHAR(255) NOT NULL,
							PRIMARY KEY intShopId (intShopId)
						);';

            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

            dbDelta($sql);

            add_option('osc_db_version', $this->db_version);
        }

        $installed_ver = get_option('osc_db_version');

        if($installed_ver != $this->db_version)
        {
            $sql = 'CREATE TABLE '. $this->table_name .'(
							intShopId int(11) NOT NULL AUTO_INCREMENT,
							vchShopName VARCHAR(255) NOT NULL,
							vchUrl VARCHAR(255) NOT NULL,
							vchUsername VARCHAR(255) NOT NULL,
							vchPassword VARCHAR(255) NOT NULL,
							vchDbName VARCHAR(255) NOT NULL,
							vchHost VARCHAR(255) NOT NULL,
							PRIMARY KEY intShopId (intShopId)
						);';

            require_once(ABSPATH .'wp-admin/includes/upgrade.php');

            dbDelta($sql);

            update_option('osc_db_version', $this->db_version);
        }
    }

    function QuoteDBValue($dtype, $value)
    {
        $value = stripslashes($value);
        // *****************************************************************************
        // CLOSES THE VALUE SENT THROUGH UP WITH SINGLE QUOTES BASED ON THE DATA ...
        // ... TYPE SPECIFIED BY THE ENTITY DEFINITION
        // *****************************************************************************

        if(((strlen($dtype) >= 6) && (substr($dtype, 0, 6) == 'string')) || ($dtype == 'permissions') || ($dtype == 'enum'))
        {
            if(strpos($value, "'") !== false)
            $value = str_replace("'", "''", $value);

            if(empty($value))
            $value = 'NULL';
            else
            $value = "'". $value ."'";
        }
        elseif(($dtype == 'datetime') || ($dtype == 'calender') || ($dtype == 'date') || ($dtype == 'time'))
        {
            $value = "'". $value ."'";
        }
        elseif($dtype == 'boolean' && empty($value))
        {
            $value = 0;
        }
        elseif(empty($value))
        {
            $value = 'NULL';
        }

        return $value;
    }

    // CHECK FOR DUPLICATE SHOP NAME
    function osc_check_shop_name($vchShopName, $intID = 0)
    {
        if(strpos($vchShopName, "'") != false)
        $vchShopName = str_replace("'", "''", $vchShopName);

        if(!empty($intID))
        $sql = 'SELECT
							COUNT('. $this->table_id_field .') AS cnt
						FROM
							'. $this->table_name .'
						WHERE
							vchShopName = \''. $vchShopName .'\'
							AND '. $this->table_id_field .' != '. $intID;
        else
        $sql = 'SELECT
							COUNT('. $this->table_id_field .') AS cnt
						FROM
							'. $this->table_name .'
						WHERE
							vchShopName = \''. $vchShopName .'\'';

        if($this->get_var($sql) > 0)
        {
            $ret_arr['ok']      = false;
            $ret_arr['message'] = __('A shop by that name already exixsts. Please choose another.', 'osCommerce');
        }
        else
        {
            $ret_arr['ok']      = true;
            $ret_arr['message'] = __('Shop information was commited successfully.', 'osCommerce');
        }

        return $ret_arr;
    }

    // CHECK FOR DUPLICATE SHOP URL
    function osc_check_shop_url($vchUrl, $intID = 0)
    {
        if(strpos($vchUrl, "'") != false)
        $vchUrl = str_replace("'", "", $vchUrl);

        if(!empty($intID))
        $sql = 'SELECT
							COUNT('. $this->table_id_field .') AS cnt
						FROM
							'. $this->table_name .'
						WHERE
							vchUrl = \''. $vchUrl .'\'
							AND '. $this->table_id_field .' != '. $intID;
        else
        $sql = 'SELECT
							COUNT('. $this->table_id_field .') AS cnt
						FROM
							'. $this->table_name .'
						WHERE
							vchUrl = \''. $vchUrl .'\'';

        if($this->get_var($sql) > 0)
        {
            $ret_arr['ok']      = false;
            $ret_arr['message'] = __('A shop with that url already exixsts. Please enter another.', 'osCommerce');
        }
        else
        {
            $ret_arr['ok']      = true;
            $ret_arr['message'] = __('Shop information was commited successfully.', 'osCommerce');
        }

        return $ret_arr;
    }

    function osc_add($_request_variables)
    {
        $chk = $this->osc_check_shop_name($_request_variables['vchShopName']);

        if($chk['ok'])
        {
            $chk = $this->osc_check_shop_url($_request_variables['vchUrl']);

            if($chk['ok'])
            {
                $sql = 'INSERT INTO '. $this->table_name .'(';

                $sql_cols = '';

                for($i = 0 ; $i < $this->db_col_count ; $i++)
                {
                    if($this->oStruc['inform'][$i] === true)
                    $sql_cols.= $this->oStruc['colnam'][$i] .', ';
                }

                $sql_cols = substr($sql_cols, 0, (strlen($sql_cols) - 2));

                $sql.= $sql_cols;
                $sql.= ') VALUES(';

                $sql_cols = '';

                for($i = 0 ; $i < $this->db_col_count ; $i++)
                {
                    if($this->oStruc['inform'][$i] === true)
                    {
                        $value = $_request_variables[$this->oStruc['colnam'][$i]];

                        $value = $this->QuoteDBValue($this->oStruc['dtype'][$i], $value);

                        $sql_cols.= $value .', ';
                    }
                }

                $sql_cols = substr($sql_cols, 0, (strlen($sql_cols) - 2));

                $sql.= $sql_cols .')';

                // DEBUGGING
                //echo $sql;
                //exit();

                $this->query($sql);
            }
        }

        return $chk;
    }

    function osc_edit($_request_variables)
    {
        $chk = $this->osc_check_shop_name($_request_variables['vchShopName'], $_request_variables['intID']);

        if($chk['ok'])
        {
            $chk = $this->osc_check_shop_url($_request_variables['vchUrl'], $_request_variables['intID']);

            if($chk['ok'])
            {
                $sql = 'UPDATE '. $this->table_name .' SET ';

                $sql_cols = '';

                for($i = 0 ; $i < $this->db_col_count ; $i++)
                {
                    if($this->oStruc['inform'][$i] === true)
                    {
                        $colnam = $this->oStruc['colnam'][$i];
                        $value  = $_request_variables[$colnam];

                        $value = $this->QuoteDBValue($this->oStruc['dtype'][$i], $value);

                        $sql_cols.= $colnam .' = '. $value .', ';
                    }
                }

                $sql_cols = substr($sql_cols, 0, (strlen($sql_cols) - 2));

                $sql.= $sql_cols;

                $sql.= ' WHERE '. $this->table_id_field .' = '. $_request_variables['intID'];

                //DEBUGGING
                //echo $sql;
                //exit();

                $this->query($sql);
            }
        }

        return $chk;
    }

    function osc_delete($intID)
    {
        $sql = 'DELETE FROM '. $this->table_name .' WHERE '. $this->table_id_field .' = '. $intID;

        $this->query($sql);
    }

    function get_shop($intID)
    {
        //			fbDebugBacktrace();
        $sql = 'SELECT '.
        $this->table_id_field .', ';

        $sql_cols = '';

        for($i = 0 ; $i < $this->db_col_count ; $i++)
        {
            if($this->oStruc['inform'][$i] === true)
            $sql_cols.= $this->oStruc['colnam'][$i] .', ';
        }

        $sql_cols = substr($sql_cols, 0, (strlen($sql_cols) - 2));

        $sql.= $sql_cols;

        $sql.= ' FROM '.
        $this->table_name .'
					WHERE '.
        $this->table_id_field .' = '. $intID;

        return $this->get_results($sql);
  		}
}
endif;
?>