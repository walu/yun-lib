var Yun_Bigpipe = function() {
    var _self = this;
    
    var js_list = {};

    this.css_handler = function(css_url_array) {
        for (i=0; i< css_url_array.length; i++) {
            jQuery("head").append("<link href='"+css_url_array[i]+"' rel='stylesheet' type='text/css' />");
        }
    }

    this.js_handler = function(js_url_array) {
    
    }

    this.html_handler = function(id, html) {
        jQuery('#'+id).html(html);
    }
    
    this.onPageletArrive = function(data) {
        if (!('id' in data) || !('is_last' in data)) {
            return;
        }

        var id = data.id;
        if ('html' in data) {
            _self.html_handler(id, data.html);
        }

        if ('css_url' in data) {
            _self.css_handler(data.css_url);
        }

        if ('js_url' in data) {
            _self.js_handler(data.js_url);
        }

        if (data.is_last) {
            //加载javascript
        }
    }

    return this;
}();
