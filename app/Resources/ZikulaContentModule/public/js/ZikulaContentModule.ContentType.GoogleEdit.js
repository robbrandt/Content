'use strict';

/**
 * Initialises the google map editing.
 */
function contentInitGoogleMapEdit() {
    var fieldPrefix;
    var latitude;
    var longitude;
    var zoom;
    var mapType;
    var latlng;
    var mapOptions;
    var map;
    var marker;

    fieldPrefix = 'zikulacontentmodule_contentitem_contentData_';

    latitude = parseFloat(jQuery('#' + fieldPrefix + 'latitude').val().replace(',', '.'));
    if (!latitude || isNaN(latitude)) {
        latitude = 54.336869;
    }

    longitude = parseFloat(jQuery('#' + fieldPrefix + 'longitude').val().replace(',', '.'));
    if (!longitude || isNaN(longitude)) {
        longitude = 10.119942;
    }

    zoom = parseInt(jQuery('#' + fieldPrefix + 'zoom').val());
    if (!zoom) {
        zoom = 5;
    }

    mapType = jQuery('#' + fieldPrefix + 'mapType').val();
    if (!mapType) {
        mapType = 'roadmap';
    }

    latlng = new google.maps.LatLng(latitude, longitude);

    mapOptions = { 
        zoom: zoom,
        center: latlng, 
        scaleControl: true,
        mapTypeId: mapType
    }; 
    map = new google.maps.Map(jQuery('#googlemap').get(0), mapOptions); 

    // add a marker to the map
    marker = new google.maps.Marker({ 
        position: latlng,
        map: map
    });

    google.maps.event.addListener(map, 'click', function(event) { 
        var coord;

        marker.setMap(null);
        marker = null;
        marker = new google.maps.Marker({ 
            position: event.latLng,
            map: map
        });
        map.setCenter(event.latLng);
        coord = event.latLng.toString();
        coord = coord.split(', ');
        latitude = coord[0].replace(/\(/, '');
        longitude = coord[1].replace(/\)/, '');

        jQuery('#' + fieldPrefix + 'latitude').val(latitude);
        jQuery('#' + fieldPrefix + 'longitude').val(longitude);
        jQuery('#' + fieldPrefix + 'zoom').val(map.getZoom());
    });

    jQuery('#' + fieldPrefix + 'mapType_0, #' + fieldPrefix + 'mapType_1, #' + fieldPrefix + 'mapType_2, #' + fieldPrefix + 'mapType_3').change(function () {
        map.setMapTypeId(jQuery(this).val());
    });
}
