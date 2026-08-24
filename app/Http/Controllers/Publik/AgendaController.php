<?php

namespace App\Http\Controllers\Publik;

use App\Http\Controllers\Controller;
use App\Models\Agenda;
use Illuminate\View\View;

class AgendaController extends Controller
{
    public function index(): View
    {
        $today = today()->toDateString();

        $agenda = Agenda::where('status', 'aktif')
            ->where('target', 'publik')
            ->where('tampil_mulai', '<=', $today)
            ->where(function ($q) use ($today) {
                $q->whereNull('tampil_selesai')->orWhere('tampil_selesai', '>=', $today);
            })
            ->orderByDesc('tanggal')
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('pages.publik.agenda.index', [
            'agenda' => $agenda,
        ]);
    }
}
