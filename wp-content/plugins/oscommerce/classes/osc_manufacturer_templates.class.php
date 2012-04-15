<?php
/////////////////////////////////////////////////////////////////////////////////////
// Class with the templates
/*
manufacturers_description "andreas paruschke start...bsp;&nbsp;&nbsp; <br />"
manufacturers_id "316"
manufacturers_image "artist_anaphie_kl.jpg"
manufacturers_image_lrg_url "/wp-content/plugins/oscommerce/images/no_image.gif"
manufacturers_image_med "rnm_170_anaphie.jpg"
manufacturers_image_url "http://shopkatapult.com...s/artist_anaphie_kl.jpg"
manufacturers_index 0
manufacturers_label "999"
manufacturers_name "Anaphie"
manufacturers_press_image "Andi_1_300.jpg"
manufacturers_url "http://www.myspace.com/anaphie"
*/
define('ARTISTPAGE','page_id=108');
class osc_manufacturer_templates extends osc_product_templates // we need all of them anyway
{
   /** inject all manufacturer templates **/
	function osc_inject_all_manufacturer_templates() {
		$this->osc_inject_manufacturer_box_template();
		$this->osc_inject_manufacturer_detail_template();
		$this->osc_inject_release_box_template();
		$this->osc_inject_release_format_tabs_template();
		$this->osc_inject_release_format_inner_tab_template();
		// from products templates
		$this->osc_inject_all_product_templates();
	}

	/** jquery template for artist RELEASE BOX in grid has different link (TODO can be a parm?)**/
    function osc_inject_release_box_template() {
        // hack to trick eclipse editor into better parsing
        // TODO make sure all URL parms are in place and deal with double paged!!!!!
        echo '<script id="release-box-template" type="text/x-jquery-tmpl">
'; ?>
<div class="release-box product-box post hentry type-post status-publish format-standard">
  <a href="#listenbuy&artistSet=${artist_set}&paged=${paged}&format=${format}&artistId=${manufacturers_id}&products_id=${products_id}&rpage=${rpage}" class="product-thumb thumb">
    <img src="${products_image_url}" alt="${products_name}" title="${products_name}" /> </a>
  <div class="product-info clear">
    <span class="product-id">${products_id}</span>
    <span class="product-model">${products_model}</span><br />
    <span class="product-title">${products_name}</span>
  </div>
</div><!-- end release-box -->
        <?php   echo '</script>
';   }

    /** jquery template for manufacturer BOX in grid **/
    function osc_inject_manufacturer_box_template() {
        // hack to trick eclipse editor into better parsing
        echo '<script id="manufacturer-box-template" type="text/x-jquery-tmpl">
'; ?>
<div class="artist-box post hentry type-post status-publish format-standard">
 <a href="#all_releases_of_artist&artistSet=${artist_set}&paged=${paged}&artistId=${manufacturers_id}" class="product-thumb thumb">
    <img src="${manufacturers_image_url}" alt="${manufacturers_name}" title="${manufacturers_name}" /> </a>
  <div class="product-info clear">
    <span class="artist-name product-title">${manufacturers_name}</span>
    <span class="artist-id product-id">${manufacturers_id}</span>
  </div>
</div><!-- end artist-box -->
        <?php   echo '</script>
';   }

        ///////////////////////////////////////////////////////////////////////////
        /** jquery template for PRODUCT detail **/
        function osc_inject_manufacturer_detail_template() {
            // hack to trick eclipse into formatting the HTML
            echo '<script id="artist-detail-template" type="text/x-jquery-tmpl">
'; ?>
<div id="artist-detail" class="product-detail format-standard">
  <div id="artist-name-detail" class="artist-name">${manufacturers_name}</div>
  <div id="artist-image-big" class="product-image-wide">
    <img class="prod-image-wide" src="${manufacturers_image_url}" alt="${manufacturers_name} ">
  </div>
  <div id="artist-text" class="artist-text">{{html manufacturers_description}}</div>
</div><!-- artist-detail -->
            <?php echo '</script>';
        }

        ///////////////////////////////////////////////////////////////////////////
        /** jquery template for PRODUCT detail **/
        function osc_inject_release_format_tabs_template() {
            // hack to trick eclipse into formatting the HTML
            echo '<script id="release-format-tabs-template" type="text/x-jquery-tmpl">
'; ?>
<div id="release-format-tabs" class="wp-tabs wpui-light wpui-styles widget">
  <div id="release-detail" class="release-detail product-detail"></div>
</div>
            <?php echo '</script>';
        }

        function osc_inject_release_format_inner_tab_template() {
            // hack to trick eclipse into formatting the HTML
            echo '<script id="release-format-inner-tab-template" type="text/x-jquery-tmpl">
'; ?>
<div id="${tab}" class="ui-tabs-panel ui-widget-content">
    <h3 class="wp-tab-title" style="display: none;">${tab}</h3>
    <div class="wp-tab-content"></div>
</div>
            <?php echo '</script>';
        }

  } // EOC osc_jquery_templates

?>