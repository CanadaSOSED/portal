window.onload = getLocationConstant;

function getLocationConstant() {
    if(navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(onGeoSuccess,onGeoError);
        console.log(navigator);
        if( "1" == navigator.doNotTrack ) {
            console.log('do not track');
            document.getElementById("latitude").value =  "User opted not to share geolocation information";
            document.getElementById("longitude").value = "User opted not to share geolocation information";
        }
    } else {
        alert( "Your browser or device doesn't support Geolocation" );
    }
}

// If we have a successful location update
function onGeoSuccess(event) {
    if( document.getElementById("latitude") !== null ) {
        document.getElementById("latitude").value = event.coords.latitude;
        document.getElementById("longitude").value = event.coords.longitude;
    }

}

function onGeoError(event) {
    if( document.getElementById("latitude") !== null ) {
        document.getElementById("latitude").value = event.message;
        document.getElementById("longitude").value = event.message;
    }
}