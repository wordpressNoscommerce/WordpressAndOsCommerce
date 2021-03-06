/***********************************************************************************************************************
 * javascript for oscommerce tabbed UI JSON injection is used for initial data to be shown on the first tab additonal
 * pages are loaded using AJAX requests to PHP server pages get_product_data.php, get_product_page.php,
 * get_manufacturer_page.php state is kept in location.hash to make local state bookmarkable (REST)
 *
 * @author: uvwild@gmail.com
 * @date: April 2012
 **********************************************************************************************************************/

jQuery.noConflict();
(function($) { // this defines a scope so we cannot simply split this up into multiple files
	$(function() {
		if (console != undefined) {
			console.log = function() {};
			if (console.clear != undefined)
				console.clear();
		}
		// dont pollute global namespace too much
		// config values for jquery and player
		var fadeintime = 500; // animation parameter
		var loadingPage = false;
		var isPageLoad = true;
		var playerId = 'jquery_jplayer_1';
		var playerShown = false;
		var playerSelector = '#' + playerId;
		var playerContent = 'jp_container_1';
		var playerContentSelector = '#' + playerContent;
//		var labels = {};

		// PHP debugging
		//		var XDEBUG = { 'XDEBUG_SESSION_START': 'ECLIPSE_DBGP', 'KEY':'123456789012345'};
		document.cookie= 'XDEBUG_SESSION_START=ECLIPSE_DBGP';
		document.cookie= 'KEY=123456789012345';
		var XDEBUGparms = '';//'&XDEBUG_SESSION_START=ECLIPSE_DBGP&KEY=123456789012345';
//		var XDEBUGparms = '&XDEBUG_SESSION_START=ECLIPSE_DBGP&KEY=123456789012345';

		var checkoutUrl = shopPrefix + '/checkout_payment.php';

		var oscPrefix = '/wp-content/plugins/oscommerce';
		var oscLinkPrefix = oscPrefix+'/osclink';
		var shoppingCartUrl = oscLinkPrefix + '/wp-handle_cart.php';
		var loginUrl = oscLinkPrefix + '/wp-login.php?action=process' + XDEBUGparms;
		var logoffUrl = oscLinkPrefix + '/wp-logoff.php' + '?a=0' + XDEBUGparms;
		var createAccountUrl = oscLinkPrefix + '/wp-create_account.php';

		// our local database where we keep everything
		var prodmap = {};
		var manumap = {};
		var relemap = {};
		// state variables
		var lastProductId = 0; // keep state
		var lastArtistId = 0;

		// URL Parms == application state
		var products_id = 0;
		var artists_id = 0;
		var paged = 1;
		var rpage = 1;// different pageno for releases
		var format = 'Vinyl'; // the tab of release format
		// var artRelTab; // the tab of the artist releases
		var artistSet = 'main'; // the tab of the arist sets
		getStateFromUrl(location.href); // set URL parms
		// keep new and changed parameters in location.hash to avoid page reload

		// the click Handler for products using closures created with selfexecuting functions
		// BUT they need to be defined before first use below!!!!
		var productClickHandler = function() {
			var curCtx = '#product-format-tabs';
			var target = '#product-detail';
			var toggleFunc = toggleProduct;
			return boxClickHandler(curCtx, target, toggleFunc);
		}();
		// the click Handler for artist releases is just like the product one but opening default to listenbuy tab
		var releaseClickHandler = function() {
			var curCtx = '#release-format-tabs';
			var target = '#release-detail';
			var toggleFunc = toggleProduct;
			return boxClickHandler(curCtx, target, toggleFunc);
		}();

		$('.wpui-light').removeClass('wpui-light').addClass('shit-theme'); // change theme for us
		attachNavClickHandler();
		var curTabCtx = getTabCtx(location.href, true); // we must load artist before artist releases!
		if (renderJsonData(curTabCtx)) { // returns false if wrong context
			fixWpTabHeader(curTabCtx); // modify formating of tabs
			initPage(location.href,true);
			var json = getJsonData();
			for ( var tab in json) {
				delete json[tab]; // remove container after loading page
			}
		}
		if (location.hash.indexOf('action') > 0) { // we have an action
			actionHandler();
		}
		// loading labels

		// error handling
		var ajaxError = $('<div id="ajaxError"></div>');
		ajaxError.ajaxError(function(event, jqXHR, ajaxSettings, thrownError){
			ajaxError.dialog({ modal: true});
			ajaxError.html('<p>calling '+ajaxSettings.url+':'+ thrownError+'</p>'+jqXHR.responseText);

		});
		// ##########################################################################
		// ##########################################################################
		// render the injected JSON data from the backend
		function renderJsonData(curTabCtx) {
			var pageno = 1;
			var templateName;
			var totalItems;
			var pageSize;
			if (isReleasePage()) {
				if (typeof products === 'undefined' || products == undefined || countProperties(products) == 0) { // empty test
					var msg = 'No JSON products received for curTabCtx: ' + curTabCtx + ' page: ' + paged;
					$('#content .page .post-content').append(
							'<h1 style="color: red;">' + msg + '<br>Check Database Connection to shopdb</h1>');
					console.error(msg);
					return false;
				}
				json = products;
				templateName = '#product-box-template';
				totalItems = productsCount;
				pageSize = productsPageSize;
				// productsReleaseFormats
			} else if (isArtistPage()) {
				if (typeof manufacturers === 'undefined' || manufacturers == undefined || countProperties(manufacturers) == 0) { // empty
					// test
					var msg = 'no JSON manufacturers received for curTabCtx: ' + curTabCtx + ' page: ' + paged;
					$('#content .page .post-content').append(
							'<h1 style="color: red;">' + msg + '<br>Check Database Connection to shopdb</h1>');
					console.error(msg);
					return false;
				}
				json = manufacturers;
				templateName = '#manufacturer-box-template';
				totalItems = manufacturersCount;
				pageSize = manufacturersPageSize;
				// manufacturersSets
			} else if (isShopPage()) {
				if (typeof products === 'undefined' || products == undefined || countProperties(products) == 0) { // empty test
					var msg = 'No JSON products received for curTabCtx: ' + curTabCtx + ' page: ' + paged;
					$('#content .page .post-content').append(
							'<h1 style="color: red;">' + msg + '<br>Check Database Connection to shopdb</h1>');
					console.error(msg);
					return false;
				}
				json = products;
				templateName = '#product-box-template';
				totalItems = productsCount;
				pageSize = productsPageSize;
			} else
				return false; // ABORT nothing to do
			// cleanup DOM struct from wordpress to match grid page
			$('#content .entry .page').appendTo('#content').prev('.entry').remove();
			hideAllTabs(curTabCtx);

			// now inject the JSON data as given (could be scaled up)
			for ( var tab in json) { // TODO what about the page number?
				var newItems = json[tab];
				addToCache(newItems);
				renderItemsInTab(curTabCtx, newItems, tab, pageno, templateName);
				renderPagination(getTabDiv(curTabCtx, tab), pageSize, totalItems, newItems, 1);
				// put a clickhandler in new pagination divs
				$(curTabCtx + ' div.pagination a').unbind('click').click(changeStateHandler);
			}
			return true; // rendering OK
		}
		// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
		// called to rebuild the page also from click handlers - extra parm to distinguish
		function initPage(href, isPageLoading) {
			isPageLoad = isPageLoading;
			console.log('initPage(%o)', href);
			if (location.href != href) // if we set this to same value the page reloads forever!!!
				location.href = href; // maintain state of javascript app in location.hash
			var curTabCtx = getTabCtx(href, isPageLoad);
			// use eval to return varnames from independent js context in helper.js
			var curTab = eval(getTabParm(href, isPageLoad));
			var curLoader = eval(getTabLoaderFun(href, isPageLoad));
			var pageno = eval(getPageVarForCtx(curTabCtx));
			curLoader(curTabCtx, curTab, pageno); // try to load the requested page
			loadGigpressWidget();
			isPageLoad = false; /// reset flag
		}
		// ###############################################################################
		// ###############################################################################
		// called to show / extend another tab (artistId unused!!!)
		function loadProductsForTab(curTabCtx, tabName, pageno) {
			if (needToLoad(curTabCtx, tabName, pageno)) {
				var fetchurl = oscPrefix + "/get_product_page.php?json=1&format=" + tabName;
				if (pageno != undefined)
					fetchurl += "&paged=" + pageno; // append extra param
				// the url returns a tuple with record count and result
				console.log('loading more products' + fetchurl);
				getTabLnk(curTabCtx, tabName).addClass('loading');
				$.ajax({
					type : 'GET',
					url : fetchurl,
					success : function(result, textStatus, jqXHR) {
						getTabLnk(curTabCtx, tabName).removeClass('loading');
						if (jqXHR.getResponseHeader('Content-type') == 'application/json') {
							addToCache(result[3]); // this is the product list
							renderItemsInTab(curTabCtx, result[3], tabName, pageno, '#product-box-template');
							renderPagination(getTabDiv(curTabCtx, tabName), result[0], result[1], result[3], pageno);
							selectTab(curTabCtx, tabName, loadProductsForTab, productClickHandler);
							// scroll box to bottom
							scrollTo(curTabCtx, false);
						} else {
							console.error(result);
							$('#product-detail').html(result).addClass('error');
						}
					},
					error : function(jqXHR, textStatus, errorThrown) {
						getTabLnk(curTabCtx, tabName).removeClass('loading');
						console.error('request(%s) error(%o)', fetchurl, jqXHR);
						var msg = "status=" + jqXHR.status + " " + errorThrown + " when trying to load Releases for " + tabName;
						getTabDiv(curTabCtx, tabName).html('<h3 class="error">' + msg + '</h3>').addClass('error');
					}
				});
			} else { // simply show the selected tab
				selectTab(curTabCtx, format, loadProductsForTab, productClickHandler);
			}
		}
		// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
		// called to show / extend another tab
		function loadArtistsForTab(curTabCtx, tabName, paged) {
			// TODO make sure artists_id is reset when needed
			if (needToLoad(curTabCtx, tabName, paged)) {
				var fetchurl = oscPrefix + "/get_manufacturer_page.php?json=1&artistSet=" + tabName;
				if (paged)
					fetchurl += "&paged=" + paged; // append extra param
				if (artists_id)
					fetchurl += "&artistId=" + artists_id; // append extra param
				if (XDEBUGparms != '')
					fetchurl += XDEBUGparms;
				// the url returns a tuple with record count and result
				// $('div.pagination').addClass('ui-tabs-hide'); // hide all before loading
				getTabLnk(curTabCtx, tabName).addClass('loading');
				$.ajax({
					type : 'GET',
					url : fetchurl,
					success : function(data, textStatus, jqXHR) {
						getTabLnk(curTabCtx, tabName).removeClass('loading');
						if (data.indexOf('No Records found') >= 0) {
							console.error(data);
							$('#artist-detail').html(data).addClass('error');
						} else {
							var result = eval('(' + data + ')'); // eval json array
							addToCache(result[3]);
							renderItemsInTab(curTabCtx, result[3], tabName, paged, '#manufacturer-box-template');
							renderPagination(getTabDiv(curTabCtx, tabName), result[0], result[1], result[3], paged);
							selectTab(curTabCtx, tabName, loadArtistsForTab, artistClickHandler);
							// scroll curTabCtx box to bottom
							scrollTo(curTabCtx, false);
						}
					},
					error : function(jqXHR, textStatus, errorThrown) {
						getTabLnk(curTabCtx, tabName).removeClass('loading');
						console.error('request(%s) error(%o)', fetchurl, jqXHR);
						var msg = "status=" + jqXHR.status + " " + errorThrown + " when trying to load Releases for " + tabName;
						getTabDiv(curTabCtx, tabName).html('<h3 class="error">' + msg + '</h3>').addClass('error');
					}
				});
			} else { // check if it has been rendered if not do so ... use id matching as we have suffixes
				var tabSelector = getTabDiv(curTabCtx, tabName);
				if (tabSelector.find('div.wp-tab-content').contents().length == 0) {
					renderItemsInTab(curTabCtx, manufacturers[tabName], tabName, 1, '#manufacturer-box-template');
				}
				// then show it
				selectTab(curTabCtx, tabName, loadArtistsForTab, artistClickHandler);
			}
		}
		// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
		// called to fetch releases for artists, parms have to adhere to function prototype
		function loadProductsForArtist(artistId) { // use closure to supply the extra artistId
			return function(curTabCtx, tabName, pageno) {
				if (relemap[artistId] == undefined)
					relemap[artistId] = []; // init empty release ARRAY before loading
				// load new data if empty or next page (as this is an object now count the props instead of length)
				if (relemap[artistId] == undefined || countProperties(relemap[artistId]) == 0 || pageno > 1) {
					var fetchurl = oscPrefix + "/get_product_page.php?json=1&artistId=" + artistId;
					fetchurl += "&format=All"; // TODO read all formats for now
					fetchurl += "&pagesize=3"; // only 3 at a time
					if (pageno != undefined)
						fetchurl += "&paged=" + pageno; // append extra param
					// DATA = array($this->records_per_page,$this->product_count, $this->max_page,
					// $this->result,$this->release_formats);
					getTabLnk(curTabCtx, tabName).addClass('loading');
					$.ajax({
						type : 'GET',
						url : fetchurl,
						success : function(result, textStatus, jqXHR) {
							getTabLnk(curTabCtx, tabName).removeClass('loading');
							if (jqXHR.getResponseHeader('Content-type') == 'application/json') {
								addToCache(result[3], artistId); // this is the product list and artist relation
								renderItemsInTab(curTabCtx, result[3], tabName, pageno, '#release-box-template'); // use pageno nos
								// function renderPagination(curTabCtx, pageSize, totalRecCount, newItems, paged)
								renderPagination(getTabDiv(curTabCtx, tabName), result[0], result[1], result[3], pageno);
								selectTab(curTabCtx, tabName, loadProductsForArtist(artistId), releaseClickHandler);
								// scroll box to bottom
								scrollTo(curTabCtx, false);
							}else {
								console.error(result);
								getTabDiv(curTabCtx, tabName).html('<h3 class="error">' + result + '</h3>').addClass('error');
							}
						},
						error : function(jqXHR, textStatus, errorThrown) {
							getTabLnk(curTabCtx, tabName).removeClass('loading');
							console.error('request(%s) error(%o)', fetchurl, jqXHR);
							var msg = "status=" + jqXHR.status + " " + errorThrown + " when trying to load Releases for "
									+ manumap[artistId].manufacturers_name;
							getTabDiv(curTabCtx, tabName).html('<h3 class="error">' + msg + '</h3>').addClass('error');
						}
					});
				} else { // check if it has been rendered if not do so ... use id matching as we have suffixes
					var tabSelector = getTabDiv(curTabCtx, tabName);
					if (tabSelector.find('div.wp-tab-content').contents().length == 0) {
						renderItemsInTab(curTabCtx, relemap[artistId], tabName, 1, '#release-box-template');
					}
					selectTab(curTabCtx, tabName, loadProductsForArtist(artistId), releaseClickHandler);
				}
			};
		}
		// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
		// check if more data is needed for current tab
		function needToLoad(curTabCtx, tabName, pageno) {
			var result = false;
			var tabSelector = getTabDiv(curTabCtx, tabName);
			var numPosts = tabSelector.find(' .grid .post').length;
			if (numPosts == 0) { // no posts in our tab
				result = true; // so need to load!
			} else {
				var curPage = getLastPageOfTab(tabSelector);
				result = pageno > curPage;
			}
			return result;
		}
		// ##########################################################################
		// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
		// use closure to pass artist_id TODO is this necessary as we have a global var
		function renderProductsForArtist(artists_id) {
			return renderItemsInTab;
		}
		// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
		// generic render new prod data into named format tab
		function renderItemsInTab(curTabCtx, newItems, tabName, pageno, templateName) {
			console.assert(newItems != undefined && newItems.length);
			var fixedTabName = tabName.toLowerCase().replace(/ /g, '_');
			// the id property gets garbled when tabs get regenerated... so match it instead
			var tabDivSel = getTabDiv(curTabCtx, tabName);
			var template = $(templateName);
			console.assert(template.length); // make sure its found
			var loopDiv = prepareLoopDiv(tabDivSel);
			// console.trace();
			console.log('renderItemsInTab %s with format %s, artistSet %s, paged = %d, pageno = %d', tabName, fixedTabName,
					artistSet, paged, pageno);
			// the template
			$.each(newItems, function(i, v) {
				v.format = fixedTabName;
				v.artist_set = artistSet;
				v.paged = (templateName == '#release-box-template') ? paged : pageno; // special case for artist releases
				v.rpage = pageno;
			});
			// show title for artist grid underneath the artist release tabset append text also
			if (templateName == '#release-box-template') {
				showArtistGridHeader();
			}
			template.tmpl(newItems).appendTo(loopDiv); // simply render the old ones
		}
		// ##########################################################################
		// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
		// this is depending on the wpui tab code which arranges the elems around
		function selectTab(curTabCtx, tabNameOrg, loadItemsForTab, clickHandler) {
			var tabName = tabNameOrg.toLowerCase().replace(/ /g, '_');
			var tabSelector = getTabDiv(curTabCtx, tabNameOrg);
			if (tabSelector.find(':visible').length) { // we are on the right tab already
				// but we still have to deal with additional parms or posts
				finishTab(curTabCtx, tabSelector, tabName, loadItemsForTab, clickHandler);
				return; // nothing more to do we are active already
			}
			// replace wptabs clickhandler so we can fuck around with it
			// attachTabClickHandler(curTabCtx); // now done with fidxtabheaders

			// remove previous jplayer first TODO check when and how its necessary
			if (products_id != undefined && products_id != 0 && $(playerSelector).length) {
				$(playerSelector).jPlayer("destroy");
				console.info('player for %d destroyed', products_id);
			}
			if (lastProductId != 0) { // productdetail always changes
				$('#product-detail').empty(); // clear prod detail when showing new tab
				lastProductId = 0; // clear the state
			}
			if (isReleasePage(curTabCtx) || isArtistPage(curTabCtx)) {
				// remove the title from the tab also
				$('div#artist-set-tabs.wp-tabs  > div.ui-tabs').children('div.ui-tabs-panel:visible').find('h3').html(
						function(i, html) {
							return html.replace(/ Artist Overview/, '');
						}).hide(); //css('display', 'none');
				$('#artist-detail').empty(); // clear artist detail only when not showing releases
				lastArtistId = 0;
			}

			// remove selection for ALL tab header LI
			var selNavLi = $(curTabCtx + ' ul.ui-tabs-nav li.ui-state-default');
			selNavLi.removeClass('ui-tabs-selected ui-state-active');
			console.log('removing selection for %o', selNavLi);

			// set active class for LI link immediately
			var selLi = $(curTabCtx).find('LI.ui-state-default').has('A[href=#' + tabName + ']');
			console.assert(selLi.length);
			selLi.addClass('ui-tabs-selected ui-state-active');
			hideAllTabs(curTabCtx);

			// TODO USE EASING on the content DIVS
			// remove hidden class add set new selection for tab
			var newSelTab = $(tabSelector);
			newSelTab.removeClass('ui-tabs-hide').addClass('ui-tabs-selected');

			console.log('selectTab(%s, %s)', tabNameOrg, curTabCtx);
			finishTab(curTabCtx, tabSelector, tabName, loadItemsForTab, clickHandler);
		}
		// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
		// finish things on current tab (pagination, addboxclick, show detail data)
		function finishTab(curTabCtx, tabSelector, tabName, loadItemsForTab, clickHandler) {
			console.log('finishTab (%o,%o,%o)', curTabCtx, tabSelector, tabName);// , loadItemsForTab, clickHandler);
			// remove pagination if we loaded last page
			showPagination(curTabCtx, tabSelector, tabName, loadItemsForTab);
			// dont forget to walk the attachGridClickhandler and fix detail
			attachGridClickhandler(tabSelector, clickHandler);
			// go deeper in the data when needed (this may create recursion)
			// this uses the urlparms for page
			if (curTabCtx == '#artist-set-tabs') {
				if (artists_id != undefined)
					if (getArtistMaster(artists_id))
						toggleArtist(artists_id, artistSet);
			} else {
				if (products_id != undefined) {
					var detail = (curTabCtx == '#product-format-tabs')?"#product-detail": "#release-detail";
					toggleProduct(curTabCtx, products_id, format, detail);
				}
			}
			loadingPage = false; // enable auto loading again
		}
		// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
		function renderPagination(tabSelector, pageSize, totalRecCount, newItems, pageno) {
			var maxPage = Math.ceil(totalRecCount / pageSize);
			var loaded = pageSize * pageno;
			if (pageno == maxPage)
				loaded = totalRecCount; // max loaded is totalRecCount on last page
			var pager = tabSelector.find('div.pagination a');
			// update previous pager
			if (pager.length) {
				pager.removeClass('loading').text('LOAD MORE'); // stop the loading image
				// put data in right DIV for current curTabCtx
				tabSelector.find('span.thispage').html(pageno);
				tabSelector.find('span.loaded').html(loaded);
				tabSelector.find('span.maxpage').html(maxPage);
				tabSelector.find('span.reccount').html(totalRecCount);
				// add tabname to anchor href to be picked up by the clickHandler
				var newHref = addHashParm(pager.attr('href'), getTabParm(tabSelector), eval(getTabParm(tabSelector.selector)));
				if (tabSelector.parents('#release-format-tabs').length)
					newHref = addHashParm(newHref, 'rpage', (+pageno) + 1); // link to the next page
				else
					newHref = addHashParm(newHref, 'paged', (+pageno) + 1); // link to the next page
				pager.attr('href', newHref);
			} else { // write new one for ARTIST RELEASE PAGES
				console.log('add missing pagination for ' + tabSelector);
				var template = $('#pagination-template');
				// <span class="pages">loaded <span class="thispage">${paged}</span>
				// out of <span class="maxpage">${maxpage}</span> pages
				// (<span class="loaded">${loaded}</span>/<span class="reccount">${reccount}</span> items) </span>
				// <a class="nextpostslink" href="${href}">LOAD MORE</a>
				console.assert(template.length);
				// append to visible div.ui-tabs-panel for our tab
				// all our parms other parms are already in location.hash except the new pageno
				var newHref = location.hash;
				if (tabSelector.parents('#release-format-tabs').length)
					newHref = addHashParm(newHref, 'rpage', (+pageno) + 1); // link to the next page
				else
					newHref = addHashParm(newHref, 'paged', (+pageno) + 1); // link to the next page
				template.tmpl({
					paged : (+pageno),
					maxpage : maxPage,
					loaded : loaded,
					reccount : totalRecCount,
					href : newHref
				}).appendTo(tabSelector); // never mind the visibility thats done later
				// reload pager JQ after creation
				pager = tabSelector.find(' div.pagination a');
				// put a clickhandler in new pagination div
				pager.unbind('click').click(changeStateHandler);
			}
			console.assert(pager.length);

			var pageDiv = pager.parent();
			if (loaded < maxPage) {
				pageDiv.removeClass('ui-tabs-hide');
			} else { // hide pagination if only single page!
				pageDiv.addClass('ui-tabs-hide');
			}
		}
		// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
		// show pagination link only if there is another page to load
		function showPagination(curTabCtx, tabSelector, tabName, loadItemsForTab) {
			var lastPage = getLastPageOfTab(tabSelector);
			var maxpage = getMaxPageOfTab(tabSelector);
			var pageDiv = tabSelector.find('div.pagination');
			if (lastPage < maxpage) {
				pageDiv.removeClass('ui-tabs-hide');
			} else { // hide pagination if only single page!
				pageDiv.addClass('ui-tabs-hide');
			}
		}
		/**
		 * ########################################################################### ############################## ARTIST
		 * MODE ################################ ###########################################################################
		 */
		// the click Handler for products
		// TODO merge code with below its the same
		function artistClickHandler(e) {
			e.preventDefault();
			e.stopPropagation(); // TODO one seems superfluous
			// post click handler not working in this context
			var newhash = $(this).find('a.thumb').attr('href').match(/.*#(.*)/)[1];
			// set new hash
			location.hash = "#" + newhash;
			// TODO replace this with urlextractor from hash its all in there
			var box = $(e.currentTarget);
			var artistId = box.find('span.artist-id').html();
			// id of containing tabs-panel is our current product
			// format/artist set
			var tab = box.closest('.ui-tabs-panel').attr('id');
			console.log('selectTab: ' + tab + '  artistId: ' + artistId);
			return toggleArtist(artistId, tab);
		}
		// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
		// show product data in product-detail DIV
		// TODO we need to deal with the release format also
		function toggleArtist(artistId, artistSet) {
			console.log('toggledArtist');
			var seltor = '#artist-detail';
			if (lastArtistId == artistId) {
				$(seltor).toggle(); // toggle if same
				if ($(seltor + ':visible').length == 0) { // no longer visible
					removeArtistGridHeader();
				} else {
					showArtistGridHeader();
					scrollTo(seltor, true); // dont forget to scroll to artist detail
				}
				return;
			} else {
				// reset page numbers when not loading
				if (!isPageLoad)
					rpage = 1;
				console.info('destroyed jplayer for %d', lastProductId);
				$(playerSelector).jPlayer("destroy"); // remove jplayer first TODO check when necessary
				$(seltor).empty(); // clear if new prod after removing player
			}
			// $(seltor).fadeOut(100); // make invisible before writing
			var artist = getArtistMaster(artistId, artistSet);
			if (artist == undefined) {
				console.error("artistruct missing for %s in %s", artistId, artistSet);
				return;
			}
			// products_id = null; // reset previous product selection NOT HERE
			// render template with artist data (header, image, text)
			$('#artist-detail-template').tmpl(artist).appendTo(seltor);
			draggableImage($('img.prod-image-wide'),$('div#artist-image-big'));
			$('#artist-text').resizable();

			$('#artist-image-big').click(toggleArtist); // close handler
			// render releases tab container for artist
			$('#release-format-tabs-template').tmpl(artist).appendTo(seltor);

			// created new tab context above
			var curTabCtx = '#release-format-tabs';
			var curTabCtxJq = $(curTabCtx);
			// create release tabs inside TODO try to create only the ones needed
			$('#release-format-inner-tab-template').tmpl(artReltabNames).appendTo(curTabCtx);

			// fix hash for wptabs
			var temphash = location.hash;
			location.hash = location.hash.replace(/&.*/, '');
			// apply tabs magic to the UL LI graph
			curTabCtxJq.wptabs();
			location.hash = temphash;
			// swap ui-tabs-nav and release-detail
			$('#release-detail').insertBefore($(curTabCtx).find('ul.ui-tabs-nav'));

			fixWpTabHeader(curTabCtx);
			// load release page for artist
			loadProductsForArtist(artistId)(curTabCtx, allArtReleases, rpage);

			// insert social links
			$('#social-buttons-template').tmpl({url: hashToParms()}).appendTo('#artist-detail');

			// load upcoming shows for artist
			loadGigpressWidget(artistId);
			
			// and activate tabs
			$(seltor).fadeIn(fadeintime);
			scrollTo(seltor, true); // dont forget to scroll to artist detail
			lastArtistId = artistId;
			return;
		}
		/**
		 * ###########################################################################
		 * ############################## PRODUCT MODE ###############################
		 * ###########################################################################
		 */
		// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
		function boxClickHandler(curCtx, target, toggleFunc) {
			return function(e) {
				e.preventDefault();
				e.stopPropagation(); // TODO one seems superfluous
				var newhash = $(this).find('a.thumb').attr('href').match(/.*#(.*)/)[1];
				location.hash = "#" + newhash;
				// TODO maybe replace this with urlextractor from hash as all parms are in there
				var box = $(e.currentTarget);
				var prodId = box.find('span.product-id').html();
				var prodModel = box.find('span.product-model').html();
				// id of containing tabs-panel is our current tabset
				// var tab = box.closest('.ui-tabs-panel').attr('id');
				var tab = box.closest('.ui-tabs-panel').find('h3.wp-tab-title').html();
				console.log('Click: ' + tab + '  prodId: ' + prodId + ' prodModel: ' + prodModel);
				toggleFunc(curCtx, prodId, tab, target);
				return false;
			};
		}
		function toggleProductCallBack(curTabCtx, prodId, format, seltorName) {
			return function() {
				return toggleProduct(curTabCtx, prodId, format, seltorName);
			};
		}
		// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
		// show product data in product-detail or release-detail DIV
		function toggleProduct(curTabCtx, prodId, format, targetName) {
			var prod = getProductWithCallback(prodId, toggleProductCallBack(curTabCtx, prodId, format, targetName));
			if (prod == undefined) {
				return false;		// just abort as there is a callback
			}
			var target = $(targetName);
			// prepare target (show/hide)
			if (lastProductId == prodId) {
				target.toggle(); // toggle if same
				if (targetName.indexOf('#release-detail')>= 0) {
					$('#artist-detail').toggle(); 	// show artist detail if hiding release detail
				}
				// remove artist tab title for releases
				$('div#release-format-tabs.wp-tabs  > div.ui-tabs').children('div.ui-tabs-panel:visible')
				.find('h3').hide(); //css( 'display', 'none');
				return;
			} else {
				if (lastProductId != undefined && lastProductId != 0 && $(playerSelector).length) {
					$(playerSelector).jPlayer("destroy"); // remove jplayer first TODO check when necessary
					console.info('player for %d destroyed', lastProductId);
				}
				target.empty(); // clear if new prod after removing player
			}

			// render TEMPLATE () with the prod data
			// @See osc_product_templates->osc_inject_product_detail_template() with the prod data
			$('#product-detail-template').tmpl(prod).appendTo(target);

			// apply tabs magic to the UL LI graph
			var curTabCtxSel = '#product-detail-tabs';
			var curTabCtx = $(curTabCtxSel);
			// fix hash for wptabs
			var temphash = location.hash;
			location.hash = location.hash.replace(/&.*/, '');
			curTabCtx.wptabs();
			location.hash = temphash;

			draggableImage($('img.prod-image-wide'),$('div#prod-image-big'));
			// make text widget resizable
			$('#product-detail-template .ui-tabs .ui-tabs-panel .textwidget').resizable();

			// TODO fix links
			curTabCtx.find('ul.ui-tabs-nav li.ui-state-default a').each(function(i, e) {
				var anchor = $(e);
				// console.log('CLICK added for anchor %s', anchor.attr('href'));
				anchor.click(function(e) { // update hash = REST parms for these links
					var anchor = $(this);

					var newHref = anchor.attr('href');
					newHref = addHashParm(newHref, 'format', fixTabName(format));
					newHref = addHashParm(newHref, 'paged', paged);
					newHref = addHashParm(newHref, 'products_id', prodId);
					location.hash = newHref;
					anchor.attr('href', newHref);
					console.log('anchor %s clicked and locaton.hash set', anchor.attr('href'));
				});
			});

			// fix artist display in product/release-detail TODO add link to artist page
			$('#prod-detail-header #product-title').html(function(i, html) {
				var sl = html.indexOf('/'); // find the slash
				if (sl >= 0) {
					var artistName = html.substring(0, sl - 1);
					var releaseTitle = html.substring(sl + 1);
					console.log('found artist (%s) title (%s)', artistName, releaseTitle);
					return releaseTitle;
				}
				var sl = html.indexOf('-'); // / or a dash?
				if (sl >= 0) {
					var artistName = html.substring(0, sl - 1);
					var releaseTitle = html.substring(sl + 1);
					console.log('found artist (%s) title (%s)', artistName, releaseTitle);
					return releaseTitle;
				}
				return html; // no change
			});
			/** *************************************************************** */
			/** * click handlers * */
			/** *************************************************************** */
			// load listenbuy immediately instead using the loaded product
			load_listenbuy_tab(curTabCtxSel, 0, prod);

			if (prod.products_image_lrg_url != "")
				addFullCoverHandler(target);

			// remove unused tabs for video and soundcloud ....
			/** *************************************************************** */
			/** * video handlers * */
			/** *************************************************************** */
			var removeVideo = false;
			if (prod.products_upc) {
				console.log('found a video link "%s"', prod.products_upc);
				renderVideo(curTabCtxSel, prod.products_upc);
				removeVideo = false;
			} else
				removeVideo = extractVideoHandlerFromInfoText(target);

			// remove video tab if no video found
			if (removeVideo) {
				getTabDiv(curTabCtxSel, 'video').remove();
				getTabLnk(curTabCtxSel, 'video').parent().remove();
			}

			/** *************************************************************** */
			/** * soundcloud handlers * */
			/** *************************************************************** */
			var removeSound = true;
			if (prod.products_isrc) {
				console.log('found a soundcloud link "%s"', prod.products_upc);
				removeSound = false;
				renderSoundCloud(curTabCtxSel, prod.products_isrc);
			}

			// remove video tab if no video found
			if (removeSound) {
				getTabDiv(curTabCtxSel, 'free_song').remove();
				getTabLnk(curTabCtxSel, 'free_song').parent().remove();
			}

			if (targetName.indexOf('#release-detail') >= 0 // show tracks when showing releases
					||	(targetName.indexOf('#product-detail') >= 0 && playerShown)) { // or player open
				getTabLnk(curTabCtxSel, 'listenbuy').click();
			}

			// insert social links
			$('#social-buttons-template').tmpl({url: hashToParms()}).appendTo('#product-detail');

			// scroll into view (its a DOM function not jquery
			target.fadeIn(fadeintime); // show active tab
			scrollTo(target, true);
			lastProductId = prodId;
		}

		// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
		/** ************************************************************************** */
		/** load listenbuy tab including the jplayer */
		function load_listenbuy_tab(curTabCtx, e, prod) {
			// TODO check if content is there already
			var lstbuytab = getTabDiv(curTabCtx, 'listenbuy');
			var loadMsg = lstbuytab.find('#loadingProds');
			var errorMsg = lstbuytab.find('.error');
			if (loadMsg.length == 0 && errorMsg.length == 0)
				return; // this means the ajax call back has found useful data
			else
				loadMsg.addClass('loading');
			var fetchurl = oscPrefix + "/get_product_data.php?json=1&pid=" + prod.products_id + '&mdl=' + prod.products_model;
			fetchurl += XDEBUGparms;
			// the url returns a tuple of record count and result lists for formats and xsell
			$.ajax({
				type : 'GET',
				url : fetchurl,
				success : function(result, textStatus, jqXHR) {
						if (jqXHR.getResponseHeader('Content-type') == 'application/json') {
						// merge named members to local database
						addToCache(result.formats, prod.products_id);
						addToCache(result.xsell, prod.products_id);
						console.log(result);
						var target = lstbuytab.find('div.wp-tab-content'); // select the content div!!!
						target.empty();
						if (result.xsell && result.xsell.length)
							renderPlaylistPlayer(target, prod, result.xsell);
						renderProductFormats(target, result.formats);
						$('#artist-header-detail').hide();
						scrollTo('#content .post-content', true);
					} else {
							console.error(result);
							lstbuytab.html(result).addClass('error');
						}
				},
				error : function(jqXHR, textStatus, errorThrown) {
					console.error('request(%s) error(%o)', fetchurl, jqXHR);
					var msg = "status=" + jqXHR.status + "   " + errorThrown + " <br>when trying to load Release Formats for "
							+ prod.products_model;
					lstbuytab.html('<h3 class="error">' + msg + '</h3>').addClass('error');
					lstbuytab.append('<A class="error">Click to Retry</a>').click(function(e) {
						load_listenbuy_tab(curTabCtx, e, prod);
					});
				}
			});
		}
		// ###############################################################################
		// ###############################################################################
		var curMP3list = null;
		// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
		// render player with playlist into listenbuy tab
		// TODO check instantiation issues....
		function renderPlaylistPlayer(target, prod, mp3list) {
			var template = $('#product-jplayerplaylist-template'); // new version
			console.assert(template.length);
			// add IDs to object to use in template NOT selectors
			prod.jplayerId = playerId;
			prod.jplayerContent = playerContent;
			template.tmpl(prod).appendTo(target); // render player with these names
			var artist_name = $('div#prod-detail-header span#artist-name-release').html();
			curMP3list = makePlayList(mp3list, artist_name);
			// Prepare audio player
			new jPlayerPlaylist({
				jPlayer : playerSelector,
				cssSelectorAncestor : playerContentSelector
			}, curMP3list, {
				backgroundColor : '#eaeaea',
				swfPath : oscPrefix + "/jplayer",
				// warningAlerts: true,
				// errorAlerts : true,
				solution : 'html,flash',
				supplied : "mp3",
				wmode : "window" // needed for firefox flash
			});
			console.info('instantiated %d players with id %s for %o', $(playerSelector).length, playerSelector, prod);
			$(playerSelector).bind($.jPlayer.event.play, function(event) {
				$(this).jPlayer("pauseOthers");
				// Add a listener to set track info
				var artistName = $('span#artist-name-release').html();
				var trackName = $(this).next().find('a.jp-playlist-current').html();
				$("#jp-track-artist").html(artistName);
				$("#jp-track-name").html(trackName);
				scrollTo('#prod-detail-header', true); // show player
			});
			// remove the handler moving to next
			$(playerSelector).unbind($.jPlayer.event.ended);
			// set default volume to 50%
			// $(playerSelector).volume(50); TODO this is not working
			// this is called after init... nothing to do with showing the tab
			$(playerSelector).bind($.jPlayer.event.ready, function(event) {
				// console.log('received JPLAYER event %o',event);
				// console.log($(playerContentSelector + " div.jp-playlist ul li"));
				$(playerContentSelector + " div.jp-playlist ul li div").each(function(i, e) {
					var jQDiv = $(e);
					jQDiv.find('a.jp-playlist-item').attr('tabindex', i); // store index in tabindex
					jQDiv.append('<span class="buy-mp3-btn">BUY MP3</span>'); // add button
					jQDiv.find('span.buy-mp3-btn').click(playlistBuyClickHandler); // only to button no div
					// console.log('found MP3 DIV %s', $(e).html());
				});
			});
		}
		// ###############################################################################
		/** render product formats (CD, LP) into listenbuy tab * */
		function renderProductFormats(target, formatList) {
			$.each(formatList, function(i, e) {
				console.log('price %s', e.products_price);
				e.products_price_gross = Math.round(e.products_price * (100.+VAT)) / 100;
			});
			console.log('renderProductFormats(%o)', formatList);
			var template = $('#product-format-template'); // new version
			console.assert(template.length);
			if (formatList == null || formatList.length == 0) {
				target.append('<hr/>');
				console.log('noFormatsFound');
				return false;
			}
			// render the box
			target.append('<div id="buyline"><div id="format-buy-box"/></div>');
			target.append('<div id="encoding">All MP3 Downloads encoded at 320kbps unless otherwise specified.</div>');
			template.tmpl(formatList).appendTo($('#format-buy-box'));
			$('#format-buy-box').append('<span id="buy-format-btn">BUY</span>');
			$('span#buy-format-btn').click(formatsBuyClickHandler);
		}

		// ###############################################################################
		// called from callback in toggleProduct after loading
		function makePlayList(mp3list, artist_name) {
			// prepare the data a little bit
			$.each(mp3list, function(i, v) {
				v.title = v.products_name;
				v.artist = v.manufacturers_name;
				if (v.artist == undefined)
					v.artist = artist_name;
				// v.poster = v.products_image;
				// v.mp3 = oscPrefix + '/jplayer/applause.mp3';
				v.mp3 = mp3Prefix + v.products_prelisten;
				// v.m4a = mp3Prefix + v.products_prelisten;
			});
			console.log('makePlayList(%s).length = %d', artist_name, mp3list.length);
			return mp3list;
		}
		// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
		// player util functions
		function playlistBuyClickHandler(e) {
			var button = $(e.currentTarget);
			var index = button.prev('a.jp-playlist-item').attr('tabindex');
			console.assert(index >= 0);
			// console.log('buy click on index %d for mp3 %o', index, curMP3list[index]);
			oscCartHandler(button,'add_product', [ curMP3list[index].products_id ], '.sidebar'); // make array of prod id
		}
		/** @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@ */
		// called by click on buy button in format line
		function formatsBuyClickHandler(e) {
			console.log($(this).parent());
			// $().addClass('loading').text('LOADING...');
			var prodlist = [];
			var button = $(e.currentTarget);
			button.parent().find('input.checkbox:checked').each(function(i, e) {
				prodlist[i] = $(e).attr('value');
			});
			// console.log('buy %d products %o', prodlist.length, prodlist);
			oscCartHandler(button,'add_product', prodlist, '.sidebar');
		}
		/** @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@ */
		// diff
		function oscCartHandler(button,action, products, target) {
			button.addClass('loading');
			var productId = (products instanceof Array) ? products.shift() : products;
			var data = {
				'products_id' : productId,
				'osCsid' : osCsid,
				'action' : action
			// add_product, remove_product,update_product, buy_now, notify, notify_remove, cust_order
			};
			// cartId is new each time when not logged in (cart in session, basket in DB)
			$.post(shoppingCartUrl, data, function(data, textStatus, jqXHR) {
				button.removeClass('loading');
				if (jqXHR.status != 200) {
					console.error('problem with shopping cart post %o', jqXHR);
					throw 'problem with shopping cart post';
				}
				if (jqXHR.getResponseHeader('Content-type') == 'application/json') {
					// json data is already interpreted
					osCsid = data.cart.osCsid = data.osCsid; // keep osCsid everywhere
					data.cart.customer_id = data.customer_id;
					data.cart.customer_firstname = data.customer_firstname;
					renderShoppingBox(data.cart, target);
				} else  {
					console.log('Problem with OSC cart. Response %s', data);
					$('<div id="errMsg">ErrorMessage</div>').dialog({
						position : [ 'center', 'top' ],
						width : '100%',
						autoOpen : false
					});
					$('#errMsg').html(data);
					$('#errMsg').dialog('open');
				}
				if (products instanceof Array && products.length) // daisy chain to handle the product list
					oscCartHandler(button,action, products, target);
				else {
					scrollTo('div.shopping-box', true);
				}
			});
		}
		// ###############################################################################
		function shopBoxClickHandler(e) {
			var button = $(e.currentTarget);
			var prodId = button.parents('.shop-cart-entry').find('.products-id').html();
			console.log('shopBoxClickHandler on %s', button.html());
			var context = button.parents('.sidebar').first().attr('class');
			switch (button.html()) {
			// cart-entry actions
			case "up": // what we put into the buttons
				console.log('increase count %o for %d', button.html(), prodId);
				oscCartHandler(button,'add_product', prodId, '#content'); // robust version
				break;
			case "down": // what we put into the buttons
				console.log('decrease count %o for %d', button.html(), prodId);
				oscCartHandler(button,'remove_product', prodId, '#content'); // robust version
				break;
			case "delete":
				console.log('delete button on %o for %d', button.html(), prodId);
				oscCartHandler(button,'delete_product', prodId, '#content'); // robust version
				break;
			// footer button actions
			case "box":
				if (context == 'sidebar') {
					showMainShoppingBox();
				} else {
					oscCartHandler(button,'return_cart', undefined, '#content');
				}
				break;
			case "check out":	// we have to post the session Id to cross domains
//				if (context == 'sidebar') {
//					showMainShoppingBox();
//				} else
					checkout();
				break;
			case "continue shopping":
			default:
				$('#content .shopping-box').fadeOut(500);
				$('.sidebar .shopping-box').remove();
				$('#content .shopping-box').prependTo('.sidebar');
				$('.sidebar .shopping-box').show(1200);
				$('#content .page').fadeIn(1000);
				console.log('action for other button "%s"', button.html());
			}
		}
		// ###############################################################################
		function reloadCart() {
			var shopbox = $('.shopping-box:visible');
			console.log(shopbox);
			showMainShoppingBox();
		}
		// ###############################################################################
		function showMainShoppingBox() {
			var contentpage = $('#content .page');
			if (!cart)
				if (osCsid) {
					console.error('found %s - CAN LOAD CART', osCsid);
					oscCartHandler($('.shopping-box'),'return_cart', undefined, '#content');
				} else {
					console.error('nothing found %o for %s', cart, osCsid);
					alert('your shopping cart is still empty!');
					return; // nothing to do
				}
			// add action parm
			location.hash = addHashParm(location.hash, 'action', 'show');
			if (contentpage.is(":visible"))
				$(contentpage).fadeOut(1000);
			var sidebarbox = $('.sidebar .shopping-box');
			if (sidebarbox.is(":visible"))
				$(sidebarbox).hide(1200, function() { // hide the main browser page
					// render the cart on top
					renderShoppingBox(cart, '#content');
				}); // super smooth
			else
				renderShoppingBox(cart, '#content');
		}
		// ###############################################################################
		// closure to be called when reloading products from db
		function renderShoppingBoxCallback(newcart, context) {
			return function () {
				renderShoppingBox(newcart, context);
			};
		}
		// ###############################################################################
		/** render shopping box / cart showing the body is controlled via CSS sidebar/post-content * */
		function renderShoppingBox(newcart, context) {
			setGreeting (newcart);
			shoppingBoxCtx = context;
			var shopboxSelector = context + ' .shopping-box';
			var shopbox = $(shopboxSelector);
			if (shopbox.length == 0) { // place shopping box div in the top of sidebar when missing
				$(context).prepend('<div class="shopping-box box cart widget"></div>');
				shopbox = $(shopboxSelector); // reload
			}
			if (newcart == undefined || newcart == 0) {
				shopbox.html('<h3>Your ShoppingBox is totally empty</h3><button class="button">Continue Shopping</button>');
				shopbox.find('.button').click(shopBoxClickHandler);
				return;
			}
			try {
				cart = prepareCart(newcart,context); // global reference to cart
			} catch (err){
				console.log('caught %s', err);
				return;	// skip this for now... the callback does it again
			}
			var cart_tmpl = $('#shopcart-template');
			shopbox.empty(); // clean up previous one
			// shopbox.hide(); // hide at first NOT WORKING
			cart_tmpl.tmpl(cart).appendTo(shopbox.first());
			// its an object not an array & we skip body on sidebar
			if (countProperties(cart.contents) && context != '.sidebar') {
				var cart_entry_tmpl = $('#shopcart-entry-template');
				console.assert(cart_entry_tmpl.length);
				var shopbody = shopbox.find('.shop-cart-body');
				shopbox.find('.shop-cart-body').empty(); // cleanup first
				cart_entry_tmpl.tmpl(cart.entries).appendTo(shopbody);
				// attach nifty updown to the quantity field
				shopbody.find('.products-qty').each(function(i, e) {
					var value = e.textContent;
					$(e).updown(0, 99, value, function(target) {
						target.html(target.data("value"));
					});
				});
			}
			if (cart.total > 2.98) {
				shopbox.find('.order-text').hide();
				shopbox.find('.shop-cart-checkout').show();
			} else {
				shopbox.find('.order-text').show();
				shopbox.find('.shop-cart-checkout').hide();
			}
			shopbox.show(fadeintime);
			// keep session in hash now
			location.hash = addHashParm(location.hash, 'osCsid', osCsid);
			// location.hash = addHashParm(location.hash, 'cartId', cart.cartId);
			// attach click handlers to the buttons in our NEW rendered box
			shopbox.find('.button').click(shopBoxClickHandler);
			// set cookie
			if (osCsid) {
				$.cookie('osCsid', osCsid);
			}
		}
		// ###############################################################################
		/*******************************************************************************************************************
		 * create datastruct in cart for display in cart-entry-template
		 *
		 * @see osc_jquery_templates.class.php c.index c.products_id, c.products_image, c.products_tax_class_id,
		 *      c.products_name, c.products_model, c.products_qty, c.products_format, c.products_price, c.products_price_tax
		 ******************************************************************************************************************/
		function prepareCart(newcart,context) {
			// read contents and create the entries list for display in the shopcart
			// console.assert(newcart.entries == undefined);
			if (newcart.entries == undefined)
				newcart.entries = []; // init cart entry array
			if (newcart.totalPrice == undefined)
				newcart.totalPrice = 0.0;
			if (newcart.totalItems == undefined)
				newcart.totalItems = 0;
			newcart.osCsid = osCsid; // keep global oscsid in cart also for rendering
			$('.site-description p').html(osCsid);
			newcart.customer_id = customer_id; // keep global customer_id in cart also for rendering
			console.dir(newcart.entries);
			console.dir(newcart.contents);
			var i = 0;
			$.each(newcart.contents, function(key, elem) { // loop over propelemrties (returned from handle_cart.php)
				console.log('addCart #%d %s: %o', i, key, elem);
				if (newcart.entries[i] == undefined || true) { // TODO check if we actually can have something here
					var prod = getProductWithCallback(key, renderShoppingBoxCallback(newcart,context));
					if (prod == undefined) {
						throw "missing product - wait for reload"; // skip the rest and wait for callback
					}
//					console.dir(prod);
					var cartEntry = new Object(); // add properties
					cartEntry.index = i;
					cartEntry.products_id = key;
					cartEntry.products_qty = elem.qty; // new one from osc cart
					// copy the product fields
					cartEntry.products_parent = prod.products_parent;
					cartEntry.products_thumb = prod.products_image_url;
					cartEntry.products_tax_class_id = prod.products_tax_class_id;
					cartEntry.products_name = prod.products_name;
					cartEntry.products_model = prod.products_model;
					cartEntry.products_format = prod.products_format;
					cartEntry.products_price_tax = prod.products_price_tax;
					cartEntry.products_price = prod.products_price;
					cartEntry.products_price_gross = prod.products_price;
					if (prod.products_tax_class_id == 2) { // vat depending on tax class: 2 => 19%
						cartEntry.products_price_gross = Math.round(prod.products_price * (100.+VAT)) / 100;
						cartEntry.products_price_tax = Math.round(prod.products_price * VAT) / 100;
					}
					cartEntry.products_price_total = Math.round(cartEntry.products_price_gross * elem.qty * 100.) / 100;
					var parent = getProductMaster(prod.products_parent);
					if (!parent) {
						console.log("no parent for %s of product %d", prod.products_id, prod.products_parent);
					} else {
						cartEntry.parents_thumb = parent.products_image_url;
						cartEntry.parents_model = parent.products_model;
						cartEntry.parents_name = parent.products_name;
						cartEntry.parents_description = parent.products_description;
					}
					if (!cartEntry.products_format) {
						cartEntry.products_format = "MP3-Single";
					}
					// add up items
					newcart.totalItems += (+elem.qty); // force int
					// messy float operations watch the rounding
					newcart.totalPrice = newcart.totalPrice + parseFloat(cartEntry.products_price_total);
					newcart.entries[i] = cartEntry;
					i++;
				} else {
					console.log('found NON empty cart entry %d %o', i, newcart.entries[i]);
				}
			});
			newcart.totalPrice = parseFloat(newcart.totalPrice).toFixed(2);
			console.log('fixed %d cart entries: total items %d OurSum %s oscsum %s', newcart.entries.length,
					newcart.totalItems, newcart.totalPrice, newcart.total);
			console.dir(newcart.entries);
			return newcart;
		}
		// ################# ##############################################################
		/** render video object into video tab * */
		function renderVideo(curTabCtx, youTubeId) {
			var prod_youtube_tmpl = $('#product-youtube-template'); // variables need to be defined before
			if (prod_youtube_tmpl.length == 0) {
				var msg = '#product-youtube-template NOT FOUND!!!!!!!!!!!!! STOP';
				alert(msg);
				console.log(msg);
				return false;
			}
			var target = getTabDiv(curTabCtx, 'video'); // browser can append suffixes
			target.empty();
			prod_youtube_tmpl.tmpl({
				'youTubeId' : youTubeId
			}).appendTo(target);
		}
		// ################# ##############################################################
		/** render soundcloud object into video tab * */
		function renderSoundCloud(curTabCtx, soundCloudId) {
			var prod_soundcloud_tmpl = $('#product-soundcloud-template'); // variables need to be defined before
			if (prod_soundcloud_tmpl.length == 0) {
				var msg = '#product-soundcloud-template NOT FOUND!!!!!!!!!!!!! STOP';
				alert(msg);
				console.log(msg);
				return false;
			}
			var target = getTabDiv(curTabCtx, 'free_song'); // browser can append suffixes
			target.empty();
			prod_soundcloud_tmpl.tmpl({
				'soundCloudId' : soundCloudId
			}).appendTo(target);
		}
		// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
		// read from our local product_cache -- all formats are merged in there
		function getProductMaster(prodId) {
			if (prodId == undefined)
				return;
			var thisProd = prodmap[prodId];
			if (thisProd)
				thisProd.products_description = unescape(thisProd.products_description);
			// console.log('getProductMaster(%d): %o', prod_id, thisProd);
			return thisProd;
		}
		// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
		// load data per artist
		// we add the selected format to this when rendering the boxes
		function getArtistMaster(artistId, artistSet) {
			if (artistId == undefined)
				return;
			console.assert(prodmap);
			console.assert(manumap);
			// console.log('found previous prodmap: %d manumap: %d', prodmap.length, manumap.length);
			var thisManu = manumap[artistId];
			// console.assert(thisManu);
			if (thisManu == undefined)
				return; // no exception as we use this for control logic
			thisManu.manufacturers_description = unescape(thisManu.manufacturers_description);
			// use large image if small one is missing
			if (thisManu.products_image_url == "" && thisManu.products_image_lrg_url && thisManu.products_image_lrg_url != "")
				thisManu.products_image_url = thisManu.products_image_lrg_url;
			console.log('getArtistMaster(%d,%s): %o', artistId, artistSet, thisManu);
			return thisManu;
		}
		// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
		// get product for cart (different from master!!)
		function getProductWithCallback(prod_id,callback) {
			var prod = prodmap[prod_id];
			if (!prod) {
				console.log('missing product %o in prodmap: %s ',prod_id, (callback?'callingback later':''));
				if (callback)
					getProductFromDb(prod_id,callback);
			}
			return prod;
		}
		// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
		/** ************************************************************************** */
		/** load missing product data from DB */
		function getProductFromDb(prodId,callback) {
			var fetchurl = oscPrefix + "/get_product_data.php?json=1&pid=" + prodId + XDEBUGparms;
			$.ajax({
				type : 'GET',
				url : fetchurl,
				success : function(result, textStatus, jqXHR) {
					if (jqXHR.getResponseHeader('Content-type') == 'application/json') {
						if (result.prodpair && result.prodpair.length) {
							var parentId = undefined;
							if (result.prodpair.length == 2) {
								var p1 = result.prodpair[0];
								var p2 = result.prodpair[1];
								if (p1.products_parent == p2.products_model)
									parentId = p2.products_model;
								else if (p2.products_parent == p1.products_model)
									parentId = p1.products_model;
							}
							addToCache(result.prodpair,parentId);
							callback();	// call 	closure
						} else
							console.error('getProductFromDb: no data for %s', prodId);
					} else
						console.error(data);
				},
				error : function(jqXHR, textStatus, errorThrown) {
					console.error('request(%s) error(%o)', fetchurl, jqXHR);
					}
			});
		}
		// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
		// add new products to artist,release cache,product cache depending on data
		// due to disjoint key sets we merge master-detail from product and artists in same map!!!
		// we store a bijective association in our datastruct (products_parent & relemap)
		function addToCache(newData, parentId) {
			if (newData == undefined || newData.length == 0 || !(newData instanceof Array))
				return;
			if (parentId != undefined) {
				if (relemap[parentId] == undefined)
					relemap[parentId] = [];
				relemap[parentId].push.apply(relemap[parentId], newData); // append to artist releases inplace

			}
			for ( var i = 0; i < newData.length; i++) {
				var o = newData[i];
				if (parentId)
					o.products_parent = parentId;
				if (o.products_id != undefined) {
					prodmap[o.products_id] = o;
					// use large image if small one is missing
					if (o.products_image_url == "" && o.products_image_lrg_url && o.products_image_lrg_url != "")
						o.products_image_url = o.products_image_lrg_url;

				}
				if (o.manufacturers_id != undefined)
					manumap[o.manufacturers_id] = o;
			}
		}
		// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
		// read parms from location or passed parameter
		function getStateFromUrl(url) {
			// page_id = getParameterFromUrl('page_id', undefined, url);
			format = getParameterFromUrl('format', 'vinyl', url);
			products_id = getParameterFromUrl('products_id', undefined, url);
			artistSet = getParameterFromUrl('artistSet', 'main', url);
			artists_id = getParameterFromUrl('artistId', undefined, url);
			artRelTab = getParameterFromUrl('artRelTab', allArtReleases, url);
			paged = getParameterFromUrl('paged', 1, url);
			rpage = getParameterFromUrl('rpage', 1, url); // different pageno for only 4 releases
			var temp = getParameterFromUrl('osCsid', undefined, url); // use global as default
			if (temp != "undefined")
				osCsid = temp;
		}
		// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
		// hash has priority over urlparm
		function getParameterFromUrl(name, defval, url) {
			var parm = getHashParm(name, url).toLowerCase();
			if (parm == "null" || url == undefined)
				// read name parm from location.search. undefined is turned into string
				parm = getUrlParm(name, location.search); // TODO do we need toLowerCase!!!
			if (parm == "null")
				parm = defval;
			// console.log('initial load for parm %s : %s', name, parm ? parm : 'undefined');
			return parm;
		}
		// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
		// TODO URI encoding????
		function addHashParm(hash, name, value) {
			if (!value)
				return;
			var newNameValue = name + '=' + encodeURI(value);
			if (hash.indexOf(name) >= 0) { // replace in place if found
			// console.log('replace %s : %s in hash %s', name, value, hash);
			// var oldval = (RegExp('(&|#)' + name + '=' + '(.+?)(&|$)').exec(hash))[2];
			// console.log('found previous value: %s', oldval);
				var match = (RegExp('(&|#)(' + name + '=' + '(.+?))(&|$)').exec(hash));
				var oldNameValue = match[2]; // name=value
				// console.log('found previous name value pair %s', oldNameValue);
				var re = new RegExp(oldNameValue);
				hash = hash.replace(re, newNameValue);
			} else
				hash += '&' + newNameValue; // append if not found
			return hash;
		}
		function getHashParm(name, hash) { // TODO always starts with & ??? really
			var value = decodeURI((RegExp('(&|#)' + name + '=' + '(.+?)(&|$)').exec(hash ? hash : location.hash) || [ , ,
					null ])[2]);
			if (value == "null" || value == undefined) {
				// if the hash is a single token (alphnumeric and _) we use it for the respective parameter
				if ((useFormatTabs(hash) && name == 'format')
						|| (isArtistPage(hash) && ((name == 'artistSet' && !products_id) || name == 'artRelTab')))
					value = decodeURI((RegExp('[^#]*#([a-z0-9_]+)(&|$)').exec(hash ? hash : location.hash) || [ , null ])[1]);
			}
			return value;
		}
		// ###############################################################################
		// #### FORMATTING HELPER ###########################################################
		// ###############################################################################
		// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
		// remove header line of artist grid
		function removeArtistGridHeader() {
			$('div#artist-set-tabs.wp-tabs > div.ui-tabs').children('div.ui-tabs-panel:visible').find('h3').html(
					function(i, html) {
						return html.replace(/ Artist Overview/, '');
					}).hide(); // .css('display', 'none');
		}
		// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
		// show header line of artist grid (reentrant)
		function showArtistGridHeader() {
			var tabHeaders = $('div#artist-set-tabs.wp-tabs  > div.ui-tabs').children('div.ui-tabs-panel:visible')
					.find('h3');
			console.log('found %d tabHeader for %o', tabHeaders.length, tabHeaders);
			tabHeaders.html(function(i, html) {
				if (html.indexOf(' Artist Overview') >= 0)
					return html;
				else
					return html + ' Artist Overview';
			}).show(); // .css('display', 'block');
		}
		// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
		// create the html to place the boxes in
		function prepareLoopDiv(tabDivSel) {
			var targetDiv = tabDivSel.find('div.wp-tab-content');
			if (targetDiv.length == 0) {
				var thisline = new Error().lineNumber;
				throw thisline + " No div found for " + targetDiv.selector; // make sure its found
			}
			// ...probably the bloody formater fucked it up again (osc_products.class.php #370)!
			if (targetDiv.find('#loop').length == 0) {
				// simply overwrite wp-tab-content if no loop found.
				targetDiv.html('<div id="loop" class="grid clear"></div>');
			}
			var loopDiv = targetDiv.find('#loop');
			console.assert(loopDiv.length); // make sure its found
			return loopDiv;
		}
		// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
		// wrap LI entries within UL block into H3 tags for wp-ui tab processing
		// ==> wrap anchors within LI inside an extra H3 tag (sharing sight CSS)
		function fixWpTabHeader(curTabCtx) {
			var anchors = $(curTabCtx + ' li.ui-state-default a');
			console.log('fixWpTabHeader found %d anchors in ', anchors.length, curTabCtx);
			anchors.each(function(i, e) {
				$(e).parent(':not(h3)').html(function(i, html) {
					return '<h3>' + html + '</h3>';
					console.log('wptabHeader in %s : %s', curTabCtx, html);
				});
			});
			// also replace wptabs clickhandler here
			attachTabClickHandler(curTabCtx);
		}
		// remove selection for ALL tab-panel DiV and hide them (robuster version)
		function hideAllTabs(curTabCtx) {
			var selContDivs = $(curTabCtx).find('div.ui-tabs DIV.ui-tabs-panel');
			selContDivs.removeClass('ui-tabs-selected ui-state-active').addClass('ui-tabs-hide');
//			console.log('hiding tabs for %o', selContDivs);
		}
		// ###############################################################################
		// #### HANDLER ##################################################################
		// ###############################################################################
		// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
		// copied from themes/sight/js/script.js for adaption as we have to use
		// more specific selectors inside tabs
		function attachGridClickhandler(tabSelector, clickHandler) {
			var loopSel = tabSelector.find('#loop');
			loopSel.find('#loop').addClass('grid').removeClass('list'); // make sure its a grid

			// fix formatting of product names in product box
			loopSel.find('.product-box .product-info').each(
					function(i, e) {
						var artistName = undefined;
						var trackTitle = undefined;
						$(this).find('span.product-title').html(function(i, html) {
							var sl = html.indexOf('/'); // find the slash
							if (sl >= 0) {
								artistName = html.substring(0, sl - 1);
								trackTitle = html.substring(sl + 1);
								// console.debug('found artist (%s) title (%s)',artistName, trackTitle);
								return '';
							}
							var sl = html.indexOf('-'); // / or a dash?
							if (sl >= 0) {
								artistName = html.substring(0, sl - 1);
								trackTitle = html.substring(sl + 1);
								// console.log('found artist (%s) title (%s)',artistName, trackTitle);
								return '';
							}
							return html; // no change
						});
						if (artistName != undefined)
							$(this).append(
									'<span class="artist-name">' + artistName + '</span>' + '<span class="track-name">' + trackTitle
											+ '</span>');
					});
			// remove old mouseenter, mouseleave,click handlers
			var posts = loopSel.find('.post');
			posts.unbind('mouseenter').mouseenter(function() {
				$(this).find('.thumb').fadeOut(300).css('z-index', '-1');
			}).unbind('mouseleave').mouseleave(function() {
				$(this).find('.thumb').fadeIn(300).css('z-index', '1');
			});
			posts.unbind('click').click(clickHandler); // set new one
			$.cookie('mode', 'grid'); // store the mode in a cookie (sight compat)
		}
		// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
		// show a video in some cases
		function extractVideoHandlerFromInfoText(curTabCtx, seltor) {
			// search for video link in infotext and fix it
			// (always use matching for id tags - they are not unique so browser
			// generates suffixes
			var infotextId = $(seltor + getTabDiv(curTabCtx, 'infotext')).attr('id');
			// browser might append suffixes
			var vidImgSel = seltor + ' #' + infotextId + ' a img[src$="watch_it.jpg"]';
			var vidLink = $(vidImgSel).parent();
			if (vidLink.length) {
				// Videobox.open('http://www.youtube.com/watch?v=-yPeQaqct7Y','Alterego Trailer','vidbox');
				// first copy click action from attribute
				var videoboxLink = vidLink.attr('onclick');
				// browser might append suffixes
				var videoId = $(seltor + getTabDiv(curTabCtx, 'video')).attr('id');
				// remove click handler from attributes
				vidLink.attr('href', '#' + videoId).attr('onclick', "");
				// add new click handler to switch tabs (easing)
				vidLink.click(function(e) {
					window.scrollTo(0, 0); // scroll up
					$(seltor + ' #' + infotextId).slideUp(300);
					$(seltor + ' #' + videoId).slideDown(500);
					// window.scrollTo(0, 0); // scroll up
				});
				var vidParams = /Videobox.open\(([^)]+)\)/.exec(videoboxLink);
				// 1. submatch inside () is in index 1
				var param = vidParams[1].split(',');
				// clean the string
				var videoLink = param[0].replace(/'/g, " ").replace(/"/g, " ").trim();
				renderVideo(videoLink);
				return false; // dont remove the video
			}
			return true;
		}
		// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
		// attach click handler to all tab links for product formats
		// this has to be called AFTER wpui which changes the DOM so this selector works
		// dont add without removing the previous wpui click handler
		function attachTabClickHandler(curTabCtx) {
			var anchors = $(curTabCtx + ' > .ui-tabs > UL.ui-tabs-nav > LI  A');
			console.log('attachTabClickHandler found %d tabs in %s', anchors.length, curTabCtx);
			anchors.unbind('click').click(changeStateHandler);
		}
		// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
		// replace click handler for navigation links -- once called by DOMloaded event
		function attachNavClickHandler() {
			// if (location.hash.indexOf('#') >= 0)
			$('div.nav ul.sub-menu li.menu-item a').unbind('click').click(changeStateHandler);
		}
		// ##############################################################################
		// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
		// attach special toggle handler to show large image after making tabs
		function addFullCoverHandler(seltor) {
			// add new fullcover handler but do not replace the wptab handler!!!
			// seltor.find('li.ui-state-default a[href^="#full_cover"]').click(fullCoverHandler(seltor));
			seltor.find('li.ui-state-default a').click(fullCoverHandler(seltor));
			seltor.find('#prod-image-big').click(hideDetailDiv(seltor));
		}
		// we can just read the height of the contained image to get its real height
		function fullCoverHandler(seltor) {
			var flatImgCss = {
				'height' : '200px'
			};
			var bigImgCss = {};
			return function(e) {
				if (e.currentTarget.href)
					playerShown = e.currentTarget.href.indexOf('listen') > 0; // is the player shown
				var divImg = $('div#prod-image-big');
				var bigimg = divImg.find('img');
				var newCss = flatImgCss;
				// we make big only if we are small and the full cover link has been clicked
				if (divImg.css('height') == '200px' && this.hash.indexOf('full_cover') >= 0) {
					bigImgCss.height = bigimg.css('height'); // read and keep realheight
					newCss = bigImgCss;
					// dont forget to remove the other handler
					divImg.unbind('click').click(fullCoverHandler(seltor));
				} else { // establish previous click handler = hide div
					divImg.unbind('click').click(hideDetailDiv(seltor));
				}
				divImg.animate(newCss, fadeintime);
				scrollTo(divImg.parent(), true);
			};
		}
		function hideDetailDiv(seltor) {
			return function() {
				$(seltor).hide();
				removeReleaseTabHeader();
			};
		}
		// this removes the wp-tab-title we added under the detail while unfolding
		function removeReleaseTabHeader() {
			var headers = $('div#release-format-tabs.wp-tabs  > div.ui-tabs').children('div.ui-tabs-panel:visible')
					.find('h3');
			if (headers.length) {
				headers.hide(); // css('display', 'none');
				console.log('removed headers ');
			} else
				console.log('no release headers found to remove');
			lastProductId = 0;
		}
		// ##############################################################################
		// ### MAIN CLICK HANDLER #######################################################
		// ##############################################################################
		function changeStateHandler(e) {
			var href = $(e.currentTarget).attr('href');
			// filter the all_releases link but not the release pager!!!
			if (href.indexOf('#all_releases_of_artist') >= 0
			    && href.indexOf('rpage=') < 0)
				return;	// do nothing
			if (href.indexOf('#') == 0)
				href = location.pathname + href; // include pathname of current page in context
			if (isCurCtx(href)) {
				// we are in the right context carry on
				getStateFromUrl(href);
				e.preventDefault();
				e.stopPropagation();
				if ($('.error').length > 0) {		// remove error message
					$('.error').empty().removeClass('error');
				}
				$(this).parent().find('.pagination').text('LOADING...');
				initPage(href); // open page for href parms... dont necessarily reload
			}
			actionHandler();
		}
		// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
		// deal with the action parameter
		function actionHandler() {
			var action = getHashParm('action');
			console.log('found action=', action);
			switch (action) {
			case "login":
				loginUser();
				break;
			case "logoff":
				logoffUser();
				break;
			case "register":
				registerUser();
				break;
			case "label":
//				showLabel();
				break;
			case "show":
//				if (osCsid) // if we have a session
					// try to read the cart
					oscCartHandler($('.shopping-box'),'return_cart', undefined, '#content');
//				else
//					showMainShoppingBox();
				break;
			case "checkout":
					checkout();
				break;
			default:
			}
			return false;
		}
		// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
		// show login form and try to login user
		function loginUser() {
			$('<div id="login-div"><div class="validateTips"></div><form><fieldset><label for="login">Email:</label> <input type="text" name="login" id="email" value="" class="text ui-widget-content ui-corner-all" /> <label for="password">Password:</label> <input type="password" name="password" id="password" value="" class="text ui-widget-content ui-corner-all" /></fieldset></form></div>')
					.dialog({
						title : 'Shopkatapult Login',
						dialogClass : 'shit-theme',
						closeOnEscape : true,
						resizable : true, // requires draggable and resible jqui extensions to be loaded
						draggable : true,
						show : 'slide',
						hide : 'slide',
						modal : true,
						// open: function() { // dont bother with the php form its only 2 fields
						// $(this).load(oscPrefix + '/login.php');
						// },
						open : function() {
							$("#password").keyup(function(event) {
								if (event.keyCode == 13) {
									// redirect retuurn key to login click
									$('#login-div').parent().find('.ui-dialog-buttonpane .ui-button span:contains("Login")').click();
								}
							});
						},
						close : function() {
							$('#login-div').remove();
						},
						buttons : {
							'Cancel' : function() {
								$(this).dialog("close");
							},
							'Login' : doLogin			// no params!!
						}
					// buttons
					});
		}
		// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
		// do the actual loging with OSC
		function doLogin(e) {
			var bValid = true;
			var email = $("#email");
			var password = $("#password");
			var tips = $("#login-div .validateTips");
			$('#login-div input').removeClass("ui-state-error");
			bValid = bValid && checkLength(tips, email, "email", 6, 80);
			bValid = bValid && checkLength(tips, password, "password", 4, 16);
			bValid = bValid && checkRegexp( tips, email,
							/^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?$/i,
							"eg. ui@jquery.com");
			if (bValid) {
				console.log('now we can try to login with: %s/%s', email.val(), password.val());
				var data = { 'email_address' : email.val(), 'password' : password.val(), 'osCsid' : osCsid };
				// cartId is new each time when not logged in (cart in session, basket in DB)
				$.post(loginUrl, data, function(result, textStatus, jqXHR) {
					var loginDiv = $('#login-div');
					if (jqXHR.status != 200) {
						console.error('problem with login in %o: %s', jqXHR, result);
						// throw 'problem with login ' + responseText;
					}
					if (jqXHR.getResponseHeader('Content-type') == 'application/json') {
						osCsid = result.osCsid;
						customer_id = result.customer_id;
						customer_firstname = result.customer_firstname;
						if (cart) {
							cart.customer_id = result.customer_id;
							cart.customer_firstname = result.customer_firstname;
						}
						$.cookie('osCsid', osCsid); // store the osCsid in cookie
						$('.debug span.osCsid').html(osCsid);	// show us during development
//						location.hash = addHashParm(location.hash, 'osCsid', osCsid);		// NOT IN THE HASH
						reloadCart();
						loginDiv.dialog('close'); // SUCCESS
					} else { // houston we have a problem - try again
						loginDiv.dialog("option", "buttons", {});
						// loginDiv.dialog("option", "position", [ 'center', 'center' ]);
						// loginDiv.dialog("option", "height", 500);
						loginDiv.html(result);
						loginDiv.find('#login-button').unbind('click').click(function(e) {
							e.preventDefault();
							e.stopPropagation();
							doLogin(e);
						});
						loginDiv.find('#create_account-button').unbind('click').click(function(e) {
							// connect registration to close event
							loginDiv.dialog( {'close' : function () {
									console.log('clicked close on $o', this);
									registerUser();
									return false;
								} });
							loginDiv.dialog('close'); // DONE
							return false;
						});
						return false;
					};
				});
			} // bValid
			return false;
		} // Login
		// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
		// show login form and try to login user
		function registerUser() {
			var debug = {
				'XDEBUG_SESSION_START' : 'ECLIPSE_DBGP',
				'KEY' : '123456789012345'
			};
			$('<div id="register-div"></div>').dialog({
				title : 'My Account Information',
				position : [ 'center', 'top' ],
				height : 'auto',
				width : 600,
				modal : true,
				show : 'slide',
				resizable : true, // not working without additional scripts
				draggable : true,
				open : function() {
					var regDiv = $('#register-div');
					$.post(createAccountUrl, debug, function(responseText, textStatus, jqXHR) {
						if (jqXHR.status != 200) {
							console.error('problem with registration in %o: %s', jqXHR, responseText);
							throw 'problem with registration ' + responseText;
						}
//						var start = responseText.indexOf('<html');
//						regDiv.html((start >= 0) ? responseText.substr(start) : responseText);
						regDiv.html(responseText);
						$('#login').click(function(e) { // handler login link
							regDiv.dialog('close');
							loginUser();
						});
						// attach submit handler
						$("form#create_account").submit(function() {
							$.post($(this).attr("action"), $(this).serialize(), function(responseText, textStatus, jqXHR) {
								regDiv.dialog("option", "position", [ 'center', 'center' ]);
								if (jqXHR.status != 200) {
									console.error('problem with registration in %o: %s', jqXHR, responseText);
									regDiv.html(textStatus);
									// throw 'problem with registration ' + responseText;
									return false;
								} else
									regDiv.html(responseText);
								$("#acct-created-continue").click(function(e) {
									e.preventDefault();
									e.stopPropagation();
									// fish the osCsid from the links
									var anchor = $("#scrollTextContent .infoBox a").first();
									var newOsCsid = getUrlParm('osCsid', anchor.attr('href'));
									if (!newOsCsid || newOsCsid == "null")
										alert('session not created');
									else {
										osCsid = newOsCsid;
										// location.hash = addHashParm(location.hash, 'osCsid', osCsid);
									}
									regDiv.dialog('close'); // DONE
								}); // end click handler
							}); // end post
							return false; // prevent normal submit
						}); // end submit
					});
					// attach handler to login link
				}
			// , close : function() {
			// console.log('register close has been called!');
			//				}
			});
			return false;
		}
		// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
		// show login form and try to login user
		function logoffUser() {
				if (confirm('Do you really want to log off?'))
					$.post(logoffUrl, { osCsid: osCsid} , function(result, textStatus, jqXHR) {
						if (jqXHR.status != 200) {
							console.error('problem with logoff in %o: %s', jqXHR, result);
						} else {
							setGreeting();
							cart = 0;
							$('.shopping-box').remove();
						}
					});
		}
		// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
		// call checkout on shopkatapult, to keep the session we create a form and post into an iframe
		function checkout() {
//			var iFrame = $('<iframe>').attr({
//				name : "checkout",
//				id: "iframe",
////				width:"900px",
//				sandbox: "allow-forms allow-scripts"
//			});
			var form = $('<form>').attr({
				id : 'checkoutform',
				name : 'checkoutform',
				method : "post",
				enctype : "multipart/form-data",
				action : checkoutUrl,
				// setting form target to a window named 'checkout'
				target : "_checkout" // "_blank"
			//}).appendTo(iFrame);
			}).appendTo('body');
			$('<input>').attr({
				type : 'hidden',
				name : 'osCsid',
				value : osCsid
			}).appendTo(form);
			form.submit();

//			$('<div id="iframediv"/>').dialog({
//				title : 'Shopkatapult Checkout',
//        autoResize: true,
//				minWidth : 950,
//				minHeight : 630,
//				position : [ 'center', 'center' ],
//				closeOnEscape : true,
//				resizable : true, // requires draggable and resible jqui extensions to be loaded
//				draggable : true,
//				show : 'slide',
//				hide : 'slide',
//				modal : true,
//				open : function() {
//					$(this).html(iFrame.html());
//					form.submit();
//					form.remove();
//				},
//				close : function () {
//					$(this).remove();
//				}
//			});
// window.open('', 'checkout', 'scrollbars=no,menubar=no,height=600,width=800,resizable=yes,toolbar=no,status=no');
//			iFrame.dialog('open');

//			var data = { 'osCsid' : osCsid };
//			for (prop in XDEBUG) { data[prop] = XDEBUG[prop];}
//			$.post(checkoutUrl, data , function (returned) {
//				$('<div id="checkout"></div>').dialog("option", "position", [ 'center', 'center' ]);
//				$('#checkout').html(returned);
//			});
//			window.open(checkoutUrl + ((checkoutUrl.indexOf('?')>0)?'&':'?')+'osCsid='+osCsid); ; // use cookies!!!!
		}
		// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
		function setGreeting (cart) {
			if (cart && cart.customer_firstname) {
				$('.site-description').html('<h3>Hello '+cart.customer_firstname+'!</h3>');
//				$('.site-description').append('<p style:"color:red;font-weight:bold;">'+osCsid+'!</p>');
			} else
				$('.site-description').empty();
		}
		// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
		// load gigpress sidebar from wordpress
		function loadGigpressWidget(artistId){
			var widget = $('.sidebar div.widget_gigpress'); 
			var artistHeader = "Upcoming shows";
			if (artistId) {
				var artist = getArtistMaster(artistId);
				artistHeader = artist.manufacturers_name+"'s Shows";
			} 
			if (widget.find('h3:contains('+artistHeader+')').length)
					return; // already there
			// redraw the sidebar widget
			var fetchurl = oscPrefix + '/get_gigpress_sidebar.php?json=1' + XDEBUGparms;
			if (artistId)
				fetchurl += '&artistId=' + artistId;
			$.ajax({
				type : 'GET',
				url : fetchurl,
				success : function(result, textStatus, jqXHR) {
					if (jqXHR.getResponseHeader('Content-type') == 'text/html') {
						widget.html("<h3>"+artistHeader+"</h3>");
						widget.append(result);
						$('.gigpress-sidebar-artist').parent().click(changeStateHandler);
					} else {
						console.error(result);
						$('#product-detail').html(result).addClass('error');
					}
				}
			});			
		}
		// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
//		function showLabel() {
//			var label = getHashParm('label', location.hash);
//			var fetchurl;
//			if (!labels[label]) {
//				$.ajax({
//					type : 'GET',
//					url : fetchurl,
//					success : function(result, textStatus, jqXHR) {
//						getTabLnk(curTabCtx, tabName).removeClass('loading');
//						if (jqXHR.getResponseHeader('Content-type') == 'application/json') {
//							labels = result;
//							showLabel();
//						} else {
//							console.error(result);
//							$('#product-detail').html(result).addClass('error');
//						}
//					}
//				});
//			} else {
//				$('#content').empty();
//			}
//		}
		// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
		// SCROLL TRIGGER to load more items when hitting bottom of page
		var cnt = 0;
		$(window).scroll(function() {
			if (!loadingPage)
				if ($(window).scrollTop() >= $(document).height() - $(window).height() - 5) {
					// find the only visible pager at the bottom of the page
					var pager = $(getTabCtx() + ' div.pagination a:visible').last();
					if (pager.length) {
						if (location.hash.indexOf('listenbuy')>0)
							return;		/// skip autoload if we are showing the player
						var page = getLastPageOfTab(pager.parent());
						console.log('%d. trigger pageload for page %d', cnt++, page);
						loadingPage = page;
						pager.click();
					}
				}
		});
	});
})(jQuery);
