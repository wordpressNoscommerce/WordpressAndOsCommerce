<?php
/*
  $Id: sts.php,v 4.5.5 2006/23/09 22:30:54 Rigadin2 Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2005 osCommerce

  Released under the GNU General Public License
  * 
  * STS v4.5.5 by Rigadin2, www.osc-help.net
*/

require (DIR_WS_FUNCTIONS.'sts.php');

class sts {
  var $sts_block, $template,
	  $display_template_output, $display_debugging_output,
	  $template_file, $template_folder;
  
  function sts (){
    $this->update_from_url(); // Check for debug mode from URL
		$this->infobox_enabled = false; // infobox templates disabled by default.
		$this->is_button = false; // We are not processing an image button
		$this->buttons_folder = 'buttons'; // Set the button folder inside [template folder]/images/[language]/
		
		// Defines constants needed when working with templates.
		// v4.4: Moved constant definitions before check of template output, so constants are also available to pagegs not using templates
    define('STS_TEMPLATE_DIR', MODULE_STS_TEMPLATES_FOLDER . $this->template_folder .'/'); // v4.4: Use of MODULE_STS_TEMPLATES_FOLDER instead of fixed path
    define('STS_DEFAULT_TEMPLATE', STS_TEMPLATE_DIR . MODULE_STS_TEMPLATE_FILE);
		
		$this->default_content_template = STS_TEMPLATE_DIR . "content/sts_template.html"; // v4.5: Set the name of the default content template
		
  // Use template output if enabled or in debug mode (=only for admin)
    if ((MODULE_STS_DEFAULT_STATUS == 'true') || ($this->display_debug_output == true))
      $this->display_template_output = true;
	  else { 
	    $this->display_template_output = false;
	    return;
	  }

  // Initialisation	of variables
	  $this->template = array('debug' => '', 
	                        'headcontent' =>'',
	                        'extracss' =>'');
    $this->version= "4.5.5";

	// Find the right template to use according to actual page and parameters. Displays normal output if no template returned
    if ($this->find_template() == '') {
	    $this->display_template_output = false; // If no template returned, do not use templates at all and exit
	    return;
	  }
	  if ($this->read_template_file() == false) {
	    $this->display_template_output = false; // If template file does not exist, do not use templates at all and exit
	    return;
	  }
		// Added in v4.3: check if infobox templates are enabled or not
		$this->infobox_enabled = ((MODULE_STS_INFOBOX_STATUS == 'true') ? true : false);
  } //end constructor
  
  function update_from_url () {
    // Allow Debugging control from the URL
    if ($_GET['sts_debug'] == MODULE_STS_DEBUG_CODE) {
      $this->display_debug_output = true;
    }
	
	// Defines constants needed when working with templates
    if ($_GET['sts_template']) {
	  $this->template_folder = $_GET['sts_template'];
    } else {
	  $this->template_folder = MODULE_STS_TEMPLATE_FOLDER;
	}
    
  }  

  function find_template (){

  // Retrieve script name without path nor parameters
    $scriptbasename = basename ($_SERVER['PHP_SELF']);

// Disable STS for popups: moved to sts_default module since v4.4

  // Check for module that will handle the template (for example module sts_index takes care of index.php templates)
	$check_file = 'sts_'.$scriptbasename;
	$modules_installed = explode (';', MODULE_STS_INSTALLED);
	if (!in_array($check_file, $modules_installed)) $check_file = 'sts_default.php';

    include_once (DIR_WS_MODULES.'sts/'.$check_file);
	$classname=substr($check_file,0,strlen($check_file)-4);
	$this->script=new $classname; // Create an object from the module
	
// If module existes but is disabled, use the default module.
	if ($this->script->enabled==false) {
	  unset ($this->script);
      include_once (DIR_WS_MODULES.'sts/sts_default.php');
	  $this->script=new sts_default; // Create an object from the module	   
	}
	
	$this->template_file = $this->script->find_template($scriptbasename); // Retrieve the template to use, $scriptbasename added in v4.4
	return $this->template_file ;
  }
  
  function start_capture () {
  // Start redirecting output to the output buffer, if template mode on.
    if ($this->display_template_output) {
	  // ob_clean(); // Clear out the capture buffer. Removed in v4.3.3
	  ob_start();
	}
  }
  
  function stop_capture ($block_name='', $action='') {
  // Store captured output to $sts_capture
    if (!$this->display_template_output) return; // Do not process anything if we are not in using templates
	  $block = ob_get_contents(); // Get content of buffer
    ob_clean(); // Clear out the capture buffer
	  if ($block_name=='') return $block; // Not need to continue if we don't want to save the buffer
	  switch($action){
		  case 'box':
			    $block = sts_strip_unwanted_tags($block, $block_name);
		    $this->template[$block_name] = $block;
		    break;
			  break;
		  default:
		    $this->template[$block_name] = $block;
	  } // switch
		return $block; // Return value added in v4.3
  }
  
