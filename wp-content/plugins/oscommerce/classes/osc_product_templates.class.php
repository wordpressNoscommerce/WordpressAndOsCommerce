<?php
/////////////////////////////////////////////////////////////////////////////////////
// Class with the templates
/* Product Properties: (from query)
 product_index,
 imageUrl
 prodUrl
 p.products_id,
 p.products_image,
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
 pd.products_head_keywords_tag */
class osc_product_templates
{
    /** inject all product templates **/
    function osc_inject_all_product_templates() {
        $this->osc_inject_product_box_template();
        $this->osc_inject_product_detail_template();
        $this->osc_inject_product_xsell_template();
        $this->osc_inject_product_formats_template();
        $this->osc_inject_cart_entry_template();
        $this->osc_inject_jplayerplaylist_template();
        $this->osc_inject_videojs_template();
        $this->osc_inject_youtube_template();
        $this->osc_inject_pagination_template();
    }

    /** jquery template for PRODUCT BOX in grid **/
    function osc_inject_product_box_template() {
        // hack to trick eclipse editor into better parsing
/*
 * <div class="product-thumb thumb">
    <a href="#infotext&format=${format}&paged=${paged}&products_id=${products_id}" class="product-thumb thumb"><img src="${products_image_url}" alt="${products_name}" title="${products_name}" /> </a>
  </div>
  */
        echo '<script id="product-box-template" type="text/x-jquery-tmpl">
'; ?>
<div class="product-box post hentry type-post status-publish format-standard">
  <a href="#infotext&format=${format}&paged=${paged}&products_id=${products_id}" class="product-thumb thumb">
  	<img src="${products_image_url}" alt="${products_name}" title="${products_name}" /> </a>
  <div class="product-info clear">
    <span class="product-id">${products_id}</span>
    <span class="product-model">${products_model}</span><br />
    <span class="product-title">${products_name}</span>
  </div>
</div>
<!-- end product-box -->
        <?php   echo '</script>
';   }

        ///////////////////////////////////////////////////////////////////////////
        /** jquery template for PRODUCT detail **/
        function osc_inject_product_detail_template() {
            // hack to trick eclipse into formatting the HTML
            echo '<script id="product-detail-template" type="text/x-jquery-tmpl">
'; ?>
  <div id="prod-detail-header" class="product-detail-header">
    <span id="product-meta" class="product-meta"></span><span id="product-release" class="product-model">${products_model}</span><br/>
    <span id="artist-name-release" class="artist-name">${manufacturers_name}</span>
    <span id="product-title" class="product-title">${products_name}</span>
  </div>
  <div id="prod-image-big" class="product-image-wide">
    <img class="prod-image-wide" src="${products_image_url}" alt="${products_name} ">
  </div>
  <div id="product-detail-tabs" class="wp-tabs shit-style wpui-styles">

    <div id="tab-info-text" class="ui-tabs-panel ui-widget-content ui-corner-bottom">
      <h3 class="wp-tab-title" style="display: none;">InfoText</h3>
      <div class="wp-tab-content textwidget">{{html products_description}}</div>
    </div> <!-- tab-info-text -->

    <div id="tab-listen-buy" class="ui-tabs-panel ui-widget-content ui-corner-bottom">
      <h3 class="wp-tab-title" style="display: none;">Listen/Buy</h3>
      <div class="wp-tab-content">
        <h4 id="loadingProds">Loading Products ... </h4>
      </div>
    </div> <!-- tab-listen-buy -->

    <div id="tab-full-cover" class="ui-tabs-panel ui-widget-content ui-corner-bottom">
      <h3 class="wp-tab-title" style="display: none;">Full Cover</h3>
      <div class="wp-tab-content"></div>
    </div> <!-- tab-full-cover -->

    <div id="tab-video" class="ui-tabs-panel ui-widget-content ui-corner-bottom">
      <h3 class="wp-tab-title" style="display: none;">Video</h3>
      <div class="wp-tab-content"></div>
      <!-- wp-tab-content -->
    </div> <!-- tab-video -->

    <div id="tab-free-song" class="ui-tabs-panel ui-widget-content ui-corner-bottom">
      <h3 class="wp-tab-title" style="display: none;">Free Song</h3>
      <div class="wp-tab-content"> </div>
    </div> <!-- tab-free-song -->

    <div id="tab-dj-mix" class="ui-tabs-panel ui-widget-content ui-corner-bottom">
      <h3 class="wp-tab-title" style="display: none;">DJ-Mix</h3>
      <div class="wp-tab-content"> </div>
    </div> <!-- tab-dj-mix -->
  </div><!-- product-detail-tabs -->
            <?php echo '</script>';
        }

        ///////////////////////////////////////////////////////////////////////////
        /** jquery template for PRODUCT XSELL listing in product-tab
         * PROPERTIES
         p.products_id          ,
         p.products_model       ,
         p.products_prelisten   ,
         p.products_image       ,
         p.products_for_free    ,
         pd.products_name       ,
         p.products_tax_class_id,
         products_price	**/
        function osc_inject_product_xsell_template() {
            // hack to trick eclipse into formatting the HTML
            echo '<script id="product-xsell-template" type="text/x-jquery-tmpl">
'; ?>
<div class="product-xsell-box format-standard">
  <span class="product-id">${products_id}</span>
  <span class="product-icon"></span>
  <span class="product-name">${products_name}</span>
  <span class="product-model">${products_model}</span>
  <span class="product-prelisten">${products_prelisten}</span>
 <div class="buy-product">
    <form action="<?php echo $this->cart_url; ?>"
      method="post" name="cart_quantity_${products_id}" target="shopping_cart">
      <input type="hidden" value="${products_id}" name="products_id">
      <input type="hidden" value="add_product" name="action">
      <span class="buy-button">Buy MP3</span>
    </form>
  </div>
</div>

<!-- end product-xsell -->
            <?php   echo '</script>
';
        }

        ///////////////////////////////////////////////////////////////////////////
        /** listing of the different product formats to buy....
         * PROPERTIES
         p.products_id,
       		p.products_tax_class_id,
       		p.products_parent products_model,
       		pd.products_format,
       		IF(s.status, s.specials_new_products_price, p.products_price) AS products_price **/
        function osc_inject_product_formats_template() {
            echo '<script id="product-formats-template" type="text/x-jquery-tmpl">
';
            // osCsid=$this->osc_sid& TODO sort out the sessions
            ?>
<div class="product-formats-entry format-standard">
  <span class="product-id">${products_id}</span>
  <span class="products-tax-class-id">${products_tax_class_id}</span>
  <span class="product-model">${products_model}</span>
  <span class="products-format">${products_format}</span>
  <span class="products-price">${products_price}</span>
  <span class="products-price-tax">${products_price_tax}€</span>
  <div class="buy-product">
    <form action="<?php echo $this->cart_url; ?>" class="product-formats-entry"
      method="post" name="cart_quantity_${products_id}" target="shopping_cart">
      <input type="hidden" value="${products_id}" name="products_id">
      <input type="hidden" value="add_product" name="action">
      <span class="buy-button"
       onclick="javascript:document.forms['cart_quantity_${products_id}'].submit()"
        onmouseout="this.style.color='#303030';this.style.backgroundColor='#d2d2d2'"
        onmouseover="this.style.color='#d2d2d2';this.style.backgroundColor='#303030'" title="add to cart"
        style="color: #303030; background-color: #d2d2d2;" >Buy It</span>
    </form>
  </div>
</div><!-- end product-formats -->
            <?php   echo '</script>
';
            echo '<script id="product-format-template" type="text/x-jquery-tmpl">
';
            // osCsid=$this->osc_sid& TODO sort out the sessions
            ?>
  <span class="format-selector">
  <input type="checkbox" checked="checked" class="checkbox" value="${products_id}"/>
  <span class="products-format">${products_format}</span>
  (€<span class="products-price">${products_price}</span>)
  </span>
            <?php   echo '</script>
';
        }

		///////////////////////////////////////////////////////////////////////////
        /** listing of the different product formats to buy....
         * cart entry PROPERTIES used in javascript parameter
         	c.index
	        c.products_id,
       		c.products_thumb,
       		c.products_tax_class_id,
       		c.products_name,
       		c.products_model,
       		c.products_qty,
       		c.products_format,
       		c.products_price,
       		c.products_price_tax
       		**/
        function osc_inject_cart_entry_template() {
            echo '<script id="cart-entry-template" type="text/x-jquery-tmpl">
';
            ?>
<div class="cart-entry format-standard">
    <form action="<?php echo $this->cart_url; ?>" class="product-box-entry"
      method="post" name="cart_quantity_${products_id}" target="shopping_cart">

      <input type="hidden" name="products_id" value="${products_id}" />
      <input type="hidden" name="action" value="update_product" />
	  <input type="checkbox" class="cart_del" name="cart_delete" value="false"/>
	  <span class="cart-index cart">${index}</span>
	  <span class="product-id cart">${products_id}</span>
	  <span class="product-thumb cart">${products_thumb}</span>
	  <span class="product-name cart">${products_name}</span>
	  <span class="products-model cart">${products_model}</span>
	  <input type="text" class="cart_qty" name="cart_quantity" value="${products_qty}" size="1"/>
	  <span class="products-format cart">${products_format}</span>
	  <span class="products-price cart">${products_price}</span>
	  <span class="products-price-tax cart">${products_price_tax} €</span>
      <span class="update box-button cart"
       onclick="javascript:document.forms['cart_quantity_${products_id}'].submit()"
        onmouseout="this.style.color='#303030';this.style.backgroundColor='#d2d2d2'"
        onmouseover="this.style.color='#d2d2d2';this.style.backgroundColor='#303030'" title="add to cart"
        style="color: #303030; background-color: #d2d2d2;" >Update</span>
    </form>
</div><!-- end cart-entry-template -->
            <?php   echo '</script>
';
        }

        ///////////////////////////////////////////////////////////////////////////
        function osc_inject_videojs_template() {
            echo '<script id="product-videojs-template" type="text/x-jquery-tmpl">
'; ?>
<!-- Begin VideoJS -->
<div class="video-js-box">
  <!-- Using the Video for Everybody Embed Code http://camendesign.com/code/video_for_everybody -->
  <video id="wp_video" class="video-js" width="640" height="264" controls="controls" preload="auto"> <source
    src="http://video-js.zencoder.com/oceans-clip.mp4" type='video/mp4; codecs="avc1.42E01E, mp4a.40.2"' /> <source src="http://video-js.zencoder.com/oceans-clip.webm" type='video/webm; codecs="vp8, vorbis"' />
     <source src="http://video-js.zencoder.com/oceans-clip.ogv" type='video/ogg; codecs="theora, vorbis"' /> <!-- Flash Fallback. Use any flash video player here. Make sure to keep the vjs-flash-fallback class. -->
     <object id="flash_fallback_1" class="vjs-flash-fallback" width="640" height="264" type="application/x-shockwave-flash"
             data="http://releases.flowplayer.org/swf/flowplayer-3.2.1.swf" >
    <param name="movie" value="http://releases.flowplayer.org/swf/flowplayer-3.2.1.swf" />
    <param name="allowfullscreen" value="true" />
    <param name="flashvars" value='config={"playlist":["http://video-js.zencoder.com/oceans-clip.png", {"url": "http://video-js.zencoder.com/oceans-clip.mp4","autoPlay":false,"autoBuffering":true}]}' />
    <!-- Image Fallback. Typically the same as the poster image.          -->
    <img src="/images/oceans-clip.png" width="640" height="264" alt="Poster Image" title="No video playback capabilities." />
  </object> </video>
</div><!-- End VideoJS-->
<?php       echo '</script>
';
        }

        function osc_inject_youtube_template() {
            echo '<script id="product-youtube-template" type="text/x-jquery-tmpl">
'; ?>
<iframe width="640" height="264" src="${youTubeUrl}" frameborder="0"></iframe>
<?php       echo '</script>
';
        }
        // to use is the javascript needs to be loaded
        function osc_inject_jplayerplaylist_template() {
            echo '<script id="product-jplayerplaylist-template" type="text/x-jquery-tmpl">
'; ?>
<div id="player-block">
	<div id="${jplayerId}" class="jp-jplayer"></div>
	<div id="${jplayerContent}" class="jp-audio">
		<div class="jp-type-playlist">
			<div class="jp-gui jp-interface">
				<div id="product-image-small">
					<img class="prod-image-small" src="${products_image_url}" alt="${products_name}" />
				</div>
				<div id="jp-track-info">
					<div id="jp-track-name"></div>
					<div id="jp-track-artist"></div>
				    <br class="clear"/>
				</div>
                <ul class="jp-controls">
                  <li><a href="javascript:;" class="jp-previous" tabindex="1">previous</a></li>
                  <li><a href="javascript:;" class="jp-play" tabindex="1">play</a></li>
                  <li><a href="javascript:;" class="jp-pause" tabindex="1">pause</a></li>
                  <li><a href="javascript:;" class="jp-next" tabindex="1">next</a></li>
                  <li><a href="javascript:;" class="jp-mute" tabindex="1" title="mute">mute</a></li>
                  <li><a href="javascript:;" class="jp-unmute" tabindex="1" title="unmute">unmute</a></li>
                </ul>
				<div class="jp-progress">
					<div class="jp-seek-bar">
						<div class="jp-play-bar"></div>
					</div>
				</div>
 				<div class="jp-volume-bar">
					<div class="jp-volume-bar-value"></div>
				</div>
				<div class="jp-time-holder">
					<div class="jp-current-time">0:00</div>
					<div class="jp-slash-time">/</div>
					<div class="jp-duration">0:00</div>
				</div>
			</div>
			<div class="jp-playlist">
				<ul>
					<li></li>
				</ul>
			</div>
			<div class="jp-no-solution">
				<span>Update Required</span>
				To play the media you will need to either update your browser to a recent version or update your <a href="http://get.adobe.com/flashplayer/" target="_blank">Flash plugin</a>.
			</div>
		</div>
	</div>
</div>
<?php       echo '</script>
';
        }
        function osc_inject_pagination_template() {
            echo '<script id="pagination-template" type="text/x-jquery-tmpl">
'; ?>
    <div class="pagination">
      <span class="pages">loaded <span class="thispage">${paged}</span>
      out of <span class="maxpage">${maxpage}</span> pages
      (<span class="loaded">${loaded}</span>/<span class="reccount">${reccount}</span>&nbsp;items) </span> <a class="nextpostslink" href="#">LOAD MORE</a>
    </div>
<?php       echo '</script>
';
        }
} // EOC osc_jquery_templates

?>