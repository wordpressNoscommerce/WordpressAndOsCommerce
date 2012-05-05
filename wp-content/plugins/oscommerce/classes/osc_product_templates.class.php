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
		$this->osc_inject_jplayerplaylist_template();
		$this->osc_inject_videojs_template();
		$this->osc_inject_youtube_template();
		$this->osc_inject_pagination_template();
		$this->osc_inject_shopcart_template();
		$this->osc_inject_shopcart_entry_template();
		//         $this->osc_inject_login_form_template();
		//         $this->osc_inject_register_form_template();
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
	<a href="#infotext&format=${format}&paged=${paged}&products_id=${products_id}" class="product-thumb thumb"> <img
		src="${products_image_url}" alt="${products_name}" title="${products_name}" />
	</a>
	<div class="product-info clear">
		<span class="product-id">${products_id}</span> <span class="product-model">${products_model}</span><br /> <span
			class="product-title">${products_name}</span>
	</div>
</div>
<!-- end product-box -->
<?php   echo '</script>
';
	}

	///////////////////////////////////////////////////////////////////////////
	/** jquery template for PRODUCT detail **/
	function osc_inject_product_detail_template() {
		// hack to trick eclipse into formatting the HTML
		echo '<script id="product-detail-template" type="text/x-jquery-tmpl">
		'; ?>
<div id="prod-detail-header" class="product-detail-header">
	<span id="product-meta" class="product-meta"></span><span id="product-release" class="product-model">${products_model}</span><br />
	<span id="artist-name-release" class="artist-name">${manufacturers_name}</span> <span id="product-title"
		class="product-title">${products_name}</span> <span id="product-id" class="hidden">${products_id}</span>
</div>
<div id="prod-image-big" class="product-image-wide">
	<img class="prod-image-wide" src="${products_image_url}" alt="${products_name} ">
</div>
<div id="product-detail-tabs" class="wp-tabs shit-style wpui-styles">

	<div id="tab-info-text" class="ui-tabs-panel ui-widget-content ui-corner-bottom">
		<h3 class="wp-tab-title" style="display: none;">InfoText</h3>
		<div class="wp-tab-content textwidget">{{html products_description}}</div>
	</div>

	<div id="tab-listen-buy" class="ui-tabs-panel ui-widget-content ui-corner-bottom">
		<h3 class="wp-tab-title" style="display: none;">Listen/Buy</h3>
		<div class="wp-tab-content">
			<h4 id="loadingProds">Loading Products ...</h4>
		</div>
	</div>

	<div id="tab-full-cover" class="ui-tabs-panel ui-widget-content ui-corner-bottom">
		<h3 class="wp-tab-title" style="display: none;">Full Cover</h3>
		<div class="wp-tab-content"></div>
	</div>

	<div id="tab-video" class="ui-tabs-panel ui-widget-content ui-corner-bottom">
		<h3 class="wp-tab-title" style="display: none;">Video</h3>
		<div class="wp-tab-content"></div>
		<!-- wp-tab-content -->
	</div>

	<div id="tab-free-song" class="ui-tabs-panel ui-widget-content ui-corner-bottom">
		<h3 class="wp-tab-title" style="display: none;">Free Song</h3>
		<div class="wp-tab-content">
			<iframe width="100%" height="450" scrolling="no" frameborder="no"
				src="http://w.soundcloud.com/player/?url=http%3A%2F%2Fapi.soundcloud.com%2Fplaylists%2F430866&show_artwork=true"> </iframe>
		</div>
	</div>

	<div id="tab-dj-mix" class="ui-tabs-panel ui-widget-content ui-corner-bottom">
		<h3 class="wp-tab-title" style="display: none;">DJ-Mix</h3>
		<div class="wp-tab-content">
			<iframe width="100%" height="166" scrolling="no" frameborder="no"
				src="http://w.soundcloud.com/player/?url=http%3A%2F%2Fapi.soundcloud.com%2Ftracks%2F43024792&show_artwork=true"></iframe>
		</div>
	</div>
	<!-- tab-dj-mix -->
</div>
<!-- product-detail-tabs -->
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
	<span class="product-id">${products_id}</span> <span class="product-icon"></span> <span class="product-name">${products_name}</span>
	<span class="product-model">${products_model}</span> <span class="product-prelisten">${products_prelisten}</span>
	<div class="buy-product">
		<form action="<?php echo $this->cart_url; ?>" method="post" name="cart_quantity_${products_id}" target="shopping_cart">
			<input type="hidden" value="${products_id}" name="products_id"> <input type="hidden" value="add_product"
				name="action"> <span class="buy-button">Buy MP3</span>
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
/*
 		echo '<script id="product-formats-template" type="text/x-jquery-tmpl">
		';
		?>
<div class="product-formats-entry format-standard">
	<span class="product-id">${products_id}</span> <span class="products-tax-class-id">${products_tax_class_id}</span> <span
		class="product-model">${products_model}</span> <span class="products-format">${products_format}</span> <span
		class="products-price">${products_price}</span> <span class="products-price-tax">${products_price_tax}€</span>
	<div class="buy-product">
		<form action="<?php echo $this->cart_url; ?>" class="product-formats-entry" method="post"
			name="cart_quantity_${products_id}" target="shopping_cart">
			<input type="hidden" value="${products_id}" name="products_id"> <input type="hidden" value="add_product"
				name="action"> <span class="buy-button" onclick="javascript:document.forms['cart_quantity_${products_id}'].submit()"
				onmouseout="this.style.color='#303030';this.style.backgroundColor='#d2d2d2'"
				onmouseover="this.style.color='#d2d2d2';this.style.backgroundColor='#303030'" title="add to cart"
				style="color: #303030; background-color: #d2d2d2;">Buy It</span>
		</form>
	</div>
</div>
<!-- end product-formats -->
<?php   echo '</script>
';*/
		echo '<script id="product-format-template" type="text/x-jquery-tmpl">
'; ?>
<span class="format-selector"> <input type="checkbox" checked="checked" class="checkbox" value="${products_id}" /> <span
	class="products-format">${products_format}</span> (€<span class="products-price">${products_price_gross}</span>)
</span>
<?php   echo '</script>
';
	}

	///////////////////////////////////////////////////////////////////////////
	function osc_inject_videojs_template() {
		echo '<script id="product-videojs-template" type="text/x-jquery-tmpl">
		'; ?>
<!-- Begin VideoJS -->
<div
	class="video-js-box">
	<!-- Using the Video for Everybody Embed Code http://camendesign.com/code/video_for_everybody -->
	<video id="wp_video" class="video-js" width="640" height="264" controls="controls" preload="auto">
		<source src="http://video-js.zencoder.com/oceans-clip.mp4" type='video/mp4; codecs="avc1.42E01E, mp4a.40.2"' />
		<source src="http://video-js.zencoder.com/oceans-clip.webm" type='video/webm; codecs="vp8, vorbis"' />
		<source src="http://video-js.zencoder.com/oceans-clip.ogv" type='video/ogg; codecs="theora, vorbis"' />
		<!-- Flash Fallback. Use any flash video player here. Make sure to keep the vjs-flash-fallback class. -->
		<object id="flash_fallback_1" class="vjs-flash-fallback" width="640" height="264" type="application/x-shockwave-flash"
			data="http://releases.flowplayer.org/swf/flowplayer-3.2.1.swf">
			<param name="movie" value="http://releases.flowplayer.org/swf/flowplayer-3.2.1.swf" />
			<param name="allowfullscreen" value="true" />
			<param name="flashvars"
				value='config={"playlist":["http://video-js.zencoder.com/oceans-clip.png", {"url": "http://video-js.zencoder.com/oceans-clip.mp4","autoPlay":false,"autoBuffering":true}]}' />
			<!-- Image Fallback. Typically the same as the poster image.          -->
			<img src="/images/oceans-clip.png" width="640" height="264" alt="Poster Image"
				title="No video playback capabilities." />
		</object>
	</video>
</div>
<!-- End VideoJS-->
<?php       echo '</script>
';
	}

	function osc_inject_youtube_template() {
		echo '<script id="product-youtube-template" type="text/x-jquery-tmpl">
		'; ?>
<iframe width="640" height="264" src="http://www.youtube.com/embed/${youTubeId}" frameborder="0" allowfullscreen> </iframe>
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
					<br class="clear" />
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
						<div class="jp-play-handle"></div>
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
				<span>Update Required</span> To play the media you will need to either update your browser to a recent version or
				update your <a href="http://get.adobe.com/flashplayer/" target="_blank">Flash plugin</a>.
			</div>
		</div>
	</div>
</div>
<?php echo '</script>
';
	}
	function osc_inject_pagination_template() {
		echo '<script id="pagination-template" type="text/x-jquery-tmpl">
		'; ?>
<div class="pagination">
	<span class="pages">loaded <span class="thispage">${paged}</span> out of <span class="maxpage">${maxpage}</span> pages
		(<span class="loaded">${loaded}</span>/<span class="reccount">${reccount}</span>&nbsp;items)
	</span> <a class="nextpostslink" href="${href}">LOAD MORE</a>
</div>
<?php       echo '</script>
';
	}

	///////////////////////////////////////////////////////////////////////////
	function osc_inject_shopcart_template() {
		echo '<script id="shopcart-template" type="text/x-jquery-tmpl">
		';
		$this->osc_show_shopcart('','');
		echo '</script>
		';
	}
	// 	draw our shopping cart, this is called from oscommerce.php
	// since we have two version of this cart on ein sidebar one in content-body we need to use classes only
	function osc_show_shopcart($osCsid,$customer_id){
		?>
<div class="shop-cart" class="box cart widget">
	<h3 class="shop-cart-header">
		Your Shopping Box <span class="shop-cart-hd-txt" class="notonsidebar">(Change Content or amount of products)</span>
	</h3>
	<div class="shop-cart-box">
		<div class="shop-cart-box-header notonsidebar uc clear">
			<span class="shop-cart-hd-total uc col right">Total</span> <span class="shop-cart-hd-remove uc col col1">Remove</span>
			<span class="shop-classrt-hd-products uc col col2">Products</span> <span class="shop-cart-hd-quantity uc col col3">Quantity</span>
		</div>
		<div class="shop-cart-body notonsidebar clear"></div>
		<div class="shop-cart-total">
			<span class="order-items"><span class="order-items-count">${totalItems}</span> </span> <span
				class="order-text notonsidebar"></span> <span class="order-total right bold col">Total: <span
				class="order-total-amount">${total}</span>€
			</span>
		</div>
		<div class="shop-cart-footer clear">
			<span class="shop-cart-checkout button right">check out</span> <span class="shop-cart-update button col1">box</span>
			<span class="shop-cart-continue button col3 notonsidebar">continue shopping</span>
			<div class="debug clear">
				osCsid:<span class="oscSid"><?php echo $osCsid; ?>${osCsid}</span> customer_id:<span class="customer_id"><?php echo $customer_id; ?>${customer_id}</span>
			</div>
		</div>
	</div>
	<!-- shop-cart-box -->
</div>
<?php
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
	function osc_inject_shopcart_entry_template() {
		echo '<script id="shopcart-entry-template" type="text/x-jquery-tmpl">
		';
		?>
<div class="shop-cart-entry format-standard">
	<span class="products-price cart-entry col right bold">${products_price_total}€</span> <span
		class="cart-entry-delete cart-entry button col ">delete</span> <span class="products-thumb cart-entry col"><img
		src="${parents_thumb}" /> </span> <span class="products-desc cart-entry col col2"> <span
		class="products-name cart-entry col float">${parents_name}</span> <span class="products-model cart-entry col float">${parents_model}</span>
		<span class="products-format cart-entry col float">${products_format}</span>
	</span> <span class="products-qty cart-entry col col3">${products_qty}</span> <span
		class="products-price-tax cart-entry hidden">${products_price_tax}</span> <span class="cart-index cart-entry hidden">${index}</span>
	<span class="products-id cart-entry hidden">${products_id}</span>
</div>
<!-- end shop-cart-entry -->
<?php   echo '</script>
';
	}
	///////////////////////////////////////////////////////////////////////////
	/** login window with jquery */
	function osc_inject_login_form_template() {
		echo '<script id="login-form-template" type="text/x-jquery-tmpl">
		';
		?>
<div id="login-form">
	<form>
		<fieldset>
			<label for="email">Email:</label> <input type="text" name="email" id="email" value=""
				class="text ui-widget-content ui-corner-all" /> <label for="password">Password:</label> <input type="password"
				name="password" id="password" value="" class="text ui-widget-content ui-corner-all" />
		</fieldset>
	</form>
</div>
<!-- end login-form -->
<?php   echo '</script>
';
	}
	///////////////////////////////////////////////////////////////////////////
	/** register window with jquery */
	function osc_inject_register_form_template() {
		echo '<script id="register-form-template" type="text/x-jquery-tmpl">
		';
		?>
<div id="register-form">
	<form>
		<fieldset>
			<label for="email">Email:</label> <input type="text" name="email" id="email" value=""
				class="text ui-widget-content ui-corner-all" /> <label for="password">Password:</label> <input type="password"
				name="password" id="password" value="" class="text ui-widget-content ui-corner-all" />
		</fieldset>
	</form>
</div>
<!-- end register-form -->
<?php   echo '</script>
';
	}
} // EOC osc_product_templates

?>