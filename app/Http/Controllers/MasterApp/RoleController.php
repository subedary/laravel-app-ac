<?php

namespace App\Http\Controllers\MasterApp;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Role;
use App\Models\Permission;
use App\Models\Department;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Gate;
use App\Core\Roles\Services\RolesService;
use App\Http\Requests\MasterApp\Roles\RolesStoreRequest;
use App\Http\Requests\MasterApp\Roles\RolesUpdateRequest;

class RoleController extends Controller
{
    public function index()
    {
        $departments = Department::orderBy('name')->pluck('name', 'id');
        return view('masterapp.roles.index', compact('departments'));
    }

    /**
     * Return data for the Roles DataTable.
     * This method handles the AJAX requests from the DataTable.
     */
    public function getRoles(Request $request)
    {
        if ($request->ajax()) {

            // $data = Role::with('permissions')->latest()->get(); 


            $query = Role::with(['permissions', 'department']);

            // --- DEPARTMENT FILTER ---
            if ($request->filled('department_id')) {
                $departmentId = $request->input('department_id');

                if (is_array($departmentId)) {
                    $query->whereIn('department_id', $departmentId);
                } else {
                    $query->where('department_id', $departmentId);
                }
            }

            // --- SEARCH FILTER ---
            if ($request->filled('search')) {
                $searchTerm = $request->get('search');

                $query->where('name', 'like', '%' . $searchTerm . '%');
            }


            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('checkbox', function ($row) {
                    return '<input type="checkbox" class="row-check" value="' . $row->id . '">';
                })
                ->addColumn('permissions', function ($row) {

                    if ($row->permissions->isNotEmpty()) {
                        $badges = '';
                        foreach ($row->permissions as $permission) {

                            $badges .= '<span class="px-3 py-1 text-xs rounded-full bg-gray-100 font-semibold shadow-sm">' . e($permission->display_name ?? $permission->name) . '</span> ';
                        }

                        return '<div class="flex flex-wrap gap-2">' . $badges . '</div>';
                    } else {

                        return '<span class="text-gray-400 italic">No Permissions</span>';
                    }
                })
               ->addColumn('department', function ($row) {
                return $row->department ? $row->department->name : '';
            })
                ->addColumn('actions', function ($row) {
                    $btn = '<div class="action-div">';

                  
                    if (Gate::allows('edit-role')) {
                        $btn .= '<button type="button" class="btn btn-link p-0 action-icon edit-item"
                                            data-url="' . route('masterapp.roles.edit', ['role' => $row->id]) . '"
                                            data-title="Edit ' . e($row->name) . '"
                                            title="Edit ' . e($row->name) . '">
                                            <i class="fa fa-edit"></i>
                                        </button>';
                    }

                  
                    if (Gate::allows('delete-role')) {
                        $btn .= '<button type="button"
                                            class="btn btn-link p-0 action-icon text-danger delete-item"
                                            data-url="' . route('masterapp.roles.destroy', ['role' => $row->id]) . '"
                                            data-name="' . e($row->name) . '"
                                            title="Delete ' . e($row->name) . '">
                                            <i class="fa fa-trash"></i>
                                        </button>';
                    }

                    $btn .= '</div>';

                    return $btn;
                })
                ->rawColumns(['checkbox', 'permissions', 'actions'])
                ->setRowAttr([
                    'data-id' => 'id'
                ])
                ->make(true);
        }

        return response()->json(['error' => 'Invalid request'], 400);
    }

    public function create()
    {
        $departments = Department::orderBy('name')->pluck('name', 'id');
        $groupedPermissions = Permission::with('module')
            ->get()
            ->groupBy(function ($permission) {
                return optional($permission->module)->name ?? 'Uncategorized';
            });

        return view('masterapp.roles.create', compact('groupedPermissions', 'departments'));
    }


    public function store(
        RolesStoreRequest $request,
        RolesService $service
    ) {
        $service->create($request->validated());

        return response()->json([
            'success' => 'Role created successfully!',
            'redirect' => route('masterapp.roles.index')
        ], 200);
    }


    public function edit(Role $role)
    {
        $departments = Department::orderBy('name')->pluck('name', 'id');
        $rolePermissions = $role->permissions->pluck('id')->toArray();

        $groupedPermissions = Permission::with('module')
            ->get()
            ->groupBy(function ($permission) {
                return optional($permission->module)->name ?? 'Uncategorized';
            });

        return view('masterapp.roles.edit', compact('role', 'groupedPermissions', 'rolePermissions', 'departments'));
    }


    public function update(RolesUpdateRequest $request, int $id, RolesService $service)
    {

        $service->update($id, $request->validated());

        return response()->json([
            'message' => 'Roles updated successfully!',
            'redirect' => route('masterapp.roles.index')
        ], 200);
    }

    public function destroy(int $id, RolesService $service)
    {
        $service->delete($id);
        return response()->json(['message' => 'Role deleted successfully!'], 200);
    }



    public function bulkDestroy(Request $request)
    {
        try {
            // Validate the incoming request data
            $request->validate([
                'ids' => 'required|array',
                // IMPORTANT: Change 'modules' to 'roles' to check against the correct table
                'ids.*' => 'integer|exists:roles,id'
            ]);

            $ids = $request->input('ids');

            // Use the Role model to delete the selected roles
            $deletedCount = Role::whereIn('id', $ids)->delete();

            return response()->json([
                'message' => "{$deletedCount} role(s) deleted successfully!"
            ]);
        } catch (\Exception $e) {
            // Log the error for debugging
            Log::error('Role Bulk Deletion Error: ' . $e->getMessage());

            // Return a generic error message to the user
            return response()->json(['message' => 'An error occurred while trying to delete the role(s).'], 500);
        }
    }
}
