var markers = [];
var mapLimitCircle;
$('#searchBtn').on('click', function () {
    function clearMap() {
        for (var i = 0; i < markers.length; i++) {
            markers[i].setMap(null);
        }
        if (mapLimitCircle) {
            mapLimitCircle.setMap(null);
        }
    }
    var searchVal = $('#searchInput').val();
    if (searchVal == '') {
        alert('Please enter a search term.');
        return false;
    } else {
        $('#results ul').html('');
        $('#searchBtn').prop('disabled', true);
        $('#loading').show();

        $.getJSON('site/searchNear', {address: searchVal}, function (result) {
            $('.notfoundlabel').hide();
            $('#loading').hide();
            $('#searchBtn').prop('disabled', false);

            if (result.status == 'OK' && result.data.length > 0) {
                var latLng = new google.maps.LatLng(result.lat, result.lng);
                map.setCenter(latLng);
                
                clearMap();

                var cityCircle = new google.maps.Circle({
                    strokeColor: '#FF0000',
                    strokeOpacity: 0.8,
                    strokeWeight: 2,
                    fillColor: '#666',
                    fillOpacity: 0.35,
                    map: map,
                    center: latLng,
                    radius: 80467
                });
                mapLimitCircle = cityCircle;

                var marker = new google.maps.Marker({
                    position: latLng,
                    map: map
                });
                markers.push(marker);

                var ul = $('#results ul');
                ul.html('');
                for (var i = 0; i < result.data.length; i++) {
                    var marker = new google.maps.Marker({
                        icon: 'http://maps.google.com/mapfiles/ms/icons/green-dot.png',
                        position: new google.maps.LatLng(result.data[i].lat, result.data[i].lng),
                        map: map,
                        title: result.data[i].name,
                        animation: google.maps.Animation.DROP
                    });
                    ul.append('<li>' + result.data[i].name + '</li>');
                    markers.push(marker);
                }
            } else {
                $('.notfoundlabel').show();
            }
        });
    }
});


