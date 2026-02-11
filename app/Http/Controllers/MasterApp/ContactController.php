<?php

namespace App\Http\Controllers\MasterApp;

use App\Core\Contacts\Services\ContactsService;
use App\Http\Controllers\Controller;
use App\Models\Contact;
use App\Models\ContactItem;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use App\Http\Requests\MasterApp\Contacts\ContactsUpdateRequest;
use App\Http\Requests\MasterApp\Contacts\ContactsStoreRequest;
use App\Http\Requests\MasterApp\Contacts\ContactItemStoreRequest;
use App\Http\Requests\MasterApp\Contacts\ContactItemUpdateRequest;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Http\JsonResponse;

class ContactController extends Controller
{
    public function index()
    {
        return view('masterapp.contacts.index');
    }


    public function getContacts(Request $request, ContactsService $service)
    {
        if ($request->ajax()) {
            $searchTerm = $request->filled('search') ? $request->get('search') : null;
            $query = $service->contactsQuery($searchTerm);

            // Debug logging removed


            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('checkbox', function ($row) {
                    return '<input type="checkbox" class="row-check" value="' . $row->id . '">';
                })
                ->editColumn('name', function ($row) {
                    if (!$row->name) {
                        return '';
                    }
                    $url = route('masterapp.contacts.show', ['id' => $row->id]);
                    return '<a href="' . $url . '" class="text-decoration-underline">' . e($row->name) . '</a>';
                })

                ->addColumn('Notes', function ($row) {
                    return $row->notes ? $row->notes : '';
                })
                ->addColumn('ItemsCount', function ($row) {
                    $count = $row->contact_items_count ?? 0;
                    $url = route('masterapp.contacts.items.summary', ['id' => $row->id]);
                    return '<span class="contact-items-count" data-contact-id="' . $row->id . '" data-url="' . $url . '" data-bs-toggle="popover" data-bs-trigger="hover" data-bs-html="true" data-bs-content="Loading...">' . $count . '</span>';
                })
                ->addColumn('actions', function ($row) {
                    $btn = '<div class="action-div">';


                    if (Gate::allows('edit-contact')) {
                        $btn .= '<button type="button" class="btn btn-link p-0 action-icon edit-item"
                                            data-url="' . route('masterapp.contacts.edit', ['id' => $row->id]) . '"
                                            data-title="Edit ' . e($row->name) . '"
                                            title="Edit ' . e($row->name) . '">
                                            <i class="fa fa-edit"></i>
                                        </button>';
                    }


                    if (Gate::allows('delete-contact')) {
                        $btn .= '<button type="button"
                                            class="btn btn-link p-0 action-icon text-danger delete-item"
                                            data-url="' . route('masterapp.contacts.destroy', ['id' => $row->id]) . '"
                                            data-name="' . e($row->name) . '"
                                            title="Delete ' . e($row->name) . '">
                                            <i class="fa fa-trash"></i>
                                        </button>';
                    }

                    $btn .= '</div>';

                    return $btn;
                })
                ->rawColumns(['checkbox', 'name', 'Notes', 'ItemsCount', 'actions'])
                ->setRowAttr([
                    'data-id' => 'id'
                ])
                ->make(true);
        }

        return response()->json(['error' => 'Invalid request'], 400);
    }


    public function create()
    {
        return view('masterapp.contacts.create');
    }

    public function store(ContactsStoreRequest $request, ContactsService $service)
    {

        $service->create($request->validated());

        return response()->json([
            'message' => 'Contact created successfully!',
            'redirect' => route('masterapp.contacts.index')
        ], 200);
    }

    public function edit(int $id, ContactsService $service)
    {
        $contact = $service->get($id);
        return view('masterapp.contacts.edit', compact('contact'));
    }

    public function show(int $id, ContactsService $service)
    {
        $contact = $service->get($id);
        return view('masterapp.contacts.show', compact('contact'));
    }

