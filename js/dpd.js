Packeta = window.Packeta || {};
Packeta.Viewport = {
    element: null,
    originalValue: null,
    set: function() {
        if(!Packeta.Viewport.element) {
            Packeta.Viewport.element = document.querySelector("meta[name=viewport]");
            if(Packeta.Viewport.element) {
                Packeta.Viewport.originalValue = Packeta.Viewport.element.getAttribute("content");
            }
            else {
                Packeta.Viewport.originalValue = 'user-scalable=yes';
                Packeta.Viewport.element = document.createElement('meta');
                Packeta.Viewport.element.setAttribute("name", "viewport");
                (document.head || document.getElementsByTagName('head')[0]).appendChild(Packeta.Viewport.element);
            }
        }
        Packeta.Viewport.element.setAttribute('content', 'width=device-width, initial-scale=1.0, minimum-scale=1.0, user-scalable=yes');
    },
    restore: function() {
        if(Packeta.Viewport.originalValue !== null) {
            Packeta.Viewport.element.setAttribute('content', Packeta.Viewport.originalValue);
        }
    }
};
Packeta.Widget = {
    baseUrl: 'https://api.dpd.cz/widget/latest/index.html',
    close: function() {},
    pick: function(apiKey, callback, opts, inElement) {
        Packeta.Widget.close();

        if(opts === undefined) {
            opts = {};
        }
        if(!('version' in opts)) {
            opts.version = 3;
        }

        opts.apiKey = apiKey;
        opts.usePreProdWidgetVersion = true;

        var url = Packeta.Widget.baseUrl;
        if (opts.apiKey==="no"){
            url = 'https://api.dpd.cz/widget/latest/index.html?disableLockers=true'
        }

        var inline = (typeof(inElement) != "undefined" && inElement !== null);
        var wrapper;
        if(inline) {
            wrapper = inElement;
        }
        else {
            Packeta.Viewport.set();
            wrapper = document.createElement("div");
            wrapper.setAttribute("style", "z-index: 999999; position: fixed; -webkit-backface-visibility: hidden; left: 0; top: 0; width: 100%; height: 100%; background: " + (opts.overlayColor || "rgba(0, 0, 0, 0.3)") + "; ");
            wrapper.addEventListener("click", function() {
                Packeta.Widget.close();
            });

            // fix for some older browsers which fail to do 100% width of position:absolute inside position:fixed element
            setTimeout(
                function() {
                    var rect = iframe.getBoundingClientRect();
                    var width = ('width' in rect ? rect.width : rect.right - rect.left);
                    if(Math.round(width) < window.innerWidth - 10) { // 10px = side padding sum, just as a safety measure
                        iframe.style.width = window.innerWidth + "px";
                        iframe.style.height = window.innerHeight + "px";
                    }
                },
                0
            );
        }
        var iframe = document.createElement("iframe");
        if(inline) {
            iframe.setAttribute("style", "border: hidden; width: 100%; height: 100%; ");
        }
        else {
            iframe.setAttribute("style", "border: hidden; position: absolute; left: 0; top: 0; width: 100%; height: 100%; padding: 10px 5px; box-sizing: border-box;background:#fff ");
        }
        iframe.setAttribute('id', "packeta-widget");
        iframe.setAttribute('sandbox', "allow-scripts allow-same-origin");
        iframe.setAttribute('allow', "geolocation");
        iframe.setAttribute('src', url);

        wrapper.appendChild(iframe);
        if(!inline) {
            document.body.appendChild(wrapper);
        }

        if(wrapper.getAttribute("tabindex") === null) {
            wrapper.setAttribute("tabindex", "-1"); // make it focusable
        }
        wrapper.setAttribute("class", "visible");
    window.addEventListener("message", (event) => {
    if(event.data.dpdWidget && event.data.dpdWidget.message === "widgetClose") {
      wrapper.setAttribute("style", "visibility: hidden");
        document.getElementById("packeta-point-id").value = "",
       document.getElementById("packeta-point-info").textContent = "Zatím nevybráno";
    } 
    }, false);
    window.addEventListener("message", (event) => {
    if(event.data.dpdWidget) {
      document.getElementById("packeta-point-id").value = event.data.dpdWidget.pickupPointResult,
      document.getElementById("packeta-point-info").textContent = event.data.dpdWidget.contactInfo.name;
      document.getElementById("ship-to-different-address-checkbox").checked = true;
      document.getElementById("shipping_first_name").value = document.getElementById("billing_first_name").value;
      document.getElementById("shipping_last_name").value = document.getElementById("billing_last_name").value;
      document.getElementById("shipping_company").value = event.data.dpdWidget.id;
      document.getElementById("shipping_postcode").value = event.data.dpdWidget.location.address.zip;
      document.getElementById("shipping_address_1").value = event.data.dpdWidget.contactInfo.name;
      document.getElementById("shipping_address_2").value = event.data.dpdWidget.location.address.street;
      document.getElementById("shipping_city").value = event.data.dpdWidget.location.address.city;
      wrapper.setAttribute("style", "visibility: hidden");
    } 
    }, false);
    wrapper.addEventListener("keyup", function(e) {
            if(e.keyCode == 27) {
                wrapper.setAttribute("style", "visibility: hidden");
                      document.getElementById("packeta-point-info").textContent = "Zatím nevybráno";
                        
            }
    });
    wrapper.focus();
    }
};
