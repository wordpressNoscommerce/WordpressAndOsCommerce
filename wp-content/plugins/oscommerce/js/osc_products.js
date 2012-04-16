/** javascript for oscommerce tabbed UI
 * JSON injection is used for initial data to be shown on the first tab
 * additonal pages are loaded using AJAX requests to PHP server pages
 * get_product_data.php, get_product_page.php, get_manufacturer_page.php
 * state is kept in location.hash to make local state bookmarkable (REST)
 * **/

jQuery.noConflict();
(function($) { //this defines a scope so we cannot simply split this up into multiple files
	$(function() {
		if (console != undefined && console.clear != undefined) console.clear();
		// dont pollute global namespace!
		// config values
		var fadeintime = 500; 		// animation parameter
		var artistPage = 20;
		var releasePage = 19;
		var playerId = 'jquery_jplayer_1';
		var playerSelector = '#'+playerId;
		var playerContent = 'jp_container_1';
		var playerContentSelector = '#'+playerContent;
		var oscPrefix = '/wp-content/plugins/oscommerce';
		var allArtReleases = 'All Releases Of Artist';
		// TODO deal with the tabnames better
		var artReltabNames = [ { tab : allArtReleases } ];//, { tab : "Singles" }, { tab : "Albums" }, { tab : "Appears On" }, { tab : "Videos" } ];

		// state variables
		var osCsid = 0;
		var cart = 0;
		var cartShown = 0;
		var relemap = {};
		var manumap = {};
		var prodmap = {};
		var lastProductId = 0; // keep state
		var lastArtistId = 0;

		// URL Parms == application state
//		var page_id = getParameterFromUrl('page_id');		// obsolete due to permalinks
		var products_id;
		var artists_id;
		var paged;
		var rpage;// different pageno for releases
		var artist_set;
		var format;
		getStateFromUrl(location.href);	// set URL parms
		// keep new and changed parameters in location.hash to avoid page reload
		// TODO fix wp-ui problems with such a hash

		// the click Handler for products using closures created with selfexecuting functions
		// BUT they need to be defined before first use below!!!!
		var productClickHandler = function () {
			var curCtx = '#product-format-tabs';
			var target = '#product-detail';
			var toggleFunc = toggleProduct;
			return boxClickHandler(curCtx, target,toggleFunc);
		}();
		// the click Handler for artist releases is just like the product one but opening default to listenbuy tab
		var releaseClickHandler = function () {
			var curCtx = '#release-format-tabs';
			var target = '#release-detail';
			var toggleFunc = toggleProduct;
			return boxClickHandler(curCtx,target,toggleFunc);
		}();

		$('.wpui-light').removeClass('wpui-light').addClass('shit-theme');	// change theme for us
		attachNavClickHandler();
		var curTabCtx = getTabCtx(location.href);	// current Ctx is the one location... we are just being loaded
		if (renderJsonData(curTabCtx)) {		// returns false if wrong context
			fixWpTabHeader(curTabCtx); 	// modify formating of tabs
			initPage(location.href);
			var json = getJsonData();
			for ( var tab in json) {
				delete json[tab]; // remove container after loading page
			}
		}
		// ##########################################################################
		// ##########################################################################
		// render the injected JSON data from the backend
		function renderJsonData(curTabCtx) {
			var pageno = 1;
			var templateName;
			var totalItems;
			var pageSize;
			if (isReleasePage()) {
				if (typeof products === 'undefined'
					|| products == undefined
					|| countProperties(products) == 0) { // empty test
					var msg = 'No JSON products received for curTabCtx: ' + curTabCtx + ' page: ' + paged;
					$('#content .page .post-content').append('<h1 style="color: red;">'+msg+'<br>Check Database Connection to shopdb</h1>');
					console.error(msg);
					return false;
				}
				json = products;
				templateName = '#product-box-template';
				totalItems = productsCount;
				pageSize = productsPageSize;
				// productsReleaseFormats
			} else if (isArtistPage()) {
				if (typeof manufacturers === 'undefined'
					|| manufacturers == undefined
					|| countProperties(manufacturers) == 0) { // empty test
					var msg = 'no JSON manufacturers received for curTabCtx: ' + curTabCtx + ' page: ' + paged;
					$('#content .page .post-content').append('<h1 style="color: red;">'+msg+'<br>Check Database Connection to shopdb</h1>');
					console.error(msg);
					return false;
				}
				json = manufacturers;
				templateName = '#manufacturer-box-template';
				totalItems = manufacturersCount;
				pageSize = manufacturersPageSize;
				//manufacturersSets
			} else
				return false; // ABORT nothing to do
			// cleanup DOM struct from wordpress to match grid page
			$('#content .entry .page').appendTo('#content').prev('.entry').remove();
			hideAllTabs(curTabCtx);

			// now inject the JSON data as given (could be scaled up)
			for ( var tab in json) { // TODO what about the page number?
				var newItems = json[tab];
				addToCache(newItems);
				renderItemsInTab(curTabCtx,newItems,tab,pageno,templateName);
				renderPagination(getTabSelector(curTabCtx,tab),pageSize, totalItems, newItems, 1);
				// put a clickhandler in new pagination divs
				$(curTabCtx+' div.pagination a').unbind('click').click(changeStateHandler);
			}
			return true; // rendering OK
		}
		// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
		// called to rebuild the page also from click handlers
		function initPage(href) {
			console.log('initPage(%o)',href);
			if (location.href != href) 	// if we set this to same value the page reloads forever!!!
				location.href = href;	// maintain restful state of javascript app in location.hash
			var curTabCtx = getTabCtx(href);
			var curTab = getTab(href);
			var curLoader = getTabLoader(href);
			curLoader(curTabCtx, curTab, paged);		// try to load the requested page
		}
		// ###############################################################################
		// ###############################################################################
		// called to show / extend another tab (artistId unused!!!)
		function loadProductsForTab(curTabCtx,tabName, pageno) {
//			if (pageno == undefined) pageno = 1;
//			if (tabName == undefined) tabName = 'vinyl';
//			if (products == undefined) products = new Object; // init undefined object before accessing
//			if (products[tabName] == undefined) products[tabName] = {}; // init empty object before loading via ajax
			// load new data if empty or next page
			if (needToLoad(curTabCtx,tabName, pageno)) {
				var fetchurl = oscPrefix + "/get_product_page.php?json=1&format=" + tabName;
				if (pageno != undefined)
					fetchurl += "&paged=" + pageno; // append extra param
				// the url returns a tuple with record count and result
				console.log('loading more products' + fetchurl);
				getTabLnk(curTabCtx,tabName).addClass('loading');
				$.ajax({
					type : 'GET',
					url : fetchurl,
					success : function(data, textStatus, jqXHR) {
						getTabLnk(curTabCtx,tabName).removeClass('loading');
						if (data.indexOf('No Records found') >= 0) {
							console.error(data);
							$('#product-detail').html(data).addClass('error');
						} else {
							var result = eval('(' + data + ')'); // eval json array
							addToCache(result[3]); // this is the product list
							renderItemsInTab(curTabCtx,result[3], tabName, pageno,'#product-box-template');
							renderPagination(getTabSelector(curTabCtx,tabName), result[0], result[1], result[3], pageno);
							selectTab(curTabCtx, tabName, loadProductsForTab, productClickHandler);
							// scroll box to bottom
							$(curTabCtx).get(0).scrollIntoView(0);
						}
					},
					error: function (jqXHR, textStatus, errorThrown) {
						getTabLnk(curTabCtx,tabName).removeClass('loading');
						console.error('request(%s) error(%o)',fetchurl, jqXHR);
						var msg = "status="+jqXHR.status+" "+errorThrown+" when trying to load Releases for " + tabName;
						$(getTabSelector(curTabCtx,tabName)).html('<h3 class="error">'+ msg+'</h3>').addClass('error');
					}
				});
			} else { // check if its rendered already
//				if ($(getTabSelector(curTabCtx,tabName)+' div.wp-tab-content').contents().length == 0) {
//					renderItemsInTab(curTabCtx,0,format,1,'#product-box-template');
//				}
				selectTab(curTabCtx,format, loadProductsForTab, productClickHandler);
			}
		}
		// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
		// called to show / extend another tab
		function loadArtistsForTab(curTabCtx, tabName, paged) {
			// TODO make sure artists_id is reset when needed
			if (needToLoad(curTabCtx,tabName, paged)) {
				var fetchurl = oscPrefix + "/get_manufacturer_page.php?json=1&artistSet=" + tabName;
				if (paged)
					fetchurl += "&paged=" + paged; // append extra param
				if (artists_id)
					fetchurl += "&artistId=" + artists_id; // append extra param
				// the url returns a tuple with record count and result
//				$('div.pagination').addClass('ui-tabs-hide'); // hide all before loading
				getTabLnk(curTabCtx,tabName).addClass('loading');
				$.ajax({
					type : 'GET',
					url : fetchurl,
					success : function(data, textStatus, jqXHR) {
						getTabLnk(curTabCtx,tabName).removeClass('loading');
						if (data.indexOf('No Records found') >= 0) {
							console.error(data);
							$('#artist-detail').html(data).addClass('error');
						} else {
							var result = eval('(' + data + ')'); // eval json array
							addToCache(result[3]);
							renderItemsInTab(curTabCtx,result[3],tabName,paged,'#manufacturer-box-template');
							renderPagination(getTabSelector(curTabCtx,tabName), result[0], result[1], result[3], paged);
							selectTab(curTabCtx, tabName, loadArtistsForTab, artistClickHandler);
							// scroll curTabCtx box to bottom
							$(curTabCtx).get(0).scrollIntoView(0);
						}
					},
					error: function (jqXHR, textStatus, errorThrown) {
						getTabLnk(curTabCtx,tabName).removeClass('loading');
						console.error('request(%s) error(%o)',fetchurl, jqXHR);
						var msg = "status="+jqXHR.status+" "+errorThrown+" when trying to load Releases for " + tabName;
						$(getTabSelector(curTabCtx,tabName)).html('<h3 class="error">'+ msg+'</h3>').addClass('error');
					}
				});
			} else { // check if it has been rendered if not do so 	... use id matching as we have suffixes
				var tabSelector = getTabSelector(curTabCtx,tabName);
				if ($(tabSelector + ' div.wp-tab-content').contents().length == 0) {
					renderItemsInTab(curTabCtx,manufacturers[tabName],tabName,1,'#manufacturer-box-template');
				}
				// then show it
				selectTab(curTabCtx,tabName, loadArtistsForTab, artistClickHandler);
			}
		}
		// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
		// called to fetch releases for artists, parms have to adhere to function prototype
		function loadProductsForArtist(artistId) { 	// use closure to supply the extra artistId
			return function(curTabCtx, tabName, rpage) {
				if (relemap[artistId] == undefined)
					relemap[artistId] = []; // init empty release ARRAY before loading
				// load new data if empty or next page (as this is an object now count the props instead of length)
				if (relemap[artistId] == undefined || countProperties(relemap[artistId]) == 0 || rpage > 1) {
					var fetchurl = oscPrefix + "/get_product_page.php?json=1&artistId=" + artistId;
					fetchurl += "&format=All"; // TODO read all formats for now
					fetchurl += "&pagesize=3"; // only 3 at a time
					if (rpage != undefined)
						fetchurl += "&paged=" + rpage; // append extra param
					// DATA = array($this->records_per_page,$this->product_count, $this->max_page, $this->result,$this->release_formats);
					getTabLnk(curTabCtx,tabName).addClass('loading');
					$.ajax({
						type : 'GET',
						url : fetchurl,
						success : function(data, textStatus, jqXHR) {
							getTabLnk(curTabCtx,tabName).removeClass('loading');
							if (data.indexOf('No Records found') >= 0) {
								console.error(data);
								$(getTabSelector(curTabCtx,tabName)).html('<h3 class="error">'+ data+'</h3>').addClass('error');
							} else {
								var result = eval('(' + data + ')'); // eval json array
								addToCache(result[3], artistId); // this is the product list and artist relation
								renderItemsInTab(curTabCtx, result[3],tabName,rpage,'#release-box-template');	// use rpage nos
								// 	function renderPagination(curTabCtx, pageSize, totalRecCount, newItems, paged)
								renderPagination(getTabSelector(curTabCtx,tabName), result[0], result[1], result[3], rpage);
								selectTab(curTabCtx,  tabName, loadProductsForArtist(artistId), releaseClickHandler);
								// scroll box to bottom
								$('#release-format-tabs').get(0).scrollIntoView(0);
							}
						},
						error: function (jqXHR, textStatus, errorThrown) {
							getTabLnk(curTabCtx,tabName).removeClass('loading');
							console.error('request(%s) error(%o)',fetchurl, jqXHR);
							var msg = "status="+jqXHR.status+" "+errorThrown+" when trying to load Releases for " + manumap[artistId].manufacturers_name;
							$(getTabSelector(curTabCtx,tabName)).html('<h3 class="error">'+ msg+'</h3>').addClass('error');
						}
					});
				} else { // check if it has been rendered if not do so 	... use id matching as we have suffixes
					var tabSelector = getTabSelector(curTabCtx,tabName);
					if ($(tabSelector + ' div.wp-tab-content').contents().length == 0) {
						renderItemsInTab(curTabCtx, relemap[artistId], tabName, 1, '#release-box-template');
					}
					selectTab(curTabCtx, tabName, loadProductsForArtist(artistId), releaseClickHandler);
				}
			};
		}
		// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
		// check if more data is needed for current tab
		function needToLoad(curTabCtx,tabName, pageno) {
			var result = false;
			var tabSelector = getTabSelector(curTabCtx,tabName);
			var numPosts = $(tabSelector+' .grid .post').length;
			if (numPosts == 0) { // no posts in our tab
				result = true;	// so need to load!
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
		function renderItemsInTab(curTabCtx,newItems, tabName, pageno,templateName) {
			console.assert(newItems != undefined && newItems.length);
			var fixedTabName = tabName.toLowerCase().replace(/ /g, '_');
			// the id property gets garbled when tabs get regenerated... so match it instead
			var tabDivSel = getTabSelector(curTabCtx,tabName);
			var template = $(templateName);
			console.assert(template.length); // make sure its found
			var loopDiv = prepareLoopDiv(tabDivSel);
//			console.trace();
			console.log('renderItemsInTab %s with format %s, artistSet %s, paged = %d, pageno = %d',tabName,fixedTabName,artist_set,paged,pageno);
			// the template
			$.each(newItems, function(i, v) {
				v.format = fixedTabName;
				v.artist_set = artist_set;
				v.paged = (templateName == '#release-box-template')?paged:pageno;	// special case for artist releases
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
		function selectTab(curTabCtx,tabNameOrg, loadItemsForTab, clickHandler) {
			var tabName = tabNameOrg.toLowerCase().replace(/ /g, '_');
			var tabSelector = getTabSelector(curTabCtx,tabNameOrg);
			if ($(tabSelector+':visible').length) { // we are on the right tab already
				// but we still have to deal with additional parms or posts
				finishTab(curTabCtx, tabSelector, tabName, loadItemsForTab, clickHandler);
				return; // nothing more to do we are active already
			}
			// replace wptabs clickhandler so we can fuck around with it
			// attachTabClickHandler(curTabCtx);	// now done with fidxtabheaders

			// remove previous jplayer first TODO check when and how its necessary
			if (products_id != undefined && products_id != 0 && $(playerSelector).length) {
				$(playerSelector).jPlayer( "destroy" );
				console.info('player for %d destroyed',products_id);
			}
			if (lastProductId != 0) { // productdetail always changes
				$('#product-detail').empty(); // clear prod detail when showing new tab
				lastProductId = 0; // clear the state
			}
			if (curTabCtx == '#release-format-tabs') {
				// remove the title from the tab also
				$('div#artist-set-tabs.wp-tabs  > div.ui-tabs').children('div.ui-tabs-panel:visible').find('h3').html(
						function(i, html) {
							return html.replace(/ Artist Overview/, '');
						}).css('display', 'none');
				$('#artist-detail').empty(); // clear artist detail only when not showing releases
				// TODO check for duplicate ID here
			}

			// remove selection for ALL tab header LI
			var selNavLi = $(curTabCtx+' ul.ui-tabs-nav li.ui-state-default');
			selNavLi.removeClass('ui-tabs-selected ui-state-active');
			console.log('removing selection for %o', selNavLi);

			// set active class for LI link immediately
			$(curTabCtx).find('LI.ui-state-default').has('A[href=#'+tabName+']')
													.addClass( 'ui-tabs-selected ui-state-active');
			hideAllTabs(curTabCtx);

			// TODO USE EASING on the content DIVS
			// remove hidden class add set new selection for tab
			var newSelTab = $(tabSelector);
			newSelTab.removeClass('ui-tabs-hide').addClass('ui-tabs-selected');

			console.log('selectTab(%s, %s)', tabNameOrg, curTabCtx);
			finishTab(curTabCtx,tabSelector, tabName, loadItemsForTab, clickHandler);
		}
		// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
		// finish things on current tab	(pagination, addboxclick, show detail data)
		function finishTab (curTabCtx, tabSelector, tabName, loadItemsForTab, clickHandler) {
			console.log('finishTab (%o,%o,%o)',curTabCtx, tabSelector, tabName);//, loadItemsForTab, clickHandler);
			// remove pagination if we loaded last page
			showPagination(curTabCtx, tabSelector, tabName, loadItemsForTab);
			// dont forget to walk the attachGridClickhandler and fix detail
			attachGridClickhandler(tabSelector, clickHandler);
			// go deeper in the data when needed (this may create recursion)
			// this uses the urlparms for page
			if (curTabCtx == '#artist-set-tabs') {
				if (artists_id != undefined)
					if (getArtistMaster(artists_id))
						toggleArtist(artists_id, artist_set);
			} else {
				if (products_id != undefined)
					if (getProductMaster(products_id)) 		// show product if available
						if (curTabCtx == '#product-format-tabs')
							toggleProduct(curTabCtx, products_id, format, "#product-detail");
						else
							toggleProduct(curTabCtx, products_id, format, "#release-detail");
			}
		}
		// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
		function renderPagination(tabSelector, pageSize, totalRecCount, newItems, pageno) {
			var maxPage = Math.ceil(totalRecCount / pageSize);
			var pager = $(tabSelector + ' div.pagination a');
			if (pager.length == 0) {
				console.log('add missing pagination for ' + tabSelector);
				var template = $('#pagination-template');
				console.assert(template.length);
				// append to div not to content div which will be overwritten
				$(template).tmpl().appendTo(tabSelector + ' .ui-tabs-panel:visible');
				// put a clickhandler in new pagination divs
				pager.unbind('click').click(changeStateHandler);
			} else // stop the loading image
				pager.removeClass('loading').text('LOAD MORE');
			if (pager.length == 0) {
				console.log('cannot find new pagination');
				return; // bail out nothing to do
			}
			// add tabname to anchor href to be picked up by the clickHandler
			var newHref = addHashParameter(pager.attr('href'), getTabParm(tabSelector), getTab(tabSelector));
			newHref = addHashParameter(newHref, 'paged', (+pageno)+1);	// link to the next page
			pager.attr('href', newHref);

			// put data in right DIV for current curTabCtx
			if (pageno) {
				var loaded = pageSize * pageno;
				if (pageno == maxPage)
					loaded = totalRecCount; // max is totalRecCount
				$(tabSelector + ' span.thispage').html(pageno);
				$(tabSelector + ' span.loaded').html(loaded); // total of loaded items
			}
			if (maxPage)
				$(tabSelector + ' span.maxpage').html(maxPage);
			if (totalRecCount)
				$(tabSelector + ' span.reccount').html(totalRecCount);

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
			var pageDiv = $(tabSelector + ' div.pagination');
			if (lastPage < maxpage) {
				pageDiv.removeClass('ui-tabs-hide');
			} else { // hide pagination if only single page!
				pageDiv.addClass('ui-tabs-hide');
			}
		}
		/**
		 * ###########################################################################
		 * ############################## ARTIST MODE ################################
		 * ###########################################################################
		 */
		// the click Handler for products
		// TODO merge code with below its the same
		function artistClickHandler(e) {
			e.preventDefault();
			e.stopPropagation();	// TODO one seems superfluous
			// post click handler not working in this context
			var newhash = $(this).find('a.thumb').attr('href').match(/.*#(.*)/)[1];
			// set new hash
			location.hash ="#"+newhash;
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
				if ($(seltor+':visible').length == 0) { // no longer visible
					removeArtistGridHeader();
				} else {
					showArtistGridHeader();
					$(seltor).get(0).scrollIntoView();	// dont forget to scroll to artist detail
				}
				return;
			} else {
				console.info('destroyed jplayer for %d', lastProductId);
				$(playerSelector).jPlayer( "destroy" ); // remove jplayer first TODO check when necessary
				$(seltor).empty(); // clear if new prod after removing player
			}
			//			$(seltor).fadeOut(100); // make invisible before writing
			var artist = getArtistMaster(artistId, artistSet);
			if (artist == undefined) {
				console.error("artistruct missing for %s in %s", artistId, artistSet);
				return;
			}
			// products_id = null;	// reset previous product selection NOT HERE
			// render template with artist data (header, image, text)
			$('#artist-detail-template').tmpl(artist).appendTo(seltor);
			// TODO fix repeated artistname
			// render releases tabs for artist
			$('#release-format-tabs-template').tmpl(artist).appendTo(seltor);
			// create release tabs TODO try to create only the ones needed
			$('#release-format-inner-tab-template').tmpl(artReltabNames).appendTo('#release-format-tabs');
			var curTabCtx = '#release-format-tabs';
			// fix hash for wptabs
			var temphash = location.hash;
			location.hash = location.hash.replace(/&.*/, '');
			// apply tabs magic to the UL LI graph
			$(curTabCtx).wptabs();
			location.hash = temphash;
			fixWpTabHeader(curTabCtx);
			// load release page for artist
			loadProductsForArtist(artistId)(curTabCtx, allArtReleases,rpage);

			// and activate tabs
			$(seltor).get(0).scrollIntoView();
			$(seltor).fadeIn(fadeintime);
			lastArtistId = artistId;
			return;
		}
		/**
		 * ###########################################################################
		 * ############################## PRODUCT MODE ###############################
		 * ###########################################################################
		 */
		// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
		function boxClickHandler(curCtx, target,toggleFunc) {
			return function (e) {
				e.preventDefault();
				e.stopPropagation();	// TODO one seems superfluous
				var newhash = $(this).find('a.thumb').attr('href').match(/.*#(.*)/)[1];
				location.hash ="#"+newhash;
				// TODO maybe replace this with urlextractor from hash as all parms are in there
				var box = $(e.currentTarget);
				var prodId = box.find('span.product-id').html();
				var prodModel = box.find('span.product-model').html();
				// id of containing tabs-panel is our current tabset
	//		var tab = box.closest('.ui-tabs-panel').attr('id');
				var tab = box.closest('.ui-tabs-panel').find('h3.wp-tab-title').html();
				console.log('Click: ' + tab + '  prodId: ' + prodId + ' prodModel: ' + prodModel);
				toggleFunc(curCtx, prodId, tab, target);
				return false;
			};
		}
		// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
		// show product data in product-detail or release-detail DIV
		function toggleProduct(curTabCtx,prodId,format,seltor) {
			if (lastProductId == prodId) {
				$(seltor).toggle(); // toggle if same
				// remove artist tab title for releases
				$('div#release-format-tabs.wp-tabs  > div.ui-tabs').children('div.ui-tabs-panel:visible').find('h3')
						.css('display', 'none');
				return;
			} else {
				if (lastProductId != undefined && lastProductId != 0 && $(playerSelector).length) {
					$(playerSelector).jPlayer( "destroy" ); // remove jplayer first TODO check when necessary
					console.info('player for %d destroyed',lastProductId);
				}
				$(seltor).empty(); // clear if new prod after removing player
			}
			//			$(seltor).fadeOut(100); // make invisible before writing
			var prod = getProductMaster(prodId, format);
			if (prod == undefined) {
				var msg = 'product missing for ' + prodId + ' '+format;
				console.log(msg);
				throw msg;
			}
			if (prod.products_image_lrg_url != "") // use large image if possible
				prod.products_image_url = prod.products_image_lrg_url;

			// render TEMPLATE with the prod data
			$('#product-detail-template').tmpl(prod).appendTo(seltor);

			// TODO now remove unused tabs....

			// apply tabs magic to the UL LI graph
			var curTabCtx = '#product-detail-tabs';
			// fix hash for wptabs
			var temphash = location.hash;
			location.hash = location.hash.replace(/&.*/, '');
			$(curTabCtx).wptabs();
			location.hash = temphash;

			// TODO fix links
			$(curTabCtx+' ul.ui-tabs-nav li.ui-state-default a').each(function(i, e) {
				console.log($(e).attr('href'));
			});

			// fix artist display in product/release-detail TODO add link to artist page
			$('#prod-detail-header #product-title').html(function (i, html) {
					var sl = html.indexOf('/'); // find the slash
					if (sl >= 0) {
						var artistName = html.substring(0,sl-1);
						var releaseTitle = html.substring(sl+1);
						console.log('found artist (%s) title (%s)',artistName, releaseTitle);
						return releaseTitle;
					}
					var sl = html.indexOf('-'); /// or a dash?
					if (sl >= 0) {
						var artistName = html.substring(0,sl-1);
						var releaseTitle = html.substring(sl+1);
						console.log('found artist (%s) title (%s)',artistName, releaseTitle);
						return releaseTitle;
					}
					return html;	// no change
				});
			// show title for releases
			$('div#release-format-tabs.wp-tabs  > div.ui-tabs').children('div.ui-tabs-panel:visible')
				.find('h3').css( 'display', 'block');

			/** *************************************************************** */
			/** * click handlers * */
			/** *************************************************************** */
			if (prod.products_image_lrg_url != "")
				addFullCoverHandler(seltor);
			// add click to close handler to large image
			$('div#prod-image-big').click(
					function() {
						$(seltor).hide(); // clear if nonempty
						$('div#.wp-tabs  > div.ui-tabs').children('div.ui-tabs-panel:visible')
							.find( 'h3').css('display', 'none');
						lastProductId = 0;
					});
			// load listenbuy immediately instead using the loaded product
			load_listenbuy_tab(curTabCtx,0, prod);

			extractVideoHandlerFromInfoText(seltor);

			if (seltor.indexOf('product') >=0)
				$(seltor+' a[href^="#listenbuy"]').click(); // show tracks
			// always scroll into view (its a DOM function not jquery
			$(curTabCtx).parent().get(0).scrollIntoView(true);	// top end
			$(seltor).fadeIn(fadeintime); //show active tab
			lastProductId = prodId;
		}
		// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
		/** ************************************************************************** */
		/** load listenbuy tab including the jplayer */
		function load_listenbuy_tab(curTabCtx,e, prod) {
			// TODO check if content is there already
			var lstbuytab = $(getTabSelector(curTabCtx,'listenbuy'));
			var loadMsg = lstbuytab.find('#loadingProds');
			var errorMsg = lstbuytab.find('.error');
			if (loadMsg.length == 0 && errorMsg.length == 0)
				return; // this means the ajax call back has found useful data
			else loadMsg.addClass('loading');
			var fetchurl = oscPrefix + "/get_product_data.php?json=1&pid=" + prod.products_id
										+ '&mdl=' + prod.products_model;
			// the url returns a tuple of record count and result lists for formats and xsell
			$.ajax({
				type : 'GET',
				url : fetchurl,
				success : function(data, textStatus, jqXHR) {
					if (data.indexOf('No Records found') >= 0) {
						console.error(data);
						lstbuytab.html(data).addClass('error');
					} else { // stop loading image
						var result = eval('(' + data + ')'); // eval json data
						// merge named members to local database TODO check if this is useful
						addToCache(result.formats);
						addToCache(result.xsell);
						console.log('got product data %o', result);
						var target = lstbuytab.find(' div.wp-tab-content'); // select the content div!!!
						target.empty();
						if (result.xsell != undefined)
							renderPlaylistPlayer(target, prod, result.xsell);
						renderProductFormats(target, result.formats);
					}
				},
				error: function (jqXHR, textStatus, errorThrown) {
					console.error('request(%s) error(%o)',fetchurl, jqXHR);
					var msg = "status="+jqXHR.status+"   "+errorThrown+" <br>when trying to load Release Formats for " + prod.products_model;
					lstbuytab.html('<h3 class="error">'+ msg+'</h3>').addClass('error');
					lstbuytab.append('<A class="error">Click to Retry</a>').click (function (e) {
						load_listenbuy_tab(curTabCtx,e, prod);
					});
				}
			});
		}
		// ###############################################################################
		// ###############################################################################
		var curMP3list  = null;
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
			new jPlayerPlaylist(
				{
					jPlayer: 			playerSelector,
					cssSelectorAncestor:playerContentSelector
				},
				curMP3list,
				{
					backgroundColor : 	'#eaeaea',
					swfPath: 			oscPrefix + "/jplayer",
//					warningAlerts:		true,
					errorAlerts : 		true,
	                solution:     		'html,flash',
					supplied: 			"mp3",
					wmode: 				"window"	// needed for firefox flash
				});
			console.info('instantiated %d players with id %s for %o', $(playerSelector).length, playerSelector, prod);
			$(playerSelector).bind($.jPlayer.event.play, function(event) {
				$(this).jPlayer("pauseOthers");
				// Add a listener to set track info
				var artistName = $('span#artist-name-release').html();
				var trackName = $(this).next().find('a.jp-playlist-current').html();
				$("#jp-track-artist").html(artistName);
				$("#jp-track-name").html(trackName);
				$('#prod-detail-header').get(0).scrollIntoView();	// show player
			});
			// remove the handler moving to next
			$(playerSelector).unbind($.jPlayer.event.ended);
			// this is called after init... nothing to do with showing the tab
			$(playerSelector).bind($.jPlayer.event.ready, function(event) {
				console.log('received JPLAYER event %o',event);
				console.log($(playerContentSelector + " div.jp-playlist ul li"));
				$(playerContentSelector + " div.jp-playlist ul li div").each(function (i,e) {
					var jQDiv = $(e);
					jQDiv.find('a.jp-playlist-item').attr('tabindex',i);			// store index in tabindex
					jQDiv.append('<span class="buy-mp3-btn">BUY MP3</span>');	// add button
					jQDiv.find('span.buy-mp3-btn').click(playlistBuyClickHandler);	// only to button no div
//					console.log('found MP3 DIV %s', $(e).html());
				});
			});
		}
		// ###############################################################################
		/** render product formats (CD, LP) into listenbuy tab * */
		function renderProductFormats(target, formatList) {
			$.each(formatList, function(i,e){
				console.log('price %s',e.products_price);
				e.products_price = Math.floor(e.products_price * 119)/100;
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
			target.append('<div id="buyline"><div id="format-buy-box"/><form id="format-buy-form"></form></div>');
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
//					v.poster = v.products_image;
//					v.mp3 = oscPrefix + '/jplayer/applause.mp3';
					v.mp3 = mp3Prefix + v.products_prelisten;
//					v.m4a = mp3Prefix + v.products_prelisten;
			});
			console.log('makePlayList(%s).length = %d', artist_name, mp3list.length);
			return mp3list;
		}
		// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
		// player util functions
		var curPlay = '';
//		var mp3Prefix = oscPrefix + '/jplayer/';
		var mp3Prefix = 'http://www.shopkatapult.com/prxlstz/';
		function playlistBuyClickHandler(e) {
			var index = $(e.currentTarget).prev('a.jp-playlist-item').attr('tabindex');
			console.assert(index);
			if(!index) console.error('no index found in Playlist');
			console.log('buy click on index %d for mp3 %o', index, curMP3list[index]);
			addProductsToCart([curMP3list[index].products_id]);	// make array of prod id
		}
		/** @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@ */
		// called by click on buy button in format line
		function formatsBuyClickHandler(e) {
			console.log($(this).parent());
//			$().addClass('loading').text('LOADING...');
			var prodlist = [];
			$(e.currentTarget).parent().find('input.checkbox:checked')
					.each(function(i,e){
						prodlist[i] = $(e).attr('value');});
			console.log('buy %d products %o', prodlist.length, prodlist);
			addProductsToCart(prodlist);
		}
		/** @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@ */
		var shoppingCartUrl = oscPrefix + '/catalog/handle_cart.php';
		/** @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@ */
		// take the first element of list and add to cart
		function addProductsToCart(prodlist) {
			var products_id = prodlist.shift();
			var data = {
				'products_id' : products_id,
				'osCsid' : osCsid,
				'action' : 'add_product'
			};
			// cartId is new each time when not logged in (cart in session, basket in DB)
			$.post(shoppingCartUrl, data, function(data, textStatus, jqXHR) {
				var result = eval('(' + data + ')'); // eval json data
				console.log(result);
				osCsid = result.osCsid;
				cart = renderShoppingBox(result.cart, true); // keep cart in global
				if (prodlist.length)		// daisy chain adding the products
					addProductsToCart(prodlist);
				else {
					$('div#shopping-box').get(0).scrollIntoView();
//					$('#artist-detail').addClass('ui-tabs-hide');
				}
			});
		}
		;
		// ###############################################################################
		var shopBoxSel = 'div#shopping-box';
		/** render shopping box / cart * */
		function renderShoppingBox(cart, showBody) {
			if ($(shopBoxSel).length == 0) {
				// place shopping box div in sidebar when missing
				$('div.sidebar').html(function(i, html) {
					return '<div id="shopping-box" class="box cart widget"></div>'+html;
				});
			}
			if ($(cart.contents).length) {
				var cart_entry_tmpl = $('#cart-entry-template');
				console.assert(cart_entry_tmpl.length);
				// draw SHOPPING BOX
				$(shopBoxSel).html('<h3>Your Shopping Box</h3>');
				$(shopBoxSel).append('<div id="cartId">');
				$(shopBoxSel).append('<span id="osCsid">' + osCsid + '</span>');
				$(shopBoxSel).append('<span id="cartId">' + cart.cartID + '</span>');
				$(shopBoxSel).append('</div>');

				$(shopBoxSel).append('<div id="cart-body">');
				if (showBody != undefined) {
					addCartEntries(cart);
					cart_entry_tmpl.tmpl(cart.entries).appendTo('#shopping-box');
				}
				$(shopBoxSel).append('</div');
				$(shopBoxSel).append(
						'<div id="total">You have <b>' + cart.total + '</b> item(s) in your box. <b>Total: '
								+ cart.totalPrice + ' €</b></div>');

				$(shopBoxSel).append('<span id="edit-box" class="box-button">Edit Box</span>');
				$(shopBoxSel).append('<span id="check-out" class="box-button">Check Out</span>');
				$(shopBoxSel+ ' #cartId').addClass('ui-icon-cart');
				$(shopBoxSel).slideDown(500);
			}
			return cart;
		}
		// ###############################################################################
		/***************************************************************************************************************
		 * create datastruct in cart for display in cart-entry-template
		 *
		 * @see osc_jquery_templates.class.php c.index c.products_id, c.products_thumb, c.products_tax_class_id,
		 *      c.products_name, c.products_model, c.products_qty, c.products_format, c.products_price,
		 *      c.products_price_tax
		 **************************************************************************************************************/
		function addCartEntries(cart, format) {
			var i = 0;
			cart.total = 0;
			$.each(cart.contents, function(key, e) {
				console.log('addCart #' + i + ' ' + key + ':');
				console.log(e);
				var charkey = key + '';
				if (cart.entries == undefined)
					cart.entries = []; // init cart entry array if new
				if (cart.entries[charkey] == undefined)
					cart.entries[charkey] = new Object();
				var p = getProductForCart(key, format);
				var cartEntry = cart.entries[charkey];
				cartEntry.index = i;
				cartEntry.products_id = key;
				cartEntry.products_qty = e.qty;
				cartEntry.products_thumb = p.products_thumb;
				cartEntry.products_tax_class_id = p.products_tax_class_id;
				cartEntry.products_name = p.products_name;
				cartEntry.products_model = p.products_model;
				// cartEntry.products_qty = p.products_qty;
				cartEntry.products_format = p.products_format;
				cartEntry.products_price = p.products_price;
				cartEntry.products_price_tax = p.products_price_tax;
				// add up items
				cart.total += e.qty;
				i++;
			});
			cart.totalPrice = 'TBD';
			console.log('fixed %d cart entries ', cart.entries.length);
		}
		// #################	##############################################################
		/** render video object into video tab * */
		var videoTemplateSel = '#product-youtube-template';
		function renderVideo(curTabCtx,videoLink) {
			var prod_youtube_tmpl = $(videoTemplateSel);
			if (prod_youtube_tmpl.length == 0) {
				var msg = '#prod_youtube_tmpl NOT FOUND!!!!!!!!!!!!! STOP';
				alert(msg);
				console.log(msg);
				return false;
			}
			var target = $(getTabSelector(curTabCtx,'video')); // browser can append suffixes
			target.empty();
			prod_youtube_tmpl.tmpl({
				'youTubeUrl' : videoLink
			}).appendTo(target);
		}

		// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
		// create a hashmap == product_cache within this scope to lookup detail via prod_id
		// all formats are merged in there
		function getProductMaster(prod_id) {
			if (prod_id == undefined) return;
			var thisProd = prodmap[prod_id];
			if (thisProd == undefined) {
//				console.log('getProductMaster(%d): undefined',prod_id);
				return;
			}
			thisProd.products_description = unescape(thisProd.products_description);
			console.log('getProductMaster(%d): %o',prod_id, thisProd);
			return thisProd;
		}
		// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
		// load data per artist
		// we add the selected format to this when rendering the boxes
		function getArtistMaster(artistId, artistSet) {
			if (artistId == undefined) return;
			console.assert(prodmap);
			console.assert(manumap);
			// console.log('found previous prodmap: %d manumap: %d', prodmap.length, manumap.length);
			var thisManu = manumap[artistId];
//			console.assert(thisManu);
			if (thisManu == undefined) return;	// no exception as we use this for control logic
			thisManu.manufacturers_description = unescape(thisManu.manufacturers_description);
			if (thisManu.products_image_lrg_url != "") // use large image if possible
				thisManu.products_image_url = thisManu.products_image_lrg_url;
			console.log('getArtistMaster(%d,%s): %o',artistId,artistSet, thisManu);
			return thisManu;
		}
		// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
		// get product for cart (different from master!!)
		function getProductForCart(prod_id) {
			return prodmap[prod_id];
		}
		// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
		// add new products to artist,release cache,product cache depending on data
		function addToCache(newData, artistId) {
			if (newData == undefined || newData.length == 0 || !(newData instanceof Array))
				return;
			if (artistId != undefined)
				relemap[artistId].push.apply(relemap[artistId], newData);	// append to artist releases inplace
			for ( var i = 0; i < newData.length; i++) {
				var o = newData[i];
				if (o.products_id != undefined) 		prodmap[o.products_id] = o;
				if (o.manufacturers_id != undefined)	manumap[o.manufacturers_id] = o;
			}
		}
		// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
		// helper to count properties
		function countProperties(obj) {
			var count = 0;
			for ( var prop in obj)
				if (obj.hasOwnProperty(prop)) ++count;
			return count;
		}
		// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
		function hasProperties(obj) {
			for ( var p in obj)
				return true;
			return false;
		}
		// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
		// read parms from location or passed parameter
		function getStateFromUrl(url) {
//			page_id = getParameterFromUrl('page_id', undefined, url);
			format = getParameterFromUrl('format','vinyl', url);
			products_id = getParameterFromUrl('products_id', undefined, url);
			artist_set = getParameterFromUrl('artistSet', 'main', url);
			artists_id = getParameterFromUrl('artistId', undefined, url);
			paged = getParameterFromUrl('paged', 1, url);
			rpage = getParameterFromUrl('rpage', 1, url);	// different pageno for only 4 releases
			var tmp = getParameterFromUrl('osCsid');
			if (tmp != undefined && tmp != "null")
				osCsid = tmp;	// set global oscommerce session ID if found in URL
		}
		// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
		// hash has priority over urlparm
		function getParameterFromUrl(name, defval, url) {
			var parm = getHashParameter(name,url).toLowerCase();
			if (parm == "null" || url == undefined)
				parm = getURLParameter(name).toLowerCase();
			if (parm == "null")
				parm = defval;
			console.log('initial load for parm %s : %s', name, parm?parm:'undefined');
			return parm;
		}
		// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
		function addHashParameter(hash,name,value) {
			var newVal = name+'='+encodeURI(value);
			if (hash.indexOf(name) >= 0) {
				console.log('replace %s : %s in hash %s', name, value, hash);
				// replace in place if found
				var oldval = (RegExp('(&|#)'+name + '=' + '(.+?)(&|$)').exec(hash))[2];
				console.log('found previous value: %s',oldval);
				var match = (RegExp('(&|#)('+name+'='+'(.+?))(&|$)').exec(hash));
				var oldNameValue = match[2];
				console.log('found previous name value pair %s',oldNameValue);
				var re = new RegExp(oldNameValue);
				hash = hash.replace(re,newVal);
			} else
				hash += '&' + newVal;	// append if not found
			return hash;
		}
		// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
		// stolen from the net... read name parm from location.. null is turned into string
		function getURLParameter(name) {
			return decodeURI((RegExp(name + '=' + '(.+?)(&|$)').exec(location.search) || [ , null ])[1]);
		}
		function getHashParameter(name, hash) {	// TODO always starts with & ??? really
			var value = decodeURI((RegExp('(&|#)'+name + '=' + '(.+?)(&|$)').exec(hash?hash:location.hash) || [ , , null])[2]);
			if (value == "null" || value == undefined){
				// if the hash is a single token (alphnumeric and  _) we use it for the respective parameter
				if ((isReleasePage(hash) && name == 'format') ||(isArtistPage(hash) && name == 'artistSet'))
					value = decodeURI((RegExp('[^#]*#([a-z0-9_]+)$').exec(hash?hash:location.hash) || [ , null])[1]);
			}
			return value;
		}
		// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
		// copied from themes/sight/js/script.js for adaption as we have to use
		// more specific selectors inside tabs
		function attachGridClickhandler(target, clickHandler) {
			var loopSel = target + ' #loop';
			$(loopSel).addClass('grid').removeClass('list'); // make sure its a grid

			// fix formatting of product names in product box
			$(loopSel+' .product-box .product-info').each(function (i,e) {
				var artistName;
				var trackTitle;
				$(this).find('span.product-title').html(function (i, html) {
					var sl = html.indexOf('/'); // find the slash
					if (sl >= 0) {
						artistName = html.substring(0,sl-1);
						trackTitle = html.substring(sl+1);
//						console.debug('found artist (%s) title (%s)',artistName, trackTitle);
						return '';
					}
					var sl = html.indexOf('-'); /// or a dash?
					if (sl >= 0) {
						artistName = html.substring(0,sl-1);
						trackTitle = html.substring(sl+1);
						console.log('found artist (%s) title (%s)',artistName, trackTitle);
						return '';
					}
					return html;	// no change
				});
				if (artistName != undefined)
					$(this).append('<span class="artist-name">'+artistName+'</span>'+'<span class="track-name">'+trackTitle+'</span>');
			});
			// remove old mouseenter, mouseleave,click handlers
			var posts = $(loopSel+' .post');
			posts.unbind('mouseenter').mouseenter(function() {
				$(this).find('.thumb').fadeOut(300).css('z-index', '-1');
			}).unbind('mouseleave').mouseleave(function() {
				$(this).find('.thumb').fadeIn(300).css('z-index', '1');
			});
			posts.unbind('click').click(clickHandler); // set new one
			$.cookie('mode', 'grid'); // store the mode in a cookie (sight compat)
		}
		// ###############################################################################
		// #### FORMATTING HELPER ###########################################################
		// ###############################################################################
		// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
		// remove header line of artist grid
		function removeArtistGridHeader() {
			$('div#artist-set-tabs.wp-tabs > div.ui-tabs').children('div.ui-tabs-panel:visible').find('h3').html(
				function(i, html) { return html.replace(/ Artist Overview/, ''); }).css('display', 'none');
		}
		// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
		// show header line of artist grid (reentrant)
		function showArtistGridHeader() {
			var tabHeaderJQ = $('div#artist-set-tabs.wp-tabs  > div.ui-tabs').children('div.ui-tabs-panel:visible').find('h3');
			console.log('found %d tabHeader for %o', tabHeaderJQ.length, tabHeaderJQ);
			tabHeaderJQ.html(
					function(i, html) {
						if (html.indexOf(' Artist Overview') >= 0)
							return html;
						else
							return html + ' Artist Overview';
					}).css('display', 'block');
		}
		// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
		// create the html to place the boxes in
		function prepareLoopDiv(tabDivSel) {
			var tabSelector = tabDivSel + ' div.wp-tab-content';
			var targetDiv = $(tabSelector);
			if (targetDiv.length == 0)
				throw "No div found for " + tabSelector; // make sure its found
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
			console.log('found %d anchors in ', anchors.length, curTabCtx);
			anchors.each(function(i, e) {
			$(e).parent().html(function(i, html) {
				return '<h3>'+html+'</h3>';
				console.log('wptabHeader in %s : %s', curTabCtx, html);
				});
			});
			// also replace wptabs clickhandler here
			attachTabClickHandler(curTabCtx);
		}
		// wrap the entries into H3 to use existing CSS
		function wrapHtmlInTag(selector, tag) {
			$(selector).each(function(i, e) {
				$(e).parent().html(function(i, html) {
					return '<'+tag+'>'+html+'</'+tag+'>';
					console.log($(e).attr('href'));
				});
			});
		}
		// remove selection for ALL tab-panel DiV and hide them (robuster version)
		function hideAllTabs(curTabCtx) {
			var selContDivs = $(curTabCtx).find('div.ui-tabs DIV.ui-tabs-panel');
			selContDivs.removeClass('ui-tabs-selected ui-state-active').addClass('ui-tabs-hide');
			console.log('hiding tabs for %o', selContDivs);
		}
		// ###############################################################################
		// #### HANDLER HELPER ###########################################################
		// ###############################################################################
		// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
		// show a video in some cases
		function extractVideoHandlerFromInfoText(curTabCtx,seltor) {
			// search for video link in infotext and fix it
			// (always use matching for id tags - they are not unique so browser
			// generates suffixes
			var infotextId = $(seltor + getTabSelector(curTabCtx,'infotext')).attr('id');
			// browser might append suffixes
			var vidImgSel = seltor + ' #' + infotextId + ' a img[src$="watch_it.jpg"]';
			var vidLink = $(vidImgSel).parent();
			if (vidLink.length) {
				// Videobox.open('http://www.youtube.com/watch?v=-yPeQaqct7Y','Alterego Trailer','vidbox');
				// first copy click action from attribute
				var videoboxLink = vidLink.attr('onclick');
				// browser might append suffixes
				var videoId = $(seltor + getTabSelector(curTabCtx,'video')).attr('id');
				// remove click handler from attributes
				vidLink.attr('href', '#' + videoId).attr('onclick', "");
				// add new click handler to switch tabs (easing)
				vidLink.click(function(e) {
					window.scrollTo(0, 0); // scroll up
					$(seltor + ' #' + infotextId).slideUp(300);
					$(seltor + ' #' + videoId).slideDown(500);
					window.scrollTo(0, 0); // scroll up
				});
				var vidParams = /Videobox.open\(([^)]+)\)/.exec(videoboxLink);
				// 1. submatch inside () is in index 1
				var param = vidParams[1].split(',');
				// clean the string
				var videoLink = param[0].replace(/'/g, " ").replace(/"/g, " ").trim();
				renderVideo(videoLink);
			}
		}
		// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
		// attach click handler to all tab links for product formats
		// this has to be called AFTER wpui which changes the DOM so this selector works
		// dont add without removing the previous wpui click handler
		function attachTabClickHandler(curTabCtx) {
			var anchors = $(curTabCtx + ' > .ui-tabs > UL.ui-tabs-nav > LI  A');
			console.log('attachTabClickHandler found %d tabs in %s', anchors.length,curTabCtx);
			anchors.unbind('click').click(changeStateHandler);
		}
		// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
		// replace click handler for navigation links -- once called by DOMloaded event
		function attachNavClickHandler() {
//			if (location.hash.indexOf('#') >= 0)
				$('div.nav ul.sub-menu li.menu-item a').unbind('click').click(changeStateHandler);
		}
		// ##############################################################################
		// ### MAIN CLICK HANDLER #######################################################
		// ##############################################################################
		function changeStateHandler(e) {
			var href = $(e.currentTarget).attr('href');
			if (href.indexOf('#')== 0)
				href = location.pathname + href;	// include pathname of current page in context
			if (!isCurCtx(href)) return true;	// reload page if we are wron
			// we are in the right context carry on
			getStateFromUrl(href);
			e.preventDefault();
			e.stopPropagation();
//			$(this).addClass('loading');
			$(this).find('.pagination').text('LOADING...');
			initPage(href); // open page for href parms... dont reload
			return false;
		}
		// ##############################################################################
		// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
		var flatImage = { 'height' : '200px' };
		var bigImage = { 'height' : 'auto' }; // just change the enclosing DIVs height
		// attach special toggle handler to show large image after making tabs
		function addFullCoverHandler(seltor) {
			$(seltor + ' li.ui-state-default a[href*="#full_cover"]')
					.click(function() {
								var bigimg = $('div#prod-image-big');
								var newCSS = flatImage;
								if (bigimg.css('height') == '200px')
									newCSS = bigImage;
								bigimg.css(newCSS);	// only the div
								bigimg.parent().get(0).scrollIntoView(true);
							});
			// attach close handler to tab links
			$(seltor + ' #prod-image-big').click(
				function() {
					$(seltor).hide();
					// dont forget to remove the wp-tab-title we added under the detail while unfolding
					var headers = $('div#release-format-tabs.wp-tabs  > div.ui-tabs')
							.children('div.ui-tabs-panel:visible').find('h3');
					if (headers.length) {
						headers .css('display', 'none');
						console.log('removed headers ');
					} else console.log('no release headers found to remove');
					lastProductId = 0;
				});
		}
		// ###############################################################################
		// #### GETTER HELPER ############################################################
		// ###############################################################################
		// to deal with funny modifications -- space in front for concatenation
		function getTabSelector(curTabCtx, tabName) {
			return curTabCtx + ' div[id^='+tabName.toLowerCase().replace(/ /g, '_')+']';
		}
		function getTabLnk(curTabCtx, tabName) {
			return $(curTabCtx).find('ul.ui-tabs-nav li a[href^=#'+tabName.toLowerCase().replace(/ /g, '_')+']');
		}
		function getTabFromSelector(tabSelector) {
			var left = tabSelector.indexOf('div[id^=');
			var right= tabSelector.indexOf(']');
			if (left >= 0)
				return tabSelector.substring(left+8,right);
		}
		function getLastPageOfTab(tabSelector) {
			return (+$(tabSelector + ' span.thispage').html());	// number conversion !!!
		}
		function getMaxPageOfTab(tabSelector) {
			return (+$(tabSelector + ' span.maxpage').html());	// number conversion !!!
		}
		// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
		// context dependent values and functions
		function getTabCtx(href) {
			if (isReleasePage(href)) {
				return '#product-format-tabs';
			} else if (isArtistPage(href)) {
				return '#artist-set-tabs';
			} else
				return;
		}
		function getTab(href) {
			if (isReleasePage(href)) {
				return format;
			} else if (isArtistPage(href)) {
				return artist_set;
			} else
				return;
		}
		function getTabParm(href) {
			if (isReleasePage(href)) {
				return 'format';
			} else if (isArtistPage(href)) {
				return 'artistSet';
			} else
				return;
		}
		function getTabLoader(href) {
			if (isReleasePage(href)) {
				return loadProductsForTab;
			} else if (isArtistPage(href)) {
				return loadArtistsForTab;
			} else
				return;
		}
		function getClickHandler(href) {
			if (isReleasePage(href)) {
				return productClickHandler;
			} else if (isArtistPage(href)) {
				return artistClickHandler;
			} else
				return;
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
			if (isArtistPage(href)) return artistsPageSize;
			if (isReleasePage(href)) return productsPageSize;
		}
		function getPageSize(href) {
			if (isArtistPage(href)) return artistsPageSize;
			if (isReleasePage(href)) return productsPageSize;
		}
		// ++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
		// match pathname or tabctx string dont match trailing slash which is optional
		function isReleasePage(href) {
			if (href == undefined) href = location.href;
			return href.indexOf('/releases') >= 0 || href.indexOf('#product-format-tabs') >=0 ;
		}
		function isArtistPage(href) {
			if (href == undefined) href = location.href;
			return href.indexOf('/artists') >= 0 || href.indexOf('#artist-set-tabs') >= 0;
		}
		// checks if location.hash and href are in the same page context
		// TODO this has to go when we render everything from this script
		// instead of relying on the server to provide rudimentary but required HTML to render into
		function isCurCtx(href) {
			return ((isReleasePage() && isReleasePage(href)) || (isArtistPage() && isArtistPage(href)));
		}
		var cnt = 0;
		$(window).scroll(function () {
		   if ($(window).scrollTop() >= $(document).height() - $(window).height() - 5) {
		      console.log('trigger pageload NOW %d %s ?????',cnt);
		   }
		});
	});
})(jQuery);
