<?php

namespace App\Http\Controllers\MasterApp;

use App\Http\Controllers\Controller;
use App\Core\Wordpress\Services\WordpressService;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use App\Models\Publication;
use App\Models\Department;
use App\Models\UserStatus;

class WordpressController extends Controller
{
    public function index(WordpressService $service): View
    {
        
        return view('masterapp.wordpress.index', [
            'users'        => $service->getAll(),
            'publications' => Publication::select('id', 'name')->get(),
            'departments'  => Department::select('id', 'name')->get(),
            'statusesList' => UserStatus::select('id', 'label')->get(),
        ]);
    }

    public function toggleActive(int $id, WordpressService $service): JsonResponse
    {
        $user = $service->toggleActive($id);

        return response()->json([
            'success' => true,
            'active'  => $user->active,
            'message' => $user->active
                ? 'WordPress user activated successfully'
                : 'WordPress user deactivated successfully',
        ]);
    }
}
