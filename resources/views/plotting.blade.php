<!DOCTYPE html>
<html>
<head>
    <title>Ruang Kerja Plotting</title>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet.vectorgrid@1.3.0/dist/Leaflet.VectorGrid.bundled.js"></script>
    <style>
        #map { height: 100vh; width: 100%; }
    </style>
</head>
<body>
    <div id="map"></div>

    <script>
        // Inisialisasi peta berpusat di Nganjuk
        var map = L.map('map').setView([-7.6044, 111.9035], 13);

        // Basemap OpenStreetMap
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        // URL Endpoint Martin di Port 3001
        // {z}/{x}/{y} akan diganti otomatis oleh Leaflet saat di-zoom/pan
        var martinUrl = "http://localhost:3001/data_plotting/{z}/{x}/{y}.pbf";

        // Styling untuk poligon/point plotting dari BPN
        var vectorTileOptions = {
            vectorTileLayerStyles: {
                // 'data_plotting' harus sama dengan nama tabel di database
                data_plotting: {
                    weight: 2,
                    color: '#FF0000',
                    fillColor: '#FF5555',
                    fillOpacity: 0.5,
                    fill: true
                }
            },
            interactive: true
        };

        // Memasukkan layer vector ke peta
        var plottingLayer = L.vectorGrid.protobuf(martinUrl, vectorTileOptions).addTo(map);

        // Event listener saat bidang tanah di klik
        plottingLayer.on('click', function(e) {
            var properties = e.layer.properties;
            // Menampilkan popup ID Berkas atau info lain yang ada di tabel
            L.popup()
                .setContent("<b>ID Plotting:</b> " + properties.id + "<br><b>ID Berkas:</b> " + properties.berkas_id)
                .setLatLng(e.latlng)
                .openOn(map);
        });
    </script>
</body>
</html>