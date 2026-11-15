<?php

namespace App\Providers;

use App\Http\Controllers\CommunityChatController;
use App\Models\Company;
use App\Models\School;
use App\Models\University;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('partials.navbar', function ($view) {
            if (Auth::check()) {
                /** @var User $user */
                $user = Auth::user();
                $summary = CommunityChatController::getUserChatSummary($user);
                $view->with('navbarChatSummary', $summary);

                // Ambil logo institusi berdasarkan role agar tampil di navbar
                $navbarLogoUrl = null;
                if ($user->role === 'school') {
                    $institution = School::where('user_id', $user->id)->first();
                    $navbarLogoUrl = $institution->logo_url ?? null;
                } elseif ($user->role === 'company') {
                    $institution = Company::where('user_id', $user->id)->first();
                    $navbarLogoUrl = $institution->logo_url ?? null;
                } elseif ($user->role === 'university') {
                    $institution = University::where('user_id', $user->id)->first();
                    $navbarLogoUrl = $institution->logo_url ?? null;
                }
                $view->with('navbarLogoUrl', $navbarLogoUrl ?? $user->avatar_url);
            } else {
                $view->with('navbarChatSummary', [
                    'communities' => [],
                    'total_unread_count' => 0,
                    'all_chat_url' => route('community.index'),
                ]);
                $view->with('navbarLogoUrl', null);
            }
        });
    }
}
