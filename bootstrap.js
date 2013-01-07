// Put everything in the mrbavii.sitehelper namespace
(function() {
    window['mrbavii'] = window['mrbavii'] || {};
    var mrbavii = window['mrbavii'];

    mrbavii['sitehelper'] = mrbavii['sitehelper'] || {};
    var sitehelper = mrbavii['sitehelper'];

    // Stylesheet functions
    sitehelper.reloadStyles = function()
    {
        var queryString = '?reload=' + new Date().getTime();
        $('link[rel="stylesheet"]').each(function(){
            this.href = this.href.replace(/\?.*|$/, queryString);
        });
    };

    // Cookie functions
    sitehelper.createCookie = function(name, value, days)
    {
        if(days)
        {
            var date = new Date();
            date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
            var expires = "; expires="+date.toGMTString();
        }
        else
            var expires = "";
            
        document.cookie = name+"="+value+expires+"; path=/";
    };

    sitehelper.readCookie = function(name)
    {
        var nameEQ = name + "=";
        var ca = document.cookie.split(';');
        for(var i = 0; i < ca.length; i++)
        {
            var c = ca[i];
            while(c.charAt(0) == ' ') 
                c = c.substring(1, c.length);
            if(c.indexOf(nameEQ) == 0)
                return c.substring(nameEQ.length, c.length);
        }
        return null;
    };

    // XML http request
    sitehelper.XMLHttpRequest = function()
    {
        if(window.XMLHttpRequest)
            return new XMLHttpRequest();

        return null;
    };

    // DOM Parser
    sitehelper.parseXML = function(xml)
    {
        if(window.DOMParser)
        {
            var parser = new DOMParser();
            return parser.parseFromString(xml);
        }
        else
        {
            var parser = new ActiveXObject("Microsoft.XMLDOM");
            parser.async=false;
            return parser.loadXML(xml);
        }
    };

    // Simple HTML5 shiv
    sitehelper.html5 = function()
    {
        var isIE = /*@cc_on ! @*/false;
        if(isIE)
        {
            var elements = ["abbr", "article", "aside", "audio", "canvas",
                            "datalist", "details", "figure", "figcaption",
                            "footer", "header", "hgroup", "mark", "menu",
                            "meter", "nav", "output", "progress", "section",
                            "summary", "time", "video"];

            for(var i in elements)
            {
                document.createElement(elements[i]);
            }
        }
    }

})();

