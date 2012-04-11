		function selectTab(tab,tabSelector) {
			var targetSel = 'div#' + tab.toLowerCase();
			if ($('#' + tab).length == 0)
				alert('cannot find tab ' + tab);

			if ($('#' + tab, '.ui-tabs-selected').length > 0)
				return; // nothing to do we are active already

			$('#art-details').empty(); // clear artist details when showing new tab
			$('#prod-details').empty(); // clear prod details when showing new tab
			lastProductId = 0;

			if ($(tabSelector + ' DIV.ui-tabs-selected').length == 0) {
				$(tabSelector + ' DIV.pagination').addClass('ui-tabs-hide');
				console.log('NO SELECTION ' + tab);
			}
			// remove selection for LI and DiV
			$(tabSelector).find('DIV.ui-tabs-selected').removeClass(
					'ui-tabs-selected ui-state-active')
					.addClass('ui-tabs-hide');
			$(tabSelector).find('LI.ui-tabs-selected').removeClass(
					'ui-tabs-selected ui-state-active');
			// remove hidden class add set new selection for tab
			$(targetSel).removeClass('ui-tabs-hide').addClass(
					'ui-tabs-selected');
			// set active class for LI link also (robust version doesnt use stepwise navigation
			$(tabSelector).find('LI.ui-state-default').has(
					'A[href="#' + tab + '"]').addClass(
					'ui-tabs-selected ui-state-active');

			// attach click handler to all tab links for product tabs
			// this has to be called AFTER wpui which changes the DOM so this selector works
			// dont add without removing the previous wpui click handler
			$(tabSelector + ' > .ui-tabs > UL.ui-tabs-nav > LI  A').unbind(
					'click').bind('click', function() {
				// use hash from anchor link
				var tab = this.hash.substring(1);
				console.log('clicked on tab: ' + tab);
				if (tab == undefined) alert('no tab found!');
				loadArtistsIntabTab(tab);
			});

			// attach click handler for pagination link only if there is
			var thispage = (+$(targetSel + ' span.thispage').html());
			var maxpage = (+$(targetSel + ' span.maxpage').html());
			if (thispage < maxpage) {
				$(targetSel + ' div.pagination').removeClass('ui-tabs-hide');
				// remove previous click handler
				$(targetSel + ' div.pagination a.nextpostslink')
						.unbind('click');
				$(targetSel + ' div.pagination a.nextpostslink').click(
						function(e) {
							e.preventDefault();
							// this value is likely to be changed in the meanwhile
							var thispage = (+$(targetSel + ' span.thispage') .html());
							$(this).addClass('loading').text('LOADING...');
							console.log('next page from ' + thispage + ' / ' + maxpage);
							loadArtistsIntabTab(tab, thispage + 1); // load next page
							return false;
						});
			} else { // hide pagination if only single page!
				$(targetSel + ' div.pagination').addClass('ui-tabs-hide');
			}
			// attach grid event handler
			grid_update_in_tab(targetSel, artistClickHandler);
			console.log('selectedTab: ' + tab);
		}
