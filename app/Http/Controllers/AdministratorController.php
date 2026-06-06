<?php

namespace App\Http\Controllers;

use App\Models\Cabang;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AdministratorController extends Controller
{
    // ── CABANG ──────────────────────────────────────────────────────────────

    public function cabangIndex()
    {
        $cabang = Cabang::orderBy('kode_cabang')->paginate(20);
        return view('administrator.cabang.index', compact('cabang'));
    }

    public function cabangStore(Request $request)
    {
        $request->validate([
            'kode_cabang'  => 'required|string|max:10|unique:cabang,kode_cabang',
            'nama_cabang'  => 'required|string|max:100',
            'alamat'       => 'nullable|string',
        ], [
            'kode_cabang.unique' => 'Kode cabang sudah digunakan.',
        ]);

        Cabang::create($request->only('kode_cabang','nama_cabang','alamat') + ['aktif' => 1]);
        return back()->with('success', 'Cabang berhasil ditambahkan.');
    }

    public function cabangUpdate(Request $request, Cabang $cabang)
    {
        $request->validate([
            'kode_cabang' => ['required','string','max:10', Rule::unique('cabang','kode_cabang')->ignore($cabang->id)],
            'nama_cabang' => 'required|string|max:100',
            'alamat'      => 'nullable|string',
            'aktif'       => 'required|boolean',
        ]);

        $cabang->update($request->only('kode_cabang','nama_cabang','alamat','aktif'));
        return back()->with('success', 'Cabang berhasil diperbarui.');
    }

    public function cabangDestroy(Cabang $cabang)
    {
        if ($cabang->pengajuan()->exists()) {
            return back()->with('error', 'Cabang tidak dapat dihapus karena memiliki data pengajuan.');
        }
        $cabang->delete();
        return back()->with('success', 'Cabang berhasil dihapus.');
    }

    // ── USERS ────────────────────────────────────────────────────────────────

    public function userIndex(Request $request)
    {
        $users  = User::with('cabangs')
            ->when($request->role, fn($q) => $q->where('role', $request->role))
            ->orderBy('nama_lengkap')
            ->paginate(20)->withQueryString();
        $cabang = Cabang::where('aktif', 1)->orderBy('nama_cabang')->get();
        return view('administrator.users.index', compact('users', 'cabang'));
    }

    public function userStore(Request $request)
    {
        $needsCabang = in_array($request->role, ['ADMIN_CABANG', 'ADMIN_PUSAT']);

        $request->validate([
            'username'     => 'required|string|max:50|unique:users,username',
            'nama_lengkap' => 'required|string|max:100',
            'role'         => 'required|in:SUPER_ADMIN,ADMIN_PUSAT,ADMIN_CABANG',
            'cabang_ids'   => $needsCabang ? 'required|array|min:1' : 'nullable|array',
            'cabang_ids.*' => 'exists:cabang,id',
            'email'        => 'required|email|unique:users,email',
            'no_whatsapp'  => 'nullable|string|max:20',
            'password'     => 'required|string|min:8|confirmed',
        ], [
            'username.unique'       => 'Username sudah digunakan.',
            'email.unique'          => 'Email sudah digunakan.',
            'cabang_ids.required'   => 'Wajib memilih minimal 1 cabang.',
            'password.min'          => 'Password minimal 8 karakter.',
            'password.confirmed'    => 'Konfirmasi password tidak cocok.',
        ]);

        $cabangIds  = $request->cabang_ids ?? [];
        $primaryId  = $cabangIds[0] ?? null;

        $user = User::create([
            'username'     => $request->username,
            'password'     => Hash::make($request->password),
            'nama_lengkap' => $request->nama_lengkap,
            'role'         => $request->role,
            'cabang_id'    => $primaryId,
            'email'        => $request->email,
            'no_whatsapp'  => $request->no_whatsapp,
            'aktif'        => 1,
        ]);

        $user->cabangs()->sync($cabangIds);

        return back()->with('success', 'Pengguna berhasil ditambahkan.');
    }

    public function userUpdate(Request $request, User $user)
    {
        $needsCabang = in_array($request->role, ['ADMIN_CABANG', 'ADMIN_PUSAT']);

        $request->validate([
            'username'     => ['required','string','max:50', Rule::unique('users','username')->ignore($user->id)],
            'nama_lengkap' => 'required|string|max:100',
            'role'         => 'required|in:SUPER_ADMIN,ADMIN_PUSAT,ADMIN_CABANG',
            'cabang_ids'   => $needsCabang ? 'required|array|min:1' : 'nullable|array',
            'cabang_ids.*' => 'exists:cabang,id',
            'email'        => ['required','email', Rule::unique('users','email')->ignore($user->id)],
            'no_whatsapp'  => 'nullable|string|max:20',
            'aktif'        => 'required|boolean',
            'password'     => 'nullable|string|min:8|confirmed',
        ], [
            'cabang_ids.required' => 'Wajib memilih minimal 1 cabang.',
        ]);

        $cabangIds = $request->cabang_ids ?? [];
        $primaryId = $cabangIds[0] ?? null;

        $data = $request->only('username','nama_lengkap','role','email','no_whatsapp','aktif');
        $data['cabang_id'] = $primaryId;
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);
        $user->cabangs()->sync($cabangIds);

        return back()->with('success', 'Pengguna berhasil diperbarui.');
    }

    public function userDestroy(User $user)
    {
        if ($user->id === auth()->user()->id) {
            return back()->with('error', 'Anda tidak dapat menghapus akun sendiri.');
        }
        $user->delete();
        return back()->with('success', 'Pengguna berhasil dihapus.');
    }
}
