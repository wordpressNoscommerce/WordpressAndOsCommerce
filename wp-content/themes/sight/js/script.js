jQuery.noConflict();
(function($) {
    $(function() {
        /*** Dropdown menu ***/

        var timeout  = 400;
        var showTime = 600;
        var hideTime = 300;
        var clearQueue = true;
        var jumpToEnd = true;

        function my_dd_open(e) {
            var ddmenuitem = $(this).find('ul');	// find submenu
//    		var which = ddmenuitem.find('li a').text();
//        	console.log('%d dd_open %s', cnt, which);
    		ddmenuitem.css('display', 'block');
            ddmenuitem.css({'visibility': 'visible'});
    		ddmenuitem.prev().addClass('dd_hover').parent().addClass('dd_hover');
            ddmenuitem.stop(clearQueue,jumpToEnd).animate({opacity: 1}, showTime, function() {
  //          	console.log('%d dd_open CALLBACK %s', cnt, which);
        		ddmenuitem.prev().addClass('dd_hover').parent().addClass('dd_hover');
            });
        }
        function my_dd_close(e) {
            var ddmenuitem = $(this).find('ul');	// find submenu
//    		var which = ddmenuitem.find('li a').text();
    		ddmenuitem.prev().removeClass('dd_hover').parent().removeClass('dd_hover');
    		ddmenuitem.stop(clearQueue,jumpToEnd).animate({opacity: 0}, hideTime, function() {
    	    // Animation complet - remove submenu so it doesnt cover content below
    			ddmenuitem.css('display', 'none');
        		ddmenuitem.prev().removeClass('dd_hover').parent().removeClass('dd_hover');
//            	console.log('%d dd_close CALLBACK %s', cnt, which);
    		});
//        	console.log('%d dd_close %s', cnt, which);
        }
        // use hover
//        document.onclick = my_dd_close;
        $('#dd > li').hover(my_dd_open,my_dd_close);

        $('#larr, #rarr').hide();
        $('.slideshow').hover(
            function(){
                $('#larr, #rarr').show();
            }, function(){
                $('#larr, #rarr').hide();
            }
        );

        /*** View mode ***/

        if ( $.cookie('mode') == 'grid' ) {
            grid_update();
        } else if ( $.cookie('mode') == 'list' ) {
            list_update();
        }

        $('#mode').toggle(
            function(){
                if ( $.cookie('mode') == 'grid' ) {
                    $.cookie('mode','list');
                    list();
                } else {
                    $.cookie('mode','grid');
                    grid();
                }
            },
            function(){
                if ( $.cookie('mode') == 'list') {
                    $.cookie('mode','grid');
                    grid();
                } else {
                    $.cookie('mode','list');
                    list();
                }
            }
        );

        function grid(){
            $('#mode').addClass('flip');
            $('#loop')
                .fadeOut('fast', function(){
                    grid_update();
                    $(this).fadeIn('fast');
                })
            ;
        }

        function list(){
            $('#mode').removeClass('flip');
            $('#loop')
                .fadeOut('fast', function(){
                    list_update();
                    $(this).fadeIn('fast');
                })
            ;
        }

        function grid_update(){
            $('#loop').addClass('grid').removeClass('list');
            $('#loop').find('.thumb img').attr({'width': '190', 'height': '190'});
            $('#loop').find('.post')
                .mouseenter(function(){
                    $(this)
                        .css('background-color','#EAEAEA')
                        .find('.thumb').hide()
                        .css('z-index','-1');
                })
                .mouseleave(function(){
                    $(this)
                        .css('background-color','#FFFFFF')
                        .find('.thumb').show()
                        .css('z-index','1');
                });
            $('#loop').find('.post').click(function(){
            	// only set this if anchor is found
            	var anchor = $(this).find('h2 a');
            	if (anchor.length > 0)
            		location.href=$(this).find('h2 a').attr('href');
            });
            $.cookie('mode','grid');
        }

        function list_update(){
            $('#loop').addClass('list').removeClass('grid');
            $('#loop').find('.post').removeAttr('style').unbind('mouseenter').unbind('mouseleave');
            $('#loop').find('.thumb img').attr({'width': '290', 'height': '290'});
            $.cookie('mode', 'list');
        }

        /*** Ajax-fetching posts ***/

        $('#pagination a').live('click', function(e){
            e.preventDefault();
            $(this).addClass('loading').text('LOADING...');
            $.ajax({
                type: "GET",
                url: $(this).attr('href') + '#loop',
                dataType: "html",
                success: function(out){
                    result = $(out).find('#loop .post');
                    nextlink = $(out).find('#pagination a').attr('href');
                    $('#loop').append(result.fadeIn(300));
                    $('#pagination a').removeClass('loading').text('LOAD MORE');
                    if (nextlink != undefined) {
                        $('#pagination a').attr('href', nextlink);
                    } else {
                        $('#pagination').remove();
                    }
                    if ( $.cookie('mode') == 'grid' ) {
                        grid_update();
                    } else {
                        list_update();
                    }
                }
            });
        });

        /*** Misc ***/

        $('#comment, #author, #email, #url')
        .focusin(function(){
            $(this).parent().css('border-color','#888');
        })
        .focusout(function(){
            $(this).parent().removeAttr('style');
        });
        $('.rpthumb:last, .comment:last').css('border-bottom','none');

        /************************* SOCIAL MENU *****************************/
        /***** call api with current location. callback then draws the links ****/
        var data = {
        		longurl: location.href,
        		};
        $.getJSON( "/tinyurlapi.php?", { longurl: location.href, }, function(data){
        	console.log(data);
        	drawSocialLinks(data);
        });
        /**** deal with the social menu .. called back by the tinyUrl request ****/
        function drawSocialLinks(tinyUrl) {
        	var ul = $('div.nav ul#dd');
//        	var anchor = $('div.nav ul#dd > li.menu-item-type-custom a');
//	        if (anchor.text() == 'SocialLinking') {
//	        	var socialMenu = anchor.parent().empty();	// clear out
	        	var title = document.title.substr(0, document.title.indexOf('|') - 1);
	        	ul.append('<li><div id="socialLinking">'
	        					+'<a href="http://facebook.com/share.php?u='+location.href+'" target="_blank" class="f" title="Share on Facebook"></a>'
	        					+'<a href="http://twitter.com/home?status='+title+'+'+tinyUrl+'" target="_blank" class="t" title="Spread the word on Twitter"></a>'
	        					+'<a href="http://digg.com/submit?phase=2&amp;url='+location.href+'" target="_blank" class="di" title="Bookmark on Del.icio.us"></a>'
	        					+'<a href="http://stumbleupon.com/submit?url='+location.href+'" target="_blank" class="su" title="Share on StumbleUpon"></a>'
	        					+'</div></li>');
//	        }
        }

        /************************* LOGO ANIMATION ********************/
		// 253 x 47
		var props =[
		            [
		             	{'margin-left': '0',	'margin-top': '0'},
		             	{'margin-left': '1',	'margin-top': '0'},
		             	{'margin-left': '1',	'margin-top': '1'},
		             	{'margin-left': '0',	'margin-top': '1'},
		             	{'margin-left': '0',	'margin-top': '0'},
		             	{'margin-left': '0',	'margin-top': '0'},
		            ],
/*					[
		             	{'height': '150',	'max-height': '150'},
		             	{'height': '5',		'max-height': '5'},
		             	{'height': '47',	'max-height': '47'},
		             	{'height': '47',	'max-height': '47'},
					],
					[
		             	{'width': '253'},
		             	{'width': '10',		'max-width': '50'},
		             	{'width': '600',	'max-width': '600'},
		             	{'width': '253',	'max-width': '253'},
					],
					[
		             	{'opacity': '1',	'width': '253','max-height': '253'},
		             	{'opacity': '0.5' },
		             	{'opacity': '1' },
		             	{'opacity': '0.8' },
					],
*/
					[
		             	{'border-width': '20',	'border-color': '#400'},
		             	{'border-width': '15',	'border-color': '#040'},
		             	{'border-width': '20',	'border-color': '#004'},
		             	{'border-width': '15',	'border-color': '#200'},
		             	{'border-width': '20',	'border-color': '#020'},
		             	{'border-width': '10',	'border-color': '#002'},
		             	{'border-width': '0'},
					],
		           ];
		// correct distances
		var shiftFactor = 50;
		props[0].map( function (item){
			item['margin-left'] *= shiftFactor;
			item['margin-top'] *= shiftFactor;
		});
		var speed = 2000;
		var imgSel = '.logo a img ';
		var index = 0;
		var mode=0;
		var stop=0;
		// LOOP FOREVER
		var anim = function () {
			if (stop) return;
			window.scrollTo(0,0);
			if (index < props[mode].length) {
				$(imgSel).animate(props[mode][index],speed, anim);
				index++;
			} else {
				mode = (1+mode) % props.length;
				index=0;
				$(imgSel).animate(props[mode][index],speed, anim);
			}
//			console.log(index);
		};
		// start animation with delay
		//setTimeout(anim, 5000);
		$('.credits').click(function () { index = 0; anim(); });
		$(imgSel).hover(function () { stop = (stop+1) % 2; console.log('stopv:'+stop);});

    });
})(jQuery);
