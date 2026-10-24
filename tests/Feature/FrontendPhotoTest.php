<?php

namespace Tests\Feature;

use App\Enums\VehicleWorkStatus;
use App\Models\FrontendPassword;
use App\Models\VehicleWork;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class FrontendPhotoTest extends TestCase
{
    use RefreshDatabase;

    public function test_frontend_serves_watermarked_copies_without_changing_originals(): void
    {
        Storage::fake('public');
        Storage::fake('local');
        $image = imagecreatetruecolor(800, 600);
        imagefill($image, 0, 0, imagecolorallocate($image, 220, 220, 220));
        ob_start();
        imagepng($image);
        $original = ob_get_clean();
        imagedestroy($image);
        Storage::disk('public')->put('vehicle-works/photo.png', $original);
        $work = VehicleWork::query()->create([
            'plate' => 'EV123', 'vehicle' => 'Tesla EV AVENUE',
            'job_description' => 'Prueba', 'job_photos' => ['vehicle-works/photo.png'],
            'status' => VehicleWorkStatus::Completed, 'paid' => false, 'payment_sent' => false,
        ]);
        $url = route('frontend.vehicle-works.photo', [$work, 0]);
        $this->get($url)->assertForbidden();
        $response = $this->withSession(['frontend_access_granted' => true])->get($url)
            ->assertOk()->assertHeader('Content-Type', 'image/jpeg');
        $result = imagecreatefromstring($response->getContent());
        $this->assertSame(800, imagesx($result));
        $this->assertSame(600, imagesy($result));
        $this->assertNotSame(imagecolorat($result, 400, 100), imagecolorat($result, 400, 560));
        imagedestroy($result);
        $thumb = $this->get($url.'?thumbnail=1')->assertOk();
        $small = imagecreatefromstring($thumb->getContent());
        $this->assertSame(240, imagesx($small));
        $this->assertSame(180, imagesy($small));
        imagedestroy($small);
        $this->assertCount(2, Storage::disk('local')->allFiles('frontend-photos'));
        $this->get($url.'?thumbnail=1')->assertContent($thumb->getContent());
        $this->assertCount(2, Storage::disk('local')->allFiles('frontend-photos'));
        $this->assertSame($original, Storage::disk('public')->get('vehicle-works/photo.png'));
        $this->get('/vehiculos')->assertDontSee('vehicle-works/photo.png');
        $this->get(route('frontend.vehicle-works.photo', [$work, 9]))->assertNotFound();

        $work->update(['payment_sent' => true]);
        $this->get($url)->assertNotFound();
        $this->get($url.'?thumbnail=1')->assertNotFound();
        $password = FrontendPassword::query()->create([
            'name' => 'EV', 'password' => 'secret', 'is_active' => true, 'can_view_electric_avenue' => true,
        ]);
        $this->withSession(['frontend_password_id' => $password->id])->get($url)->assertOk();
        $this->get($url.'?thumbnail=1')->assertOk();
        $password->update(['can_view_electric_avenue' => false]);
        $this->get($url)->assertNotFound();
        $this->get($url.'?thumbnail=1')->assertNotFound();
        $work->update(['payment_sent' => false, 'paid' => true]);
        $this->get($url)->assertNotFound();
    }
}
