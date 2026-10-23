<?php

namespace App\Http\Controllers;

use App\Enums\VehicleWorkStatus;
use App\Models\FrontendPassword;
use App\Models\VehicleWork;
use App\Services\AccessLogService;
use App\Services\FrontendPhotoService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Response;
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
        $request->session()->put('frontend_password_id', $password->id);
        $request->session()->regenerate();

        return redirect()->route('frontend.vehicle-works');
    }

    public function index(Request $request): View|RedirectResponse
    {
        if (! (bool) $request->session()->get('frontend_access_granted')) {
            return redirect()->route('frontend.access');
        }

        $isElectricAvenue = $request->routeIs('frontend.electric-avenue');
        $canViewElectricAvenue = $this->canViewElectricAvenue($request);
        abort_if($isElectricAvenue && ! $canViewElectricAvenue, 403);

        return view('frontend.vehicle-works', [
            'isElectricAvenue' => $isElectricAvenue,
            'canViewElectricAvenue' => $canViewElectricAvenue,
            'accessName' => $request->session()->get('frontend_access_name'),
            'vehicleWorks' => VehicleWork::query()
                ->where('status', VehicleWorkStatus::Completed->value)
                ->where('paid', false)
                ->when($isElectricAvenue, function ($query): void {
                    $query->where('vehicle', 'like', '%EV AVENUE%');
                }, function ($query): void {
                    $query->where(function ($query): void {
                        $query
                            ->where('payment_sent', false)
                            ->orWhereNull('payment_sent');
                    });
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

        if ($request->input('list') === 'electric-avenue') {
            abort_unless($this->canViewElectricAvenue($request), 403);
        }

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
            ->route($request->input('list') === 'electric-avenue' ? 'frontend.electric-avenue' : 'frontend.vehicle-works')
            ->with('status', 'El pago fue marcado como enviado.');
    }

    public function logout(Request $request): RedirectResponse
    {
        $request->session()->forget([
            'frontend_access_granted',
            'frontend_access_name',
            'frontend_password_id',
        ]);

        $request->session()->regenerateToken();

        return redirect()->route('frontend.access');
    }

    private function canViewElectricAvenue(Request $request): bool
    {
        return FrontendPassword::query()
            ->whereKey($request->session()->get('frontend_password_id'))
            ->where('is_active', true)
            ->where('can_view_electric_avenue', true)
            ->exists();
    }

    public function photo(Request $request, VehicleWork $vehicleWork, int $photo, FrontendPhotoService $photos): Response
    {
        abort_unless((bool) $request->session()->get('frontend_access_granted'), 403);
        $visibleInGeneral = ! $vehicleWork->payment_sent;
        $visibleInElectricAvenue = $this->canViewElectricAvenue($request)
            && str_contains(strtoupper($vehicleWork->vehicle), 'EV AVENUE');
        abort_unless(
            $vehicleWork->status === VehicleWorkStatus::Completed
            && ! $vehicleWork->paid
            && ($visibleInGeneral || $visibleInElectricAvenue),
            404,
        );

        $path = ($vehicleWork->job_photos ?? [])[$photo] ?? null;
        $disk = Storage::disk('public');
        abort_unless(is_string($path) && $disk->exists($path), 404);

        return response($photos->watermark($disk->get($path)), 200, [
            'Content-Type' => 'image/jpeg',
            'Cache-Control' => 'private, no-store',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }
}
