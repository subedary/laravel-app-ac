<?php

namespace App\Http\Controllers\MasterApp;

use App\Http\Controllers\Controller;
use App\Core\Driver\Services\DriverService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use App\Models\Publication;
use App\Models\Department;
use App\Models\UserStatus;
use Illuminate\Http\JsonResponse;

class DriverController extends Controller
{
    public function index(DriverService $service): View
    {
        
        $publications = Publication::select('id', 'name')->get();
        $departments = Department::select('id', 'name')->get();
        $statusesList = UserStatus::select('id', 'label')->get();
        $drivers = $service->getAll();
        return view('masterapp.drivers.index', compact('drivers', 'publications','departments','statusesList'));
    }

    public function toggleActive(int $id, DriverService $service): JsonResponse
    {
        $driver = $service->get($id);

        $updated = $service->update($id, [
            'active' => ! $driver->active,
        ]);

        return response()->json([
            'success' => true,
            'active'  => $updated->active,
            'message' => $updated->active
                ? 'Driver activated successfully'
                : 'Driver deactivated successfully',
        ]);
    }
}
