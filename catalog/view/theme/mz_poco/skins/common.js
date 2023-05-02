"use strict";

function getURLVar(key) {
	var value = [];

	var query = String(document.location).split('?');

	if (query[1]) {
		var part = query[1].split('&');

		for (i = 0; i < part.length; i++) {
			var data = part[i].split('=');

			if (data[0] && data[1]) {
				value[data[0]] = data[1];
			}
		}

		if (value[key]) {
			return value[key];
		} else {
			return '';
		}
	}
}

/** JQuery cookie
 * */
(function (factory) {
	if (typeof define === 'function' && define.amd) {
		// AMD
		define(['jquery'], factory);
	} else if (typeof exports === 'object') {
		// CommonJS
		factory(require('jquery'));
	} else {
		// Browser globals
		factory(jQuery);
	}
}(function ($) {

	var pluses = /\+/g;

	function encode(s) {
		return config.raw ? s : encodeURIComponent(s);
	}

	function decode(s) {
		return config.raw ? s : decodeURIComponent(s);
	}

	function stringifyCookieValue(value) {
		return encode(config.json ? JSON.stringify(value) : String(value));
	}

	function parseCookieValue(s) {
		if (s.indexOf('"') === 0) {
			// This is a quoted cookie as according to RFC2068, unescape...
			s = s.slice(1, -1).replace(/\\"/g, '"').replace(/\\\\/g, '\\');
		}

		try {
			// Replace server-side written pluses with spaces.
			// If we can't decode the cookie, ignore it, it's unusable.
			// If we can't parse the cookie, ignore it, it's unusable.
			s = decodeURIComponent(s.replace(pluses, ' '));
			return config.json ? JSON.parse(s) : s;
		} catch(e) {}
	}

	function read(s, converter) {
		var value = config.raw ? s : parseCookieValue(s);
		return $.isFunction(converter) ? converter(value) : value;
	}

	var config = $.mz_cookie = function (key, value, options) {

		// Write

		if (value !== undefined && !$.isFunction(value)) {
			options = $.extend({}, config.defaults, options);

			if (typeof options.expires === 'number') {
				var days = options.expires, t = options.expires = new Date();
				t.setTime(+t + days * 864e+5);
			}

			return (document.cookie = [
				encode(key), '=', stringifyCookieValue(value),
				options.expires ? '; expires=' + options.expires.toUTCString() : '', // use expires attribute, max-age is not supported by IE
				options.path    ? '; path=' + options.path : '',
				options.domain  ? '; domain=' + options.domain : '',
				options.secure  ? '; secure' : ''
			].join(''));
		}

		// Read

		var result = key ? undefined : {};

		// To prevent the for loop in the first place assign an empty array
		// in case there are no cookies at all. Also prevents odd result when
		// calling $.mz_cookie().
		var cookies = document.cookie ? document.cookie.split('; ') : [];

		for (var i = 0, l = cookies.length; i < l; i++) {
			var parts = cookies[i].split('=');
			var name = decode(parts.shift());
			var cookie = parts.join('=');

			if (key && key === name) {
				// If second argument (value) is a function it's a converter...
				result = read(cookie, value);
				break;
			}

			// Prevent storing a cookie that we couldn't decode.
			if (!key && (cookie = read(cookie)) !== undefined) {
				result[name] = cookie;
			}
		}

		return result;
	};

	config.defaults = {};

	$.removeCookie = function (key, options) {
		if ($.mz_cookie(key) === undefined) {
			return false;
		}

		// Must not alter options, thus extending a fresh object...
		$.mz_cookie(key, '', $.extend({}, options, { expires: -1 }));
		return !$.mz_cookie(key);
	};

}));

function finishPreloader(){
    $('#page-preloader').removeClass('active');
}

function pageLoad(content){
        // Active first tab
        $(content).find('.nav-tabs.active-first').each(function(){
            $(this).find('li:first-child a').tab('show');
        });

        // Active first accordion
        $(content).find('.accordion.active-first').each(function(){
            $(this).find('.collapse:first').collapse('show');
        });
        
        // Collapsible text
        $(content).find('[data-line].text-collapsed').textOverflowReadmore();

        // Deal module countdown disable compact mode
        $('.module-deals [data-countdown]').data('compact', 0);
        
        // Countdown
        $(content).find('[data-countdown]').each(function(){
            if($(this).data('countdown')){
                var layout = null;
                if($(this).data('countdown-layout')){
                    layout = $(this).data('countdown-layout');
                }
                $(this).countdown({
                    until: new Date($(this).data('countdown').replace(/-/g, '/')),
                    layout: layout,
                    compact: $(this).data('compact')
                });
            }
        });
        
        // Lazy loading
        if($.Lazy !== undefined){
            $(content).find('.lazy-load').Lazy({
                effect: "fadeIn",
                effectTime: 300,
                threshold: 1,
                visibleOnly: false,
                beforeLoad: function(img) {
                    // called before an elements gets handled
                    img.addClass('loader-spinner');
                },
                afterLoad: function(img) {
                    // called after an element was successfully handled
                    img.removeClass('loader-spinner');
                }
            });
        }
        
        // Products
        $(content).find('.content-products .product-layout .carousel-inner, .product-thumb.image-left .carousel-inner').each(function(){
            $(this).css('max-width', $(this).find('img').prop('naturalWidth'));
        });
        
        // Article fix carusel image width
        $(content).find('.content-articles .article-list .carousel-inner').each(function(){
            $(this).css('max-width', $(this).find('img').prop('naturalWidth'));
        });
        
        // Spinner
        $('[data-toggle="spinner"]').each(function(){
            var numberInput = $(this).find('[type="number"]');
            
            $(this).find('[data-spinner="up"]').on('click', function(){
                var currentNumber = parseInt(numberInput.val());
                
                if($(this).data('max')){
                    var maxNumber = parseInt($(this).data('max'));
                } else {
                    var maxNumber = -1;
                }

                console.log(maxNumber);
                
                if(maxNumber < 0 || currentNumber < maxNumber){
                    numberInput.val(currentNumber + 1);
                } else {
                    numberInput.val(maxNumber);
                }
            });
            
            $(this).find('[data-spinner="down"]').on('click', function(){
                var currentNumber = parseInt(numberInput.val());
                
                if($(this).data('min')){
                    var minNumber = parseInt($(this).data('min'));
                } else {
                    var minNumber = 1;
                }
                
                if(currentNumber > minNumber){
                    numberInput.val(currentNumber - 1);
                } else {
                    numberInput.val(minNumber);
                }
            });
        });
}

$(function() {
    pageLoad(document); // Javascript for ajax or dynamic content

    $(document).tooltip({
        selector: '[data-toggle="tooltip"]',
        trigger : 'hover',
        container: 'body'
    });

    $(document).popover({selector: '[data-toggle="popover"]'});
        
    // Active current link
    $('a[href="' + window.location.href + '"], a[href="' + window.location.href.slice(0, -1) + '"]').addClass('active');
        
	// Currency
	$('.currency-select').on('click', function(e) {
		e.preventDefault();

		$('#form-currency input[name=\'code\']').val($(this).data('code'));
                $('#form-currency input[name=\'redirect\']').val(window.location.href);

		$('#form-currency').submit();
	});

	// Language
    $('.language-select').on('click', function(e) {
		e.preventDefault();

		$('#form-language input[name=\'code\']').val($(this).data('code'));
                $('#form-language input[name=\'redirect\']').val(window.location.href);

		$('#form-language').submit();
	});
        
    // Product List
	$('#list-view').on('click', function() {
        var $class = $('.content-products > .row').data('list');
        $('.content-products .product-grid').each(function(){
            $(this).attr('class', $class);

            if($(this).find('.caption .product-action').length == 0){
                $(this).find('.caption').append($(this).find('.product-action'));
                $(this).find('.product-action').data('toCaption', true);
            }
            
            $(this).find('.text-collapsed').textOverflowReadmore();;
        });
        
        $('#grid-view').removeClass('active');
        $('#list-view').addClass('active');

		localStorage.setItem('display_' + $('.content-products > .row').data('view_id'), 'list');
	});

	// Product Grid
	$('#grid-view').on('click', function() {
		var $class = $('.content-products > .row').data('grid');
        $('.content-products .product-list').each(function(){
            $(this).attr('class', $class);

            if($(this).find('.product-action').data('toCaption')){
                $(this).find('.product-thumb-top').append($(this).find('.product-action'));
            }
        });

		$('#list-view').removeClass('active');
		$('#grid-view').addClass('active');

		localStorage.setItem('display_' + $('.content-products > .row').data('view_id'), 'grid');
	});
        
    var $view = localStorage.getItem('display_' + $('.content-products > .row').data('view_id'));
    var $default_view = $('.content-products > .row').data('default_view');
        
	if ($view) {
		$('#' + $view + '-view').trigger('click').addClass('active');
	} else if($default_view) {
        $('#' + $default_view + '-view').trigger('click').addClass('active');
    }

    /* Sticky */
    mz_sticky.init();
        
    /* Pure drawer */
    $('[data-toggle="mz-pure-drawer"]').on('click', function(e){
        e.preventDefault();
        
        var target = $(this).data('target');
        if(!target){
            target = $(this).attr('href');
        }

        if($(target).hasClass('active')){
            $(target).removeClass('active');
        } else {
            $('.mz-pure-drawer.active').removeClass('active');
            $(target).addClass('active');
        }
    });
    $('.mz-pure-overlay').on('click', function(){
        $('.mz-pure-drawer').removeClass('active');
    });

    // Escape action
    $(document).on('keyup', function(e) {
        if (e.key == "Escape") {
            $('.mz-pure-drawer').removeClass('active');
        } 
    });

    // Remove unsed toggle
    $('.entry-design > a[data-toggle]').each(function(){
        if(!$($(this).attr('href') || $(this).data('target')).length){
            $(this).parent().remove();
        }
    });
        
	// Autocomplete search
    $('.search-category').each(function(){
        var ctx = this;
        
        $(this).find('.dropdown-item').on('click', function(e){
            e.preventDefault();
            
            $(ctx).find('[data-toggle="dropdown"]').text($(this).text());
            
            if($(this).data('category_id')){
                $(ctx).find('[name="category_id"]').val($(this).data('category_id')).prop('disabled', 0);
            } else {
                $(ctx).find('[name="category_id"]').val($(this).data('category_id')).prop('disabled', 1);
            }
            
            $(ctx).find('.dropdown-item').removeClass('active');
            $(this).addClass('active');
        });
    });
    
    $('#search input[name=\'search\'][data-autocomplete]').searchAutocomplete({
            dropdown: function(input){
                return $(input).parents('#search').find('.autocomplete');
            },
            source: function(request, response) {
                    if($(this).parents('#search').find('[name="category_id"]').length){
                        var category_id = $(this).parents('#search').find('[name="category_id"]').val();
                    } else {
                        var category_id = 0;
                    }
                    
                    $.ajax({
                            url: 'index.php?route=' + $(this).data('autocomplete_route') + '&filter_name=' +  encodeURIComponent(request) + '&filter_category_id=' + (category_id?category_id:0) + '&limit=' + $(this).data('autocomplete'),
                            dataType: 'html',
                            success: function(html) {
                                    response(html);
                            }
                    });
            },
            response: function(html) {
                    if (html) {
                            this.show();
                    } else {
                            this.hide();
                    }

                    this.dropdown.html(html);
            }
    });
        
    // Multi level dropdown for nav menu
    $('.navbar-nav').each(function(){
        var menu = $(this);
        var lang_dir = $(document).attr('dir');
        
        var align_dropdown_ltr = function() {
            var dropdown = $(this).offset();
            
            // Ignore collapsed navbar
            if($(this).children('.dropdown-menu').css('position') === 'static'){
                return;
            }
            
            var dropdown_right_space = document.documentElement.clientWidth - (dropdown.left + $(this).outerWidth());
            var dropdown_menu_width = $(this).children('.dropdown-menu').outerWidth();
            
            if(menu.hasClass('vertical')){
                var open_left = dropdown_right_space < dropdown.left;
                
                if (open_left) {
                    $(this).removeClass('dropright').addClass('dropleft');
                } else {
                    $(this).removeClass('dropleft').addClass('dropright');
                }
                
                // Prevent to overflow dropdown outside screen
                if(open_left){
                    var i = dropdown_menu_width - dropdown.left;

                    if (i > 0) {
                            $(this).children('.dropdown-menu').outerWidth(dropdown.left - 15);
                    }
                } else {
                    var i = dropdown_menu_width - dropdown_right_space;

                    if (i > 0) {
                        $(this).children('.dropdown-menu').outerWidth(dropdown_right_space - 30);
                    }
                }

            } else if(!$(this).children('.dropdown-menu').hasClass('full-width')) {
                var overflow_space = dropdown_menu_width - document.documentElement.clientWidth;
                
                if(overflow_space > 0){
                    $(this).children('.dropdown-menu').outerWidth(document.documentElement.clientWidth - 30).css('margin-left', '-' + (dropdown.left - 15) + 'px');
                } else {
                    var i = (dropdown.left + dropdown_menu_width + 15) - document.documentElement.clientWidth;

                    if (i > 0) {
                            $(this).children('.dropdown-menu').css('margin-left', '-' + i + 'px');
                    }
                }
            } 
            
            // Align dropdown submenu
            $(this).on('shown.bs.dropdown', function(){
                $(this).children('.dropdown-menu').children('.dropdown-submenu').each(align_dropdown_submenu_ltr);
                $(this).off('shown.bs.dropdown');
            });
        };
        
        var align_dropdown_rtl = function() {
            var dropdown = $(this).offset();
            
            // Ignore collapsed navbar
            if($(this).children('.dropdown-menu').css('position') === 'static'){
                return;
            }
            
            var dropdown_right_space = document.documentElement.clientWidth - (dropdown.left + $(this).outerWidth());
            var dropdown_menu_width = $(this).children('.dropdown-menu').outerWidth();
            
            if(menu.hasClass('vertical')){
                var open_left = dropdown_right_space < dropdown.left;
                
                if (open_left) {
                    $(this).removeClass('dropleft').addClass('dropright');
                } else {
                    $(this).removeClass('dropright').addClass('dropleft');
                }
                
                // Prevent to overflow dropdown outside screen
                if(open_left){
                    var i = dropdown_menu_width - dropdown.left;

                    if (i > 0) {
                            $(this).children('.dropdown-menu').outerWidth(dropdown.left - 15);
                    }
                } else {
                    var i = dropdown_menu_width - dropdown_right_space;

                    if (i > 0) {
                        $(this).children('.dropdown-menu').outerWidth(dropdown_right_space - 30);
                    }
                }

            } else if(!$(this).children('.dropdown-menu').hasClass('full-width')) {
                var overflow_space = dropdown_menu_width - document.documentElement.clientWidth;
                
                if(overflow_space > 0){
                    $(this).children('.dropdown-menu').outerWidth(document.documentElement.clientWidth - 30).css('margin-right', '-' + (dropdown_right_space - 15) + 'px');
                } else {
                    var i = (dropdown_menu_width + 15) - dropdown.left;

                    if (i > 0) {
                            $(this).children('.dropdown-menu').css('margin-right', '-' + i + 'px');
                    }
                }
            } 
            
            // Align dropdown submenu
            $(this).on('shown.bs.dropdown', function(){
                $(this).children('.dropdown-menu').children('.dropdown-submenu').each(align_dropdown_submenu_rtl);
                $(this).off('shown.bs.dropdown');
            });
        };
        
        // Navbar collapse
        $(this).parent('.navbar-collapse:not(.show)').on('shown.bs.collapse', function(){
            $(this).find('.dropdown').each(lang_dir === 'rtl'?align_dropdown_rtl:align_dropdown_ltr);
            $(this).off('shown.bs.collapse');
        });
        
        $(this).find('.dropdown').each(lang_dir === 'rtl'?align_dropdown_rtl:align_dropdown_ltr);
        
        var align_dropdown_submenu_ltr = function() {
            var dropdown = $(this).offset();
            
            // Ignore collapsed navbar
            if($(this).children('.dropdown-menu').css('position') === 'static'){
                return;
            }
            
            var dropdown_right_space = document.documentElement.clientWidth - (dropdown.left + $(this).outerWidth());
            var dropdown_menu_width = $(this).children('.dropdown-menu').outerWidth();

            var open_left = dropdown_right_space < dropdown_menu_width && dropdown_right_space < dropdown.left;

            if (open_left) {
                $(this).removeClass('dropright').addClass('dropleft');
            } else {
                $(this).removeClass('dropleft').addClass('dropright');
            }

            // Prevent to overflow dropdown outside screen
            if(open_left){
                var i = dropdown_menu_width - dropdown.left;

                if (i > 0) {
                        $(this).children('.dropdown-menu').outerWidth(dropdown.left - 15);
                }
            } else {
                var i = dropdown_menu_width - dropdown_right_space;

                if (i > 0) {
                    $(this).children('.dropdown-menu').outerWidth(dropdown_right_space - 30);
                }
            }
        };
        
        var align_dropdown_submenu_rtl = function() {
            var dropdown = $(this).offset();
            
            // Ignore collapsed navbar
            if($(this).children('.dropdown-menu').css('position') === 'static'){
                return;
            }
            
            var dropdown_right_space = document.documentElement.clientWidth - (dropdown.left + $(this).outerWidth());
            var dropdown_menu_width = $(this).children('.dropdown-menu').outerWidth();

            var open_right = dropdown.left < dropdown_menu_width && dropdown.left < dropdown_right_space;

            if (open_right) {
                $(this).removeClass('dropright').addClass('dropleft');
            } else {
                $(this).removeClass('dropleft').addClass('dropright');
            }

            // Prevent to overflow dropdown outside screen
            if(open_right){
                var i = dropdown_menu_width - dropdown_right_space;

                if (i > 0) {
                    $(this).children('.dropdown-menu').outerWidth(dropdown_right_space - 30);
                }
            } else {
                var i = dropdown_menu_width - dropdown.left;

                if (i > 0) {
                        $(this).children('.dropdown-menu').outerWidth(dropdown.left - 15);
                }
            }
        };
        
        $(this).find('.dropdown-menu a.dropdown-toggle').on('click', function(e) {
            if (!$(this).next().hasClass('show')) {
                $(this).parents('.dropdown-menu').first().find('.show').removeClass("show");
            }

            var $subMenu = $(this).next(".dropdown-menu");
            $subMenu.toggleClass('show');

            $(this).parents('li.nav-item.dropdown.show').on('hidden.bs.dropdown', function(e) {
                $('.dropdown-submenu .show').removeClass("show");
            });
            
            $subMenu.children('.dropdown-submenu').each(lang_dir === 'rtl'?align_dropdown_submenu_rtl:align_dropdown_submenu_ltr);

            return false;
        });
    });
        
    // avoid dropdown menu to close on click inside
    $('.dropdown-menu.mega-menu-content').on('click', function(event){
        var events = $._data(document, 'events') || {};
        events = events.click || [];
        
        for(var i = 0; i < events.length; i++) {
            if(events[i].selector) {

                //Check if the clicked element matches the event selector
                if($(event.target).is(events[i].selector)) {
                    events[i].handler.call(event.target, event);
                }

                // Check if any of the clicked element parents matches the 
                // delegated event selector (Emulating propagation)
                $(event.target).parents(events[i].selector).each(function(){
                    events[i].handler.call(this, event);
                });
            }
        }
        event.stopPropagation(); //Always stop propagation
    });
    
    // Fix z-index issue with multiple absolute menu in #main-navigation
    $('.header .navbar-collapse .dropdown').on('show.bs.dropdown', function(){
        $(this).parents('.navbar-collapse').addClass('menu-active');
    });
    $('.header .navbar-collapse .dropdown').on('hidden.bs.dropdown', function(){
        $(this).parents('.navbar-collapse').removeClass('menu-active');
    });

    // Hoverable dropdown
    $('.navbar.hoverable .dropdown-hoverable').hover(function(){
        var dropdown = $(this).children('.dropdown-menu');
        if(!dropdown.hasClass('show') && dropdown.css('position') !== 'static'){ // Ignore collapsed navbar
            $(this).children('.dropdown-toggle').click();
        }
    }, function(){
        var dropdown = $(this).children('.dropdown-menu');
        if(dropdown.hasClass('show') && dropdown.css('position') !== 'static'){ // Ignore collapsed navbar
            $(this).children('.dropdown-toggle').click();
        }
    });

    // Open parent link on mouse down
    $('.navbar.hoverable .dropdown-hoverable > a[href].dropdown-toggle').on('mousedown', function(){
        var link = $(this).attr('href');
        if(link && $(this).next('.dropdown-menu').css('position') !== 'static'){
            location.href = link;
        }
    });
});

(function($) {
        // Search Autocomplete */
	$.fn.searchAutocomplete = function(option) {
		return this.each(function() {
			this.timer = null;
			this.items = new Array();

			$.extend(this, option);
                        
            this.dropdown = option.dropdown(this);

			$(this).attr('autocomplete', 'off');

			// Focus
			$(this).on('focus', function() {
				this.request();
			});

			// Blur
			$(this).on('blur', function() {
				setTimeout(function(object) {
					object.hide();
				}, 200, this);
			});

			// Keydown
			$(this).on('keydown', function(event) {
				switch(event.keyCode) {
					case 27: // escape
						this.hide();
						break;
					default:
						this.request();
						break;
				}
			});

			// Show
			this.show = function() {
				this.dropdown.show();
			};

			// Hide
			this.hide = function() {
				this.dropdown.hide();
			};

			// Request
			this.request = function() {
				clearTimeout(this.timer);

				this.timer = setTimeout(function(object) {
					object.source($(object).val(), $.proxy(object.response, object));
				}, 200, this);
			};

		});
	};
        
        
        // all listing tab module
        $.fn.mz_tabSlider = function(setting){
            // tab listing context
            var tab_listing_context = $(this);

            // Active tab swiper
            tab_listing_context.swiper_container = tab_listing_context.find('.tab-pane.active > .swiper-container');

            // prev button
            var prev_button = $(this).find('.mz-swiper-nav-prev, .swiper-button-prev');
            prev_button.on('click', function(){
                tab_listing_context.swiper_container.data('swiper').slidePrev();
            });
            // Next button
            var next_button = $(this).find('.mz-swiper-nav-next, .swiper-button-next');
            next_button.on('click', function(){
                tab_listing_context.swiper_container.data('swiper').slideNext();
            });
            
            // Initialise navigation status
            tab_listing_context.updateNav = function(){
                var swiper = tab_listing_context.swiper_container.data('swiper');
                if(swiper.isBeginning){
                    prev_button.addClass('swiper-button-disabled');
                } else {
                    prev_button.removeClass('swiper-button-disabled');
                }
                if(swiper.isEnd){
                    next_button.addClass('swiper-button-disabled');
                } else {
                    next_button.removeClass('swiper-button-disabled');
                }
                if(swiper.isBeginning && swiper.isEnd){
                    next_button.parent().hide();
                } else {
                    next_button.parent().show();
                }
            };

            // initialise swiper
            $.extend(setting, {
                loop: false,
                disableOnInteraction: true,
                pagination: false,
                observer: true,
                observeParents: true,
                autoHeight: setting.slidesPerColumn == 1,
                onSlideNextEnd: function(swiper){
                    $(swiper.slides[swiper.activeIndex]).find('img.lazy-load').each(function(){
                        $(this).data("plugin_lazy").update();
                    });
                    prev_button.removeClass('swiper-button-disabled');
                },
                onSlidePrevEnd: function(swiper){
                    next_button.removeClass('swiper-button-disabled');
                },
                onReachBeginning: function(_){
                    prev_button.addClass('swiper-button-disabled');
                },
                onReachEnd: function(_){
                    next_button.addClass('swiper-button-disabled');
                },
            });
            
            tab_listing_context.initSwiper = function(swiper_container){
                swiper_container.swiper(setting);
                tab_listing_context.updateNav();
            };

            // Init swiper for active tab
            tab_listing_context.initSwiper(tab_listing_context.swiper_container);

            // check whether swiper is initialise for new active tab
            // If not initialise then initialise it
            tab_listing_context.find('.nav-tabs').on('shown.bs.tab', function(){
                tab_listing_context.swiper_container = tab_listing_context.find('.tab-pane.active > .swiper-container');

                if(!tab_listing_context.swiper_container.data('swiper')){
                    tab_listing_context.initSwiper(tab_listing_context.swiper_container);
                } else {
                    tab_listing_context.updateNav();
                }
            });
        };
        
        // Text overflow with read more button
        $.fn.textOverflowReadmore = function(setting){
            $(this).each(function(){
                var _setting = $.extend({
                    lines: $(this).data('line'),
                    showtext: $(this).data('showtext'),
                    hidetext: $(this).data('hidetext'),
                    expandOnly: $(this).data('expandonly'),
                    overflow: $(this).data('overflow'),
                    onChanged: function(){}
                }, setting);
                
                var line_height = 0;
                var visible_height = 0;
                var text = $(this);
                var _ctx = this;
                this.mz_readmore = {};
                
                // Distroy
                this.mz_readmore.destroy = function(){
                    text.css('max-height', 'none');
                    text.find('.block-toggle').remove();
                    text.removeClass('expand');

                    $(_ctx).undelegate('a.text-toggle', 'click');

                    return _ctx;
                };

                // Initialise oveflow toggle control
                this.mz_readmore.init = function(){
                    _ctx.mz_readmore.destroy();
                    
                    line_height = Math.ceil(parseFloat($(_ctx).css('lineHeight')));
                    visible_height = line_height * parseInt(_setting.lines);
                    
                    if((text.height() > visible_height) || _setting.overflow){
                        text.css('max-height', visible_height);

                        text.append('<div class="block-toggle"><a href="#" class="text-toggle badge badge-secondary">' + _setting.showtext + '</a></div>');
                    } else {
                        text.addClass('expand');
                    }

                    return _ctx;
                };

                this.mz_readmore.init(); // Initialise

                var textToggle = text.find('a.text-toggle'); // Overflow control toggle button

                // Expand text
                this.mz_readmore.textExpand = function(){
                    text.css('max-height', 'none').addClass('expand');
                    textToggle.html(_setting.hidetext);

                    if(_setting.expandOnly){
                        _ctx.mz_readmore.destroy();
                    }

                    _setting.onChanged(_ctx); // Trigger event

                    return _ctx;
                };

                // Collapse text
                this.mz_readmore.textCollapse = function(){
                    text.css('max-height', visible_height).removeClass('expand');
                    textToggle.html(_setting.showtext);

                    _setting.onChanged(_ctx); // Trigger event

                    return _ctx;
                };

                $(this).delegate('a.text-toggle', 'click', function(e){
                    e.preventDefault();

                    if(text.hasClass('expand')){
                        _ctx.mz_readmore.textCollapse();
                    } else {
                        _ctx.mz_readmore.textExpand();
                    }
                });
            });
        };
        
})(window.jQuery);

// Cart add remove functions
var cart = {
	'add': function(product_id, quantity) {
		$.ajax({
			url: 'index.php?route=checkout/cart/add',
			type: 'post',
			data: 'product_id=' + product_id + '&quantity=' + (typeof(quantity) != 'undefined' ? quantity : 1),
			dataType: 'json',
			beforeSend: function() {
                    $('.cart-' + product_id).addClass('loading').closest('.product-action').addClass('loading');
			},
			complete: function() {
                    $('.cart-' + product_id).removeClass('loading');
                    if(!$('.cart-' + product_id).closest('.product-action').find('button.loading').length){
                        $('.cart-' + product_id).closest('.product-action').removeClass('loading');
                    }
			},
			success: function(json) {

				if (json['redirect']) {
					//location = json['redirect'];
                    mz_quick_view.show(product_id);
				}

				if (json['success']) {
                        var toast =  $(json['toast']);
                                        
					    $('#notification-box-top').append(toast);
                        toast.toast('show');
                                        
                        // Dispose toast
                        toast.on('hidden.bs.toast', function(){ toast.remove(); });

                        // Need to set timeout otherwise it wont update the total
                        setTimeout(function () {
                            $('.cart').each(function(){
                                $(this).find('.cart-items').text(json['total']);
                                
                                var regex = new RegExp('^' + $(this).data('total_format') + '$');
                                var total = regex.exec(json['total']);
                                
                                if(total != null){
                                    $(this).find('.cart-icon .cart-item-total').text(total[1]);
                                    $(this).find('.cart-info .cart-item-total').text('(' + total[1] + ')');
                                }
                            });
					    }, 100);

					//$('#cart > ul').load('index.php?route=common/cart/info ul li');
				}
			},
			error: function(xhr, ajaxOptions, thrownError) {
				alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			}
		});
	},
	'update': function(key, quantity) {
		$.ajax({
			url: 'index.php?route=checkout/cart/edit',
			type: 'post',
			data: 'key=' + key + '&quantity=' + (typeof(quantity) != 'undefined' ? quantity : 1),
			dataType: 'json',
			beforeSend: function() {},
			complete: function() {},
			success: function(json) {
                location.reload();
			},
			error: function(xhr, ajaxOptions, thrownError) {
				alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			}
		});
	},
	'remove': function(key) {
		$.ajax({
			url: 'index.php?route=checkout/cart/remove',
			type: 'post',
			data: 'key=' + key,
			dataType: 'json',
			beforeSend: function() {},
			complete: function() {},
			success: function(json) {
                location.reload();
			},
			error: function(xhr, ajaxOptions, thrownError) {
				alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			}
		});
	}
};

var voucher = {
	'add': function() {

	},
	'remove': function(key) {
		$.ajax({
			url: 'index.php?route=checkout/cart/remove',
			type: 'post',
			data: 'key=' + key,
			dataType: 'json',
			beforeSend: function() {},
			complete: function() {},
			success: function(json) {
                location.reload();
			},
			error: function(xhr, ajaxOptions, thrownError) {
				alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			}
		});
	}
};

var wishlist = {
	'add': function(product_id, by) {
        // Toggle wishlist
        if(by !== undefined && $(by).data('wishlist')){
            return this.remove(product_id);
        }

		$.ajax({
			url: 'index.php?route=account/wishlist/add',
			type: 'post',
			data: 'product_id=' + product_id,
			dataType: 'json',
            beforeSend: function() {
                $('.wishlist-' + product_id).addClass('loading').closest('.product-action').addClass('loading');
            },
			success: function(json) {
                $('.wishlist-' + product_id).removeClass('loading');
                if(!$('.wishlist-' + product_id).closest('.product-action').find('button.loading').length){
                    $('.wishlist-' + product_id).closest('.product-action').removeClass('loading');
                }
                                
				// if (json['redirect']) {
				// 	location = json['redirect'];
				// }
                                
				if (json['success']) {
                    $('#quick-view').modal('hide');

                    var toast =  $(json['toast']);
                    
                    if(!json['success'].match(/account\/login/g)){
                        // Change wish status
                        $('.wishlist-' + product_id).addClass('wished').data('wishlist', 1);
                    }
                                        
					$('#notification-box-top').append(toast);
                    toast.toast('show');
                                        
                    // Dispose toast
                    toast.on('hidden.bs.toast', function(){ toast.remove() });
				}

				$('#wishlist-total span').html(json['total']);
				$('#wishlist-total').attr('title', json['total']);
			},
			error: function(xhr, ajaxOptions, thrownError) {
				alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			}
		});
	},
	'remove': function(product_id) {
        $.ajax({
			url: 'index.php?route=extension/maza/account/wishlist/remove',
			type: 'post',
			data: 'product_id=' + product_id,
			dataType: 'json',
            beforeSend: function() {
                    $('.wishlist-' + product_id).addClass('loading').closest('.product-action').addClass('loading');
            },
			success: function(json) {
                $('.wishlist-' + product_id).removeClass('loading');
                if(!$('.wishlist-' + product_id).closest('.product-action').find('button.loading').length){
                    $('.wishlist-' + product_id).closest('.product-action').removeClass('loading');
                }
                                
				$('.alert-dismissible').remove();

				// Change wish status
                $('.wishlist-' + product_id).removeClass('wished').data('wishlist', 0);

				$('.wishlist-total').html(json['total']);
				$('.wishlist-total').attr('title', json['total']);
			},
			error: function(xhr, ajaxOptions, thrownError) {
				alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			}
		});
	}
};

var compare = {
	'add': function(product_id, by) {
        if(by !== undefined && $(by).data('compare')){
            return this.remove(product_id);
        }
            
		$.ajax({
			url: 'index.php?route=product/compare/add',
			type: 'post',
			data: 'product_id=' + product_id,
			dataType: 'json',
            beforeSend: function() {
                $('.compare-' + product_id).addClass('loading').closest('.product-action').addClass('loading');
            },
			success: function(json) {
                $('.compare-' + product_id).removeClass('loading');
                if(!$('.compare-' + product_id).closest('.product-action').find('button.loading').length){
                    $('.compare-' + product_id).closest('.product-action').removeClass('loading');
                }
                                
				if (json['success']) {
                    $('#quick-view').modal('hide');
                    
                    var toast =  $(json['toast']);
                                        
					$('#notification-box-top').append(toast);
                    toast.toast('show');
                                        
                    // Dispose toast
                    toast.on('hidden.bs.toast', function(){ toast.remove(); });
                    
                    // Change wish status
                    $('.compare-' + product_id).addClass('compared').data('compare', 1);
                    
                    // Update compare total
                    $('.compare-total').text(json['total']);
                    $('.compare-total').attr('title', json['total']);
				}
			},
			error: function(xhr, ajaxOptions, thrownError) {
				alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			}
		});
	},
	'remove': function(product_id) {
        $.ajax({
			url: 'index.php?route=extension/maza/product/compare/remove',
			type: 'post',
			data: 'product_id=' + product_id,
			dataType: 'json',
            beforeSend: function() {
                    $('.compare-' + product_id).addClass('loading').closest('.product-action').addClass('loading');
            },
			success: function(json) {
                $('.compare-' + product_id).removeClass('loading');
                if(!$('.compare-' + product_id).closest('.product-action').find('button.loading').length){
                    $('.compare-' + product_id).closest('.product-action').removeClass('loading');
                }
                     
				// Change wish status
                $('.compare-' + product_id).removeClass('compared').data('compare', 0);
			},
			error: function(xhr, ajaxOptions, thrownError) {
				alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			}
		});
	}
};

/* Agree to Terms */
$(document).on('click', '.agree', function(e) {
	e.preventDefault();

	$('#modal-agree').remove();

	var element = this;

	$.ajax({
		url: $(element).attr('href'),
		type: 'get',
		dataType: 'html',
		success: function(data) {
			var html  = '<div id="modal-agree" class="modal">';
			html += '  <div class="modal-dialog modal-dialog-centered">';
			html += '    <div class="modal-content">';
			html += '      <div class="modal-header">';
			html += '        <h4 class="modal-title">' + $(element).text() + '</h4>';
            html += '        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>';
			html += '      </div>';
			html += '      <div class="modal-body">' + data + '</div>';
			html += '    </div>';
			html += '  </div>';
			html += '</div>';

			$('body').append(html);

			$('#modal-agree').modal('show');
		}
	});
});

// Autocomplete */
(function($) {
	$.fn.autocomplete = function(option) {
		return this.each(function() {
			this.timer = null;
			this.items = new Array();

			$.extend(this, option);

			$(this).attr('autocomplete', 'off');

			// Focus
			$(this).on('focus', function() {
				this.request();
			});

			// Blur
			$(this).on('blur', function() {
				setTimeout(function(object) {
					object.hide();
				}, 200, this);
			});

			// Keydown
			$(this).on('keydown', function(event) {
				switch(event.keyCode) {
					case 27: // escape
						this.hide();
						break;
					default:
						this.request();
						break;
				}
			});

			// Click
			this.click = function(event) {
				event.preventDefault();

				var value = $(event.target).parent().attr('data-value');

				if (value && this.items[value]) {
					this.select(this.items[value]);
				}
			}

			// Show
			this.show = function() {
				var pos = $(this).position();

				$(this).siblings('ul.dropdown-menu').css({
					top: pos.top + $(this).outerHeight(),
					left: pos.left
				});

				$(this).siblings('ul.dropdown-menu').show();
			}

			// Hide
			this.hide = function() {
				$(this).siblings('ul.dropdown-menu').hide();
			}

			// Request
			this.request = function() {
				clearTimeout(this.timer);

				this.timer = setTimeout(function(object) {
					object.source($(object).val(), $.proxy(object.response, object));
				}, 200, this);
			}

			// Response
			this.response = function(json) {
				var html = '';

				if (json.length) {
					for (var i = 0; i < json.length; i++) {
						this.items[json[i]['value']] = json[i];
					}

					for (var i = 0; i < json.length; i++) {
						if (!json[i]['category']) {
							html += '<li class="dropdown-item" data-value="' + json[i]['value'] + '"><a href="#">' + json[i]['label'] + '</a></li>';
						}
					}

					// Get all the ones with a categories
					var category = new Array();

					for (var i = 0; i < json.length; i++) {
						if (json[i]['category']) {
							if (!category[json[i]['category']]) {
								category[json[i]['category']] = new Array();
								category[json[i]['category']]['name'] = json[i]['category'];
								category[json[i]['category']]['item'] = new Array();
							}

							category[json[i]['category']]['item'].push(json[i]);
						}
					}

					for (var i in category) {
						html += '<li class="dropdown-header">' + category[i]['name'] + '</li>';

						for (j = 0; j < category[i]['item'].length; j++) {
							html += '<li data-value="' + category[i]['item'][j]['value'] + '"><a href="#">&nbsp;&nbsp;&nbsp;' + category[i]['item'][j]['label'] + '</a></li>';
						}
					}
				}

				if (html) {
					this.show();
				} else {
					this.hide();
				}

				$(this).siblings('ul.dropdown-menu').html(html);
			}

			$(this).after('<ul class="dropdown-menu"></ul>');
			$(this).siblings('ul.dropdown-menu').delegate('a', 'click', $.proxy(this.click, this));

		});
	}
})(window.jQuery);

// Quick view
var mz_quick_view = {
    show: function(product_id){
            // Show quick view
            $('#quick-view').modal('show');

            // Load product data
            this.loadData(product_id);
    },
    loadData: function(product_id){
            $.ajax({
                    url: 'index.php?route=extension/maza/product/quick_view&product_id=' + product_id,
                    type: 'post',
                    data: 'product_id=' + product_id,
                    dataType: 'html',
                    beforeSend: function() {
                        $('#quick-view .modal-content').addClass('loading');
                    },
                    success: function(html) {
                        $('#quick-view .modal-content').removeClass('loading');
                        $('#quick-view .modal-body').html(html);
                        pageLoad($('#quick-view .modal-body'));
                    },
                    error: function(xhr, ajaxOptions, thrownError) {
                        alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
                    }
            });
    }
};
$('#quick-view').on('hide.bs.modal', function(){
    $(this).find('[data-countdown]').countdown('destroy');
});

/* newsletter */
var newsletter = {
    button: false,
	subscribe: function(submit_btn) {
        this.button = submit_btn;
        this.request('index.php?route=extension/maza/newsletter/subscribe');
	},
	unsubscribe: function(submit_btn) {
        this.button = submit_btn;
        this.request('index.php?route=extension/maza/newsletter/unsubscribe');
	},
    request: function(url){
        var _this = this;
                
        $.ajax({
			url: url,
			type: 'post',
			data: 'newsletter_email=' + $(this.button).parents('form').find('input[name="newsletter_email"]').val(),
			dataType: 'json',
            beforeSend: function () {
                $(_this.button).addClass('loading');
                $(_this.button).prepend('<span class="spinner-grow spinner-grow-sm" role="status" aria-hidden="true"></span>');
            },
			success: function(json) {
				$('.alert-dismissible').remove();

				if (json['success']) {
                    // Close modal if newsletter is added in bootstrap modal
                    var modal = $(_this.button).parents('.modal');
                    if(modal.length > 0) modal.modal('hide');
            
                    var toast = $('<div class="toast m-3" role="alert" aria-live="assertive" aria-atomic="true" data-delay="10000"><p class="toast-body"><i class="fas fa-thumbs-up"></i> ' + json['success'] + '</p>');
				}
                if (json['error']) {
                    var toast = $('<div class="toast m-3" role="alert" aria-live="assertive" aria-atomic="true" data-delay="10000"><p class="toast-body"><i class="fas fa-exclamation-circle"></i> ' + json['error'] + '</p>');
				}
                                     
                $('#notification-box-top').append(toast);
                toast.toast('show')

                // Dispose toast
                toast.on('hidden.bs.toast', function(){ toast.remove() });
			},
            complete: function () {
                $(_this.button).removeClass('loading');
                $(_this.button).children('.spinner-grow').remove();
            },
			error: function(xhr, ajaxOptions, thrownError) {
				alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			}
		});
    }
};

// Sticky
var mz_sticky = {
    targets: [],
    active: [],
    offsetY: 0,
    zIndex: 1030,
    animationQ: [],
    init: function(){
        this.targets = [];

        $('[data-toggle="sticky"]').each(function(){
            mz_sticky.targets.push({
                offsetTop: this.offsetTop,
                element: this,
                isActive: false,
            });
        });

        this.reset();

        window.addEventListener("resize", this.reset);
        window.addEventListener("scroll", this.scroll);
    },
    _build: function(_target){
        var stickyUp = Number($(_target.element).data('stickyUp'));
        if(stickyUp <= window.screen.width){
            mz_sticky.active.push(_target);
        }
    },
    reset: function(){
        mz_sticky.offsetY = 0;
        mz_sticky.active = [];
        mz_sticky.targets.forEach(mz_sticky._build);
    },
    animationComplete: function(){
        mz_sticky.animationQ.shift();
        if(mz_sticky.animationQ.length > 0){
            mz_sticky.makeSticky(mz_sticky.animationQ[0].el, mz_sticky.animationQ[0].offsetY);
        }
    },
    scroll: function(){
        mz_sticky.active.forEach(function(target, i){
            var el = target.element;
            var height = el.offsetHeight;
            var sticky = height + target.offsetTop + window.innerHeight * 0.8;

            if((window.pageYOffset > sticky) && !target.isActive){
                target.isActive = true;
                el.style.zIndex = mz_sticky.zIndex - i;
                
                // Animate sticky
                if(mz_sticky.animationQ.length == 0){
                    mz_sticky.makeSticky(el, mz_sticky.offsetY);
                }
                mz_sticky.animationQ.push({el: el, offsetY: mz_sticky.offsetY});

                mz_sticky.offsetY += height; // add Y offset for next sticky

            } else if((window.pageYOffset < sticky) && target.isActive){
                target.isActive = false;
                mz_sticky.offsetY -= height; // remove Y offset of current sticky
                el.classList.remove('mz-sticky', 'shadow');

                mz_sticky.onStateChanged();
            }
        });
    },
    makeSticky: function(el, offset){
        el.style.top = (offset - el.offsetHeight) + 'px';
        el.classList.add('mz-sticky', 'shadow');
        $(el).animate({top: offset}, 300, mz_sticky.animationComplete);

        mz_sticky.onStateChanged();
    },
    onStateChanged: function(){
        var activeSticky = mz_sticky.active.filter(function(a){
            return a.element.classList.contains('mz-sticky');
        });

        // Add padding top to body
        var stickyHeight = activeSticky.reduce(function(total, val){
            return total + val.element.offsetHeight;
        }, 0);
        document.body.style.paddingTop = stickyHeight + 'px';

        // Add shadow to last sticky
        mz_sticky.active.forEach(function(a){ a.element.classList.remove('shadow') });
        if(activeSticky.length > 0){
            activeSticky[activeSticky.length - 1].element.classList.add('shadow');
        }
    }
};

/* Send message from contact form */
function mz_sendMessage(form){
        var button_submit = $(form).find('[type="submit"]');
        var button_text_org = button_submit.html();
        
        $.ajax({
                url: 'index.php?route=extension/mz_widget/contact_form/submit',
                type: 'post',
                data: $(form).serialize(),
                dataType: 'json',
                beforeSend: function () {
                    button_submit.addClass('loading');
                    button_submit.html('<span class="spinner-grow spinner-grow-sm" role="status" aria-hidden="true"></span>' + button_submit.data('loading'));
                },
                success: function(json) {
                        $('.alert-dismissible').remove();
                        $(form).find('.error').remove();
                        
                        if (json['success']) {
                                // Close modal if form is added in bootstrap modal
                                var modal = $(form).parents('.modal');
                                if(modal.length > 0) modal.modal('hide');
                            
                                var alert = $('<div class="alert alert-success alert-notification w-50 alert-dismissible"><i class="fa fa-check-circle"></i> ' + json['success'] + ' <button type="button" class="close" data-dismiss="alert">&times;</button></div>');
                                
                                $('body').append(alert);
                                        
                                setTimeout(function(){
                                    alert.hide("slow"); 
                                }, 10000);
                        }
                        if(json['error']){
                            if (json['error']['name']) {
                                    $(form).find('[name="name"]').after('<div class="error text-danger">' + json['error']['name'] + '</div>');
                            }
                            if (json['error']['email']) {
                                    $(form).find('[name="email"]').after('<div class="error text-danger">' + json['error']['email'] + '</div>');
                            }
                            if (json['error']['subject']) {
                                    $(form).find('[name="subject"]').after('<div class="error text-danger">' + json['error']['subject'] + '</div>');
                            }
                            if (json['error']['message']) {
                                    $(form).find('[name="message"]').after('<div class="error text-danger">' + json['error']['message'] + '</div>');
                            }
                            if (json['error']['captcha']) {
                                    $(form).find('[name="captcha"]').after('<div class="error text-danger">' + json['error']['captcha'] + '</div>');
                            }
                        }
                },
                complete: function () {
                    button_submit.removeClass('loading');
                    button_submit.html(button_text_org);
                },
                error: function(xhr, ajaxOptions, thrownError) {
                    alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
                }
        });
}

// Google map callback
function widgetMap(){
    $('.google-map').each(function(){
        var latitude = $(this).data('latitude');
        var longitude = $(this).data('longitude');
        
        var map = new google.maps.Map(this, {
            center: {lat: latitude, lng: longitude},
            zoom: $(this).data('zoom'),
            zoomControl: $(this).data('zoomcontrol'),
            mapTypeControl: $(this).data('maptypecontrol'),
            scaleControl: $(this).data('scalecontrol'),
            streetViewControl: $(this).data('streetviewcontrol'),
            rotateControl: $(this).data('rotatecontrol'),
            fullscreenControl: $(this).data('fullscreencontrol')
        });
        
        if($(this).data('marker')){
            var map_marker = new google.maps.Marker({
                position: {lat: latitude, lng: longitude},
                map: map,
                animation: google.maps.Animation.DROP,
                icon: $(this).data('marker_icon')
            });
        }
    });
}

// Ajax form
$('form.ajax-form').on('submit', function(e){
    e.preventDefault();

    var form = $(this);
    var button_submit = $(this).find('[type="submit"]');
    var button_text_org = button_submit.html();
    
    $.ajax({
        type: "POST",
        url: form.attr('action'),
        enctype: form.attr('enctype'),
        data: new FormData(this),
        processData: false,
        contentType: false,
        dataType: 'json',
        beforeSend: function () {
            button_submit.addClass('loading');
            button_submit.html('<span class="spinner-grow spinner-grow-sm" role="status" aria-hidden="true"></span>' + button_submit.data('loading'));

            form.find('.invalid-feedback').remove();
            form.find('.is-invalid').removeClass('is-invalid');
        },
        success: function(json) {
            if (json['success']) {
                // Close modal if form is added in bootstrap modal
                var modal = $(form).parents('.modal');
                if(modal.length > 0) modal.modal('hide');
        
                var toast = $('<div class="toast m-3" role="alert" aria-live="assertive" aria-atomic="true" data-delay="10000"><div class="toast-header"><span class="mr-auto">' + json['title'] + '</span><button type="button" class="ml-2 mb-1 close" data-dismiss="toast" aria-label="Close"><span aria-hidden="true">&times;</span></button></div><p class="toast-body"><i class="fas fa-thumbs-up"></i> ' + json['success'] + '</p>');

                $('#notification-box-top').append(toast);
                toast.toast('show')

                // Dispose toast
                toast.on('hidden.bs.toast', function(){ toast.remove() });
            }

            if(json['error']){
                for(var name in json['error']){
                    form.find('[name="' + name + '"]').addClass('is-invalid').after('<div class="invalid-feedback">' + json['error'][name] + '</div>');
                }
            }
        },
        complete: function () {
            button_submit.removeClass('loading');
            button_submit.html(button_text_org);
        },
        error: function(xhr, ajaxOptions, thrownError) {
            alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
        }
    });
});