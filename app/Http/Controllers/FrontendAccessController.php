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
                ->where(function ($query): void {
                    $query
                        ->where('payment_sent', false)
                        ->orWhereNull('payment_sent');
                })
                ->latest()
                ->get(),
        ]);
    }

    public function markPaymentSent(Request $request, VehicleWork $vehicleWork): RedirectResponse
    {
        if (! (bool) $request->session()->get('frontend_access_granted')) {
            return redirect()->route('frontend.access');
        }

        $validated = $request->validate([
            'comment' => ['nullable', 'string', 'max:2000'],
        ]);

        abort_unless(
            $vehicleWork->status === VehicleWorkStatus::Completed
            && ! $vehicleWork->paid
            && ! $vehicleWork->payment_sent,
            404,
        );

        $vehicleWork->forceFill([
            'comment' => blank($validated['comment'] ?? null) ? null : $validated['comment'],
            'payment_sent' => true,
        ])->save();

        return redirect()
            ->route('frontend.vehicle-works')
            ->with('status', 'El pago fue marcado como enviado.');
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
