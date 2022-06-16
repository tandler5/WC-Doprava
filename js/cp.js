Packetaa = window.Packetaa || {};
Packetaa.Viewport = {
    element: null,
    originalValue: null,
    set: function() {
        if(!Packetaa.Viewport.element) {
            Packetaa.Viewport.element = document.querySelector("meta[name=viewport]");
            if(Packetaa.Viewport.element) {
                Packetaa.Viewport.originalValue = Packetaa.Viewport.element.getAttribute("content");
            }
            else {
                Packetaa.Viewport.originalValue = 'user-scalable=yes';
                Packetaa.Viewport.element = document.createElement('meta');
                Packetaa.Viewport.element.setAttribute("name", "viewport");
                (document.head || document.getElementsByTagName('head')[0]).appendChild(Packeta.Viewport.element);
            }
        }
        Packetaa.Viewport.element.setAttribute('content', 'width=device-width, initial-scale=1.0, minimum-scale=1.0, user-scalable=yes');
    },
    restore: function() {
        if(Packetaa.Viewport.originalValue !== null) {
            Packetaa.Viewport.element.setAttribute('content', Packetaa.Viewport.originalValue);
        }
    }
};
var wrapper;
function closeA(){
    wrapper.setAttribute("style","visibility:hidden;display: none;")
}
Packetaa.Widget = {
    baseUrl: 'https://b2c.cpost.cz/locations/',
    close: function() {},
    pick: function(apiKey,apiKey2, callback, opts, inElement) {
        Packetaa.Widget.close();

        if(opts === undefined) {
            opts = {};
        }
        if(!('version' in opts)) {
            opts.version = 3;
        }
        opts.apiKey = apiKey;
        opts.usePreProdWidgetVersion = true;
        var url = Packetaa.Widget.baseUrl;
        if(apiKey==="yes"&&apiKey2==="no"){
             url = 'https://b2c.cpost.cz/locations/?type=POST_OFFICE'
        }
        else if(apiKey==="no"&&apiKey2==="yes"){
            url = 'https://b2c.cpost.cz/locations/?type=BALIKOVNY'
        }
        else{
            url = 'https://b2c.cpost.cz/locations/'
        }

        var inline = (typeof(inElement) != "undefined" && inElement !== null);
        if(inline) {
            wrapper = inElement;
        }
        else {
            Packetaa.Viewport.set();
            menu = document.createElement("div");
            menu.setAttribute("class", "menu");
            menu.setAttribute("style", "background: #fff;color: rgb(0,39,118);font-weight: 700;padding: 0 0 0 1rem;height: 40px;font-size: 22px;width: 100%;");
            nazev = document.createElement("span");
            nazev.classList.add("ui-dialog-title");
            nazev.textContent = "Vyberte výdejní místo pošty";
            button= document.createElement("button");
            button.setAttribute("type", "button");
            button.setAttribute("title", "Zavřít");
            button.setAttribute("onclick", "closeA()");
            button.setAttribute("style", "float:right;background:0;display:flex;padding: 0;");
            span= document.createElement("span");
            span.textContent = "X";
            span.setAttribute("style","background:rgb(0,39,118);color:#fff;width:40px;height:40px;")
            wrapper = document.createElement("div");
            wrapper.setAttribute("style", "z-index: 999999; position: fixed; -webkit-backface-visibility: hidden; left: 0;top:0; width: 100%;display:flex;flex-direction: column;align-items: center; height: 100%; background: " + (opts.overlayColor || "rgba(0, 0, 0, 0.3)") + "; ");
            wrapper.addEventListener("click", function() {
                Packetaa.Widget.close();
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
            iframe.setAttribute("style", "border: hidden; position: relative; max-width: 100%; max-height: 100%; padding:  0 5px 10px 5px; box-sizing: border-box;background:#fff ");
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
        wrapper.setAttribute("style, "display:none");
    function iframeListener(event) {
     if (event.data.message === "pickerResult") {
         document.getElementById("ship-to-different-address-checkbox").checked = true;
         var id = document.getElementById("shipping_address_1");
         var splitArr1 = event.data.point.address.split(",");
         if(event.data.point.type==="BALIKOVNY"){
             if(event.data.point.subtype==="PARTNER"){
                document.getElementById("packeta-point-id").value = "Balíkomat - " + event.data.point.address;
                document.getElementById("shipping_company").value = "Balíkomat - " + event.data.point.name;
             }
             else{
                document.getElementById("packeta-point-id").value = "Balíkomat na poště - " + event.data.point.address;
                document.getElementById("shipping_company").value = "Balíkomat na poště - " + event.data.point.name;
             }
         }
         else{
             document.getElementById("packeta-point-id").value = "Pošta - "+ event.data.point.address;
             document.getElementById("shipping_company").value = "Pošta - "+ event.data.point.name;
         }
        document.getElementById("shipping_first_name").value = document.getElementById("billing_first_name").value;
        document.getElementById("shipping_last_name").value = document.getElementById("billing_last_name").value;
        id.value = splitArr1[0];
        document.getElementById("shipping_postcode").value = event.data.point.zip;
        if(event.data.point.municipality_district_name!=event.data.point.municipality_name){
        document.getElementById("shipping_address_2").value = event.data.point.municipality_district_name;
        }
        document.getElementById("shipping_address_2").value = event.data.point.municipality_district_name;
        document.getElementById("shipping_city").value = event.data.point.municipality_name;
        document.getElementById("packeta-point-info").textContent = event.data.point.address;
        wrapper.setAttribute("style", "visibility: hidden");
        wrapper.setAttribute("style", "height: 0");
     }
    }
window.addEventListener("message", iframeListener) 
    wrapper.addEventListener("keyup", function(e) {
            if(e.keyCode == 27) {
                wrapper.setAttribute("style", "visibility: hidden");
                      document.getElementById("packeta-point-info").textContent = "Zatím nevybráno";
                        
            }
    });
    wrapper.focus();
    }
};
