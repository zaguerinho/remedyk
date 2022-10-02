var autocomplete = {};
var componentForm = {
    street_number: 'short_name',
    route: 'long_name',
    locality: 'long_name',
    administrative_area_level_1: 'short_name',
    administrative_area_level_2: 'long_name',
    country: 'long_name',
    postal_code: 'short_name'
};

function initialize() {
    // Create autocomplete object, and restrict the search
	var autocompletes = document.getElementsByClassName('autocomplete');
	
	Array.prototype.forEach.call(autocompletes, function(element, index, array){
		var id = element.getAttribute('id');
		initializeElement(id);
	});
	
}

function initializeElement(id){
	autocomplete[id] = new google.maps.places.Autocomplete(
	        (document.getElementById(id)),
	        { types: ['geocode'] });

	    // When the user selects an address in the dropdown menu,
	    // fill in the form fields.
	    google.maps.event.addListener(autocomplete[id], 'place_changed', function() {
	        fillInAddress(id);
	    });
}

function fillInAddress(id) {
    // Get place details from the autocomplete object
    var place = autocomplete[id].getPlace();
    // Get each component from the address details
    // and fill in the corresponding field in the form
    if (place != undefined) {
        for (var component in componentForm) {
            document.getElementById(id+'_'+component).value = '';
            document.getElementById(id+'_'+component).disabled = false;
        }

        for (var i = 0; i < place.address_components.length; i++) {
            var addressType = place.address_components[i].types[0];
            if (componentForm[addressType]) {
                var val = place.address_components[i][componentForm[addressType]];
                document.getElementById(id+'_'+addressType).value = val;
                document.getElementById(id+'_'+addressType).click();
            }
        }

        document.getElementById(id+'_lat').value = place.geometry.location.lat();
        document.getElementById(id+'_lng').value = place.geometry.location.lng();
        document.getElementById(id+'_url_gmaps').value = place.url;
    }
}

//user geolocation,
function geolocate() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(position) {
            var geolocation = new google.maps.LatLng(
                position.coords.latitude, position.coords.longitude);
            var circle = new google.maps.Circle({
                center: geolocation,
                radius: position.coords.accuracy
            });
            autocomplete.setBounds(circle.getBounds());
            autocomplete_other.setBounds(circle.getBounds());
            autocomplete_textarea.setBounds(circle.getBounds());
        });
    }
}