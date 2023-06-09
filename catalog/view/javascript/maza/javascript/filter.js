function modifyURLQuery(url, param){
        var value = {};

        var query = String(url).split('?');

        if (query[1]) {
            var part = query[1].split('&');

            for (i = 0; i < part.length; i++) {
                var data = part[i].split('=');

                if (data[0] && data[1]) {
                    value[data[0]] = data[1];
                }
            }
        }
        
        $.extend(value, param);
        
        // Generate query parameter string
        var query_param = '';
        for (i in value){
            if(value[i]){
                query_param += '&' + i + '=' + value[i];
            }
        }
        
        // Return url with modified parameter
        if(query_param){
            return query[0] + '?' + query_param.substring(1);
        } else {
            return query[0];
        }
}

$.fn.mz_filter = function(setting){
    // Default setting
    var default_setting = {
        delay: 2, // Second
        requestURL: null,
        searchEl: null,
        ajax: true,
        search_in_description: true,
        no_result: '', // HTML for empty product result
        countProduct: null,
        sortBy: 'product',
        onParamChange: function(){},
        onInputChange: function(){},
        onReset: function(){},
        onResult: function(){}, // Result function for json
        onBeforeSend: function(){},
        onComplete: function(){},
        status: {
            price: 1,
            sub_category: 1,
            manufacturer: 1,
            search: 1,
            rating: 1,
            discount: 1,
            filter: 1,
            custom: 1,
            availability: 1
        }
    };
    
    setting = $.extend({}, default_setting, setting);
    
    var mz_filter = this;
    
    // Filter inputs
    this.inputs = $(this).find('input:not([type="search"])');
    
    // Start filter
    this.start = function(param){
        if(!setting.ajax){
            // Reload page with filter parameter
            window.location.href = modifyURLQuery(window.location.href, $.extend({}, param, {page: null}));
            return;
        }
        
        // Send request to filter products
        $.ajax({
            url: setting.requestURL,
            data: param,
            dataType: 'json',
            beforeSend: function(){
                $('body').addClass('mz-filter-loading');
                mz_filter.inputs.prop('disabled', 1);
                
                setting.onBeforeSend(); // Trigger event
            },
            success: function(json){
                mz_filter.inputs.prop('disabled', 0);
                
                setting.onResult(json); // Trigger event
                
                mz_filter.updateFilter(json['filter']); // Update filter
            },
            complete: function(){
                $('body').removeClass('mz-filter-loading');
                $(setting.loaderEl).hide();
                
                setting.onComplete(); // Trigger event
            }
        });
    };
    
    // Update filters values
    this.updateFilter = function(data){
            var inputs = mz_filter.find(':checkbox,:radio');
            
            inputs.filter(':not(:checked)').prop("disabled", 1); // Disable all unchecked input
            mz_filter.find('.mz-product-total').text('0').addClass('label-danger'); // Reset product count to zero
            
            // Manufacturer
            if(data['manufacturer']){
                var input_manufacturer = inputs.filter('[name="mz_fm"]');
                
                for(var i in data['manufacturer']){
                    input_manufacturer.filter('[value="' + data['manufacturer'][i]['manufacturer_id'] + '"]')
                            .prop('disabled', 0).parents('.mz-filter-value').find('.mz-product-total').text(data['manufacturer'][i]['total']).removeClass('label-danger');
                }
                
                this.sort(input_manufacturer);
            }
            
            // Sub category
            if(data['sub_category']){
                var input_sub_category = inputs.filter('[name="mz_fsc"]');
                
                for(var i in data['sub_category']){
                    input_sub_category.filter('[value="' + data['sub_category'][i]['category_id'] + '"]')
                            .prop('disabled', 0).parents('.mz-filter-value').find('.mz-product-total').text(data['sub_category'][i]['total']).removeClass('label-danger');
                }
                
                this.sort(input_sub_category);
            }
            
            // Availability
            if(data['availability']){
                var input_availability = inputs.filter('[name="mz_fs"]');
                
                if(data['availability']['in_stock'] > 0){
                    input_availability.filter('[value="1"]')
                            .prop('disabled', 0).parents('.mz-filter-value').find('.mz-product-total').text(data['availability']['in_stock']).removeClass('label-danger');
                }
                
                if(data['availability']['out_of_stock'] > 0){
                    input_availability.filter('[value="0"]')
                            .prop('disabled', 0).parents('.mz-filter-value').find('.mz-product-total').text(data['availability']['out_of_stock']).removeClass('label-danger');
                }
                    
            }
            
            // Rating
            if(data['rating']){
                var input_rating = inputs.filter('[name="mz_fr"]');
                
                for(var i in data['rating']){
                    input_rating.filter('[value="' + data['rating'][i]['rating'] + '"]')
                            .prop('disabled', 0).parents('.mz-filter-value').find('.mz-product-total').text(data['rating'][i]['total']).removeClass('label-danger');
                }
            }
            
            // discount
            if(data['discount']){
                var input_discount = inputs.filter('[name="mz_fd"]');
                
                for(var i in data['discount']){
                    input_discount.filter('[value="' + data['discount'][i]['value'] + '"]')
                            .prop('disabled', 0).parents('.mz-filter-value').find('.mz-product-total').text(data['discount'][i]['total']).removeClass('label-danger');
                }
            }
            
            // Filter
            if(data['filter']){
                var input_filter = inputs.filter('[name="mz_ff"]');
                
                for(var i in data['filter']){
                    input_filter.filter('[value="' + data['filter'][i]['filter_id'] + '"]')
                            .prop('disabled', 0).parents('.mz-filter-value').find('.mz-product-total').text(data['filter'][i]['total']).removeClass('label-danger');
                }
            }
            
            // Custom
            if(data['custom']){
                var input_custom = inputs.filter('[name^="mz_fc"]');
                
                for(var i in data['custom']){
                    input_custom.filter('[value="' + data['custom'][i]['value_id'] + '"]')
                            .prop('disabled', 0).parents('.mz-filter-value').find('.mz-product-total').text(data['custom'][i]['total']).removeClass('label-danger');
                }
                
                mz_filter.find('[data-custom-filter]').each(function(){
                    mz_filter.sort(inputs.filter('[name="mz_fc' + $(this).data('custom-filter') + '"]'));
                });
            }
    };
    
    // Sort filter value
    this.sort = function(values){
        if(setting.countProduct && setting.sortBy === 'product'){
            var $wrapper = values.parents('.mz-filter-group-content');
            var sortByproduct = function(a, b){
                return Number($(a).parents('.mz-filter-value').find('.mz-product-total').text()) < Number($(b).parents('.mz-filter-value').find('.mz-product-total').text());
            };
            var sortByInput = function(a, b){
                return $(a).prop('disabled') > $(b).prop('disabled');
            };

            if(setting.countProduct){
                var callBack = sortByproduct;
            } else {
                var callBack = sortByInput;
            }

            values.sort(callBack).each(function(){
                $wrapper.append($(this).parents('.mz-filter-value'));
            });

            $wrapper.append($wrapper.find('[data-toggle="mz-seemore"]'));
        }
    };
    
    this.getParam = function(){
        var param = {};
        
        // price
        if(setting.status.price){ 
            var price = '';
            var min_price_input = mz_filter.inputs.filter('[name="mz_fp[min]"]');
            var max_price_input = mz_filter.inputs.filter('[name="mz_fp[max]"]');
            
            if(min_price_input.attr('min') !== min_price_input.val()){ // When minimum price change
                price +=  min_price_input.val();
            }
            
            if(max_price_input.attr('max') !== max_price_input.val()){ // When maximum price change
                price += 'p' + max_price_input.val();
            }
            
            if(price){
                param.mz_fp = price;
            }
        }
        
        // Search
        if(setting.status.search){
            var keyword = mz_filter.inputs.filter('[name="mz_fq"]').val();
            
            if(keyword){
                param.mz_fq = keyword;
                
                if(setting.search_in_description){
                    param.description = 1;
                }
            }
        }
        
        // Rating
        if(setting.status.rating){
            var min_rating = mz_filter.inputs.filter('[name="mz_fr"]:checked').val();
            
            if(min_rating){
                param.mz_fr = min_rating;
            }
        }
        
        // Discount
        if(setting.status.discount){
            var min_discount = mz_filter.inputs.filter('[name="mz_fd"]:checked').val();
            
            if(min_discount){
                param.mz_fd = min_discount;
            }
        }
        
        // Availability
        if(setting.status.availability){
            var in_stock = mz_filter.inputs.filter('[name="mz_fs"]:checked').val();
            
            if(in_stock !== undefined){
                param.mz_fs = in_stock;
            }
        }
        
        // Manufacturer
        if(setting.status.manufacturer){
            var manufacturer_ids = mz_filter.inputs.filter('[name="mz_fm"]:checked').map(function(){
                return $(this).val();
            }).get().join('.');
            
            if(manufacturer_ids){
                param.mz_fm = manufacturer_ids;
            }
        }
        
        // Sub category
        if(setting.status.sub_category){
            var sub_category_ids = mz_filter.inputs.filter('[name="mz_fsc"]:checked').map(function(){
                return $(this).val();
            }).get().join('.');
            
            if(sub_category_ids){
                param.mz_fsc = sub_category_ids;
            }
        }
        
        // Filter
        if(setting.status.filter){
            var filter_ids = mz_filter.inputs.filter('[name="mz_ff"]:checked').map(function(){
                return $(this).val();
            }).get().join('.');
            
            if(filter_ids){
                param.mz_ff = filter_ids;
            }
        }
        
        // Custom
        if(setting.status.custom){
            var mz_fc = '';
            
            mz_filter.find('[data-custom-filter]').each(function(){
                var value_ids =  mz_filter.inputs.filter('[name="mz_fc' + $(this).data('custom-filter') + '"]:checked').map(function(){
                    return $(this).val();
                }).get().join('.');

                if(value_ids){
                    mz_fc += 'c' + value_ids;
                }
            });
            
            if(mz_fc){
                param.mz_fc = mz_fc.substring(1);
            }
        }
        
        return $.extend({
            mz_fp: null,
            mz_fq: null,
            mz_fr: null,
            mz_fd: null,
            mz_fs: null,
            mz_fm: null,
            mz_fsc: null,
            mz_ff: null,
            mz_fc: null
        }, param);
    };
    
    // Run task after user change filter
    mz_filter.on('change', function(){
        // Clear past timeout
        if(mz_filter.timeoutId !== undefined){
            clearTimeout(mz_filter.timeoutId);
        }
        
        // Get filter param
        var param = mz_filter.getParam();
        
        // Delay before to start filter
        mz_filter.timeoutId = setTimeout(function(){
            mz_filter.start(param);
        }, setting.delay * 1000);
        
        // Update page URL
        history.pushState(null, null, modifyURLQuery(window.location.href, $.extend({}, param, {page: null})));
        
        // Trigger param change event
        setting.onParamChange(param);
        
        
    });
    
    // Input change event
    this.inputs.on('change', function(e){
        setting.onInputChange(e);
    });
    
    // Search
    $(setting.searchEl).on('keyup', function(){
        var value = $(this).val().trim().toLowerCase();
        
        $(this).parents('.mz-filter-group').find('.mz-filter-value').filter(function(){
            var text = $(this).text().trim().toLowerCase();
            if(!text){ // Check in image
                text = $(this).find('img').attr('alt').trim().toLowerCase();
            }
            
            $(this).toggle(text.indexOf(value) > -1);
        });
    });
    
    // See more
    mz_filter.find("[data-toggle='mz-seemore']").click(function(e){
        e.preventDefault();
        var wrapper = $(this).parent();
        if(wrapper.hasClass('show')){
            wrapper.removeClass('show');
            $(this).text($(this).data('show'));
        } else {
            wrapper.addClass('show');
            $(this).text($(this).data('hide'));
        }
    });
    
    // # Reset #
    // Radio and checkbox
    mz_filter.find('[data-mz-reset="check"]').on('click', function(e){
        e.stopImmediatePropagation();
        $(this).parents('.mz-filter-group').find('input').prop('checked', false);
        
        setting.onReset(this);
        mz_filter.change();
    });

    // Price
    mz_filter.find('[data-mz-reset="price"]').on('click', function(e){
        e.stopImmediatePropagation();
        
        mz_filter.find('[name="mz_fp[min]"]').val(mz_filter.find('[name="mz_fp[min]"]').attr('min'));
        mz_filter.find('[name="mz_fp[max]"]').val(mz_filter.find('[name="mz_fp[max]"]').attr('max'));
        
        setting.onReset(this);
        mz_filter.change();
    });

    // Text
    mz_filter.find('[data-mz-reset="text"]').on('click', function(e){
        e.stopImmediatePropagation();
        $(this).parents('.mz-filter-group').find('input').val('');
        
        setting.onReset(this);
        mz_filter.change();
    });

    // Reset all
    $(this).find('[data-mz-reset="all"]').on('click', function(e){
        e.stopImmediatePropagation();

        // Radio and checkbox
        mz_filter.find('.mz-filter-group :checkbox, .mz-filter-group :radio').prop('checked', false);
        
        // Text
        mz_filter.find('.mz-filter-group input[type="text"]').val('');

        // Price
        mz_filter.find('[name="mz_fp[min]"]').val(mz_filter.find('[name="mz_fp[min]"]').attr('min'));
        mz_filter.find('[name="mz_fp[max]"]').val(mz_filter.find('[name="mz_fp[max]"]').attr('max'));
        
        // Trigger events
        setting.onReset(this);
        mz_filter.change();
    });
};