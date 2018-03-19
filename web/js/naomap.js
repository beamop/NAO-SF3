var naomap = L.map('mapid').setView([48.862725, 2.287592], 7);

L.tileLayer('https://api.tiles.mapbox.com/v4/{id}/{z}/{x}/{y}.png?access_token={accessToken}', {
    attribution: 'Nos Amis les Oiseaux, 2018',
    maxZoom: 18,
    id: 'mapbox.streets',
    accessToken: 'pk.eyJ1IjoiYmVhbW9wIiwiYSI6ImNqZXBoMGdmajBmenUyd3F4eXQ4MnRiaXcifQ.7agOB2TyJ_HSG9Mjrl6GjA'
}).addTo(naomap);

var marker = L.marker([48.862725, 2.287592]).addTo(naomap);