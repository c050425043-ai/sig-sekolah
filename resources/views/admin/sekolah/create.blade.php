@extends('layouts.app')
@section('title', 'Tambah Sekolah')

@section('content')
<h1 class="text-2xl font-bold mb-4">Tambah Sekolah</h1>

<form action="{{ route('admin.sekolah.store') }}" method="POST" class="bg-white p-6 rounded shadow space-y-4">
    @csrf

    <div>
        <label class="block font-medium">NPSN</label>
        <input type="text" name="npsn" value="{{ old('npsn') }}" class="w-full border rounded p-2">
        @error('npsn') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="block font-medium">Nama Sekolah</label>
        <input type="text" name="nama_sekolah" value="{{ old('nama_sekolah') }}" class="w-full border rounded p-2">
        @error('nama_sekolah') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
    </div>

    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="block font-medium">Jenjang</label>
            <select name="jenjang" class="w-full border rounded p-2">
                @foreach(['SD', 'SMP', 'SMA', 'SMK'] as $j)
                    <option value="{{ $j }}">{{ $j }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block font-medium">Status</label>
            <select name="status" class="w-full border rounded p-2">
                <option value="Negeri">Negeri</option>
                <option value="Swasta">Swasta</option>
            </select>
        </div>
    </div>

    <div>
        <label class="block font-medium">Alamat</label>
        <textarea name="alamat" class="w-full border rounded p-2">{{ old('alamat') }}</textarea>
    </div>

    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="block font-medium">Kecamatan</label>
            <input type="text" name="kecamatan" value="{{ old('kecamatan') }}" class="w-full border rounded p-2">
        </div>
        <div>
            <label class="block font-medium">Kabupaten/Kota</label>
            <input type="text" name="kabupaten_kota" value="{{ old('kabupaten_kota', 'Kota Banjarmasin') }}" class="w-full border rounded p-2">
        </div>
    </div>

    <div>
        <div class="flex items-center justify-between mb-1">
            <label class="font-medium">Pilih Lokasi di Peta / Isi Koordinat Manual:</label>
            <!-- Tombol Gunakan Lokasi Saya (Latihan Mandiri 4) -->
            <button type="button" id="btn-lokasi-saya" class="px-3 py-1 text-sm bg-emerald-600 hover:bg-emerald-700 text-white font-medium rounded flex items-center gap-1">
                📍 Gunakan Lokasi Saya
            </button>
        </div>
        <p id="status-lokasi" class="text-xs text-gray-500 mb-2"></p>
        <div id="map" class="h-64 w-full rounded border"></div>
    </div>

    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="block font-medium">Latitude</label>
            <input type="text" id="latitude" name="latitude" value="{{ old('latitude', '-3.319400') }}" class="w-full border rounded p-2">
        </div>
        <div>
            <label class="block font-medium">Longitude</label>
            <input type="text" id="longitude" name="longitude" value="{{ old('longitude', '114.590800') }}" class="w-full border rounded p-2">
        </div>
    </div>

    <div class="flex gap-2">
        <button type="submit" class="px-4 py-2 bg-blue-700 text-white rounded">Simpan</button>
        <a href="{{ route('admin.sekolah.index') }}" class="px-4 py-2 bg-gray-500 text-white rounded">Batal</a>
    </div>
</form>
@endsection

@push('scripts')
<script>
    const latInput = document.getElementById('latitude');
    const lngInput = document.getElementById('longitude');

    const defaultLat = parseFloat(latInput.value) || -3.319400;
    const defaultLng = parseFloat(lngInput.value) || 114.590800;

    const map = L.map('map').setView([defaultLat, defaultLng], 13);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap'
    }).addTo(map);

    const marker = L.marker([defaultLat, defaultLng], { draggable: true }).addTo(map);

    function updateCoords(lat, lng) {
        latInput.value = lat.toFixed(8);
        lngInput.value = lng.toFixed(8);
    }

    function updateMapFromInput() {
        const lat = parseFloat(latInput.value);
        const lng = parseFloat(lngInput.value);

        if (!isNaN(lat) && !isNaN(lng)) {
            const newLatLng = new L.LatLng(lat, lng);
            marker.setLatLng(newLatLng);
            map.panTo(newLatLng);
        }
    }

    map.on('click', (e) => {
        marker.setLatLng(e.latlng);
        updateCoords(e.latlng.lat, e.latlng.lng);
    });

    marker.on('dragend', () => {
        const pos = marker.getLatLng();
        updateCoords(pos.lat, pos.lng);
    });

    latInput.addEventListener('input', updateMapFromInput);
    lngInput.addEventListener('input', updateMapFromInput);

    // Feature Navigator Geolocation (Latihan Mandiri 4)
    const btnLokasi = document.getElementById('btn-lokasi-saya');
    const statusLokasi = document.getElementById('status-lokasi');

    btnLokasi.addEventListener('click', () => {
        if (!navigator.geolocation) {
            statusLokasi.textContent = '❌ Fitur Geolocation tidak didukung oleh browser ini.';
            statusLokasi.className = 'text-xs text-red-500 mb-2';
            return;
        }

        statusLokasi.textContent = '⏳ Mengambil titik lokasi...';
        statusLokasi.className = 'text-xs text-blue-500 mb-2';

        navigator.geolocation.getCurrentPosition(
            (position) => {
                const lat = position.coords.latitude;
                const lng = position.coords.longitude;

                updateCoords(lat, lng);
                marker.setLatLng([lat, lng]);
                map.flyTo([lat, lng], 16);

                statusLokasi.textContent = '✅ Lokasi berhasil diperbarui ke posisi Anda!';
                statusLokasi.className = 'text-xs text-green-600 mb-2';
            },
            (error) => {
                let pesanError = 'Gagal memperoleh lokasi.';
                switch (error.code) {
                    case error.PERMISSION_DENIED:
                        pesanError = 'Izin akses lokasi ditolak oleh browser/pengguna.';
                        break;
                    case error.POSITION_UNAVAILABLE:
                        pesanError = 'Informasi lokasi tidak tersedia.';
                        break;
                    case error.TIMEOUT:
                        pesanError = 'Waktu permintaan lokasi habis.';
                        break;
                }
                statusLokasi.textContent = '❌ ' + pesanError;
                statusLokasi.className = 'text-xs text-red-500 mb-2';
            },
            {
                enableHighAccuracy: true,
                timeout: 10000,
                maximumAge: 0
            }
        );
    });
</script>
@endpush