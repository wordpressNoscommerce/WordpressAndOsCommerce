/**
 * helper functions to reduce size of osc javascript
 **/
// ###############################################################################
// #### GETTER HELPER ############################################################
// ###############################################################################
// to deal with funny modifications -- space in front for concatenation
function fixTabName(tabName) {
	return tabName.toLowerCase().replace(/ /g, '_');
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
	else if (isArtistReleasePage(href) && !isPageLoad) // when we need to load artists from json first
		return '#release-format-tabs';
	else if (isArtistPage(href))
		return '#artist-set-tabs';
	else
		if (!isPageLoad) console.assert(false);
	// "product-detail-tabs"
}
function getTabParm(href, isPageLoad) {
	if (isReleasePage(href))
		return 'format';
	else if (isArtistReleasePage(href) && !isPageLoad) // when we need to load artists from json first
		return 'artRelTab';
	else if (isArtistPage(href))
		return 'artistSet';
	else
		console.assert(false);
}
function getTabLoaderFun(href, isPageLoad) {
	if (isReleasePage(href))
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
	return ((isReleasePage() && isReleasePage(href)) || (isArtistPage() && isArtistPage(href)));
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
	if (typeof selector == 'string')
		selector = jQuery(selector);
	var elem = selector.get(0);
	if (elem)
		elem.scrollIntoView(top);
}
