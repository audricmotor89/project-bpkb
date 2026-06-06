<?php

namespace App\Http\Controllers;

use App\Models\KomentarPengajuan;
use App\Models\Notifikasi;
use App\Models\Pengajuan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KomentarController extends Controller
{
    public function store(Request $request, Pengajuan $pengajuan)
    {
        $request->validate([
            'komentar' => 'required|string|max:2000',
        ], [
            'komentar.required' => 'Komentar tidak boleh kosong.',
        ]);

        $user = Auth::user();

        KomentarPengajuan::create([
            'pengajuan_id' => $pengajuan->id,
            'user_id'      => $user->id,
            'komentar'     => $request->komentar,
        ]);

        // Kirim notifikasi ke pembuat jika yang berkomentar bukan pembuat
        $pembuat = $pengajuan->pembuatnya;
        if ($pembuat && $pembuat->id !== $user->id) {
            Notifikasi::kirim(
                userId:      $pembuat->id,
                judul:       'Komentar Baru',
                pesan:       "{$user->nama_lengkap} menambahkan komentar pada pengajuan {$pengajuan->no_pengajuan}.",
                tipe:        'INFO',
                url:         route('pengajuan.show', $pengajuan),
                pengajuanId: $pengajuan->id,
            );
        }

        // Notifikasi ke admin pusat jika pembuat yang berkomentar
        if ($user->isAdminCabang()) {
            $judul = 'Komentar dari Cabang';
            $pesan = "{$user->nama_lengkap} ({$user->cabang?->nama_cabang}) menambahkan komentar pada {$pengajuan->no_pengajuan}.";
            // Kirim ke semua admin pusat
            \App\Models\User::where('role', 'ADMIN_PUSAT')->each(function ($ap) use ($pengajuan, $judul, $pesan) {
                Notifikasi::kirim(
                    userId:      $ap->id,
                    judul:       $judul,
                    pesan:       $pesan,
                    tipe:        'INFO',
                    url:         route('adminpusat.show', $pengajuan),
                    pengajuanId: $pengajuan->id,
                );
            });
        }

        return back()->with('success', 'Komentar berhasil ditambahkan.');
    }

    public function destroy(KomentarPengajuan $komentar)
    {
        $user = Auth::user();
        // Hanya pembuat komentar atau super admin yang bisa hapus
        if ($komentar->user_id !== $user->id && !$user->isSuperAdmin()) {
            abort(403);
        }
        $komentar->delete();
        return back()->with('success', 'Komentar dihapus.');
    }
}
