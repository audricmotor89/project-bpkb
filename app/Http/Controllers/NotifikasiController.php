<?php

namespace App\Http\Controllers;

use App\Models\Notifikasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotifikasiController extends Controller
{
    public function index()
    {
        $notifikasi = Notifikasi::where('user_id', Auth::user()->id)
            ->latest()->paginate(20);

        Notifikasi::where('user_id', Auth::user()->id)
            ->where('dibaca', false)->update(['dibaca' => true]);

        return view('notifikasi.index', compact('notifikasi'));
    }

    public function baca(int $id)
    {
        Notifikasi::where('id', $id)->where('user_id', Auth::user()->id)->update(['dibaca' => true]);
        return response()->json(['ok' => true]);
    }

    public function bacaSemua()
    {
        Notifikasi::where('user_id', Auth::user()->id)->update(['dibaca' => true]);
        return back()->with('success', 'Semua notifikasi telah ditandai dibaca.');
    }
}
