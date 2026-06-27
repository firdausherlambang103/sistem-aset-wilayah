<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ruang Kerja Plotting - Mitra BPN</title>

    <script src="https://cdn.tailwindcss.com"></script>
    
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <link href="https://unpkg.com/maplibre-gl@3.x/dist/maplibre-gl.css" rel="stylesheet" />
    <script src="https://unpkg.com/maplibre-gl@3.x/dist/maplibre-gl.js"></script>
    
    <link rel="stylesheet" href="https://unpkg.com/@mapbox/mapbox-gl-draw@1.4.3/dist/mapbox-gl-draw.css" type="text/css" />
    <script src="https://unpkg.com/@mapbox/mapbox-gl-draw@1.4.3/dist/mapbox-gl-draw.js"></script>
    
    <script src="https://cdn.jsdelivr.net/npm/exif-js"></script>

    <style>
        .custom-scrollbar::-webkit-scrollbar { width: 6px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
        
        @media (max-width: 768px) {
            .mobile-search { top: 12px !important; width: 92vw !important; padding: 6px 12px !important; }
            .mobile-picking { top: auto !important; bottom: 85px !important; transform: translateX(-50%) scale(0.85) !important; width: max-content !important; max-width: 95vw !important; padding: 8px 16px !important; }
        }

        /* OVERRIDE DESAIN POP-UP MAPLIBRE */
        .maplibregl-popup-content {
            border-radius: 16px !important;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1) !important;
            padding: 14px 16px !important;
            border: 1px solid #f1f5f9;
        }
        .maplibregl-popup-close-button {
            top: 12px !important; right: 12px !important; color: #94a3b8 !important;
            font-size: 16px !important; border-radius: 50% !important;
            width: 24px !important; height: 24px !important;
            display: flex !important; align-items: center !important; justify-content: center !important;
            transition: all 0.2s !important;
        }
        .maplibregl-popup-close-button:hover { background-color: #fef2f2 !important; color: #f43f5e !important; }
    </style>
</head>
<body class="bg-slate-200 m-0 p-0 overflow-hidden">

    <div x-data="mapApp()" x-init="initMap()" class="relative w-full h-screen">
        
        <div id="map" class="absolute inset-0 w-full h-full z-0"></div>

        <div class="absolute top-6 left-1/2 transform -translate-x-1/2 z-[10] bg-white/95 backdrop-blur-md px-4 py-2.5 rounded-full shadow-lg border border-slate-200 flex items-center gap-2 w-[340px] max-w-[90vw] mobile-search">
            <i class="fa-solid fa-location-crosshairs text-blue-600 shrink-0"></i>
            <input type="text" x-model="searchCoord" @keydown.enter="cariKoordinat()" class="w-full text-sm border-none bg-transparent focus:ring-0 px-2 py-1 text-center font-semibold text-slate-700 outline-none" placeholder="Lat, Lng (Ex: -7.629, 111.523)">
            <button type="button" @click="cariKoordinat()" class="text-white bg-blue-600 hover:bg-blue-700 rounded-full w-8 h-8 flex items-center justify-center transition shadow-sm shrink-0">
                <i class="fa-solid fa-search text-xs"></i>
            </button>
        </div>

        <div class="absolute top-20 md:top-6 right-3 md:right-6 z-[20] flex flex-col items-end gap-2 md:gap-3">
            <button type="button" @click="isMenuOpen = !isMenuOpen" class="bg-white/90 backdrop-blur-md shadow-md text-slate-700 w-10 h-10 rounded-full flex items-center justify-center hover:bg-slate-100 transition border border-slate-200 focus:outline-none">
                <i class="fa-solid transition-all duration-300 text-lg" :class="isMenuOpen ? 'fa-minus' : 'fa-layer-group'"></i>
            </button>

            <div x-show="isMenuOpen" x-transition.opacity class="flex flex-col items-end gap-2 md:gap-3 origin-top-right">
                <div class="bg-white/95 backdrop-blur-md rounded-2xl shadow-lg border border-slate-100 p-1.5 w-40 md:w-48">
                    <button type="button" @click="showSatellite = !showSatellite; toggleSatellite()" class="w-full flex items-center justify-between p-2 rounded-xl transition-all hover:bg-slate-50">
                        <div class="flex items-center gap-2"><div class="w-7 h-7 rounded flex items-center justify-center" :class="showSatellite ? 'bg-blue-100 text-blue-600' : 'bg-slate-100 text-slate-500'"><i class="fa-solid fa-satellite text-xs"></i></div><span class="text-xs font-bold text-slate-700">Satelit</span></div>
                        <div class="w-7 h-4 rounded-full relative transition-colors" :class="showSatellite ? 'bg-blue-500' : 'bg-slate-300'"><div class="absolute top-0.5 left-0.5 w-3 h-3 bg-white rounded-full transition-transform" :class="showSatellite ? 'translate-x-3' : 'translate-x-0'"></div></div>
                    </button>
                    <button type="button" @click="getLocation()" class="w-full flex items-center gap-2 p-2 rounded-xl transition-all hover:bg-slate-50">
                        <div class="w-7 h-7 rounded bg-slate-100 flex items-center justify-center text-slate-600"><i class="fa-solid fa-street-view text-xs"></i></div><span class="text-xs font-bold text-slate-700">Lokasiku</span>
                    </button>
                </div>

                <button type="button" @click="openModal()" x-show="!isPickingMap" class="w-40 md:w-48 bg-blue-600 hover:bg-blue-700 text-white rounded-2xl py-3 shadow-lg flex items-center justify-center gap-2 transition-transform hover:scale-105 border-[3px] border-white/20 focus:outline-none">
                    <i class="fa-solid fa-plus text-sm"></i><span class="font-bold text-sm">Plotting Berkas</span>
                </button>
            </div>
        </div>

        <div x-show="isPickingMap" x-cloak x-transition.opacity class="absolute top-20 left-1/2 -translate-x-1/2 z-10 bg-slate-900/95 text-white px-6 py-3 rounded-full shadow-2xl flex items-center gap-3 border border-slate-700 mobile-picking">
            <i class="fa-solid animate-bounce text-rose-500 text-xl" :class="drawMode === 'polygon' ? 'fa-draw-polygon' : 'fa-location-dot'"></i>
            <span class="text-sm font-semibold tracking-wide" x-text="drawMode === 'polygon' ? 'Klik peta untuk menggambar area (Poligon)' : 'Klik lokasi aset pada peta...'"></span>
            <button type="button" @click.prevent="cancelMapPicking()" class="ml-3 bg-slate-700 hover:bg-rose-500 px-4 py-1.5 rounded-full text-xs font-bold transition-colors shadow-md">Batal</button>
        </div>

        <div x-show="modalOpen" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center bg-slate-900/60 backdrop-blur-sm p-4">
            <div x-show="modalOpen" @click.away="closeModal()" x-transition.scale.origin.bottom.duration.300ms class="bg-white rounded-3xl w-full max-w-lg shadow-2xl flex flex-col max-h-[90vh] overflow-hidden border border-slate-100">
                
                <div class="px-6 py-5 flex justify-between items-center bg-gradient-to-r from-blue-600 to-indigo-600 shrink-0">
                    <div>
                        <h3 class="font-extrabold text-white text-lg tracking-wide"><i class="fa-solid fa-folder-plus mr-2 opacity-80"></i>Input Berkas Baru</h3>
                        <p class="text-[11px] text-blue-100 mt-0.5">Sistem Monitoring Berkas Nganjuk</p>
                    </div>
                    <button type="button" @click.prevent="closeModal()" class="text-white hover:text-rose-200 transition w-8 h-8 flex items-center justify-center rounded-full bg-white/10 hover:bg-rose-500 focus:outline-none">
                        <i class="fa-solid fa-xmark text-lg"></i>
                    </button>
                </div>

                <div class="px-8 py-4 bg-slate-50 border-b border-slate-200 shrink-0 shadow-sm z-10">
                    <div class="flex items-center justify-between relative">
                        <div class="absolute left-0 top-1/2 -translate-y-1/2 w-full h-1 bg-slate-200 rounded-full z-0"></div>
                        <div class="absolute left-0 top-1/2 -translate-y-1/2 h-1 bg-blue-500 rounded-full z-0 transition-all duration-500 ease-in-out" :style="'width: ' + ((step - 1) * 50) + '%'"></div>

                        <div class="relative z-10 flex flex-col items-center gap-1.5 cursor-pointer" @click="step = 1">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-[13px] shadow-md transition-colors duration-300" :class="step >= 1 ? 'bg-blue-600 text-white ring-4 ring-blue-100' : 'bg-white text-slate-400 border border-slate-300'"><i class="fa-solid fa-file-lines" x-show="step > 1"></i><span x-show="step === 1">1</span></div>
                            <span class="text-[10px] font-bold uppercase tracking-wider" :class="step >= 1 ? 'text-blue-700' : 'text-slate-400'">Info Dasar</span>
                        </div>
                        <div class="relative z-10 flex flex-col items-center gap-1.5 transition-opacity cursor-pointer" :class="step >= 2 || validateStep1() ? 'opacity-100' : 'opacity-50'" @click="if(validateStep1()) step = 2">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-[13px] shadow-md transition-colors duration-300" :class="step >= 2 ? 'bg-blue-600 text-white ring-4 ring-blue-100' : 'bg-white text-slate-400 border border-slate-300'"><i class="fa-solid fa-camera" x-show="step > 2"></i><span x-show="step <= 2">2</span></div>
                            <span class="text-[10px] font-bold uppercase tracking-wider" :class="step >= 2 ? 'text-blue-700' : 'text-slate-400'">Visual</span>
                        </div>
                        <div class="relative z-10 flex flex-col items-center gap-1.5 transition-opacity" :class="step === 3 ? 'cursor-pointer opacity-100' : 'opacity-50'">
                            <div class="w-8 h-8 rounded-full flex items-center justify-center font-bold text-[13px] shadow-md transition-colors duration-300" :class="step >= 3 ? 'bg-blue-600 text-white ring-4 ring-blue-100' : 'bg-white text-slate-400 border border-slate-300'">3</div>
                            <span class="text-[10px] font-bold uppercase tracking-wider" :class="step >= 3 ? 'text-blue-700' : 'text-slate-400'">Spasial</span>
                        </div>
                    </div>
                </div>
                
                <div class="p-6 overflow-y-auto custom-scrollbar flex-1 bg-white">
                    <form id="plotForm" @submit.prevent="submitForm" enctype="multipart/form-data">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">

                        <div x-show="step === 1" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform translate-x-4" x-transition:enter-end="opacity-100 transform translate-x-0" class="space-y-4">
                            
                            <div>
                                <label class="block text-[11px] font-bold text-slate-600 mb-1.5">NAMA PEMOHON <span class="text-rose-500">*</span></label>
                                <input type="text" name="nama_pemohon" x-model="formData.nama_pemohon" class="w-full bg-slate-50 border border-slate-200 text-slate-800 text-sm rounded-xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 p-2.5 outline-none" required>
                            </div>
                            
                            <div>
                                <label class="block text-[11px] font-bold text-slate-600 mb-1.5">JENIS PERMOHONAN <span class="text-rose-500">*</span></label>
                                <input type="text" name="jenis_permohonan" x-model="formData.jenis_permohonan" class="w-full bg-slate-50 border border-slate-200 text-slate-800 text-sm rounded-xl focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 p-2.5 outline-none" required>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-[11px] font-bold text-slate-600 mb-1.5">JENIS HAK <span class="text-rose-500">*</span></label>
                                    <input type="text" name="jenis_hak" x-model="formData.jenis_hak" class="w-full bg-slate-50 border border-slate-200 text-slate-800 text-sm rounded-xl p-2.5 outline-none" required>
                                </div>
                                <div>
                                    <label class="block text-[11px] font-bold text-slate-600 mb-1.5">NOMOR HAK <span class="text-rose-500">*</span></label>
                                    <input type="text" name="nomer_hak" x-model="formData.nomer_hak" class="w-full bg-slate-50 border border-slate-200 text-slate-800 text-sm rounded-xl p-2.5 outline-none" required>
                                </div>
                            </div>
                            
                            <div class="grid grid-cols-2 gap-4 border-t border-slate-100 pt-4 mt-2">
                                <div>
                                    <label class="block text-[11px] font-bold text-slate-600 mb-1.5">KECAMATAN <span class="text-rose-500">*</span></label>
                                    <input type="text" name="kecamatan" x-model="formData.kecamatan" class="w-full bg-slate-50 border border-slate-200 text-slate-800 text-sm rounded-xl p-2.5 outline-none" required>
                                </div>
                                <div>
                                    <label class="block text-[11px] font-bold text-slate-600 mb-1.5">DESA <span class="text-rose-500">*</span></label>
                                    <input type="text" name="desa" x-model="formData.desa" class="w-full bg-slate-50 border border-slate-200 text-slate-800 text-sm rounded-xl p-2.5 outline-none" required>
                                </div>
                            </div>

                            <button type="button" @click="nextStep(2)" class="w-full mt-6 bg-slate-800 hover:bg-slate-900 text-white font-bold py-3.5 rounded-2xl transition-colors flex justify-center items-center gap-3">
                                <span>Selanjutnya: Visual Aset</span> <i class="fa-solid fa-arrow-right text-sm"></i>
                            </button>
                        </div>

                        <div x-show="step === 2" x-cloak x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform translate-x-4" x-transition:enter-end="opacity-100 transform translate-x-0" class="space-y-4">
                            <div class="bg-blue-50 border border-blue-100 rounded-2xl p-4 flex items-start gap-3 mb-2">
                                <i class="fa-solid fa-circle-info text-blue-500 mt-0.5"></i>
                                <div>
                                    <p class="text-xs font-bold text-blue-800 mb-1">Panduan Pengambilan Foto</p>
                                    <p class="text-[11px] text-blue-600 leading-relaxed">Ambil foto langsung di lokasi agar sistem dapat membaca metadata koordinat GPS secara otomatis, lalu peta akan bergeser ke lokasi tersebut.</p>
                                </div>
                            </div>

                            <div class="border-2 border-dashed border-slate-300 rounded-2xl p-8 text-center bg-slate-50 hover:bg-blue-50 hover:border-blue-300 transition-all cursor-pointer relative group" @click="$refs.fotoInput.click()">
                                <div class="w-16 h-16 bg-white rounded-full shadow-sm flex items-center justify-center mx-auto mb-4 group-hover:scale-110 transition-transform"><i class="fa-solid fa-camera text-2xl text-slate-400 group-hover:text-blue-500 transition-colors"></i></div>
                                <span class="block text-sm font-extrabold text-slate-600 group-hover:text-blue-700">Pilih / Ambil Foto</span>
                                <input type="file" name="foto_lokasi" x-ref="fotoInput" accept="image/*" capture="environment" class="hidden" @change="previewImage">
                                
                                <div x-show="isExtractingGps" class="absolute inset-0 bg-white/90 backdrop-blur-sm flex flex-col items-center justify-center rounded-2xl z-10">
                                    <i class="fa-solid fa-circle-notch fa-spin text-blue-600 text-3xl mb-3"></i>
                                    <span class="text-sm font-bold text-slate-700">Membaca Metadata GPS...</span>
                                </div>
                            </div>
                            
                            <template x-if="imagePreview">
                                <div x-transition.opacity>
                                    <div class="mt-4 relative rounded-2xl overflow-hidden border border-slate-200 shadow-md">
                                        <img :src="imagePreview" class="w-full h-56 object-cover">
                                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
                                        <button type="button" @click.stop="imagePreview = null; $refs.fotoInput.value = ''; gpsStatus = ''" class="absolute top-3 right-3 bg-rose-500 text-white w-9 h-9 flex items-center justify-center rounded-full shadow-lg hover:bg-rose-600 transition"><i class="fa-solid fa-trash-can text-sm"></i></button>
                                        <p class="absolute bottom-3 left-0 w-full text-xs font-bold text-center px-4" :class="gpsStatus.includes('berhasil') ? 'text-emerald-400' : 'text-amber-400'" x-text="gpsStatus"></p>
                                    </div>
                                </div>
                            </template>

                            <div class="flex gap-3 mt-8">
                                <button type="button" @click="step = 1" class="w-14 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold py-3.5 rounded-2xl transition flex justify-center items-center shrink-0 border border-slate-200"><i class="fa-solid fa-arrow-left"></i></button>
                                <button type="button" @click="nextStep(3)" class="flex-1 bg-slate-800 hover:bg-slate-900 text-white font-bold py-3.5 rounded-2xl transition flex justify-center items-center gap-3" :disabled="!imagePreview"><span>Lanjut Spasial</span> <i class="fa-solid fa-arrow-right text-sm"></i></button>
                            </div>
                        </div>

                        <div x-show="step === 3" x-cloak x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform translate-x-4" x-transition:enter-end="opacity-100 transform translate-x-0" class="space-y-4">
                            <div>
                                <label class="block text-[11px] font-bold text-slate-600 mb-1.5 text-center uppercase tracking-widest text-blue-600">GeoJSON Berkas <span class="text-rose-500">*</span></label>
                                <textarea name="geo_json_data" x-model="formData.geo_json_data" rows="3" class="w-full border-2 border-slate-200 rounded-xl bg-slate-50 text-xs p-3 text-center font-mono text-slate-600 font-semibold cursor-not-allowed resize-none outline-none" placeholder="Data spasial dari peta otomatis tampil di sini" readonly required></textarea>
                            </div>
                            
                            <div class="grid grid-cols-2 gap-3 mt-4">
                                <button type="button" @click="startMapPicking('point')" class="bg-white border-2 border-blue-100 hover:border-blue-400 text-slate-700 py-4 rounded-2xl transition-all flex flex-col items-center gap-2 shadow-sm focus:outline-none group">
                                    <div class="w-12 h-12 bg-blue-50 rounded-full flex items-center justify-center group-hover:bg-blue-500 group-hover:text-white transition-colors text-blue-500"><i class="fa-solid fa-location-dot text-xl"></i></div>
                                    <span class="text-[11px] font-extrabold uppercase tracking-wide">Titik Lokasi</span>
                                </button>
                                
                                <button type="button" @click="startMapPicking('polygon')" class="bg-white border-2 border-indigo-100 hover:border-indigo-400 text-slate-700 py-4 rounded-2xl transition-all flex flex-col items-center gap-2 shadow-sm focus:outline-none group">
                                    <div class="w-12 h-12 bg-indigo-50 rounded-full flex items-center justify-center group-hover:bg-indigo-500 group-hover:text-white transition-colors text-indigo-500"><i class="fa-solid fa-draw-polygon text-xl"></i></div>
                                    <span class="text-[11px] font-extrabold uppercase tracking-wide">Gambar Area</span>
                                </button>
                            </div>

                            <div class="flex gap-3 mt-6 pt-6 border-t border-slate-100">
                                <button type="button" @click="step = 2" class="w-14 bg-slate-100 hover:bg-slate-200 text-slate-600 font-bold py-3.5 rounded-2xl transition flex justify-center items-center shrink-0 border border-slate-200"><i class="fa-solid fa-arrow-left"></i></button>
                                <button type="submit" class="flex-1 bg-gradient-to-r from-emerald-500 to-teal-500 hover:from-emerald-600 hover:to-teal-600 text-white font-extrabold py-3.5 rounded-2xl transition shadow-[0_6px_20px_rgba(16,185,129,0.3)] flex justify-center items-center gap-2" :disabled="isSubmitting">
                                    <i x-show="!isSubmitting" class="fa-solid fa-cloud-arrow-up text-lg"></i>
                                    <i x-show="isSubmitting" class="fa-solid fa-circle-notch fa-spin text-lg"></i>
                                    <span x-text="isSubmitting ? 'Menyimpan...' : 'Kirim Berkas'"></span>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function mapApp() {
            return {
                map: null, draw: null, marker: null, searchMarker: null, 
                modalOpen: false, step: 1, 
                isPickingMap: false, drawMode: 'point', 
                isLoadingGPS: false, isExtractingGps: false, isSubmitting: false, 
                imagePreview: null, gpsStatus: '', searchCoord: '', showSatellite: true,
                isMenuOpen: window.innerWidth > 768, 
                
                formData: { nama_pemohon: '', jenis_permohonan: '', jenis_hak: '', nomer_hak: '', kecamatan: '', desa: '', geo_json_data: '' },

                validateStep1() { 
                    return this.formData.nama_pemohon && this.formData.jenis_permohonan && 
                           this.formData.jenis_hak && this.formData.nomer_hak && 
                           this.formData.kecamatan && this.formData.desa; 
                },

                initMap() {
                    // Konfigurasi Peta dengan Koneksi ke Martin Server MVT (Port 3001)
                    const mapStyle = {
                        "version": 8,
                        "sources": {
                            "google-satellite": { "type": "raster", "tiles": ["https://mt1.google.com/vt/lyrs=s&x={x}&y={y}&z={z}"], "tileSize": 256 },
                            "osm-street": { "type": "raster", "tiles": ["https://a.tile.openstreetmap.org/{z}/{x}/{y}.png"], "tileSize": 256 },
                            "martin-data-plotting": { 
                                "type": "vector", 
                                // URL Endpoint Martin Server Anda
                                "tiles": ["http://localhost:3001/data_plotting/{z}/{x}/{y}.pbf"] 
                            }
                        },
                        "layers": [
                            { "id": "osm-street-layer", "type": "raster", "source": "osm-street", "layout": { "visibility": "none" } },
                            { "id": "google-satellite-layer", "type": "raster", "source": "google-satellite", "layout": { "visibility": "visible" } },
                            { 
                                "id": "plotting-fill", 
                                "type": "fill", 
                                "source": "martin-data-plotting", 
                                "source-layer": "data_plotting", // Sesuai nama tabel di DB
                                "paint": { "fill-color": "#FF0000", "fill-opacity": 0.4 } 
                            },
                            { 
                                "id": "plotting-line", 
                                "type": "line", 
                                "source": "martin-data-plotting", 
                                "source-layer": "data_plotting",
                                "paint": { "line-color": "#FF0000", "line-width": 2 } 
                            }
                        ]
                    };

                    this.map = new maplibregl.Map({ container: 'map', style: mapStyle, center: [111.9035, -7.6046], zoom: 12, attributionControl: false });
                    this.map.addControl(new maplibregl.NavigationControl(), 'bottom-left');
                    this.draw = new MapboxDraw({ displayControlsDefault: false, controls: { polygon: true, trash: true } });
                    this.map.addControl(this.draw, 'top-right');

                    this.map.on('draw.create', this.updateGeoJSON.bind(this));
                    this.map.on('draw.update', this.updateGeoJSON.bind(this));
                },

                updateGeoJSON(e) {
                    const data = this.draw.getAll();
                    if (data.features.length > 0) {
                        const feature = e.features ? e.features[0] : data.features[data.features.length - 1];
                        
                        // Menyimpan Data sebagai String JSON Spasial untuk diproses PostGIS
                        this.formData.geo_json_data = JSON.stringify(feature.geometry);

                        if (feature.geometry.type === 'Point') {
                            this.draw.delete(feature.id);
                            const lng = feature.geometry.coordinates[0];
                            const lat = feature.geometry.coordinates[1];
                            if (this.marker) this.marker.remove();
                            this.marker = new maplibregl.Marker({ color: '#f43f5e' }).setLngLat([lng, lat]).addTo(this.map); 
                        }

                        this.isPickingMap = false;
                        this.modalOpen = true;
                        this.map.getCanvas().style.cursor = '';
                        setTimeout(() => { if (this.draw && typeof this.draw.getMode === 'function') { this.draw.changeMode('simple_select'); } }, 100);
                    }
                },

                cariKoordinat() {
                    let input = this.searchCoord.trim();
                    if (!input) return;
                    let coords = input.split(',').map(c => parseFloat(c.trim()));
                    if (coords.length === 2 && !isNaN(coords[0]) && !isNaN(coords[1])) {
                        this.map.flyTo({ center: [coords[1], coords[0]], zoom: 18 });
                        if(this.searchMarker) this.searchMarker.remove();
                        this.searchMarker = new maplibregl.Marker({ color: '#2563eb' }).setLngLat([coords[1], coords[0]]).addTo(this.map);
                    } else { alert('Format salah! Gunakan: Lat, Lng'); }
                },

                toggleSatellite() {
                    this.map.setLayoutProperty('google-satellite-layer', 'visibility', this.showSatellite ? 'visible' : 'none');
                    this.map.setLayoutProperty('osm-street-layer', 'visibility', this.showSatellite ? 'none' : 'visible');
                },

                openModal() { this.modalOpen = true; },
                
                closeModal() { 
                    this.modalOpen = false; this.isPickingMap = false; 
                    if (this.draw && typeof this.draw.getMode === 'function' && this.draw.getMode() !== 'simple_select') { this.draw.changeMode('simple_select'); }
                },
                
                nextStep(target) {
                    if (target > 1 && !this.validateStep1()) { alert('Silakan lengkapi Info Dasar terlebih dahulu!'); this.step = 1; return; }
                    this.step = target;
                },
                
                startMapPicking(mode) { 
                    this.drawMode = mode; this.modalOpen = false; this.isPickingMap = true; 
                    if(mode === 'polygon') { this.draw.changeMode('draw_polygon'); } else { this.draw.changeMode('draw_point'); }
                    this.map.getCanvas().style.cursor = 'crosshair'; 
                },

                cancelMapPicking() { 
                    this.isPickingMap = false; this.modalOpen = true; this.map.getCanvas().style.cursor = ''; 
                    setTimeout(() => { if (this.draw && typeof this.draw.getMode === 'function') { this.draw.changeMode('simple_select'); } }, 100);
                },
                
                getLocation() {
                    if (navigator.geolocation) {
                        navigator.geolocation.getCurrentPosition(
                            pos => {
                                const lng = pos.coords.longitude; const lat = pos.coords.latitude;
                                this.map.flyTo({ center: [lng, lat], zoom: 17 });
                            },
                            err => { alert("Pastikan izin lokasi GPS aktif pada browser Anda."); }, { enableHighAccuracy: true }
                        );
                    }
                },
                
                previewImage(e) { 
                    const file = e.target.files[0];
                    if (file) {
                        this.imagePreview = URL.createObjectURL(file); this.isExtractingGps = true; this.gpsStatus = '';
                        const self = this;
                        EXIF.getData(file, function() {
                            const lat = EXIF.getTag(this, "GPSLatitude"); const lon = EXIF.getTag(this, "GPSLongitude");
                            const latRef = EXIF.getTag(this, "GPSLatitudeRef") || "S"; const lonRef = EXIF.getTag(this, "GPSLongitudeRef") || "E";
                            if (lat && lon) {
                                let latDec = lat[0].valueOf() + lat[1].valueOf()/60 + lat[2].valueOf()/3600;
                                let lonDec = lon[0].valueOf() + lon[1].valueOf()/60 + lon[2].valueOf()/3600;
                                if (latRef === "S") latDec = latDec * -1; if (lonRef === "W") lonDec = lonDec * -1;
                                
                                // Auto Fly & Drop Marker dari Koordinat Foto
                                self.map.flyTo({ center: [lonDec, latDec], zoom: 17 });
                                self.formData.geo_json_data = JSON.stringify({ type: "Point", coordinates: [lonDec, latDec] });
                                
                                if (self.marker) self.marker.remove();
                                self.marker = new maplibregl.Marker({ color: '#f43f5e' }).setLngLat([lonDec, latDec]).addTo(self.map); 

                                self.gpsStatus = "✅ Koordinat berhasil ditarik dari foto!";
                            } else { self.gpsStatus = "⚠️ Foto tidak memiliki GPS. Silakan gambar di peta manual."; }
                            self.isExtractingGps = false;
                        });
                    } 
                },
                
                async submitForm(e) {
                    if (!this.formData.geo_json_data) { alert("Data Spasial Geometri pada Step 3 wajib digambar/diisi!"); return; }
                    this.isSubmitting = true; 
                    
                    const formDataObj = new FormData(e.target);
                    
                    try {
                        const response = await fetch("{{ route('berkas.plotting.store') }}", { 
                            method: 'POST', 
                            body: formDataObj 
                        });
                        
                        if (response.ok) {
                            alert('✅ Berkas Plotting Berhasil Dikirim!');
                            window.location.reload();
                        } else { 
                            alert('❌ Gagal mengirim berkas. Mohon periksa kembali isian Anda.'); 
                        }
                    } catch (error) { 
                        alert('Terjadi kesalahan koneksi server.'); 
                    } finally { 
                        this.isSubmitting = false; 
                    }
                }
            };
        }
    </script>
</body>
</html>