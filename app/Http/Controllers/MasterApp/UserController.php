<?php
namespace App\Http\Controllers\MasterApp;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserStatus;
use Spatie\Permission\Models\Role;
use App\Core\User\Services\UserService;
use App\Http\Requests\MasterApp\User\UserStoreRequest;
use App\Http\Requests\MasterApp\User\UserUpdateRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use App\Models\Publication;
use App\Models\Department;
use App\Helpers\NotificationHelper;
use App\Helpers\AppNotification;
use App\Notifications\RoleUpdatedNotification;
class constructor
{

    private UserService $userService;

    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }
}
class UserController extends Controller
{
   
   public function index(UserService $service):view
    {
        // $users = User::latest()->paginate(10);
      
        // return view('masterapp.users.index', compact('users'));
        $publications = Publication::select('id', 'name')->get();
        $departments = Department::select('id', 'name')->get();
        $statusesList = UserStatus::select('id', 'label')->get();
        
        $users = $service->getAll();
        
        return view('masterapp.users.index', compact('users', 'publications','departments','statusesList' ));
    }

    public function create()
    {
        $publications = Publication::select('id', 'name')->get();
        $departments = Department::select('id', 'name')->get();
    //  $statusesList = UserStatus::all()->map(function ($s) {
    //     return [
    //         'id' => $s->id,
    //         'label' => $s->label
    //     ];
    // });
        $statusesList = UserStatus::select('id', 'label')->get();
        return view('masterapp.users.create', [
            'roles' => Role::pluck('name', 'id'),
            'userStatuses' => UserStatus::all(),
            'publications' => Publication::all(),
            'departments' => Department::all(),
            'statusesList' => UserStatus::all(),
            // compact('publications')
        ]);
    }
 

    public function store(UserStoreRequest $request, UserService $service): JsonResponse|RedirectResponse
    {

        $user = $service->create($request->validated());

        // Send universal notification for user creation
        AppNotification::notify_event('user.created', $user, auth()->user() ?? $user);

        //  If request is AJAX to return JSON
        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'User created successfully'
            ], 201);
        }

        //  Normal form submit to redirect
        return redirect()
            ->route('masterapp.users.index')
            ->with('success', 'User created successfully');
    }


    public function edit(int $id, UserService $service):View
    {
        $user = $service->get($id);
        $publications = Publication::select('id', 'name')->get();
        $departments = Department::select('id', 'name')->get();
        $statusesList = UserStatus::select('id', 'label')->get();


        // print_r($user->roles->pluck('name')->toArray());exit;

        return view('masterapp.users.edit', [
            'user' => $user,
            'roles' => Role::pluck('name','id'),
            'userRoles' => $user->roles->pluck('name','id')->toArray(),
            'publications' => Publication::pluck('name', 'id'),
            'userPublications' => $user->publications->pluck('name', 'id')->toArray(),
            'departments' => Department::all(),
            'statusesList' => UserStatus::all(),
        ]);
    }

     public function update(UserUpdateRequest $request, int $id, UserService $service): JsonResponse|RedirectResponse
    {
        // $service->update($id, $request->validated());    
    // Get user before update to compare roles
        $user = $service->get($id);
        $oldRoles = $user->roles->pluck('name')->toArray();

        $updatedUser = $service->update($id, $request->validated());

        // Send universal notification for user update
        AppNotification::notify_event('user.updated', $updatedUser, auth()->user() ?? $updatedUser);

        // Check if roles were updated and send notification
        $newRoles = $updatedUser->roles->pluck('name')->toArray();
        if ($oldRoles !== $newRoles) {
            // LEGACY NOTIFICATION CODE - COMMENTED OUT FOR REFERENCE
            // Notify the user about role changes
            // $updatedUser->sendRoleUpdatedNotification($oldRoles, $newRoles);

            // Notify admins about role changes (excluding current user if they're an admin)
            // $this->notifyAdminsAboutRoleUpdate($updatedUser, $oldRoles, $newRoles);

            // Send universal notification for role update
            AppNotification::notify_event('role.updated', $updatedUser, auth()->user() ?? $updatedUser);
        }

        //  If request is AJAX → return JSON
        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'User updated successfully'
            ], 201);
        }

        //  Normal form submit → redirect
        return redirect()
            ->route('masterapp.users.index')
            ->with('success', 'User updated successfully');
    }

    public function destroy(int $id, UserService $service, User $user=null): JsonResponse {
        $user = $service->get($id);

        // Send universal notification for user deletion
        AppNotification::notify_event('user.deleted', $user, auth()->user() ?? $user);

        $service->delete($id);
        // $user->delete();

        return response()->json([
            'message' => 'User deleted successfully',
        ]);
    }

    // public function show(int $id, UserService $service): View
    // {
    //     $user = $service->get($id);
    //     return view('masterapp.users.show', compact('user'));
    // }

    public function apiIndex(UserService $service): JsonResponse {
        $users = $service->index();

        return response()->json([
            'users' => $users,
        ]);
    }

    public function toggleActive(Request $request,int $id, UserService $service) : JsonResponse
    {
    $user = $service->get($id);

    $service->update($id, [
        'active' => ! $user->active,

    ]);

    return response()->json([
        'message' => $user->active ? 'User Deactivated.' : 'User Activated.',
        // 'active'  => ! $user->active,
    ]);
    }

    // Update user status via AJAX
    public function updateStatus(Request $request, int $id)
    {
        $request->validate([
            'status_id' => ['required', 'exists:user_statuses,id'],
        ]);

        //  Define the user
        $user = User::findOrFail($id);

        //  Update status
        $user->update([
            'status_id' => $request->status_id,
        ]);

        //  Reload relationship (CRITICAL)
        $user->load('status');


    return response()->json([
        'success' => true,
        'label' => $user->status->label ?? 'N/A',
        'badge_class' => $user->status->badge_class ?? 'badge-secondary',
    ]);
    }

  // Show modal form (AJAX)
    public function changePasswordForm(User $user)
    {
        $user->load('roles', 'status');
        return view('users.partials.change-password-form', compact('user'));
    }

// Handle submit
    public function updatePassword(Request $request, $id): JsonResponse
    {
    $user = User::findOrFail($id);

        $validated = $request->validate([
            'password' => 'required|string|min:6|confirmed',
        ]);

        $user->password = Hash::make($validated['password']);
        $user->save();

    return response()->json([
        'message' => 'Password changed successfully!'
    ]);
    }

    
    //  Notify admins about role updates
    
    private function notifyAdminsAboutRoleUpdate(User $user, array $oldRoles, array $newRoles): void
    {
        $admins = User::whereHas('roles', function ($query) {
            $query->whereIn('name', ['Admin User', 'Super Admin']);
        })->where('id', '!=', auth()->id())->get();

        foreach ($admins as $admin) {
            $admin->notify(new RoleUpdatedNotification($user, $oldRoles, $newRoles, auth()->user()));
        }
    }
    
}
