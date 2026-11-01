@extends('layouts.app')
@section('title', 'Ubah Sekolah')

@section('content')
<h1 class="text-2xl font-bold mb-4">Ubah Data Sekolah</h1>

<form action="{{ route('admin.sekolah.update', $sekolah) }}" method="POST" class="bg-white p-6 rounded shadow space-y-4">
    @csrf
    @method('PUT')

    <div>
        <label class="block font-medium">NPSN</label>
        <input type="text" name="npsn" value="{{ old('npsn', $sekolah->npsn) }}" class="w-full border rounded p-2">
        @error('npsn') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
    </div>

    <div>
        <label class="block font-medium">Nama Sekolah</label>
        <input type="text" name="nama_sekolah" value="{{ old('nama_sekolah', $sekolah->nama_sekolah ?? $sekolah->nama) }}" class="w-full border rounded p-2">
        @error('nama_sekolah') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
    </div>

    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="block font-medium">Jenjang</label>
            <select name="jenjang" class="w-full border rounded p-2">
                @foreach(['SD', 'SMP', 'SMA', 'SMK'] as $j)
                    <option value="{{ $j }}" {{ old('jenjang', $sekolah->jenjang) == $j ? 'selected' : '' }}>{{ $j }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block font-medium">Status</label>
            <select name="status" class="w-full border rounded p-2">
                <option value="Negeri" {{ old('status', $sekolah->status) == 'Negeri' ? 'selected' : '' }}>Negeri</option>
                <option value="Swasta" {{ old('status', $sekolah->status) == 'Swasta' ? 'selected' : '' }}>Swasta</option>
            </select>
        </div>
    </div>

    <div>
        <label class="block font-medium">Alamat</label>
        <textarea name="alamat" class="w-full border rounded p-2">{{ old('alamat', $sekolah->alamat) }}</textarea>
    </div>

    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="block font-medium">Kecamatan</label>
            <input type="text" name="kecamatan" value="{{ old('kecamatan', $sekolah->kecamatan) }}" class="w-full border rounded p-2">
        </div>
        <div>
            <label class="block font-medium">Kabupaten/Kota</label>
            <input type="text" name="kabupaten_kota" value="{{ old('kabupaten_kota', $sekolah->kabupaten_kota) }}" class="w-full border rounded p-2">
        </div>
    </div>

    <div>
        <label class="block font-medium mb-1">Geser Marker untuk Mengubah Lokasi:</label>
        <div id="map" class="h-64 w-full rounded border"></div>
    </div>

    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="block font-medium">Latitude</label>
            <input type="text" id="latitude" name="latitude" value="{{ old('latitude', $sekolah->latitude) }}" readonly class="w-full border bg-gray-100 rounded p-2">
        </div>
        <div>
            <label class="block font-medium">Longitude</label>
            <input type="text" id="longitude" name="longitude" value="{{ old('longitude', $sekolah->longitude) }}" readonly class="w-full border bg-gray-100 rounded p-2">
        </div>
    </div>

    <div class="flex gap-2">
        <button type="submit" class="px-4 py-2 bg-blue-700 text-white rounded">Perbarui</button>
        <a href="{{ route('admin.sekolah.index') }}" class="px-4 py-2 bg-gray-500 text-white rounded">Batal</a>
    </div>
</form>
@endsection

@push('scripts')
<script>
    const defaultLat = parseFloat(document.getElementById('latitude').value);
    const defaultLng = parseFloat(document.getElementById('longitude').value);

    const map = L.map('map').setView([defaultLat, defaultLng], 15);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap'
    }).addTo(map);

    const marker = L.marker([defaultLat, defaultLng], { draggable: true }).addTo(map);

    function updateCoords(lat, lng) {
        document.getElementById('latitude').value = lat.toFixed(8);
        document.getElementById('longitude').value = lng.toFixed(8);
    }

    map.on('click', (e) => {
        marker.setLatLng(e.latlng);
        updateCoords(e.latlng.lat, e.latlng.lng);
    });

    marker.on('dragend', () => {
        const pos = marker.getLatLng();
        updateCoords(pos.lat, pos.lng);
    });
</script>
@endpush