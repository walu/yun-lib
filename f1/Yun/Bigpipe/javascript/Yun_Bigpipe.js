function Yun_Bigpipe() {
    var _self = this;
    
    var js_list = {};

    _self.css_handler = function(css_url_array) {
        for (i=0; i< css_url_array.length; i++) {
            jQuery("head").append("<link href='"+css_url_array[i]+"' rel='stylesheet' type='text/css' />");
        }
    }

    _self.js_handler = function(js_url_array) {
    
    }

    _self.html_handler = function(id, html) {
        JQuery('#'+id).html(html);
    }
    
    _self.onPageletArrival = function(data) {
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
}
