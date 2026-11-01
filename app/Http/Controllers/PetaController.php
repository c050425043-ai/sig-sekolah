<?php

namespace App\Http\Controllers;

use App\Models\Sekolah;
use Illuminate\Http\Request;

class PetaController extends Controller
{
    public function index()
    {
        return view('peta');
    }

    public function geojson(Request $request)
    {
        $features = Sekolah::query()
            ->when($request->jenjang, fn($q, $j) => $q->where('jenjang', $j))
            ->when($request->status, fn($q, $s) => $q->where('status', $s))
            ->get()
            ->map(fn($s) => [
                'type' => 'Feature',
                'geometry' => [
                    'type' => 'Point',
                    'coordinates' => [(float)$s->longitude, (float)$s->latitude],
                ],
                'properties' => [
                    'npsn' => $s->npsn,
                    'nama' => $s->nama_sekolah ?? $s->nama,
                    'jenjang' => $s->jenjang,
                    'status' => $s->status,
                    'alamat' => $s->alamat,
                    'kecamatan' => $s->kecamatan,
                    'telepon' => $s->telepon ?? null,
                ],
            ]);

        return response()->json([
            'type' => 'FeatureCollection',
            'features' => $features,
        ]);
    }
}