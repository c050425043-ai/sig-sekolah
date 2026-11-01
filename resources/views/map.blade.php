<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Peta Web SIG Sekolah</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
</head>
<body class="bg-slate-100 flex flex-col min-h-screen">
    <header class="bg-indigo-700 text-white p-4 shadow-md flex justify-between items-center">
        <h1 class="text-xl font-bold">Peta Persebaran Sekolah (Banjarmasin)</h1>
        <a href="{{ route('admin.sekolah.index') }}" class="bg-indigo-600 hover:bg-indigo-800 px-4 py-2 rounded text-sm transition">
            Kelola Data
        </a>
    </header>

    <main class="flex-1 p-4">
        <div id="map" class="w-full h-[85vh] rounded-xl shadow-md border border-slate-300"></div>
    </main>

    <script>
        const map = L.map('map').setView([-3.319400, 114.590800], 13);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        fetch("{{ route('api.sekolah.geojson') }}")
            .then(response => response.json())
            .then(data => {
                L.geoJSON(data, {
                    onEachFeature: function (feature, layer) {
                        const props = feature.properties;
                        const popupContent = `
                            <div class="p-2">
                                <h3 class="font-bold text-base text-indigo-700">${props.nama_sekolah}</h3>
                                <p class="text-xs text-slate-500 font-mono mb-2">NPSN: ${props.npsn}</p>
                                <hr class="my-1">
                                <p class="text-xs"><b>Jenjang:</b> ${props.jenjang} (${props.status})</p>
                                <p class="text-xs"><b>Kecamatan:</b> ${props.kecamatan}</p>
                                <p class="text-xs"><b>Alamat:</b> ${props.alamat}</p>
                            </div>
                        `;
                        layer.bindPopup(popupContent);
                    }
                }).addTo(map);
            });
    </script>
</body>
</html>