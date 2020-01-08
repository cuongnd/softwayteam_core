//huong dan su dung
/*
 $('.render_block_config').render_block_config();
 render_block_config=$('.render_block_config').data('render_block_config');
 console.log(render_block_config);
 */
// jQuery Plugin for SprFlat admin render_block_config
// Control options and basic function of render_block_config
// version 1.0, 28.02.2013
// by SuggeElson www.suggeelson.com
(function ($) {
    // here we go!
    $.render_block_config = function (element, options) {
        // plugin's default options
        let defaults = {
            //main color scheme for render_block_config
            //be sure to be same as colors on main.css or custom-variables.less,
            id:0,
            block_setting:{}
        };
        // current instance of the object
        let plugin = this;
        // this will hold the merged default, and user-provided options
        plugin.settings = {};
        let $element = $(element); // reference to the jQuery version of DOM element
        // reference to the actual DOM element
        // the "constructor" method that gets called when the object is created
        plugin.ajax_save_block = function (data) {
            $.ajax({
                type: "POST",
                dataType: "json",
                url: root_url + api_task,
                data: data,
                beforeSend: function () {
                    // setting a timeout
                    //
                },
                error: function (xhr) { // if error occured

                },
                complete: function () {

                },
                success: function (response) {
                    response = JSON.parse(response);
                    loadLockScripts(response);
                    if (response.result === "success") {
                        $element.find('.block-content').html(response.data);



                    }
                }
            });

        };
        plugin.init = function () {
            plugin.settings = $.extend({}, defaults, options);
            let block_setting=plugin.settings.block_setting;
            let id=plugin.settings.id;
            console.log("block_setting",block_setting);

            $element.closest('.vc_ui-panel-window-inner').addClass('vc_ui-panel-woo-booking');
            let $button_save=$('.vc_ui-panel-woo-booking').find('span[data-vc-ui-element="button-save"]');
            $.ajax({
                type: "POST",
                dataType: "json",
                url: root_url + api_task,
                data: {
                    type: block_setting.param_name,
                    id:id,
                    task: "block.ajax_get_config_blog"
                },
                beforeSend: function () {
                    // setting a timeout
                    //
                },
                error: function (xhr) { // if error occured

                },
                complete: function () {

                },
                success: function (response) {
                    response = JSON.parse(response);
                    loadLockScripts(response);
                    if (response.result === "success") {
                        $element.find('.block-content').html(response.data);
                    }
                }
            });
            //TODO sẽ update khi tìm hiểu được cách gọi function khi người dùng nhấn vào nút lưu

            let $mark=$(`<span></span>`);
            $mark.addClass('button-save-woo-booking-mark');
            $button_save.append($mark);
            $mark.click(function (e) {
                if($element.find('form').length===0){
                    $.alert({
                        title: 'Error ',
                        content: 'Cannot found Form !, you need add tag form in layout config',
                    });
                    return;
                }
                $element.find('form').serializeObject().done(function(data){
                    plugin.ajax_save_block(data);
                    console.log("data",data);
                    delete data.task;
                    $element.find(`input[name="${block_setting.param_name}"]`).val(data.id);

                });

            });


        };
        plugin.example_function = function () {
        };
        plugin.init();
    };
    // add the plugin to the jQuery.fn object
    $.fn.render_block_config = function (options) {
        // iterate through the DOM elements we are attaching the plugin to
        return this.each(function () {
            // if plugin has not already been attached to the element
            if (undefined === $(this).data('render_block_config')) {
                let plugin = new $.render_block_config(this, options);
                $(this).data('render_block_config', plugin);
            }
        });
    }
})(jQuery);

jQuery.getBlockMultiScripts = function (arr, path) {
    var _arr = $.map(arr, function (scr) {
        return $.getScript(scr);
    });
    _arr.push($.Deferred(function (deferred) {
        $(deferred.resolve);
    }));

    return $.when.apply($, _arr);
};
jQuery.getBlockMultiLess = function (arr, path) {
    var _arr_less = $.map(arr, function (scr) {
        let  current_less = document.createElement('link');
        current_less.setAttribute('rel','stylesheet/less');
        current_less.setAttribute('type','text/css');
        current_less.setAttribute('href',scr);
        document.head.appendChild(current_less);

        //jQuery('head').append(`<link rel="stylesheet/less" href="${scr}" type="text/css" />`);
    });
    _arr_less.push($.Deferred(function (deferred) {
        $(deferred.resolve);
    }));
    return $.when.apply($, _arr_less);
};
list_js_installed=[];
list_less_install=[];
function loadLockScripts(response) {
    $ = jQuery;
    $('link[rel="stylesheet/less"]').remove();
    var styleSheets = response.styleSheets;
    $.each(styleSheets, function (src, value) {
        $('head').append(`<link rel="stylesheet" href="${root_url_plugin}${src}" type="text/css" />`);
    });
    less_arr = [];
    var lessStyleSheets = response.lessStyleSheets;
    $.each(lessStyleSheets, function (src, value){
        if( src.indexOf('http') >= 0){
            less_arr.push(src);
        }else{
            less_arr.push(root_url_plugin+src);
        }
    });

    $.getBlockMultiLess(less_arr, root_url_plugin).done(function () {
        script_arr = [];
        var scripts = response.scripts;
        $.each(scripts, function (src, value) {
            if( src.indexOf('http') >= 0){
                script_arr.push(src);
            }else{
                script_arr.push(root_url_plugin+src);
            }
        });

        $.getBlockMultiScripts(script_arr, root_url_plugin).done(function () {
            var script = response.script;
            $('head').append(`<script type="text/javascript">${script['text/javascript']}</script>`);
        });
    });
}


