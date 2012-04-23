<?php
/*
  $Id: boxes.php,v 4.5.2 2006/12/05 19:05:12 wilt Exp $

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2003 osCommerce

  Released under the GNU General Public License
*/

  class tableBox {
    var $table_border = '0';
    var $table_width = '100%';
    var $table_cellspacing = '0';
    var $table_cellpadding = '2';
    var $table_parameters = '';
    var $table_row_parameters = '';
    var $table_data_parameters = '';

// class constructor
    function tableBox($contents, $direct_output = false) {
      $tableBox_string = '<table border="' . tep_output_string($this->table_border) . '" width="' . tep_output_string($this->table_width) . '" cellspacing="' . tep_output_string($this->table_cellspacing) . '" cellpadding="' . tep_output_string($this->table_cellpadding) . '"';
      if (tep_not_null($this->table_parameters)) $tableBox_string .= ' ' . $this->table_parameters;
      $tableBox_string .= '>' . "\n";

      for ($i=0, $n=sizeof($contents); $i<$n; $i++) {
        if (isset($contents[$i]['form']) && tep_not_null($contents[$i]['form'])) $tableBox_string .= $contents[$i]['form'] . "\n";
        $tableBox_string .= '  <tr';
        if (tep_not_null($this->table_row_parameters)) $tableBox_string .= ' ' . $this->table_row_parameters;
        if (isset($contents[$i]['params']) && tep_not_null($contents[$i]['params'])) $tableBox_string .= ' ' . $contents[$i]['params'];
        $tableBox_string .= '>' . "\n";

        if (isset($contents[$i][0]) && is_array($contents[$i][0])) {
          for ($x=0, $n2=sizeof($contents[$i]); $x<$n2; $x++) {
            if (isset($contents[$i][$x]['text']) && tep_not_null($contents[$i][$x]['text'])) {
              $tableBox_string .= '    <td';
              if (isset($contents[$i][$x]['align']) && tep_not_null($contents[$i][$x]['align'])) $tableBox_string .= ' align="' . tep_output_string($contents[$i][$x]['align']) . '"';
              if (isset($contents[$i][$x]['valign']) && tep_not_null($contents[$i][$x]['valign'])) $tableBox_string .= ' valign="' . $contents[$i][$x]['valign'] . '"'; // Added in v4.4
              if (isset($contents[$i][$x]['params']) && tep_not_null($contents[$i][$x]['params'])) {
                $tableBox_string .= ' ' . $contents[$i][$x]['params'];
              } elseif (tep_not_null($this->table_data_parameters)) {
                $tableBox_string .= ' ' . $this->table_data_parameters;
              }
              $tableBox_string .= '>';
              if (isset($contents[$i][$x]['form']) && tep_not_null($contents[$i][$x]['form'])) $tableBox_string .= $contents[$i][$x]['form'];
              $tableBox_string .= $contents[$i][$x]['text'];
              if (isset($contents[$i][$x]['form']) && tep_not_null($contents[$i][$x]['form'])) $tableBox_string .= '</form>';
              $tableBox_string .= '</td>' . "\n";
            }
          }
        } else {
          $tableBox_string .= '    <td';
          if (isset($contents[$i]['align']) && tep_not_null($contents[$i]['align'])) $tableBox_string .= ' align="' . tep_output_string($contents[$i]['align']) . '"';
          if (isset($contents[$i]['params']) && tep_not_null($contents[$i]['params'])) {
            $tableBox_string .= ' ' . $contents[$i]['params'];
          } elseif (tep_not_null($this->table_data_parameters)) {
            $tableBox_string .= ' ' . $this->table_data_parameters;
          }
          $tableBox_string .= '>' . $contents[$i]['text'] . '</td>' . "\n";
        }

        $tableBox_string .= '  </tr>' . "\n";
        if (isset($contents[$i]['form']) && tep_not_null($contents[$i]['form'])) $tableBox_string .= '</form>' . "\n";
      }

      $tableBox_string .= '</table>' . "\n";

      if ($direct_output == true) echo $tableBox_string;

      return $tableBox_string;
    }
	
	function infoBoxHeaderTemplate($headertext,$right_arrow) {
	// STS 4.3: put header template and tags in $sts object, do not display them now.
	// STS 4.5.2: BUG corrected, was checking content cache instead of header cache
	  global $sts;
	
		$btrace=debug_backtrace();
		$boxname=basename($btrace[1]['file'],".php");
		$boxprefix = "infobox_";
		
		// Added in v4.4: allows to use catalog_filename.html as template for boxes created directly in a catalog script.
		$boxname2 = basename($btrace[2]['file'],".php"); // backtrace 2 is the file calling the calling file (like sts_column_left.php)
    if ($boxname2=='') $boxprefix = "catalog_";

	  if (file_exists(STS_TEMPLATE_DIR."boxes/$boxprefix".$boxname."_header.php.html")) {
			$template=sts_read_template_file (STS_TEMPLATE_DIR."boxes/$boxprefix".$boxname."_header.php.html");
		} elseif (isset($sts->infobox['default_header'])) { // Corrected in v4.5.2, was checking content cache
		  $template = $sts->infobox['default_header']; // Default box already in memory, get it from there
		}	elseif (file_exists(STS_TEMPLATE_DIR."boxes/infobox_header.php.html"))  { // v4.5.2: header template optional
			$template=sts_read_template_file (STS_TEMPLATE_DIR."boxes/infobox_header.php.html");
			$sts->infobox['default_header'] = $template;
		} else $sts->infobox['default_header'] = ''; // v4.5.2: If no header template, cache an empty string
		$sts->infobox_header_template = $template;
		$sts->infobox_headertext = $headertext;
		$sts->infobox_right_arrow = $right_arrow;
	}

	function infoBoxTemplate($content) {
	// STS 4.3: read content, display header & content.
	// STS 4.4: reset headertext and right_arrow variables in case next box has no header.
	  global $sts;
		$btrace=debug_backtrace();
		$boxname=basename($btrace[1]['file'],".php"); // backtrace 1 is the calling file
		$boxprefix = "infobox_"; // Added in v4.3SP2.
		
		// Added in v4.4: allows to use catalog_filename.html as template for boxes created directly in a catalog script.
		$boxname2 = basename($btrace[2]['file'],".php"); // backtrace 2 is the file calling the calling file (like sts_column_left.php)
    if ($boxname2=='') $boxprefix = "catalog_";
		
	  if (file_exists(STS_TEMPLATE_DIR."boxes/$boxprefix$boxname.php.html")) {
			$template=sts_read_template_file (STS_TEMPLATE_DIR."boxes/$boxprefix$boxname.php.html");
		} elseif (isset($sts->infobox['default_content'])) {
		  $template = $sts->infobox['default_content']; // Default box already in memory, get it from there
		} else { // Otherwise read it from file and save it
		  $template = sts_read_template_file (STS_TEMPLATE_DIR."boxes/infobox.php.html");
			$sts->infobox['default_content'] = $template;
			
		}
		
		$template = $sts->infobox_header_template."\n".$template;	// Add header before the content. Header can be empty.
		$template = str_replace('$headertext', $sts->infobox_headertext, $template);
		$template = str_replace('$right_arrow', $sts->infobox_right_arrow, $template);
		$template = str_replace('$content', $content, $template);
		
		echo $template;
		$sts->infobox_header_template = '';
		$sts->infobox_headertext = '';
		$sts->infobox_right_arrow = '';
	}
  } // END tableBox class

  class infoBox extends tableBox {
    function infoBox($contents) {

    $info_box_contents = array();
    $info_box_contents[] = array('text' => $this->infoBoxContents($contents));
    $this->table_cellpadding = '1';
    $this->table_parameters = 'class="infoBox"';

	  
	  // START  STS
	  global $sts;
	  if ($sts->infobox_enabled == true) {
		  $this->infoboxtemplate($this->infoBoxContents($contents));
	  } else {
		  $this->tableBox($info_box_contents, true);
	  }
	  // END STS

  }

    function infoBoxContents($contents) {
      $this->table_cellpadding = '3';
      $this->table_parameters = 'class="infoBoxContents"';
      $info_box_contents = array();
      $info_box_contents[] = array(array('text' => tep_draw_separator('pixel_trans.gif', '100%', '1')));
      for ($i=0, $n=sizeof($contents); $i<$n; $i++) {
        $info_box_contents[] = array(array('align' => (isset($contents[$i]['align']) ? $contents[$i]['align'] : ''),
                                           'form' => (isset($contents[$i]['form']) ? $contents[$i]['form'] : ''),
                                           'params' => 'class="boxText"',
                                           'text' => (isset($contents[$i]['text']) ? $contents[$i]['text'] : '')));
      }
      $info_box_contents[] = array(array('text' => tep_draw_separator('pixel_trans.gif', '100%', '1')));
      return $this->tableBox($info_box_contents);
    }
  }

  class infoBoxHeading extends tableBox {
    function infoBoxHeading($contents, $left_corner = true, $right_corner = true, $right_arrow = false) {
      $this->table_cellpadding = '0';

      if ($left_corner == true) {
        $left_corner = tep_image(DIR_WS_IMAGES . 'infobox/corner_left.gif');
      } else {
        $left_corner = tep_image(DIR_WS_IMAGES . 'infobox/corner_right_left.gif');
      }
      if ($right_arrow == true) {
        $right_arrow = '<a href="' . $right_arrow . '">' . tep_image(DIR_WS_IMAGES . 'infobox/arrow_right.gif', ICON_ARROW_RIGHT) . '</a>';
      } else {
        $right_arrow = '';
      }
      if ($right_corner == true) {
        $right_corner = $right_arrow . tep_image(DIR_WS_IMAGES . 'infobox/corner_right.gif');
      } else {
        $right_corner = $right_arrow . tep_draw_separator('pixel_trans.gif', '11', '14');
      }

	  
	  // START  STS
	  global $sts;
	  if ($sts->infobox_enabled == true) {
      $info_box_contents = array();
      $info_box_contents[] = array(array('params' => 'width="100%" class="infoBoxHeading"',
                                         'text' => $contents[0]['text']));

	    $this->infoBoxHeaderTemplate($this->tablebox($info_box_contents),$right_arrow);
	  } else {
      $info_box_contents = array();
      $info_box_contents[] = array(array('params' => 'height="14" class="infoBoxHeading"',
                                         'text' => $left_corner),
                                   array('params' => 'width="100%" height="14" class="infoBoxHeading"',
                                         'text' => $contents[0]['text']),
                                   array('params' => 'height="14" class="infoBoxHeading" nowrap',
                                         'text' => $right_corner));
  	  $this->tableBox($info_box_contents, true);
	  }
	  // END  STS
    }
  }

  class contentBox extends tableBox {
    function contentBox($contents) {

	    global $sts;
	    if ($sts->infobox_enabled == true) {
		    $this->infoBoxTemplate($this->tableBox($contents));
	    } else {
        $info_box_contents = array();
        $info_box_contents[] = array('text' => $this->contentBoxContents($contents));
        $this->table_cellpadding = '1';
        $this->table_parameters = 'class="infoBox"';
        $this->tableBox($info_box_contents, true);
	    }
    }

    function contentBoxContents($contents) {
      $this->table_cellpadding = '4';
      $this->table_parameters = 'class="infoBoxContents"';
	  
      return $this->tableBox($contents);
    }
  }

  class contentBoxHeading extends tableBox {
    function contentBoxHeading($contents) {

  	  // START  STS
	    global $sts;
	    if ($sts->infobox_enabled == true) {
        $info_box_contents = array();
        $info_box_contents[] = array(array('params' => 'class="infoBoxHeading" width="100%"',
                                           'text' => $contents[0]['text']));
	  
	      $this->infoBoxHeaderTemplate($this->tablebox($info_box_contents),$right_arrow);
	    } else {
        $this->table_width = '100%';
        $this->table_cellpadding = '0';

        $info_box_contents = array();
        $info_box_contents[] = array(array('params' => 'height="14" class="infoBoxHeading"',
                                           'text' => tep_image(DIR_WS_IMAGES . 'infobox/corner_left.gif')),
                                     array('params' => 'height="14" class="infoBoxHeading" width="100%"',
                                           'text' => $contents[0]['text']),
                                     array('params' => 'height="14" class="infoBoxHeading"',
                                           'text' => tep_image(DIR_WS_IMAGES . 'infobox/corner_right_left.gif')));
        $this->tableBox($info_box_contents, true);
	    }
  	  // END STS

    }
  }

  class errorBox extends tableBox {
    function errorBox($contents) {
      $this->table_data_parameters = 'class="errorBox"';
	  
	  	$this->infoBoxTemplate($this->infoBoxContents($contents));
    }
  }

  class productListingBox extends tableBox {
    function productListingBox($contents) {
		  // START  STS 4.4: optionally use template infobox_product_listing.php.html around product listing table.
			// If template does not exist, do not use default infobox template but use standard osC.
	    global $sts;
	    if ($sts->infobox_enabled == true && file_exists(STS_TEMPLATE_DIR."boxes/infobox_product_listing.php.html")) {
		    //$this->infoBoxHeaderTemplate("","");
		    $this->infoBoxTemplate($this->tablebox($contents));
	    } else {
        $this->table_parameters = 'class="productListing"';
        $this->tableBox($contents, true);
			}
		  // STOP STS
    }

  }
?>