  function restart_capture ($block_name='', $action='') {
  // Capture buffer, save it and start a new capture
    if (!$this->display_template_output) return;
    $block = $this->stop_capture($block_name, $action);
	  $this->start_capture();
		return $block; // Return value added in v4.3
  }
	
	function array_capture ($array){
	// Function added in v4.5 to merge a full array directly into template array.
	  $this->template = array_merge($this->template, $array);
	}
  
  function capture_fields (){
// If we use template, ask to module what file(s) to include for building fields
    if ($this->display_template_output) {
	  $fields_arr= explode(';', $this->script->capture_fields ());
	}
	return $fields_arr;	
  }
  
  function read_template_file (){
  // Purpose: Open Template file and read it

	// Generate an error if the template file does not exist and return 'false'.
    if (! file_exists($this->template_file)) {
      print 'Template file does not exist: ['.$this->template_file.']';
	  return false;
    }
	// We use templates and the template file exists
	// Capture the template, this way we can use php code inside templates
	
	$this->start_capture (); // Start capture to buffer
	require $this->template_file; // Includes the template, this way php code can be used in templates
	$this->stop_capture ('template_html');
	return true;
  } // End read_template_file
  

  function replace (){
    global $messageStack, $request_type;
    
	if (!$this->display_template_output) return;  // Go out if we don't use template
    if (defined("STS_END_CHAR") == false) define ('STS_END_CHAR', ''); // An end char must be defined, even if empty.
    
	// Load up the <head> content that we need to link up everything correctly.  Append to anything that may have been set in sts_user_code.php
	// Note that since v3.0, stylesheet is not defined here but in the template file, allowing different stylesheet for different template.
    $this->template['headcontent'] = $this->template['headcontent'].'';
    $this->template['headcontent'] = $this->template['headcontent'].'<meta http-equiv="Content-Type" content="text/html; charset=' . CHARSET . '">' . "\n"; 
    $this->template['headcontent'] = $this->template['headcontent'].$this->template['headertags']. "\n"; ;
    $this->template['headcontent'] = $this->template['headcontent'].'<base href="' . (($request_type == 'SSL') ? HTTPS_SERVER : HTTP_SERVER) . DIR_WS_CATALOG . '">' . "\n";
    $this->template['headcontent'] = $this->template['headcontent'].get_javascript($this->template['applicationtop2header'],'get_javascript(applicationtop2header)');
	
	  $this->script->replace($this->template); // Module can make tricks here, just before replacing, like using own content template
    // Add messages before the content
    if ($messageStack->size('header') > 0) {
      $this->template['content'] = $messageStack->output('header') . $this->template['content'];
    }
		
		// v4.5.4, error and info message from URL were never displayed, so add them before content.
		$this->template['content'] = $this->template['error_message'].$this->template['info_message'].$this->template['content'];
		
  
  // Manually replace the <!--$headcontent--> if present
    $this->template['template_html'] = str_replace('<!--$headcontent-->', $this->template['headcontent'], $this->template['template_html']);
  // Manually replace the <!--$extracss--> with template['extracss']
    $this->template['template_html'] = str_replace('<!--$extracss-->', $this->template['extracss'], $this->template['template_html']);

  // Automatically replace all the other template variables
    if (STS_END_CHAR=='') { // If no end char defined for the placeholders, have to sort the placeholders.
      uksort($this->template, "sortbykeylength"); // Sort array by string length, so that longer strings are replaced first
    }
    foreach ($this->template as $key=>$value) {
      $this->template['template_html'] = str_replace('$' . $key . STS_END_CHAR , $value, $this->template['template_html']);
    }
  }

	function image (&$src) {
	// Added in v4.4: use image from the template folder if exists.
	  // Check only if STS is enabled.
    if (MODULE_STS_DEFAULT_STATUS=="true" && $this->is_button ==false)
      if (file_exists(STS_TEMPLATE_DIR . $src)) $src = STS_TEMPLATE_DIR . $src;
		$this->is_button = false;
	}
	
	function image_button ($src, $language, $is_button = false) {
	// Check if button exists in template folder.
	// $is_button=true will cancel the check in function "image".
		
		$this->is_button = $is_button;
		// Check only if STS is enabled.
    if (MODULE_STS_DEFAULT_STATUS=="true") {
		  $check_file = STS_TEMPLATE_DIR . 'images/'. $language . '/' .$this->buttons_folder . '/' .$src;
      if (file_exists($check_file)) return $check_file;
    }
		return '';

	}
	
// *****************************************
// Functions added for debug
// *****************************************   
  function add_debug ($text, $br=true) {
  // STS v4.1: Add debug text to the STS debug variable. If $br=false, then no line break added
    $this->template['debug'].= $text . ($br ? "\n" : '');
  }

}  //end class
?>