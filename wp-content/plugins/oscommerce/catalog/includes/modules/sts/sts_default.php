<?php
/*
$Id: sts_default.php,v 2.1.1 2006/23/09 09:36:00 rigadin2 Exp $

osCommerce, Open Source E-Commerce Solutions
http://www.oscommerce.com

Copyright (c) 2005 osCommerce

Released under the GNU General Public License
* 
* STS v4 module for pages without own module by Rigadin2 (rigadin@osc-help.net)
* 
* v2.0.0: added parameter MODULE_STS_INFOBOX_STATUS for infobox templates.
* v2.1.0: added possibility to have content templates without creating a module for it.
* v2.1.1: BUG removed where module was always using the default template.
* Requires at least STS v4.4 to work correctly
* 
*/


class sts_default {

  var $template_file;
  
  function sts_default (){
    $this->code = 'sts_default';
    $this->title = MODULE_STS_DEFAULT_TITLE;
    $this->description = MODULE_STS_DEFAULT_DESCRIPTION.' (v2.1.1)';
	  $this->sort_order=1;
		$this->content_template_file='';
  }

  function find_template ($scriptbasename){
  // Return an html file to use as template
    // Check if there is a template for this script
	
	// If script name contains "popup" then turn off templates and display the normal output
  // This is required to prevent display of standard page elements (header, footer, etc) from the template and allow javascript code to run properly
  // Do not add pages here unless it is from the standard osC and really should be there. If you have a special page that you don't want with template,
  // Create a module sts_mypagename.php that returns an empty string as template filename, it will automatically switch off STS for this page.
  if (strstr($scriptbasename, "popup")|| strstr($scriptbasename, "info_shopping_cart"))
	  return ''; // We don't use template for these scripts
	
	$check_file = STS_TEMPLATE_DIR .$scriptbasename . ".html";
	if (file_exists($check_file)) return $check_file;

	// No template for this script, returns the default template
    return STS_DEFAULT_TEMPLATE;
  } // End function

  function capture_fields () {
  // Returns list of files to include from folder sts_inc in order to build the $template fields
    return 'general.php;'.MODULE_STS_DEFAULT_NORMAL;
  }

  function replace (&$template) {
	// Function modified in v2.1.0 to use content template without specific module.
  // If we do not use a content template, extract the content from buffer
    if ($this->content_template_file=='') {
	    $template['content']= sts_strip_content_tags($template['content'], 'Default Content');
	    return;
	  }
	
  // Otherwise continue and use the content template to build the content
	
	  global $template_content;
	
    // Read content template file
	  $template_html = sts_read_template_file($this->content_template_file);

    foreach ($template_content as $key=>$value) {
	    $template_html = str_replace('{$' . $key . '}' , $value, $template_html);
    }

    $template['content'] = $template_html;
  }

//======================================
// Functions needed for admin
//======================================

    function check() {
      if (!isset($this->_check)) {
        $check_query = tep_db_query("select configuration_value from " . TABLE_CONFIGURATION . " where configuration_key = 'MODULE_STS_DEFAULT_STATUS'");
        $this->_check = tep_db_num_rows($check_query);
      }
      return $this->_check;
    }

    function keys() {
      return array('MODULE_STS_DEFAULT_STATUS', 'MODULE_STS_DEBUG_CODE' ,'MODULE_STS_DEFAULT_NORMAL', 'MODULE_STS_TEMPLATES_FOLDER', 'MODULE_STS_TEMPLATE_FOLDER','MODULE_STS_TEMPLATE_FILE', 'MODULE_STS_INFOBOX_STATUS');
    }

    function install() {
      tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Use Templates?', 'MODULE_STS_DEFAULT_STATUS', 'false', 'Do you want to use Simple Template System?', '6', '1','tep_cfg_select_option(array(\'true\', \'false\'), ', now())");
	    tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Code for debug output', 'MODULE_STS_DEBUG_CODE', 'debug', 'Code to enable debug output from URL (ex: index.php?sts_debug=debug', '6', '2', now())");
	    tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Files for normal template', 'MODULE_STS_DEFAULT_NORMAL', 'sts_user_code.php', 'Files to include for a normal template, separated by semicolon', '6', '2', now())");
	    tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Base folder', 'MODULE_STS_TEMPLATES_FOLDER', 'includes/sts_templates/', 'Base folder where the templates folders are located. Relative to your catalog folder. Should end with a slash', '6', '2', now())");
			tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Template folder', 'MODULE_STS_TEMPLATE_FOLDER', 'test', 'This is the template folder in use, located inside the previous parameter. Do not start nor end with a slash', '6', '2', now())");
	    tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, date_added) values ('Default template file', 'MODULE_STS_TEMPLATE_FILE', 'sts_template.html', 'Name of the default template file', '6', '2', now())");
		  tep_db_query("insert into " . TABLE_CONFIGURATION . " (configuration_title, configuration_key, configuration_value, configuration_description, configuration_group_id, sort_order, set_function, date_added) values ('Use template for infoboxes', 'MODULE_STS_INFOBOX_STATUS', 'false', 'Do you want to use templates for infoboxes?', '6', '1','tep_cfg_select_option(array(\'true\', \'false\'), ', now())");
    }

    function remove() {
      tep_db_query("delete from " . TABLE_CONFIGURATION . " where configuration_key in ('" . implode("', '", $this->keys()) . "')");
    }  
  
}// end class
?>
