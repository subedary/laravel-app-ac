<?php

namespace App\Http\Controllers\MasterApp;

use App\Core\TimeOff\Services\TimeOffRequestService;
use App\Http\Controllers\Controller;
use App\Http\Requests\MasterApp\TimeOffRequest\StoreTimeOffRequest;
use App\Http\Requests\MasterApp\TimeOffRequest\UpdateTimeOffRequest;
use App\Models\User;
use Illuminate\Http\Request;
use App\Helpers\AppNotification;

class TimeOffRequestController extends Controller
{
    private $service;

    public function __construct(TimeOffRequestService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        // Enforce base access permission
        if (!auth()->user()->can('admin-time-off-requests') && !auth()->user()->can('list-time-off-requests')) {
            abort(403, 'Unauthorized action.');
        }

        $users = User::all(); 
        $canCreate = auth()->user()->can('create-time-off-request');
        $canEdit = auth()->user()->can('edit-time-off-request');
        $canDelete = auth()->user()->can('delete-time-off-request');
        $canAdmin = auth()->user()->can('admin-time-off-requests');
        $canChangeStatus = auth()->user()->can('status-time-off-request');
        
        return view('masterapp.time_off_requests.index', compact('users', 'canCreate', 'canEdit', 'canDelete', 'canAdmin', 'canChangeStatus'));
    }

    public function data(Request $request)
    {
        // Enforce data access permission
        if (!auth()->user()->can('admin-time-off-requests') && !auth()->user()->can('list-time-off-requests')) {
             abort(403, 'Unauthorized action.');
        }

        $filters = $request->only(['id_from', 'id_to', 'date_from', 'date_to', 'status']);
        
        // Enforce Admin vs Own List
        if (!auth()->user()->can('admin-time-off-requests')) {
            $filters['user_id'] = auth()->id();
        }

        $start = $request->input('start', 0);
        $length = $request->input('length', 10);
        $search = $request->input('search.value');
        
        // Handle sorting mapping
        $columns = ['id', 'user_id', 'start_time', 'end_time', 'added_timestamp', 'paid', 'status', 'notes', 'actions']; 
        $orderInput = $request->input('order.0');
        $orderColumn = $columns[$orderInput['column'] ?? 4] ?? 'added_timestamp'; 
        $orderDir = $orderInput['dir'] ?? 'desc';

        $result = $this->service->getDataTableData($filters, $search, $start, $length, ['column' => $orderColumn, 'dir' => $orderDir]);

        $canChangeStatus = auth()->user()->can('status-time-off-request');
        $canAdmin = auth()->user()->can('admin-time-off-requests');

        // Transform data for DataTables
        $transformedData = $result['data']->map(function($request) use ($canChangeStatus, $canAdmin) {
            return [
                'id' => $request->id, 
                'user_name' => $request->user ? $request->user->first_name . ' ' . $request->user->last_name : 'N/A', 
                'start_time' => $request->start_time ? $request->start_time->format('F jS Y, g:i:s A') : '', 
                'end_time' => $request->end_time ? $request->end_time->format('F jS Y, g:i:s A') : '', 
                'start_time_raw' => $request->start_time ? $request->start_time->format('Y-m-d\TH:i') : '', 
                'end_time_raw' => $request->end_time ? $request->end_time->format('Y-m-d\TH:i') : '', 
                'added_timestamp' => $request->added_timestamp ? $request->added_timestamp->format('F jS Y, g:i:s A') : '', 
                'paid' => $request->paid ? 'Yes' : 'No', 
                'status' => $request->status, 
                'notes' => $request->notes, 
                'actions' => $request->id, 
                
                'user_id' => $request->user_id,
                'status_label' => ucfirst(str_replace('_', ' ', $request->status)),
                'can_change_status' => $canChangeStatus,
                'can_admin' => $canAdmin
            ];
        });

        return response()->json([
            'draw' => intval($request->input('draw')),
            'recordsTotal' => $result['recordsTotal'],
            'recordsFiltered' => $result['recordsFiltered'],
            'data' => $transformedData,
        ]);
    }

