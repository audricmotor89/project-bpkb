<?php

namespace App\Http\Controllers;

use App\Models\Cabang;
use App\Models\LampiranReimburse;
use App\Models\Reimburse;
use App\Services\NomorPengajuanService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ReimburseController extends Controller
{
    public function index(Request $request)
    {
        $user  = Auth::user();
        $query = Reimburse::with(['cabang', 'pembuatnya'])
            ->when($user->isAdminCabang(), fn($q) => $q->whereIn('cabang_id', $user->cabangIds()))
            ->when($request->status,        fn($q) => $q->where('status', $request->status))
            ->when($request->kategori,      fn($q) => $q->where('kategori', $request->kategori))
            ->when($request->tgl_dari,      fn($q) => $q->whereDate('tanggal_pengeluaran', '>=', $request->tgl_dari))
            ->when($request->tgl_sampai,    fn($q) => $q->whereDate('tanggal_pengeluaran', '<=', $request->tgl_sampai))
            ->when($request->cari, fn($q) => $q->where(function ($s) use ($request) {
                $s->where('no_reimburse', 'like', '%'.$request->cari.'%')
                  ->orWhere('nama_pemohon', 'like', '%'.$request->cari.'%')
                  ->orWhere('keterangan', 'like', '%'.$request->cari.'%');
            }))
            ->latest();

        $reimburse = $query->paginate(20)->withQueryString();

        $summary = [
            'menunggu'  => (clone $query->getQuery())->where('status', 'MENUNGGU')->count(),
            'disetujui' => Reimburse::when($user->isAdminCabang(), fn($q) => $q->whereIn('cabang_id', $user->cabangIds()))->where('status', 'DISETUJUI')->count(),
            'ditolak'   => Reimburse::when($user->isAdminCabang(), fn($q) => $q->whereIn('cabang_id', $user->cabangIds()))->where('status', 'DITOLAK')->count(),
        ];

        return view('reimburse.index', compact('reimburse', 'summary'));
    }

    public function create()
    {
        $user     = Auth::user();
        $cabang   = $user->isAdminCabang()
            ? $user->cabangs()->where('aktif', 1)->orderBy('nama_cabang')->get()
            : Cabang::where('aktif', 1)->orderBy('nama_cabang')->get();
        $kategori = Reimburse::labelKategori();
        return view('reimburse.create', compact('cabang', 'kategori'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'cabang_id'                           => 'required|exists:cabang,id',
            'nama_pemohon'                        => 'required|string|max:100',
            'jabatan'                             => 'nullable|string|max:100',
            'items'                               => 'required|array|min:1',
            'items.*.tanggal_pengeluaran'         => 'required|date|before_or_equal:today',
            'items.*.kategori'                    => 'required|in:TRANSPORT,MAKAN,AKOMODASI,OPERASIONAL,LAINNYA',
            'items.*.keterangan'                  => 'required|string|max:1000',
            'items.*.nominal_diajukan'            => 'required|numeric|min:1',
        ], [
            'cabang_id.required'                          => 'Cabang wajib dipilih.',
            'nama_pemohon.required'                       => 'Nama pemohon wajib diisi.',
            'items.required'                              => 'Minimal 1 item reimburse wajib diisi.',
            'items.*.tanggal_pengeluaran.required'        => 'Tanggal pengeluaran wajib diisi.',
            'items.*.tanggal_pengeluaran.before_or_equal' => 'Tanggal pengeluaran tidak boleh lebih dari hari ini.',
            'items.*.kategori.required'                   => 'Kategori wajib dipilih.',
            'items.*.keterangan.required'                 => 'Keterangan wajib diisi.',
            'items.*.nominal_diajukan.required'           => 'Nominal wajib diisi.',
            'items.*.nominal_diajukan.numeric'            => 'Nominal harus berupa angka.',
        ]);

        // Validate that each item has at least 1 lampiran file
        $items = $request->input('items', []);
        foreach (array_keys($items) as $idx) {
            if (!$request->hasFile("lampiran_{$idx}")) {
                return back()->withInput()->withErrors(["items.{$idx}.lampiran" => "Item #" . ($idx + 1) . ": Minimal 1 lampiran bukti wajib diupload."]);
            }
        }

        $cabang  = Cabang::findOrFail($request->cabang_id);
        $batchId = (string) Str::uuid();
        $count   = count($items);
        $created = [];

        DB::transaction(function () use ($request, $user, $cabang, $batchId, $items, &$created) {
            foreach (array_keys($items) as $idx) {
                $item        = $items[$idx];
                $noReimburse = NomorPengajuanService::generateReimburse($cabang->kode_cabang);

                $reimburse = Reimburse::create([
                    'no_reimburse'        => $noReimburse,
                    'batch_id'            => $batchId,
                    'cabang_id'           => $request->cabang_id,
                    'dibuat_oleh'         => $user->id,
                    'nama_pemohon'        => $request->nama_pemohon,
                    'jabatan'             => $request->jabatan,
                    'tanggal_pengeluaran' => $item['tanggal_pengeluaran'],
                    'kategori'            => $item['kategori'],
                    'keterangan'          => $item['keterangan'],
                    'nominal_diajukan'    => $item['nominal_diajukan'],
                    'status'              => 'MENUNGGU',
                ]);

                $files  = $request->file("lampiran_{$idx}", []);
                $jeniss = $request->input("jenis_lampiran_{$idx}", []);
                foreach ($files as $i => $file) {
                    $jenis  = $jeniss[$i] ?? 'LAINNYA';
                    $ext    = $file->getClientOriginalExtension();
                    $stored = Str::uuid() . '.' . $ext;
                    $file->storeAs('private/reimburse', $stored);
                    LampiranReimburse::create([
                        'reimburse_id'      => $reimburse->id,
                        'jenis_dokumen'     => $jenis,
                        'nama_file_asli'    => $file->getClientOriginalName(),
                        'nama_file_storage' => $stored,
                        'ukuran_file'       => $file->getSize(),
                        'mime_type'         => $file->getMimeType(),
                        'diupload_oleh'     => $user->id,
                    ]);
                }

                $created[] = $noReimburse;
            }
        });

        $msg = count($created) === 1
            ? "Pengajuan reimburse {$created[0]} berhasil dibuat."
            : count($created) . " pengajuan reimburse berhasil dibuat dalam 1 batch.";

        return redirect()->route('reimburse.index')->with('success', $msg);
    }

    public function show(Reimburse $reimburse)
    {
        $user = Auth::user();
        if ($user->isAdminCabang() && !in_array($reimburse->cabang_id, $user->cabangIds())) {
            abort(403);
        }
        $reimburse->load(['cabang', 'pembuatnya', 'pemrosesnya', 'lampiran.uploader']);
        return view('reimburse.show', compact('reimburse'));
    }

    public function edit(Reimburse $reimburse)
    {
        $user = Auth::user();
        if ($user->isAdminCabang() && !in_array($reimburse->cabang_id, $user->cabangIds())) {
            abort(403);
        }
        $reimburse->load(['lampiran']);
        $cabang   = Cabang::where('aktif', 1)->orderBy('nama_cabang')->get();
        $kategori = Reimburse::labelKategori();
        return view('reimburse.edit', compact('reimburse', 'cabang', 'kategori'));
    }

    public function update(Request $request, Reimburse $reimburse)
    {
        $user = Auth::user();
        if ($user->isAdminCabang() && !in_array($reimburse->cabang_id, $user->cabangIds())) {
            abort(403);
        }

        $request->validate([
            'cabang_id'              => 'required|exists:cabang,id',
            'nama_pemohon'           => 'required|string|max:100',
            'jabatan'                => 'nullable|string|max:100',
            'tanggal_pengeluaran'    => 'required|date|before_or_equal:today',
            'kategori'               => 'required|in:TRANSPORT,MAKAN,AKOMODASI,OPERASIONAL,LAINNYA',
            'keterangan'             => 'required|string|max:1000',
            'nominal_diajukan'       => 'required|numeric|min:1',
            'lampiran_baru.*'        => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'jenis_lampiran_baru.*'  => 'nullable|in:KWITANSI,STRUK,FOTO,LAINNYA',
        ], [
            'cabang_id.required'            => 'Cabang wajib dipilih.',
            'nama_pemohon.required'         => 'Nama pemohon wajib diisi.',
            'tanggal_pengeluaran.required'  => 'Tanggal pengeluaran wajib diisi.',
            'tanggal_pengeluaran.before_or_equal' => 'Tanggal pengeluaran tidak boleh lebih dari hari ini.',
            'kategori.required'             => 'Kategori wajib dipilih.',
            'keterangan.required'           => 'Keterangan wajib diisi.',
            'nominal_diajukan.required'     => 'Nominal wajib diisi.',
            'nominal_diajukan.numeric'      => 'Nominal harus berupa angka.',
        ]);

        DB::transaction(function () use ($request, $reimburse, $user) {
            $reimburse->update([
                'cabang_id'           => $request->cabang_id,
                'nama_pemohon'        => $request->nama_pemohon,
                'jabatan'             => $request->jabatan,
                'tanggal_pengeluaran' => $request->tanggal_pengeluaran,
                'kategori'            => $request->kategori,
                'keterangan'          => $request->keterangan,
                'nominal_diajukan'    => $request->nominal_diajukan,
            ]);

            if ($request->hapus_lampiran) {
                foreach ($request->hapus_lampiran as $lampId) {
                    $lamp = LampiranReimburse::find($lampId);
                    if ($lamp && $lamp->reimburse_id === $reimburse->id) {
                        Storage::delete('private/reimburse/' . $lamp->nama_file_storage);
                        $lamp->delete();
                    }
                }
            }

            if ($request->hasFile('lampiran_baru')) {
                foreach ($request->file('lampiran_baru') as $i => $file) {
                    $jenis  = $request->jenis_lampiran_baru[$i] ?? 'LAINNYA';
                    $ext    = $file->getClientOriginalExtension();
                    $stored = Str::uuid() . '.' . $ext;
                    $file->storeAs('private/reimburse', $stored);
                    LampiranReimburse::create([
                        'reimburse_id'      => $reimburse->id,
                        'jenis_dokumen'     => $jenis,
                        'nama_file_asli'    => $file->getClientOriginalName(),
                        'nama_file_storage' => $stored,
                        'ukuran_file'       => $file->getSize(),
                        'mime_type'         => $file->getMimeType(),
                        'diupload_oleh'     => $user->id,
                    ]);
                }
            }
        });

        return redirect()->route('reimburse.show', $reimburse)
            ->with('success', "Reimburse {$reimburse->no_reimburse} berhasil diperbarui.");
    }

    public function destroy(Reimburse $reimburse)
    {
        $noReimburse = $reimburse->no_reimburse;

        DB::transaction(function () use ($reimburse) {
            $reimburse->load('lampiran');
            foreach ($reimburse->lampiran as $lamp) {
                Storage::delete('private/reimburse/' . $lamp->nama_file_storage);
            }
            $reimburse->lampiran()->delete();
            $reimburse->delete();
        });

        return redirect()->route('reimburse.index')
            ->with('success', "Reimburse $noReimburse berhasil dihapus.");
    }

    public function downloadLampiran(LampiranReimburse $lampiran)
    {
        $user = Auth::user();
        if ($user->isAdminCabang() && !in_array($lampiran->reimburse->cabang_id, $user->cabangIds())) {
            abort(403);
        }
        $path = 'private/reimburse/' . $lampiran->nama_file_storage;
        if (!Storage::exists($path)) abort(404);
        return Storage::download($path, $lampiran->nama_file_asli);
    }

    public function previewLampiran(LampiranReimburse $lampiran)
    {
        $user = Auth::user();
        if ($user->isAdminCabang() && !in_array($lampiran->reimburse->cabang_id, $user->cabangIds())) {
            abort(403);
        }
        $path = 'private/reimburse/' . $lampiran->nama_file_storage;
        if (!Storage::exists($path)) abort(404);
        return response()->file(Storage::path($path), [
            'Content-Type'        => $lampiran->mime_type,
            'Content-Disposition' => 'inline; filename="' . $lampiran->nama_file_asli . '"',
        ]);
    }
}
