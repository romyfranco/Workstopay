<?php

namespace Tests\Feature;

use App\Enums\UserRole;
use App\Enums\VehicleWorkStatus;
use App\Filament\Resources\VehicleWorks\Pages\ListVehicleWorks;
use App\Models\User;
use App\Models\VehicleWork;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class VehicleWorksTableTest extends TestCase
{
    use RefreshDatabase;

    public function test_default_state_order_and_selected_column_order_work_independently(): void
    {
        $this->actingAs(User::factory()->create(['role' => UserRole::Admin]));
        Filament::setCurrentPanel(Filament::getPanel('admin'));
        $progress = $this->work('Zulu', VehicleWorkStatus::InProgress, true, '2026-01-01');
        $waiting = $this->work('Mike', VehicleWorkStatus::Waiting, false, '2026-01-03');
        $completed = $this->work('Alpha', VehicleWorkStatus::Completed, false, '2026-01-02');
        $page = Livewire::test(ListVehicleWorks::class)
            ->assertCanSeeTableRecords([$progress, $waiting, $completed], inOrder: true);
        $page->sortTable('vehicle', 'asc')->assertCanSeeTableRecords([$completed, $waiting, $progress], inOrder: true);
        $page->sortTable('vehicle', 'desc')->assertCanSeeTableRecords([$progress, $waiting, $completed], inOrder: true);
        $page->sortTable('paid', 'asc')->assertCanSeeTableRecords([$waiting, $completed, $progress], inOrder: true);
        $page->sortTable('paid', 'desc')->assertCanSeeTableRecords([$progress, $waiting, $completed], inOrder: true);
        $page->sortTable('created_at', 'desc')->assertCanSeeTableRecords([$waiting, $completed, $progress], inOrder: true);
        $page->sortTable(null)->assertCanSeeTableRecords([$progress, $waiting, $completed], inOrder: true);
    }

    public function test_table_search_includes_full_description_and_comment(): void
    {
        $this->actingAs(User::factory()->create(['role' => UserRole::Admin]));
        Filament::setCurrentPanel(Filament::getPanel('admin'));
        $description = $this->work('Uno', VehicleWorkStatus::Completed, false, '2026-01-01');
        $description->update(['job_description' => str_repeat('Trabajo ', 20).'compresor']);
        $comment = $this->work('Dos', VehicleWorkStatus::Completed, false, '2026-01-01');
        $comment->update(['comment' => str_repeat('Comentario ', 20).'transferencia']);
        Livewire::test(ListVehicleWorks::class)
            ->searchTable('compresor')->assertCanSeeTableRecords([$description])->assertCanNotSeeTableRecords([$comment])
            ->searchTable('transferencia')->assertCanSeeTableRecords([$comment])->assertCanNotSeeTableRecords([$description]);
    }

    private function work(string $vehicle, VehicleWorkStatus $status, bool $paid, string $created): VehicleWork
    {
        $work = new VehicleWork(['plate' => $vehicle, 'vehicle' => $vehicle, 'job_description' => 'Trabajo', 'status' => $status, 'paid' => $paid]);
        $work->created_at = $created;
        $work->save();

        return $work;
    }
}
