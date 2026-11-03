<?php

namespace App\Http\Controllers;

use App\Models\UserReport;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserReportController extends Controller
{
    /**
     * Simpan laporan baru dari user.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'category' => ['required', 'string', 'in:Bug,Konten,Teknis,Lainnya'],
            'subject' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:2000'],
            'priority' => ['required', 'string', 'in:low,medium,high'],
        ]);

        UserReport::create([
            ...$validated,
            'user_id' => Auth::id(),
            'status' => 'pending',
        ]);

        return redirect()->route('profile.edit')
            ->with('report_success', 'Laporan Anda berhasil dikirim. Tim kami akan segera meninjau laporan tersebut.');
    }
}
