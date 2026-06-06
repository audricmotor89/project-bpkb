<?php

namespace App\Http\Controllers;

use App\Models\Cabang;
use App\Models\JaminanKerja;
use App\Models\LampiranJaminanKerja;
use App\Services\NomorPengajuanService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class JaminanKerjaController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $base = JaminanKerja::when($user->isAdminCabang(), fn($q) => $q->whereIn('cabang_id', $user->cabangIds()));

        $summary = [
            'semua'   => (clone $base)->count(),
            'aktif'   => (clone $base)->where('status', 'AKTIF')->count(),
            'kembali' => (clone $base)->where('status', 'KEMBALI')->count(),
        ];

        $data = $base->with(['cabang'])
            ->when($request->status,     fn($q) => $q->where('status', $request->status))
            ->when($request->jaminan,    function ($q) use ($request) {
                if ($request->jaminan === 'AKTE')   return $q->where('has_akte', true);
                if ($request->jaminan === 'BPKB')   return $q->where('has_bpkb', true);
                if ($request->jaminan === 'IJASAH')  return $q->where('has_ijasah', true);
            })
            ->when($request->tgl_dari,   fn($q) => $q->whereDate('created_at', '>=', $request->tgl_dari))
            ->when($request->tgl_sampai, fn($q) => $q->whereDate('created_at', '<=', $request->tgl_sampai))
            ->when($request->cari,       fn($q) => $q->where(function ($q2) use ($request) {
                $q2->where('no_jaminan', 'like', '%'.$request->cari.'%')
                   ->orWhere('nama_karyawan', 'like', '%'.$request->cari.'%')
                   ->orWhere('no_ktp', 'like', '%'.$request->cari.'%');
            }))
            ->latest()
            ->paginate(20)->withQueryString();

        return view('jaminan-kerja.index', compact('data', 'summary'));
    }

    public function create()
    {
        $user   = Auth::user();
        $cabang = $user->isAdminCabang()
            ? $user->cabangs()->where('aktif', 1)->orderBy('nama_cabang')->get()
            : Cabang::where('aktif', 1)->orderBy('nama_cabang')->get();

        return view('jaminan-kerja.create', compact('cabang'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'cabang_id'         => 'required|exists:cabang,id',
            'nama_karyawan'     => 'required|string|max:100',
            'no_ktp'            => 'required|digits:16',
            'jabatan'           => 'required|string|max:100',
            'no_hp'             => 'nullable|string|max:20',
            'tgl_masuk_kerja'   => 'required|date',
            'has_akte'          => 'nullable|boolean',
            'has_bpkb'          => 'nullable|boolean',
            'has_ijasah'        => 'nullable|boolean',
            'catatan'           => 'nullable|string|max:1000',
            'foto_penerimaan'   => 'required|array|min:1',
            'foto_penerimaan.*' => 'required|file|mimes:jpg,jpeg,png|max:5120',
            'file_akte'         => 'nullable|array',
            'file_akte.*'       => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'file_bpkb'         => 'nullable|array',
            'file_bpkb.*'       => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'file_ijasah'       => 'nullable|array',
            'file_ijasah.*'     => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'file_lainnya'      => 'nullable|array',
            'file_lainnya.*'    => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ], $this->pesanValidasi());

        $hasAkte   = $request->boolean('has_akte');
        $hasBpkb   = $request->boolean('has_bpkb');
        $hasIjasah = $request->boolean('has_ijasah');

        if (!$hasAkte && !$hasBpkb && !$hasIjasah) {
            return back()->withInput()->withErrors(['has_akte' => 'Minimal satu jenis jaminan harus dipilih.']);
        }

        if ($hasAkte && !$request->hasFile('file_akte')) {
            return back()->withInput()->withErrors(['file_akte' => 'File Akte Kelahiran wajib diupload jika jaminan akte dipilih.']);
        }
        if ($hasBpkb && !$request->hasFile('file_bpkb')) {
            return back()->withInput()->withErrors(['file_bpkb' => 'File BPKB wajib diupload jika jaminan BPKB dipilih.']);
        }
        if ($hasIjasah && !$request->hasFile('file_ijasah')) {
            return back()->withInput()->withErrors(['file_ijasah' => 'File Ijasah wajib diupload jika jaminan ijasah dipilih.']);
        }

        $cabang    = Cabang::findOrFail($request->cabang_id);
        $noJaminan = NomorPengajuanService::generateJaminanKerja($cabang->kode_cabang);

        DB::transaction(function () use ($request, $user, $noJaminan, $hasAkte, $hasBpkb, $hasIjasah) {
            $jaminan = JaminanKerja::create([
                'no_jaminan'      => $noJaminan,
                'cabang_id'       => $request->cabang_id,
                'nama_karyawan'   => $request->nama_karyawan,
                'no_ktp'          => $request->no_ktp,
                'jabatan'         => $request->jabatan,
                'no_hp'           => $request->no_hp,
                'tgl_masuk_kerja' => $request->tgl_masuk_kerja,
                'has_akte'        => $hasAkte,
                'has_bpkb'        => $hasBpkb,
                'has_ijasah'      => $hasIjasah,
                'catatan'         => $request->catatan,
                'status'          => 'AKTIF',
                'dibuat_oleh'     => $user->id,
                'diterima_oleh'   => $user->id,
                'tgl_diterima'    => now(),
            ]);

            // Upload dokumen jaminan (multi-file)
            if ($hasAkte && $request->hasFile('file_akte')) {
                foreach ($request->file('file_akte') as $file) {
                    $this->simpanLampiran($jaminan, $file, 'AKTE_KELAHIRAN', $user->id);
                }
            }
            if ($hasBpkb && $request->hasFile('file_bpkb')) {
                foreach ($request->file('file_bpkb') as $file) {
                    $this->simpanLampiran($jaminan, $file, 'BPKB', $user->id);
                }
            }
            if ($hasIjasah && $request->hasFile('file_ijasah')) {
                foreach ($request->file('file_ijasah') as $file) {
                    $this->simpanLampiran($jaminan, $file, 'IJASAH', $user->id);
                }
            }

            // Foto penerimaan (multi-foto, wajib minimal 1)
            foreach ($request->file('foto_penerimaan') as $file) {
                $this->simpanLampiran($jaminan, $file, 'FOTO_PENERIMAAN', $user->id);
            }

            // File tambahan
            if ($request->hasFile('file_lainnya')) {
                foreach ($request->file('file_lainnya') as $file) {
                    $this->simpanLampiran($jaminan, $file, 'LAINNYA', $user->id);
                }
            }
        });

        return redirect()->route('jaminan-kerja.index')
            ->with('success', "Jaminan Kerja $noJaminan berhasil dicatat.");
    }

    public function show(JaminanKerja $jaminanKerja)
    {
        $user = Auth::user();
        if ($user->isAdminCabang() && !in_array($jaminanKerja->cabang_id, $user->cabangIds())) {
            abort(403);
        }
        $jaminanKerja->load(['cabang', 'pembuatnya', 'penerimanya', 'pengembaliannya', 'lampiran.uploader']);
        return view('jaminan-kerja.show', compact('jaminanKerja'));
    }

    public function approvePusat(Request $request, JaminanKerja $jaminanKerja)
    {
        if ($jaminanKerja->status_pusat !== 'MENUNGGU') {
            return back()->with('error', 'Jaminan ini sudah dikonfirmasi sebelumnya.');
        }

        $request->validate([
            'catatan_pusat' => 'nullable|string|max:500',
        ]);

        $jaminanKerja->update([
            'status_pusat'      => 'DITERIMA',
            'catatan_pusat'     => $request->catatan_pusat,
            'dikonfirmasi_oleh' => Auth::id(),
            'tgl_dikonfirmasi'  => now(),
        ]);

        return back()->with('success', "Jaminan {$jaminanKerja->no_jaminan} telah DITERIMA di Kantor Pusat.");
    }

    public function rejectPusat(Request $request, JaminanKerja $jaminanKerja)
    {
        if ($jaminanKerja->status_pusat !== 'MENUNGGU') {
            return back()->with('error', 'Jaminan ini sudah dikonfirmasi sebelumnya.');
        }

        $request->validate([
            'catatan_pusat' => 'required|string|max:500',
        ], [
            'catatan_pusat.required' => 'Alasan penolakan wajib diisi.',
        ]);

        $jaminanKerja->update([
            'status_pusat'      => 'DITOLAK',
            'catatan_pusat'     => $request->catatan_pusat,
            'dikonfirmasi_oleh' => Auth::id(),
            'tgl_dikonfirmasi'  => now(),
        ]);

        return back()->with('success', "Jaminan {$jaminanKerja->no_jaminan} telah DITOLAK.");
    }

    public function kembalikan(Request $request, JaminanKerja $jaminanKerja)
    {
        $user = Auth::user();
        if ($user->isAdminCabang() && !in_array($jaminanKerja->cabang_id, $user->cabangIds())) {
            abort(403);
        }

        if ($jaminanKerja->status === 'KEMBALI') {
            return back()->with('error', 'Jaminan ini sudah dikembalikan sebelumnya.');
        }

        $request->validate([
            'foto_pengembalian'    => 'required|array|min:1',
            'foto_pengembalian.*'  => 'required|file|mimes:jpg,jpeg,png|max:5120',
            'catatan_pengembalian' => 'nullable|string|max:1000',
        ], [
            'foto_pengembalian.required'   => 'Foto pengembalian wajib diupload sebagai bukti.',
            'foto_pengembalian.min'        => 'Minimal 1 foto pengembalian harus diupload.',
            'foto_pengembalian.*.mimes'    => 'Foto pengembalian harus berformat JPG atau PNG.',
            'foto_pengembalian.*.max'      => 'Ukuran foto pengembalian maksimal 5MB.',
        ]);

        DB::transaction(function () use ($request, $user, $jaminanKerja) {
            $jaminanKerja->update([
                'status'               => 'KEMBALI',
                'dikembalikan_oleh'    => $user->id,
                'tgl_dikembalikan'     => now(),
                'catatan_pengembalian' => $request->catatan_pengembalian,
            ]);

            foreach ($request->file('foto_pengembalian') as $file) {
                $this->simpanLampiran($jaminanKerja, $file, 'FOTO_PENGEMBALIAN', $user->id);
            }
        });

        return redirect()->route('jaminan-kerja.show', $jaminanKerja)
            ->with('success', 'Jaminan berhasil dikembalikan ke karyawan.');
    }

    public function edit(JaminanKerja $jaminanKerja)
    {
        $jaminanKerja->load(['cabang', 'lampiran']);
        $cabang = Cabang::where('aktif', 1)->orderBy('nama_cabang')->get();
        return view('jaminan-kerja.edit', compact('jaminanKerja', 'cabang'));
    }

    public function update(Request $request, JaminanKerja $jaminanKerja)
    {
        $request->validate([
            'cabang_id'         => 'required|exists:cabang,id',
            'nama_karyawan'     => 'required|string|max:100',
            'no_ktp'            => 'required|digits:16',
            'jabatan'           => 'required|string|max:100',
            'no_hp'             => 'nullable|string|max:20',
            'tgl_masuk_kerja'   => 'required|date',
            'has_akte'          => 'nullable|boolean',
            'has_bpkb'          => 'nullable|boolean',
            'has_ijasah'        => 'nullable|boolean',
            'catatan'           => 'nullable|string|max:1000',
            'file_akte.*'       => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'file_bpkb.*'       => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'file_ijasah.*'     => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'foto_penerimaan.*' => 'nullable|file|mimes:jpg,jpeg,png|max:5120',
            'file_lainnya.*'    => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ], $this->pesanValidasi());

        $hasAkte   = $request->boolean('has_akte');
        $hasBpkb   = $request->boolean('has_bpkb');
        $hasIjasah = $request->boolean('has_ijasah');

        if (!$hasAkte && !$hasBpkb && !$hasIjasah) {
            return back()->withInput()->withErrors(['has_akte' => 'Minimal satu jenis jaminan harus dipilih.']);
        }

        DB::transaction(function () use ($request, $jaminanKerja, $hasAkte, $hasBpkb, $hasIjasah) {
            $user = Auth::user();

            $jaminanKerja->update([
                'cabang_id'       => $request->cabang_id,
                'nama_karyawan'   => $request->nama_karyawan,
                'no_ktp'          => $request->no_ktp,
                'jabatan'         => $request->jabatan,
                'no_hp'           => $request->no_hp,
                'tgl_masuk_kerja' => $request->tgl_masuk_kerja,
                'has_akte'        => $hasAkte,
                'has_bpkb'        => $hasBpkb,
                'has_ijasah'      => $hasIjasah,
                'catatan'         => $request->catatan,
            ]);

            if ($request->hapus_lampiran) {
                foreach ($request->hapus_lampiran as $lampId) {
                    $lamp = LampiranJaminanKerja::find($lampId);
                    if ($lamp && $lamp->jaminan_kerja_id === $jaminanKerja->id) {
                        Storage::delete('private/jaminan-kerja/' . $lamp->nama_file_storage);
                        $lamp->delete();
                    }
                }
            }

            if ($hasAkte && $request->hasFile('file_akte')) {
                foreach ($request->file('file_akte') as $file) {
                    $this->simpanLampiran($jaminanKerja, $file, 'AKTE_KELAHIRAN', $user->id);
                }
            }
            if ($hasBpkb && $request->hasFile('file_bpkb')) {
                foreach ($request->file('file_bpkb') as $file) {
                    $this->simpanLampiran($jaminanKerja, $file, 'BPKB', $user->id);
                }
            }
            if ($hasIjasah && $request->hasFile('file_ijasah')) {
                foreach ($request->file('file_ijasah') as $file) {
                    $this->simpanLampiran($jaminanKerja, $file, 'IJASAH', $user->id);
                }
            }
            if ($request->hasFile('foto_penerimaan')) {
                foreach ($request->file('foto_penerimaan') as $file) {
                    $this->simpanLampiran($jaminanKerja, $file, 'FOTO_PENERIMAAN', $user->id);
                }
            }
            if ($request->hasFile('file_lainnya')) {
                foreach ($request->file('file_lainnya') as $file) {
                    $this->simpanLampiran($jaminanKerja, $file, 'LAINNYA', $user->id);
                }
            }
        });

        return redirect()->route('jaminan-kerja.show', $jaminanKerja)
            ->with('success', "Jaminan Kerja {$jaminanKerja->no_jaminan} berhasil diperbarui.");
    }

    public function destroy(JaminanKerja $jaminanKerja)
    {
        $noJaminan = $jaminanKerja->no_jaminan;

        DB::transaction(function () use ($jaminanKerja) {
            $jaminanKerja->load('lampiran');
            foreach ($jaminanKerja->lampiran as $lamp) {
                Storage::delete('private/jaminan-kerja/' . $lamp->nama_file_storage);
            }
            $jaminanKerja->lampiran()->delete();
            $jaminanKerja->delete();
        });

        return redirect()->route('jaminan-kerja.index')
            ->with('success', "Jaminan Kerja $noJaminan berhasil dihapus.");
    }

    public function downloadLampiran(LampiranJaminanKerja $lampiran)
    {
        $user = Auth::user();
        $jaminan = $lampiran->jaminanKerja;
        if ($user->isAdminCabang() && !in_array($jaminan->cabang_id, $user->cabangIds())) {
            abort(403);
        }
        $path = 'private/jaminan-kerja/' . $lampiran->nama_file_storage;
        if (!Storage::exists($path)) abort(404);
        return Storage::download($path, $lampiran->nama_file_asli);
    }

    public function previewLampiran(LampiranJaminanKerja $lampiran)
    {
        $user = Auth::user();
        $jaminan = $lampiran->jaminanKerja;
        if ($user->isAdminCabang() && !in_array($jaminan->cabang_id, $user->cabangIds())) {
            abort(403);
        }
        $path = 'private/jaminan-kerja/' . $lampiran->nama_file_storage;
        if (!Storage::exists($path)) abort(404);
        return response()->file(Storage::path($path), [
            'Content-Type'        => $lampiran->mime_type,
            'Content-Disposition' => 'inline; filename="' . $lampiran->nama_file_asli . '"',
        ]);
    }

    private function simpanLampiran(JaminanKerja $jaminan, $file, string $jenis, int $userId): void
    {
        $ext    = $file->getClientOriginalExtension();
        $stored = Str::uuid() . '.' . $ext;
        $file->storeAs('private/jaminan-kerja', $stored);

        LampiranJaminanKerja::create([
            'jaminan_kerja_id'  => $jaminan->id,
            'jenis_dokumen'     => $jenis,
            'nama_file_asli'    => $file->getClientOriginalName(),
            'nama_file_storage' => $stored,
            'ukuran_file'       => $file->getSize(),
            'mime_type'         => $file->getMimeType(),
            'diupload_oleh'     => $userId,
        ]);
    }

    private function pesanValidasi(): array
    {
        return [
            'cabang_id.required'          => 'Cabang wajib dipilih.',
            'nama_karyawan.required'      => 'Nama karyawan wajib diisi.',
            'no_ktp.required'             => 'No. KTP wajib diisi.',
            'no_ktp.digits'               => 'No. KTP harus 16 digit angka.',
            'jabatan.required'            => 'Jabatan wajib diisi.',
            'tgl_masuk_kerja.required'    => 'Tanggal masuk kerja wajib diisi.',
            'foto_penerimaan.required'    => 'Minimal 1 foto penerimaan jaminan wajib diupload.',
            'foto_penerimaan.min'         => 'Minimal 1 foto penerimaan jaminan wajib diupload.',
            'foto_penerimaan.*.required'  => 'File foto tidak valid.',
            'foto_penerimaan.*.mimes'     => 'Foto penerimaan harus berformat JPG atau PNG.',
            'foto_penerimaan.*.max'       => 'Ukuran foto penerimaan maksimal 5MB.',
            'file_akte.*.mimes'           => 'File Akte harus berformat JPG, PNG, atau PDF.',
            'file_akte.*.max'             => 'Ukuran file Akte maksimal 5MB.',
            'file_bpkb.*.mimes'           => 'File BPKB harus berformat JPG, PNG, atau PDF.',
            'file_bpkb.*.max'             => 'Ukuran file BPKB maksimal 5MB.',
            'file_ijasah.*.mimes'         => 'File Ijasah harus berformat JPG, PNG, atau PDF.',
            'file_ijasah.*.max'           => 'Ukuran file Ijasah maksimal 5MB.',
        ];
    }
}
