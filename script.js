jQuery(document).ready(function($){

    // check if map container is already initialized

        var container = L.DomUtil.get('map');
        var map = L.map('map').setView([51.981, 18.940, -0.09], 9);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);
    

    getUrlParameter = function getUrlParameter(sParam) {
        var sPageURL = window.location.search.substring(1),
            sURLVariables = sPageURL.split('&'),
            sParameterName,
            i;
        
        // return only value of parameter
        for (i = 0; i < sURLVariables.length; i++) {
            sParameterName = sURLVariables[i].split('=');
        
            if (sParameterName[0] === sParam) {
                return sParameterName[1] === undefined ? true : decodeURIComponent(sParameterName[1]);
            }
        }
    };
    
    // on load full page after one second
    setTimeout(function() {
        // if #search_asarino exist
        //if ($('#search_asarino').length) 
        //    $('#search_asarino').click();
        
    }, 500);

    function hasAnyGetParameter() {
        const params = new URLSearchParams(window.location.search);
        const keys = [
            'action', 'type', 'type2', 'sort', 'area_from', 'area_to',
            'price_from', 'price_to', 'pokoje', 'perM2', 'city',
            'discrict', 'transaction', 'rooms_since', 'rooms_to'
        ];

        let has = false;
    
        for (let key of keys) {
            const value = params.get(key);
            if (value !== null && value.trim() !== "") {
                // find select or input with this name and set value
                $('#' + key).val(value);
                has = true;
            }
        }
    
        return has;
    }
    
    // Przykładowe użycie:
    if (hasAnyGetParameter()) {
        //console.log("Przynajmniej jeden parametr GET istnieje.");
        // after one second trigger #search_asarino click
        setTimeout(function() {
            $('#search_asarino').click();
        }
        , 500);
    }

    $('#search_asarino').on( "click", function(e) {
        e.preventDefault();

        var data = {
            'action': 'asarinos_search',
            'type' : $("#type").val(),
            'type2' : $("#type2").val(),
            'sort' : $("#sort").val(),
            'area_from' : $("#area_from").val(),
            'area_to' : $("#area_to").val(),
            'price_from' : $("#price_from").val(),
            'price_to' : $("#price_to").val(),
            'pokoje' : $("#pokoje").val(),
            'perM2' : $("#perM2").val(),
            'city' : $("#city").val(),
            'discrict' : $("#discrict").val(),
            'transaction' : $("#transaction").val(),
            'rooms_since' : $("#rooms_since").val(),
            'rooms_to' : $("#rooms_to").val(),
        };

        // if exist $_GET['agent'] add to data
        if (getUrlParameter('agent') != undefined) {
            data['agent'] = getUrlParameter('agent');
        }

        $('#search_results').html('');
        
        jQuery.post(ajax_object.ajax_url, data, function(response) {

               
            if ( $('#map').hasClass('leaflet-container')) {
                map.invalidateSize();
                jQuery('.leaflet-marker-pane, .leaflet-shadow-pane').html('');
                
            }

            var orangeIcon = new L.Icon({
                iconUrl: 'https://raw.githubusercontent.com/pointhi/leaflet-color-markers/master/img/marker-icon-2x-orange.png',
                shadowUrl: 'https://cdnjs.cloudflare.com/ajax/libs/leaflet/0.7.7/images/marker-shadow.png',
                iconSize: [25, 41],
                iconAnchor: [12, 41],
                popupAnchor: [1, -34],
                shadowSize: [41, 41]
              });     
                  
            $('#search_results').append('<div class="count_offers">Liczba ofert: <b>' + response.data.length + '</b></div>');
              
            let markers = [];
            response.data.forEach((property) => {
                //console.log(property);

                let extra_info = '';

                // check if have locationCommuneName, properties_area_size m2, descriptionRoom, if not empty then add
                if (property['locationCommuneName'] != '' && property['locationCommuneName'] != property['properties_city']) {
                    extra_info += ', <span class="commune">' + property['locationCommuneName'] + '</span>';
                }

                // if not undefined or empty add to extra_info
                if (property['apartmentRoomNumber'] != '' && property['apartmentRoomNumber'] != 0 && property['apartmentRoomNumber'] != null && property['apartmentRoomNumber'] != undefined) {
                    extra_info += ', <span class="rooms">' + property['apartmentRoomNumber'] + ' pokoje</span>';
                }
                
                $('#search_results').append('<a href="' + property['url'] + '" class="property"><div class="picture"><img src="' + property['thumb'] + '" /></div><div class="extra-info"><div class="exta-info-wrapper"><h2 class="title">' + property['properties_city'] + '' + extra_info + '</h2><div class="property-details"><span class="city">' + ((property['locationExportStreetType'] != '') ? property['locationExportStreetType'] : '') + ' ' + property['locationExportStreetName'] +' </span><br /><span class="price">' + Math.round(property['properties_price']) +' PLN</span> <span class="meters">' + property['properties_area_size'] +' m2</span> <span class="per_meter">' + Math.round(property['properties_perM2']) +' PLN za m2</span> </div></div></div><div class="button">Zobacz szczeóły</div></div></a>');
              
 
                L.marker([property['properties_latitude'][0], property['properties_longitude'][0]], {icon: orangeIcon}).addTo(map)
                .bindPopup('<a href="' + property['url'] +'"><img src="' + property['thumb'] + '" />' + property['title'] + '</a>');     
                let marker = [property['properties_latitude'][0],property['properties_longitude'][0]];
                markers.push(marker);      
            
            })


            // center map
            var bounds = new L.LatLngBounds(markers);
            map.fitBounds(bounds);
            console.log(markers);

        });

    });

    $(".js-range-slider").ionRangeSlider();


});