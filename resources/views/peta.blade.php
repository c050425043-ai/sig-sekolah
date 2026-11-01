<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Peta Sekolah</title>

    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    
    <!-- Leaflet MarkerCluster CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.css" />
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster@1.5.3/dist/MarkerCluster.Default.css" />

    <style>
        html, body {
            height: 100%;
            margin: 0;
            font-family: system-ui, -apple-system, sans-serif;
        }

        header {
            height: 56px;
            box-sizing: border-box;
            padding: 0 16px;
            display: flex;
            align-items: center;
            gap: 10px;
            background: #1e3a8a;
            color: #fff;
        }

        header h1 {
            font-size: 16px;
            margin: 0;
            white-space: nowrap;
        }

        header select, header input {
            padding: 6px 10px;
            border-radius: 6px;
            border: 1px solid #d1d5db;
            font-size: 13px;
        }

        header input {
            width: 180px;
        }

        header a {
            color: #bfdbfe;
            margin-left: auto;
            white-space: nowrap;
            font-size: 14px;
            text-decoration: none;
        }

        header a:hover {
            text-decoration: underline;
        }

        #map {
            height: calc(100% - 56px);
            width: 100%;
        }

        #jumlah {
            white-space: nowrap;
            font-size: 13px;
        }
    </style>
</head>

<body>
    <header>
        <h1>Peta Sekolah</h1>

        <!-- Filter Jenjang -->
        <select id="filter-jenjang">
            <option value="">Semua jenjang</option>
            <option value="SD">SD</option>
            <option value="SMP">SMP</option>
            <option value="SMA">SMA</option>
            <option value="SMK">SMK</option>
        </select>

        <!-- Filter Status -->
        <select id="filter-status">
            <option value="">Semua status</option>
            <option value="Negeri">Negeri</option>
            <option value="Swasta">Swasta</option>
        </select>

        <!-- Pencarian Sekolah -->
        <input type="text" id="cari-sekolah" placeholder="Cari nama sekolah..." list="list-sekolah" autocomplete="off">
        <datalist id="list-sekolah"></datalist>

        <span id="jumlah">Memuat data...</span>

        <a href="{{ route('admin.sekolah.index') }}">Kelola Data</a>
    </header>

    <div id="map"></div>

    <!-- Leaflet & MarkerCluster JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet.markercluster@1.5.3/dist/leaflet.markercluster.js"></script>

    <script>
        // Provider Peta Dasar Gratis Tanpa API Key
        const osm = L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; OpenStreetMap'
        });

        const esriSatelit = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
            maxZoom: 18,
            attribution: '&copy; Esri World Imagery'
        });

        const topoMap = L.tileLayer('https://{s}.tile.opentopomap.org/{z}/{x}/{y}.png', {
            maxZoom: 17,
            attribution: '&copy; OpenTopoMap'
        });

        const map = L.map('map', {
            center: [-3.3194, 114.5908],
            zoom: 12,
            layers: [osm]
        });

        // Kontrol Layer Peta Dasar (Latihan Mandiri 6)
        const baseMaps = {
            "OpenStreetMap (Standar)": osm,
            "Satelit (Esri)": esriSatelit,
            "Topografi (Relief)": topoMap
        };

        L.control.layers(baseMaps).addTo(map);

        function esc(teks) {
            const d = document.createElement('div');
            d.textContent = teks ?? '';
            return d.innerHTML;
        }

        function getWarnaJenjang(jenjang) {
            switch (jenjang) {
                case 'SD': return '#ef4444';
                case 'SMP': return '#3b82f6';
                case 'SMA': return '#22c55e';
                case 'SMK': return '#f97316';
                default: return '#6b7280';
            }
        }

        let markersMap = {};
        const clusterGroup = L.markerClusterGroup();
        map.addLayer(clusterGroup);

        async function muatData() {
            const jenjang = document.getElementById('filter-jenjang').value;
            const status = document.getElementById('filter-status').value;
            const jumlah = document.getElementById('jumlah');
            const datalist = document.getElementById('list-sekolah');

            try {
                jumlah.textContent = 'Memuat data...';

                const url = new URL(
                    @json(route('peta.data')),
                    window.location.origin
                );

                if (jenjang) url.searchParams.set('jenjang', jenjang);
                if (status) url.searchParams.set('status', status);

                const respon = await fetch(url);
                if (!respon.ok) throw new Error('HTTP Error: ' + respon.status);

                const data = await respon.json();

                clusterGroup.clearLayers();
                markersMap = {};
                datalist.innerHTML = '';

                if (!data.features || data.features.length === 0) {
                    jumlah.textContent = '0 sekolah ditemukan';
                    return;
                }

                const geoJsonLayer = L.geoJSON(data, {
                    pointToLayer: (fitur, latlng) => {
                        const warna = getWarnaJenjang(fitur.properties.jenjang);
                        return L.circleMarker(latlng, {
                            radius: 9,
                            fillColor: warna,
                            color: '#ffffff',
                            weight: 2,
                            opacity: 1,
                            fillOpacity: 0.85
                        });
                    },
                    onEachFeature: (fitur, layer) => {
                        const p = fitur.properties;
                        const [lng, lat] = fitur.geometry.coordinates;

                        if (p.nama) {
                            markersMap[p.nama.toLowerCase()] = layer;

                            const opt = document.createElement('option');
                            opt.value = p.nama;
                            datalist.appendChild(opt);
                        }

                        const popup = `
                            <strong>${esc(p.nama)}</strong><br>
                            NPSN: ${esc(p.npsn)}<br>
                            ${esc(p.jenjang)} - ${esc(p.status)}<br>
                            ${esc(p.alamat)}, ${esc(p.kecamatan)}<br>
                            Telp: ${esc(p.telepon) || '-'}<br>
                            <small>Koordinat: ${lat}, ${lng}</small>
                        `;

                        layer.bindPopup(popup);
                    }
                });

                clusterGroup.addLayer(geoJsonLayer);
                jumlah.textContent = data.features.length + ' sekolah';

                if (data.features.length > 0) {
                    map.fitBounds(clusterGroup.getBounds().pad(0.1));
                }
            } catch (error) {
                console.error('Kesalahan memuat data:', error);
                jumlah.textContent = 'Gagal memuat data';
            }
        }

        document.getElementById('cari-sekolah').addEventListener('input', function (e) {
            const query = e.target.value.toLowerCase().trim();
            const targetLayer = markersMap[query];

            if (targetLayer) {
                clusterGroup.zoomToShowLayer(targetLayer, function () {
                    targetLayer.openPopup();
                });
            }
        });

        document.getElementById('filter-jenjang').addEventListener('change', muatData);
        document.getElementById('filter-status').addEventListener('change', muatData);

        muatData();
    </script>
</body>

</html>