<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\Cabang;
use App\Models\LampiranDokumen;
use App\Models\Pengajuan;
use App\Services\NomorPengajuanService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PengajuanController extends Controller
{
    public function index(Request $request)
    {
        $user  = Auth::user();
        $base  = Pengajuan::when($user->isAdminCabang(), fn($q) => $q->whereIn('cabang_id', $user->cabangIds()));

        $summary = [
            'semua'     => (clone $base)->count(),
            'menunggu'  => (clone $base)->where('status', 'MENUNGGU')->count(),
            'diproses'  => (clone $base)->where('status', 'DIPROSES')->count(),
            'disetujui' => (clone $base)->where('status', 'DISETUJUI')->count(),
            'ditolak'   => (clone $base)->where('status', 'DITOLAK')->count(),
        ];

        $pengajuan = $base->with(['cabang', 'detailBpkb', 'detailSertifikat'])
            ->when($request->status,      fn($q) => $q->where('status', $request->status))
            ->when($request->jenis,       fn($q) => $q->where('jenis_jaminan', $request->jenis))
            ->when($request->tgl_dari,    fn($q) => $q->whereDate('tgl_dibuat', '>=', $request->tgl_dari))
            ->when($request->tgl_sampai,  fn($q) => $q->whereDate('tgl_dibuat', '<=', $request->tgl_sampai))
            ->when($request->cari,        fn($q) => $q->where(function($q2) use ($request) {
                $q2->where('no_pengajuan', 'like', '%'.$request->cari.'%')
                   ->orWhereHas('detailBpkb',        fn($d) => $d->where('nama_nasabah', 'like', '%'.$request->cari.'%'))
                   ->orWhereHas('detailSertifikat',   fn($d) => $d->where('nama_nasabah', 'like', '%'.$request->cari.'%'));
            }))
            ->latest('tgl_dibuat')
            ->paginate(20)->withQueryString();

        return view('pengajuan.index', compact('pengajuan', 'summary'));
    }

    public function createBpkb()
    {
        $user   = Auth::user();
        $cabang = $user->isAdminCabang()
            ? $user->cabangs()->where('aktif', 1)->orderBy('nama_cabang')->get()
            : Cabang::where('aktif', 1)->orderBy('nama_cabang')->get();
        return view('pengajuan.create-bpkb', compact('cabang'));
    }

    public function createSertifikat()
    {
        $user   = Auth::user();
        $cabang = $user->isAdminCabang()
            ? $user->cabangs()->where('aktif', 1)->orderBy('nama_cabang')->get()
            : Cabang::where('aktif', 1)->orderBy('nama_cabang')->get();
        return view('pengajuan.create-sertifikat', compact('cabang'));
    }

    public function storeBpkb(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'cabang_id'         => 'required|exists:cabang,id',
            'nama_nasabah'      => 'required|string|max:100',
            'no_ktp'            => 'required|digits:16',
            'no_polisi'         => 'required|string|max:15',
            'merek_motor'       => 'required|string|max:50',
            'tipe_motor'        => 'required|string|max:50',
            'no_bpkb'           => 'required|string|max:30',
            'no_mesin'          => 'required|string|max:30',
            'no_rangka'         => 'required|string|max:30',
            'total_pinjaman'    => 'required|numeric|min:1',
            'no_kartu_piutang'  => 'required|string|max:30',
            'file_bpkb'         => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'file_ktp'          => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'file_lainnya.*'    => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ], $this->pesanValidasi());

        // Cek duplikasi no_kartu_piutang
        if (Pengajuan::whereHas('detailBpkb', fn($q) => $q->where('no_kartu_piutang', $request->no_kartu_piutang))->exists()) {
            return back()->withInput()->withErrors(['no_kartu_piutang' => 'No. Kartu Piutang ini sudah pernah diajukan.']);
        }

        $cabang   = Cabang::findOrFail($request->cabang_id);
        $noPengajuan = NomorPengajuanService::generate($cabang->kode_cabang, 'BPKB');

        DB::transaction(function () use ($request, $user, $noPengajuan) {
            $pengajuan = Pengajuan::create([
                'no_pengajuan'  => $noPengajuan,
                'jenis_jaminan' => 'BPKB',
                'cabang_id'     => $request->cabang_id,
                'dibuat_oleh'   => $user->id,
                'status'        => 'MENUNGGU',
            ]);

            $pengajuan->detailBpkb()->create([
                'nama_nasabah'     => $request->nama_nasabah,
                'no_ktp'           => $request->no_ktp,
                'no_polisi'        => strtoupper($request->no_polisi),
                'merek_motor'      => $request->merek_motor,
                'tipe_motor'       => $request->tipe_motor,
                'no_bpkb'          => $request->no_bpkb,
                'no_mesin'         => $request->no_mesin,
                'no_rangka'        => $request->no_rangka,
                'total_pinjaman'   => $request->total_pinjaman,
                'no_kartu_piutang' => $request->no_kartu_piutang,
            ]);

            $this->simpanLampiran($pengajuan, $request->file('file_bpkb'), 'BPKB', $user->id);
            $this->simpanLampiran($pengajuan, $request->file('file_ktp'), 'KTP', $user->id);

            if ($request->hasFile('file_lainnya')) {
                foreach ($request->file('file_lainnya') as $file) {
                    $this->simpanLampiran($pengajuan, $file, 'LAINNYA', $user->id);
                }
            }

            AuditLog::create([
                'pengajuan_id' => $pengajuan->id,
                'user_id'      => $user->id,
                'aksi'         => 'BUAT',
                'status_baru'  => 'MENUNGGU',
                'keterangan'   => 'Pengajuan BPKB dibuat oleh ' . $user->nama_lengkap,
                'ip_address'   => request()->ip(),
            ]);
        });

        return redirect()->route('pengajuan.index')
            ->with('success', "Pengajuan $noPengajuan berhasil dibuat.");
    }

    public function storeSertifikat(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'cabang_id'         => 'required|exists:cabang,id',
            'nama_nasabah'      => 'required|string|max:100',
            'no_ktp'            => 'required|digits:16',
            'no_sertifikat'     => 'required|string|max:50',
            'total_pinjaman'    => 'required|numeric|min:1',
            'no_kartu_piutang'  => 'required|string|max:30',
            'file_sertifikat'   => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'file_ktp'          => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'file_lainnya.*'    => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ], $this->pesanValidasi());

        if (Pengajuan::whereHas('detailSertifikat', fn($q) => $q->where('no_kartu_piutang', $request->no_kartu_piutang))->exists()) {
            return back()->withInput()->withErrors(['no_kartu_piutang' => 'No. Kartu Piutang ini sudah pernah diajukan.']);
        }

        $cabang      = Cabang::findOrFail($request->cabang_id);
        $noPengajuan = NomorPengajuanService::generate($cabang->kode_cabang, 'SERTIFIKAT');

        DB::transaction(function () use ($request, $user, $noPengajuan) {
            $pengajuan = Pengajuan::create([
                'no_pengajuan'  => $noPengajuan,
                'jenis_jaminan' => 'SERTIFIKAT',
                'cabang_id'     => $request->cabang_id,
                'dibuat_oleh'   => $user->id,
                'status'        => 'MENUNGGU',
            ]);

            $pengajuan->detailSertifikat()->create([
                'nama_nasabah'     => $request->nama_nasabah,
                'no_ktp'           => $request->no_ktp,
                'no_sertifikat'    => $request->no_sertifikat,
                'total_pinjaman'   => $request->total_pinjaman,
                'no_kartu_piutang' => $request->no_kartu_piutang,
            ]);

            $this->simpanLampiran($pengajuan, $request->file('file_sertifikat'), 'SERTIFIKAT', $user->id);
            $this->simpanLampiran($pengajuan, $request->file('file_ktp'), 'KTP', $user->id);

            if ($request->hasFile('file_lainnya')) {
                foreach ($request->file('file_lainnya') as $file) {
                    $this->simpanLampiran($pengajuan, $file, 'LAINNYA', $user->id);
                }
            }

            AuditLog::create([
                'pengajuan_id' => $pengajuan->id,
                'user_id'      => $user->id,
                'aksi'         => 'BUAT',
                'status_baru'  => 'MENUNGGU',
                'keterangan'   => 'Pengajuan Sertifikat dibuat oleh ' . $user->nama_lengkap,
                'ip_address'   => request()->ip(),
            ]);
        });

        return redirect()->route('pengajuan.index')
            ->with('success', "Pengajuan $noPengajuan berhasil dibuat.");
    }

    public function show(Pengajuan $pengajuan)
    {
        $user = Auth::user();
        if ($user->isAdminCabang() && !in_array($pengajuan->cabang_id, $user->cabangIds())) {
            abort(403);
        }
        $pengajuan->load(['cabang','pembuatnya','pemrosesnya','detailBpkb','detailSertifikat','lampiran.uploader','auditLog.user','komentar.user']);
        return view('pengajuan.show', compact('pengajuan'));
    }

    public function edit(Pengajuan $pengajuan)
    {
        $pengajuan->load(['cabang','detailBpkb','detailSertifikat','lampiran']);
        $cabang = Cabang::where('aktif', 1)->orderBy('nama_cabang')->get();
        return view('pengajuan.edit', compact('pengajuan', 'cabang'));
    }

    public function update(Request $request, Pengajuan $pengajuan)
    {
        $isBpkb = $pengajuan->jenis_jaminan === 'BPKB';

        // Validasi field umum
        $rules = [
            'cabang_id'        => 'required|exists:cabang,id',
            'nama_nasabah'     => 'required|string|max:100',
            'no_ktp'           => 'required|digits:16',
            'total_pinjaman'   => 'required|numeric|min:1',
            'no_kartu_piutang' => 'required|string|max:30',
        ];

        if ($isBpkb) {
            $rules += [
                'no_polisi'   => 'required|string|max:15',
                'merek_motor' => 'required|string|max:50',
                'tipe_motor'  => 'required|string|max:50',
                'no_bpkb'     => 'required|string|max:30',
                'no_mesin'    => 'required|string|max:30',
                'no_rangka'   => 'required|string|max:30',
            ];
        } else {
            $rules['no_sertifikat'] = 'required|string|max:50';
        }

        // File opsional (hanya jika di-upload)
        $rules['file_tambahan.*'] = 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120';

        $request->validate($rules);

        // Cek duplikasi no_kartu_piutang (kecuali diri sendiri)
        $dupQuery = $isBpkb
            ? \App\Models\Pengajuan::whereHas('detailBpkb', fn($q) => $q->where('no_kartu_piutang', $request->no_kartu_piutang))
            : \App\Models\Pengajuan::whereHas('detailSertifikat', fn($q) => $q->where('no_kartu_piutang', $request->no_kartu_piutang));

        if ($dupQuery->where('id', '!=', $pengajuan->id)->exists()) {
            return back()->withInput()->withErrors(['no_kartu_piutang' => 'No. Kartu Piutang ini sudah digunakan pengajuan lain.']);
        }

        DB::transaction(function () use ($request, $pengajuan, $isBpkb) {
            $user = Auth::user();

            // Catat perubahan untuk audit
            $sebelum = $isBpkb
                ? $pengajuan->detailBpkb->toArray()
                : $pengajuan->detailSertifikat->toArray();

            // Update cabang
            $pengajuan->update(['cabang_id' => $request->cabang_id]);

            // Update detail
            if ($isBpkb) {
                $pengajuan->detailBpkb->update([
                    'nama_nasabah'     => $request->nama_nasabah,
                    'no_ktp'           => $request->no_ktp,
                    'no_polisi'        => strtoupper($request->no_polisi),
                    'merek_motor'      => $request->merek_motor,
                    'tipe_motor'       => $request->tipe_motor,
                    'no_bpkb'          => $request->no_bpkb,
                    'no_mesin'         => $request->no_mesin,
                    'no_rangka'        => $request->no_rangka,
                    'total_pinjaman'   => $request->total_pinjaman,
                    'no_kartu_piutang' => $request->no_kartu_piutang,
                ]);
            } else {
                $pengajuan->detailSertifikat->update([
                    'nama_nasabah'     => $request->nama_nasabah,
                    'no_ktp'           => $request->no_ktp,
                    'no_sertifikat'    => $request->no_sertifikat,
                    'total_pinjaman'   => $request->total_pinjaman,
                    'no_kartu_piutang' => $request->no_kartu_piutang,
                ]);
            }

            // Simpan file tambahan jika ada
            if ($request->hasFile('file_tambahan')) {
                foreach ($request->file('file_tambahan') as $file) {
                    $this->simpanLampiran($pengajuan, $file, 'LAINNYA', $user->id);
                }
            }

            // Hapus lampiran yang diminta
            if ($request->hapus_lampiran) {
                foreach ($request->hapus_lampiran as $lampId) {
                    $lamp = \App\Models\LampiranDokumen::find($lampId);
                    if ($lamp && $lamp->pengajuan_id === $pengajuan->id) {
                        Storage::delete('private/lampiran/' . $lamp->nama_file_storage);
                        $lamp->delete();
                    }
                }
            }

            AuditLog::create([
                'pengajuan_id' => $pengajuan->id,
                'user_id'      => $user->id,
                'aksi'         => 'EDIT',
                'status_lama'  => $pengajuan->status,
                'status_baru'  => $pengajuan->status,
                'keterangan'   => 'Data dikoreksi oleh Super Admin: ' . $user->nama_lengkap,
                'ip_address'   => request()->ip(),
            ]);
        });

        return redirect()->route('pengajuan.show', $pengajuan)
            ->with('success', "Pengajuan {$pengajuan->no_pengajuan} berhasil diperbarui.");
    }

    public function destroy(Pengajuan $pengajuan)
    {
        $noPengajuan = $pengajuan->no_pengajuan;

        DB::transaction(function () use ($pengajuan) {
            $pengajuan->load('lampiran');
            foreach ($pengajuan->lampiran as $lamp) {
                Storage::delete('private/lampiran/' . $lamp->nama_file_storage);
            }
            $pengajuan->lampiran()->delete();
            $pengajuan->auditLog()->delete();
            $pengajuan->komentar()->delete();
            $pengajuan->detailBpkb()->delete();
            $pengajuan->detailSertifikat()->delete();
            $pengajuan->delete();
        });

        return redirect()->route('pengajuan.index')
            ->with('success', "Pengajuan $noPengajuan berhasil dihapus.");
    }

    public function downloadLampiran(LampiranDokumen $lampiran)
    {
        $user = Auth::user();
        $pengajuan = $lampiran->pengajuan;
        if ($user->isAdminCabang() && !in_array($pengajuan->cabang_id, $user->cabangIds())) {
            abort(403);
        }
        $path = 'private/lampiran/' . $lampiran->nama_file_storage;
        if (!Storage::exists($path)) abort(404);
        return Storage::download($path, $lampiran->nama_file_asli);
    }

    public function previewLampiran(LampiranDokumen $lampiran)
    {
        $user = Auth::user();
        $pengajuan = $lampiran->pengajuan;
        if ($user->isAdminCabang() && !in_array($pengajuan->cabang_id, $user->cabangIds())) {
            abort(403);
        }
        $path = 'private/lampiran/' . $lampiran->nama_file_storage;
        if (!Storage::exists($path)) abort(404);
        return response()->file(Storage::path($path), [
            'Content-Type'        => $lampiran->mime_type,
            'Content-Disposition' => 'inline; filename="' . $lampiran->nama_file_asli . '"',
        ]);
    }

    public function downloadZip(Pengajuan $pengajuan)
    {
        $user = Auth::user();
        if ($user->isAdminCabang() && !in_array($pengajuan->cabang_id, $user->cabangIds())) {
            abort(403);
        }

        $lampiran = $pengajuan->lampiran;
        if ($lampiran->isEmpty()) {
            return back()->with('error', 'Tidak ada lampiran untuk diunduh.');
        }

        $zipName = 'lampiran-' . $pengajuan->no_pengajuan . '.zip';
        $zipPath = storage_path('app/private/temp/' . $zipName);
        @mkdir(dirname($zipPath), 0755, true);

        $zip = new \ZipArchive();
        $zip->open($zipPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);

        foreach ($lampiran as $lamp) {
            $filePath = Storage::path('private/lampiran/' . $lamp->nama_file_storage);
            if (file_exists($filePath)) {
                $zip->addFile($filePath, $lamp->nama_file_asli);
            }
        }

        $zip->close();

        return response()->download($zipPath, $zipName)->deleteFileAfterSend();
    }

    private function simpanLampiran(Pengajuan $pengajuan, $file, string $jenis, int $userId): void
    {
        $ext      = $file->getClientOriginalExtension();
        $stored   = Str::uuid() . '.' . $ext;
        $file->storeAs('private/lampiran', $stored);

        LampiranDokumen::create([
            'pengajuan_id'      => $pengajuan->id,
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
            'cabang_id.required'        => 'Cabang wajib dipilih.',
            'nama_nasabah.required'     => 'Nama nasabah wajib diisi.',
            'no_ktp.required'           => 'No. KTP wajib diisi.',
            'no_ktp.digits'             => 'No. KTP harus 16 digit angka.',
            'no_polisi.required'        => 'No. polisi wajib diisi.',
            'merek_motor.required'      => 'Merek motor wajib diisi.',
            'tipe_motor.required'       => 'Tipe motor wajib diisi.',
            'no_bpkb.required'          => 'No. BPKB wajib diisi.',
            'no_mesin.required'         => 'No. mesin wajib diisi.',
            'no_rangka.required'        => 'No. rangka wajib diisi.',
            'no_sertifikat.required'    => 'No. sertifikat wajib diisi.',
            'total_pinjaman.required'   => 'Total pinjaman wajib diisi.',
            'total_pinjaman.numeric'    => 'Total pinjaman harus berupa angka.',
            'no_kartu_piutang.required' => 'No. kartu piutang wajib diisi.',
            'file_bpkb.required'        => 'File BPKB wajib diupload.',
            'file_sertifikat.required'  => 'File sertifikat wajib diupload.',
            'file_ktp.required'         => 'File KTP nasabah wajib diupload.',
            'file_bpkb.mimes'           => 'File BPKB harus berformat JPG, PNG, atau PDF.',
            'file_sertifikat.mimes'     => 'File sertifikat harus berformat JPG, PNG, atau PDF.',
            'file_ktp.mimes'            => 'File KTP harus berformat JPG, PNG, atau PDF.',
            'file_bpkb.max'             => 'Ukuran file BPKB maksimal 5MB.',
            'file_sertifikat.max'       => 'Ukuran file sertifikat maksimal 5MB.',
            'file_ktp.max'              => 'Ukuran file KTP maksimal 5MB.',
        ];
    }
}
