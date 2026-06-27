<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Peta Utama Aset Nganjuk - BPN</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <link href="https://unpkg.com/maplibre-gl@3.x/dist/maplibre-gl.css" rel="stylesheet" />
    <script src="https://unpkg.com/maplibre-gl@3.x/dist/maplibre-gl.js"></script>

    <style>
        .maplibregl-popup-content { border-radius: 16px !important; padding: 14px 16px !important; }
    </style>
</head>
<body class="bg-slate-200 m-0 p-0 overflow-hidden text-slate-800">

    <div x-data="mainMapApp()" x-init="initMap()" class="relative w-full h-screen">
        
        <div id="map" class="absolute inset-0 w-full h-full z-0"></div>

        <a href="{{ route('bpn.dashboard') }}" class="absolute top-6 left-6 z-10 bg-white/95 backdrop-blur shadow-lg px-4 py-2.5 rounded-full font-bold text-sm text-slate-700 hover:text-blue-600 hover:bg-blue-50 transition border border-slate-200 flex items-center gap-2">
            <i class="fa-solid fa-arrow-left"></i> Kembali
        </a>

        <div class="absolute bottom-6 left-6 z-10 bg-white/95 backdrop-blur shadow-2xl p-5 rounded-2xl border border-slate-200 w-80">
            <div class="flex items-center gap-3 mb-3 pb-3 border-b border-slate-100">
                <div class="w-10 h-10 rounded-lg bg-blue-600 text-white flex items-center justify-center text-lg shadow-inner"><i class="fa-solid fa-map-location-dot"></i></div>
                <div>
                    <h2 class="font-black text-sm">Peta Aset Nganjuk</h2>
                    <p class="text-[10px] font-bold text-slate-400 uppercase">Render by Martin Server MVT</p>
                </div>
            </div>
            
            <div class="space-y-2">
                <div class="flex items-center gap-3">
                    <div class="w-5 h-5 rounded border-2 border-emerald-500 bg-emerald-500/50"></div>
                    <span class="text-xs font-bold text-slate-600">Poligon Berkas Selesai</span>
                </div>
                </div>
        </div>

        <div class="absolute top-6 right-6 z-10 flex flex-col gap-2">
            <button @click="toggleSatellite()" class="bg-white/95 backdrop-blur shadow-lg w-10 h-10 rounded-full flex items-center justify-center text-slate-600 hover:text-blue-600 border border-slate-200 transition focus:outline-none" title="Ubah Basemap">
                <i class="fa-solid fa-layer-group"></i>
            </button>
            <button @click="flyToNganjuk()" class="bg-white/95 backdrop-blur shadow-lg w-10 h-10 rounded-full flex items-center justify-center text-slate-600 hover:text-blue-600 border border-slate-200 transition focus:outline-none" title="Fokus ke Nganjuk">
                <i class="fa-solid fa-compress"></i>
            </button>
        </div>

    </div>

    <script>
        function mainMapApp() {
            return {
                map: null,
                isSatellite: true,

                initMap() {
                    const mapStyle = {
                        "version": 8,
                        "sources": {
                            "google-satellite": { "type": "raster", "tiles": ["https://mt1.google.com/vt/lyrs=s&x={x}&y={y}&z={z}"], "tileSize": 256 },
                            "osm-street": { "type": "raster", "tiles": ["https://a.tile.openstreetmap.org/{z}/{x}/{y}.png"], "tileSize": 256 },
                            "martin-data": { 
                                "type": "vector", 
                                // URL Endpoint Martin Server Anda (Port 3001)
                                "tiles": ["http://localhost:3001/data_plotting/{z}/{x}/{y}.pbf"] 
                            }
                        },
                        "layers": [
                            { "id": "osm-street-layer", "type": "raster", "source": "osm-street", "layout": { "visibility": "none" } },
                            { "id": "google-satellite-layer", "type": "raster", "source": "google-satellite", "layout": { "visibility": "visible" } },
                            
                            // Layer Data Plotting BPN
                            { 
                                "id": "plotting-fill", 
                                "type": "fill", 
                                "source": "martin-data", 
                                "source-layer": "data_plotting",
                                "paint": { "fill-color": "#10b981", "fill-opacity": 0.4 } // Warna Emerald untuk Selesai
                            },
                            { 
                                "id": "plotting-line", 
                                "type": "line", 
                                "source": "martin-data", 
                                "source-layer": "data_plotting",
                                "paint": { "line-color": "#047857", "line-width": 2 } 
                            }
                        ]
                    };

                    this.map = new maplibregl.Map({ 
                        container: 'map', 
                        style: mapStyle, 
                        center: [111.9035, -7.6046], // Koordinat Pusat Nganjuk
                        zoom: 12, 
                        attributionControl: false 
                    });

                    this.map.addControl(new maplibregl.NavigationControl(), 'bottom-right');

                    // Interaksi Klik pada Poligon Aset
                    this.map.on('click', 'plotting-fill', (e) => {
                        const props = e.features[0].properties;
                        
                        // Menampilkan Pop-Up berdasarkan data dari PostGIS yang disajikan Martin
                        new maplibregl.Popup()
                            .setLngLat(e.lngLat)
                            .setHTML(`
                                <div class="text-xs">
                                    <div class="font-black text-sm text-indigo-700 mb-1 border-b pb-1">Data Spasial #${props.id}</div>
                                    <div class="font-bold text-slate-700 mt-1">ID Berkas Induk: <span class="text-blue-600">${props.berkas_id}</span></div>
                                    <div class="text-[10px] text-slate-500 mt-2">Dibuat: ${props.created_at || '-'}</div>
                                </div>
                            `)
                            .addTo(this.map);
                    });

                    // Efek kursor tangan saat hover di atas poligon
                    this.map.on('mouseenter', 'plotting-fill', () => { this.map.getCanvas().style.cursor = 'pointer'; });
                    this.map.on('mouseleave', 'plotting-fill', () => { this.map.getCanvas().style.cursor = ''; });
                },

                toggleSatellite() {
                    this.isSatellite = !this.isSatellite;
                    this.map.setLayoutProperty('google-satellite-layer', 'visibility', this.isSatellite ? 'visible' : 'none');
                    this.map.setLayoutProperty('osm-street-layer', 'visibility', this.isSatellite ? 'none' : 'visible');
                },

                flyToNganjuk() {
                    this.map.flyTo({ center: [111.9035, -7.6046], zoom: 12, duration: 2000 });
                }
            };
        }
    </script>
</body>
</html>