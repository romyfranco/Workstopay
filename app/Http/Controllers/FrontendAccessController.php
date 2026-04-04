<?php

namespace App\Http\Controllers;

use App\Enums\VehicleWorkStatus;
use App\Models\FrontendPassword;
use App\Models\VehicleWork;
use App\Services\AccessLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FrontendAccessController extends Controller
{
    public function showLogin(): View
    {
        return view('frontend.access', [
            'hasActivePasswords' => FrontendPassword::query()->where('is_active', true)->exists(),
        ]);
    }

    public function login(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'password' => ['required', 'string'],
        ]);

        $password = FrontendPassword::query()
            ->where('is_active', true)
            ->get()
            ->first(fn (FrontendPassword $frontendPassword): bool => $frontendPassword->matches($validated['password']));

        if (! $password) {
            return back()
                ->withInput()
                ->withErrors([
                    'password' => 'La contrasena no es valida o no se encuentra activa.',
                ]);
        }

        app(AccessLogService::class)->logFrontendPasswordUse($password, $request);

        $request->session()->put('frontend_access_granted', true);
        $request->session()->put('frontend_access_name', $password->name);
        $request->session()->regenerate();

        return redirect()->route('frontend.vehicle-works');
    }

    public function index(Request $request): View|RedirectResponse
    {
        if (! (bool) $request->session()->get('frontend_access_granted')) {
            return redirect()->route('frontend.access');
        }

        return view('frontend.vehicle-works', [
            'accessName' => $request->session()->get('frontend_access_name'),
            'vehicleWorks' => VehicleWork::query()
                ->where('status', VehicleWorkStatus::Completed->value)
                ->where('paid', false)
                ->latest()
                ->get(),
        ]);
    }

    public function logout(Request $request): RedirectResponse
    {
        $request->session()->forget([
            'frontend_access_granted',
            'frontend_access_name',
        ]);

        $request->session()->regenerateToken();

        return redirect()->route('frontend.access');
    }
}
