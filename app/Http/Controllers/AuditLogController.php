<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;

class AuditLogController extends Controller
{
    public function index(Request $request)
    {
        $users = User::orderBy('nama_lengkap')->get();

        $logs = AuditLog::with(['pengajuan', 'user'])
            ->when($request->aksi,     fn($q) => $q->where('aksi', $request->aksi))
            ->when($request->user_id,  fn($q) => $q->where('user_id', $request->user_id))
            ->when($request->status,   fn($q) => $q->where('status_baru', $request->status))
            ->when($request->tgl_dari, fn($q) => $q->whereDate('created_at', '>=', $request->tgl_dari))
            ->when($request->tgl_sampai, fn($q) => $q->whereDate('created_at', '<=', $request->tgl_sampai))
            ->latest()
            ->paginate(30)
            ->withQueryString();

        return view('audit_log.index', compact('logs', 'users'));
    }
}
