Packetaaaa = window.Packetaaaa || {};
Packetaaaa.Viewport = {
    element: null,
    originalValue: null,
    set: function() {
        if(!Packetaaaa.Viewport.element) {
            Packetaaaa.Viewport.element = document.querySelector("meta[name=viewport]");
            if(Packetaaaa.Viewport.element) {
                Packetaaaa.Viewport.originalValue = Packetaaaa.Viewport.element.getAttribute("content");
            }
            else {
                Packetaaaa.Viewport.originalValue = 'user-scalable=yes';
                Packetaaaa.Viewport.element = document.createElement('meta');
                Packetaaaa.Viewport.element.setAttribute("name", "viewport");
                (document.head || document.getElementsByTagName('head')[0]).appendChild(Packeta.Viewport.element);
            }
        }
        Packetaaaa.Viewport.element.setAttribute('content', 'width=device-width, initial-scale=1.0, minimum-scale=1.0, user-scalable=yes');
    },
    restore: function() {
        if(Packetaaaa.Viewport.originalValue !== null) {
            Packetaaaa.Viewport.element.setAttribute('content', Packetaaaa.Viewport.originalValue);
        }
    }
};
var wrapper;
function closeAA(){
    wrapper.setAttribute("style","visibility:hidden;display:none")
}
Packetaaaa.Widget = {
    baseUrl: 'https://widget.intime.cz/v5/widget-content',
    close: function() {},
    pick: function(apiKey,apiKey2, callback, opts, inElement) {
        Packetaaaa.Widget.close();

        if(opts === undefined) {
            opts = {};
        }
        if(!('version' in opts)) {
            opts.version = 3;
        }
        opts.apiKey = apiKey;
        opts.usePreProdWidgetVersion = true;
        var url = Packetaaaa.Widget.baseUrl;
        if(apiKey==="yes"&&apiKey2==="no"){
             url = 'https://widget.intime.cz/v5/widget-content?fixedType=Ulozenka'
        }
        else if(apiKey==="no"&&apiKey2==="yes"){
            url = 'https://widget.intime.cz/v5/widget-content?fixedType=Box'
        }
        else{
            url = 'https://widget.intime.cz/v5/widget-content'
        }

        var inline = (typeof(inElement) != "undefined" && inElement !== null);
        if(inline) {
            wrapper = inElement;
        }
        else {
            Packetaaaa.Viewport.set();
            menu = document.createElement("div");
            menu.setAttribute("class", "menu");
            menu.setAttribute("style", "background: rgb(41, 40, 41);color: #fff;font-weight: 700;padding: 0 0 0 1rem;height: 40px;font-size: 22px;width: 100%;");
            nazev = document.createElement("span");
            nazev.classList.add("ui-dialog-title");
            nazev.textContent = "Vyberte výdejní místo WE|DO";
            button= document.createElement("button");
            button.setAttribute("type", "button");
            button.setAttribute("title", "Zavřít");
            button.setAttribute("onclick", "closeAA()");
            button.setAttribute("style", "float:right;background:0;display:flex;padding: 0;");
            span= document.createElement("span");
            span.textContent = "X";
            span.setAttribute("style","background:#4cb45a;color:rgb(41, 40, 41);width:40px;height:40px;border-radius:6px");
            span.setAttribute("onMouseOver","this.style.backgroundColor='#22d760',this.style.color='#000'");
            span.setAttribute("onMouseOut","this.style.backgroundColor='#4cb45a'")

            wrapper = document.createElement("div");
            wrapper.setAttribute("style", "z-index: 999999; position: fixed; -webkit-backface-visibility: hidden; left: 0;top:0; width: 100%;display:flex;flex-direction: column;align-items: center; height: 100%; background: " + (opts.overlayColor || "rgba(0, 0, 0, 0.3)") + "; ");
            wrapper.addEventListener("click", function() {
                Packetaaaa.Widget.close();
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
            iframe.setAttribute("style", "border: hidden; position: relative; max-width: 100%; max-height: 100%; box-sizing: border-box;background:#fff ");
        }
        iframe.setAttribute('id', "packeta-widget");
        iframe.setAttribute('sandbox', "allow-scripts allow-same-origin allow-forms");
        iframe.setAttribute('allow', "geolocation");
        iframe.setAttribute('src', url);
        wrapper.appendChild(menu);
        menu.appendChild(nazev);
        menu.appendChild(button);
        button.appendChild(span);
        wrapper.appendChild(iframe);
        if(!inline) {
            document.body.appendChild(wrapper);
        }

        if(wrapper.getAttribute("tabindex") === null) {
            wrapper.setAttribute("tabindex", "-1"); // make it focusable
        }
        wrapper.setAttribute("class", "visible");
        
window.addEventListener("message", (event) => {
      document.getElementById("packeta-point-id").value = event.data.selectedID +" - "+event.data.selectedName;
      document.getElementById("packeta-point-info").textContent = event.data.selectedName;
      document.getElementById("ship-to-different-address-checkbox").checked = true;
      document.getElementById("shipping_first_name").value = document.getElementById("billing_first_name").value;
      document.getElementById("shipping_last_name").value = document.getElementById("billing_last_name").value;
      document.getElementById("shipping_company").value = event.data.selectedID;
      document.getElementById("shipping_address_1").value = event.data.selectedName;
      document.getElementById("shipping_address_2").value = "";
      document.getElementById("shipping_postcode").value = "000 00";
      document.getElementById("shipping_city").value = "---";
        wrapper.setAttribute("style", "visibility: hidden");
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