    public function update(ContactsUpdateRequest $request, int $id, ContactsService $service)
    {

        $service->update($id, $request->validated());

        return response()->json([
            'message' => 'Contact updated successfully!',
            'redirect' => route('masterapp.contacts.index')
        ], 200);
    }

    public function destroy(int $id, ContactsService $service)
    {
        $service->delete($id);

        return response()->json(['message' => 'Contact deleted successfully!'], 200);
    }

    public function getContactsitems(Request $request, int $id, ContactsService $service)
    {
        if ($request->ajax()) {
            $searchTerm = $request->filled('search') ? $request->get('search') : null;
            $query = $service->contactItemsQuery($id, $searchTerm);


            return DataTables::of($query)
                ->addIndexColumn()
                ->editColumn('item_value', function ($row) {
                    if ($row->item_type === 'phone') {
                        return $this->formatPhoneValue($row->item_value);
                    }

                    return $row->item_value ?? '';
                })
                ->addColumn('actions', function ($row) {
                    $btn = '<div class="action-div">';

                    if (Gate::allows('edit-contact-item')) {
                        $btn .= '<button type="button" class="btn btn-link p-0 action-icon edit-item"
                                            data-url="' . route('masterapp.contact-items.edit', ['id' => $row->id]) . '"
                                            data-title="Edit Item"
                                            title="Edit Item">
                                            <i class="fa fa-edit"></i>
                                        </button>';
                    }

                    if (Gate::allows('delete-contact-item')) {
                        $btn .= '<button type="button"
                                            class="btn btn-link p-0 action-icon text-danger delete-item"
                                            data-url="' . route('masterapp.contact-items.destroy', ['id' => $row->id]) . '"
                                            data-name="Contact Item"
                                            title="Delete Item">
                                            <i class="fa fa-trash"></i>
                                        </button>';
                    }

                    $btn .= '</div>';

                    return $btn;
                })
                ->rawColumns(['actions'])
                ->setRowAttr([
                    'data-id' => 'id'
                ])
                ->make(true);
        }

        return response()->json(['error' => 'Invalid request'], 400);
    }

    public function editContactItem(int $id, ContactsService $service)
    {
        $contactItem = $service->findContactItem($id);
        return view('masterapp.contact-items.edit', compact('contactItem'));
    }

    public function createContactItem(int $id, ContactsService $service)
    {
        $contact = $service->get($id);
        return view('masterapp.contact-items.create', compact('contact'));
    }

    public function storeContactItem(ContactItemStoreRequest $request, int $id, ContactsService $service)
    {
        $service->createContactItem($id, $request->validated());

        return response()->json([
            'message' => 'Contact item created successfully!',
            'redirect' => route('masterapp.contacts.show', ['id' => $id]),
        ], 200);
    }

    public function updateContactItem(ContactItemUpdateRequest $request, int $id, ContactsService $service)
    {
        $service->updateContactItem($id, $request->validated());

        return response()->json([
            'message' => 'Contact item updated successfully!',
            'redirect' => url()->previous(),
        ], 200);
    }

    public function destroyContactItem(int $id, ContactsService $service)
    {
        $service->deleteContactItem($id);

        return response()->json(['message' => 'Contact item deleted successfully!'], 200);
    }

    public function getContactItemsSummary(int $id, ContactsService $service): JsonResponse
    {
        $items = $service->contactItemsSummary($id);

        $items->transform(function ($item) {
            if ($item->type === 'phone') {
                $item->value = $this->formatPhoneValue($item->value);
            }

            return $item;
        });

        return response()->json([
            'items' => $items,
        ]);
    }

    private function formatPhoneValue(?string $value): string
    {
        $digits = preg_replace('/\D+/', '', $value ?? '');
        $digits = substr($digits, 0, 10);

        $parts = [];
        if (strlen($digits) > 0) {
            $parts[] = substr($digits, 0, 3);
        }
        if (strlen($digits) > 3) {
            $parts[] = substr($digits, 3, 3);
        }
        if (strlen($digits) > 6) {
            $parts[] = substr($digits, 6, 4);
        }

        return implode('-', $parts);
    }
}
