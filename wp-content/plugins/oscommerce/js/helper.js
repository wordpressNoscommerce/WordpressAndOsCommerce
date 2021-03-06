/**
 * helper functions to reduce size of osc javascript
 **/
// lets pollute some global namespace :)
var mp3Prefix = 'http://www.shopkatapult.com/prxlstz/';
var shopPrefix = 'http://dev2.shitkatapult.com:8080';
var allArtReleases = 'All Releases Of Artist';
// TODO deal with the tabnames better
var artReltabNames = [ {
	tab : allArtReleases
} ];
// , { tab : "Singles" }, { tab : "Albums" }, { tab : "Appears On" }, { tab : "Videos" } ]; var artistPage = '/artists';
var artistPage = '/artists';
var releasePage = '/releases';
var osCsid = 0; // osc session
var customer_id = 0; // osc customer
var customer_firstname = 0; // osc customer
var cart = 0;
var shoppingBoxCtx = 0;
var ajaxPending = false;
var VAT = 19.; // 0.;
var shopFormat = undefined;

// ###############################################################################
// #### GETTER HELPER ############################################################
// ###############################################################################
// to deal with funny modifications -- space in front for concatenation
function fixTabName(tabName) {
	return tabName.toLowerCase().replace(/ /g, '_');
}

// move hash parms to URL parms
function hashToParms() {
	var parms = '';
	var nohash = location.href.substr(0, location.href.indexOf('#'));
	if (location.hash) {
		parms = encodeURI(location.hash.substr(location.hash.indexOf('#') + 1));
	}
	if (nohash.indexOf('?') >= 0)
		return nohash + parms;
	else
		return nohash + '?' + parms;
}

function getPageVarForCtx(curTabCtx) {
	if (curTabCtx == '#release-format-tabs')
		return 'rpage';
	else
		return 'paged';
}

function getUrlParm(name, url) {
	return decodeURI((RegExp(name + '=' + '(.+?)(&|$)').exec(url) || [ , null ])[1]);
}
// get parms from hash
function getHashVars()
{
    var vars = [], hash;
    var hashes = window.location.hash.slice(window.location.hash.indexOf('#') + 1).split('&');
    for(var i = 0; i < hashes.length; i++)
    {
        hash = hashes[i].split('=');
        vars.push(hash[0]);
        vars[hash[0]] = hash[1];
    }
    return vars;
}
function getTabDiv(curTabCtx, tabName) {
	var divsel = 'div[id^=' + fixTabName(tabName) + ']';
	if (typeof curTabCtx == 'string')
		return jQuery(curTabCtx).find(divsel);
	else
		return curTabCtx.find(divsel);
}
function getTabLnk(curTabCtx, tabName) {
	var ansel = 'ul.ui-tabs-nav li a[href^=#' + fixTabName(tabName) + ']'; // this needs the hash
	if (typeof curTabCtx == 'string')
		return jQuery(curTabCtx).find(ansel);
	else
		return curTabCtx.find(ansel);
}
function getTabFromSelector(tabSelector) {
	var left = tabSelector.indexOf('div[id^=');
	var right = tabSelector.indexOf(']');
	if (left >= 0)
		return tabSelector.substring(left + 8, right);
}
// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// 2 DOM accessor functions to get the paging state
function getLastPageOfTab(tabSelector) {
	if (typeof tabSelector == 'string')
		return (+jQuery(tabSelector + ' span.thispage').html());
	else
		return (+tabSelector.find('span.thispage').html());
}
function getMaxPageOfTab(tabSelector) {
	if (typeof tabSelector == 'string')
		return (+jQuery(tabSelector + ' span.maxpage').html());
	else
		return (+tabSelector.find('span.maxpage').html());
}
// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// context dependent values and functions
function getTabCtx(href, isPageLoad) {
	if (isReleasePage(href))
		return '#product-format-tabs';
	else if (isShopPage(href))
		return '#product-format-tabs';
	else if (isArtistReleasePage(href) && !isPageLoad) // when we need to load artists from json first
		return '#release-format-tabs';
	else if (isArtistPage(href))
		return '#artist-set-tabs';
	else
		if (!isPageLoad)
			if (href)
				console.assert(false);
	// "product-detail-tabs"
}
function getTabParm(href, isPageLoad) {
	if (isReleasePage(href))
		return 'format';
	else if (isShopPage(href)) {
		if (href.indexOf('#') >= 0) {
			shopFormat = href.substring(href.indexOf('#')+1);
			var format = getUrlParm('format',shopFormat);
			if (format != 'null')
				shopFormat = format;	//use url parm if found
			else {
				var ampPos = shopFormat.indexOf('&') ; 
				if (ampPos< 0) {		// no & so only the tabname
					if (shopFormat.indexOf('=') > 0)
						shopFormat = 'Vinyl';
				} else {
					shopFormat = shopFormat.substring(ampPos);
					console.log('found string for navigation: %s', shopFormat);
				}
			}
			return 'shopFormat';
		} else
			return 'format';
	} else if (isArtistReleasePage(href) && !isPageLoad) // when we need to load artists from json first
		return 'artRelTab';
	else if (isArtistPage(href))
		return 'artistSet';
	else
		console.assert(false);
}
function getTabLoaderFun(href, isPageLoad) {
	if (isReleasePage(href))
		return 'loadProductsForTab';
	else if (isShopPage(href))
		return 'loadProductsForTab';		
	else if (isArtistReleasePage(href) && !isPageLoad) // when we need to load artists from json first
		return 'loadProductsForArtist(artists_id)';
	else if (isArtistPage(href))
		return 'loadArtistsForTab';
	else
		console.assert(false);
}
function getClickHandler(href, isPageLoad) {
	if (isReleasePage(href))
		return productClickHandler;
	else if (isShopPage(href))
		return productClickHandler;		
	else if (isArtistReleasePage(href) && !isPageLoad) // when we need to load artists from json first
		return releaseClickHandler;
	else if (isArtistPage(href))
		return artistClickHandler;
	else
		console.assert(false);
}
function getJsonData(href) {
	if (isReleasePage(href)) {
		// proper empty test
		if (typeof products === 'undefined' || products == undefined || countProperties(products) == 0)
			return {};
		else
			return products;
	} else if (isArtistPage(href)) {
		// proper empty test
		if (typeof manufacturers === 'undefined' || manufacturers == undefined || countProperties(manufacturers) == 0)
			return {};
		else
			return manufacturers;
	} else
		return {};
}
function getPageSize(href) {
	if (isArtistPage(href))
		return artistsPageSize;
	if (isReleasePage(href))
		return productsPageSize;
}
// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function useFormatTabs(href) {
	if (href == undefined)
		href = location.href;
	if (typeof href != 'string')
		href = href.selector; // use the selector string of jQ object
	return href.indexOf('/releases') >= 0 || href.indexOf('/shop') >= 0;
}
// match pathname or tabctx string dont match trailing slash which is optional
function isReleasePage(href) {
	if (href == undefined)
		href = location.href;
	if (typeof href != 'string')
		href = href.selector; // use the selector string of jQ object
	return href.indexOf('/releases') >= 0 || href.indexOf('#product-format-tabs') >= 0;
}
function isArtistPage(href) {
	if (href == undefined)
		href = location.href;
	if (typeof href != 'string')
		href = href.selector; // use the selector string of jQ object
	return href.indexOf('/artists') >= 0 || href.indexOf('#artist-set-tabs') >= 0;
}
function isShopPage(href) {
	if (href == undefined)
		href = location.href;
	if (typeof href != 'string')
		href = href.selector; // use the selector string of jQ object
	return href.indexOf('/shop') >= 0 || href.indexOf('#product-format-tabs') >= 0;
}
function isArtistReleasePage(href) {
	if (href == undefined)
		href = location.href;
	if (typeof href != 'string')
		href = href.selector; // use the selector string of jQ object
	if (href.indexOf('#') <0)
		return false;	// no hash no releases
	if (href.indexOf('#artist-set-tabs') >= 0)
		return false;
	var result = false;
	jQuery.each(artReltabNames, function(i, v) { // look for a release tab link
		if (href.indexOf(fixTabName(v.tab)) >= 0) {// dont forget to normalize name
			result = true;
			return false; // found
		}
	});
	return result;
}
// checks if location.hash and href are in the same page context
// TODO this has to go when we render everything from this script
// instead of relying on the server to provide rudimentary but required HTML to render into
function isCurCtx(href) {
	return ((isReleasePage() && isReleasePage(href)) 
			|| (isArtistPage() && isArtistPage(href))
			|| (isShopPage() && isShopPage(href))
			);
}

// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// helper to count properties
function countProperties(obj) {
	var count = 0;
	for ( var prop in obj)
		if (obj.hasOwnProperty(prop))
			++count;
	return count;
}
// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function hasProperties(obj) {
	for ( var p in obj)
		return p == p;
	return false;
}
//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function scrollTo(selector,top) {
	if (top) {
		document.body.scrollIntoView(top);		/// override top scrolls
		return;
	}
	if (typeof selector == 'string')
		selector = jQuery(selector);
	var elem = selector.get(0);
	if (elem)
		elem.scrollIntoView(top);
}
//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function checkLength(tips, o, n, min, max) {
	if (o.val().length > max || o.val().length < min) {
		o.addClass("ui-state-error");
		updateTips(tips, "Length of " + n + " must be between " + min + " and " + max + ".");
		return false;
	} else {
		return true;
	}
}
//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function checkRegexp(tips, o, regexp, n) {
	if (!(regexp.test(o.val()))) {
		o.addClass("ui-state-error");
		updateTips(tips, n);
		return false;
	} else {
		return true;
	}
}
//++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
function updateTips(tips, t) {
	tips.text(t).addClass("ui-state-highlight");
	setTimeout(function() {
		tips.removeClass("ui-state-highlight", 1500);
	}, 500);
}
// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
// take 2 jq selectors to describe what we move in what
function draggableImage(img,mask) {
	mask.css({
		top : 0,
		left : 0
	});
	var maskWidth = mask.width();
	var maskHeight = mask.height();
	var imgPos = img.offset();
	var imgWidth = img.width();
	var imgHeight = img.height();

	var x1 = (imgPos.left + maskWidth) - imgWidth;
	var y1 = (imgPos.top + maskHeight) - imgHeight;
	var x2 = imgPos.left;
	var y2 = imgPos.top;

	img.draggable({
//		containment : [ x1, y1, x2, y2 ]
	});
	img.css({
		cursor : 'move'
	});
}