    public function store(StoreTimeOffRequest $request)
    {
        if (!auth()->user()->can('create-time-off-request')) {
            abort(403, 'Unauthorized action.');
        }

        $data = $request->validated();
        $canAdmin = auth()->user()->can('admin-time-off-requests');

        // If user is not admin, they can only create for themselves
        if (!$canAdmin) {
            $data['user_id'] = auth()->id();
        }

        // Only those with permission can set status directly, otherwise default to pending
        if (!auth()->user()->can('status-time-off-request') && !$canAdmin) {
            $data['status'] = 'pending';
            unset($data['paid']);
        }

        $timeOffRequest = $this->service->createRequest($data);

        // Send universal notification for timeoff creation
        AppNotification::notify_event('timeoff.created', $timeOffRequest, auth()->user());

        return redirect()->back()->with('success', 'Time off request created successfully.');
    }

    public function update(UpdateTimeOffRequest $request, int $id)
    {
        $existingRequest = $this->service->getRequest($id);
        $canAdmin = auth()->user()->can('admin-time-off-requests');
        
        // Ownership check: If not admin, must own the record to act
        if (!$canAdmin) {
            if ($existingRequest->user_id != auth()->id()) {
                abort(403, 'Unauthorized action.');
            }
        }

        if (!auth()->user()->can('edit-time-off-request')) {
             abort(403, 'Unauthorized action.');
        }

        // --- NEW RULE: Non-admins can only edit PENDING entries ---
        if (!$canAdmin) {
            if ($existingRequest->status !== 'pending') {
                return redirect()->back()->with('error', 'Only pending requests can be modified.');
            }
        }

        $data = $request->validated();

        if (!$canAdmin) {
            // Standard users cannot change user_id
            unset($data['user_id']);
            // Standard users cannot change status/paid
            unset($data['status']);
            unset($data['paid']);
            // Note: start_time and end_time ARE allowed here because we already 
            // checked that the request is 'pending' for non-admins above.
        } else {
             if (!auth()->user()->can('status-time-off-request') && !$canAdmin) { // redundant but safe
                 unset($data['status']);
                 unset($data['paid']);
             }
        }

        $updatedRequest = $this->service->updateRequest($id, $data);

        // Send universal notification for timeoff update
        AppNotification::notify_event('timeoff.updated', $updatedRequest, auth()->user());

        return redirect()->back()->with('success', 'Time off request updated successfully.');
    }

    public function destroy(int $id)
    {
        if (!auth()->user()->can('delete-time-off-request')) {
             abort(403, 'Unauthorized action.');
        }

        $existingRequest = $this->service->getRequest($id);
        $canAdmin = auth()->user()->can('admin-time-off-requests');

        // --- NEW RULE: Non-admins can only delete PENDING entries ---
        if (!$canAdmin) {
            if ($existingRequest->user_id != auth()->id()) {
                abort(403, 'Unauthorized action.');
            }
            if ($existingRequest->status !== 'pending') {
                return redirect()->back()->with('error', 'Only pending requests can be deleted.');
            }
        }

        // Send universal notification for timeoff deletion before deleting
        AppNotification::notify_event('timeoff.deleted', $existingRequest, auth()->user());

        $this->service->deleteRequest($id);
        return redirect()->back()->with('success', 'Time off request deleted successfully.');
    }

    public function updateStatus(Request $request, int $id)
    {
        if (!auth()->user()->can('status-time-off-request') && !auth()->user()->can('admin-time-off-requests')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'status' => 'required|in:pending,denied,approved_paid,approved_unpaid',
        ]);

        $this->service->updateRequest($id, ['status' => $request->status]);

        return response()->json(['success' => true, 'message' => 'Status updated successfully']);
    }

    public function bulkUpdateStatus(Request $request)
    {
        if (!auth()->user()->can('status-time-off-request') && !auth()->user()->can('admin-time-off-requests')) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $request->validate([
            'status' => 'required|in:pending,denied,approved_paid,approved_unpaid',
            'ids' => 'required_without:select_all|array',
            'ids.*' => 'exists:time_off_requests,id',
            'select_all' => 'sometimes|boolean',
            'filters' => 'sometimes|array'
        ]);

        if ($request->select_all) {
            $filters = $request->filters ?? [];
            $this->service->bulkUpdateStatusByFilter($filters, $request->status);
        } else {
            $this->service->bulkUpdateStatus($request->ids, $request->status);
        }

        return response()->json(['success' => true, 'message' => 'Statuses updated successfully']);
    }

    public function export(Request $request)
    {
        $filters = $request->only(['id_from', 'id_to', 'date_from', 'date_to', 'status', 'sort_by', 'sort_dir']);
        
        if (!auth()->user()->can('admin-time-off-requests')) {
            $filters['user_id'] = auth()->id();
        }
        
        $type = $request->query('type', 'csv');
        return $this->service->exportIds($type, $filters);
    }
}
