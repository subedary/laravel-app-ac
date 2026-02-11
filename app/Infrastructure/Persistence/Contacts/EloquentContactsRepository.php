<?php

namespace App\Infrastructure\Persistence\Contacts;

use App\Core\Contacts\Contracts\ContactsRepository;
use App\Models\Contact;
use App\Models\ContactItem;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB; 

class EloquentContactsRepository implements ContactsRepository
{
    public function find(int $id): Contact
    {
        return Contact::findOrFail($id);
    }

    public function create(array $data): Contact
    {
        return DB::transaction(function () use ($data) {
           
           $contact = Contact::create([
            'name' => $data['name'],
            'notes' => $data['notes'] ?? null,
        ]);
            return $contact;
        });
    }

    public function update(int $id, array $data): Contact
    {
       
        return DB::transaction(function () use ($id, $data) {
            $contact = Contact::findOrFail($id);
            $contact->update([
            'name' => $data['name'],
            'notes' => $data['notes'] ?? null,
        ]);
            
            return $contact;
        });
    }

    public function delete(int $id): void
    {
        // No transaction needed here as it's a single delete operation.
        Contact::findOrFail($id)->delete();
    }

    public function createContactItem(int $contactId, array $data): ContactItem
    {
        return DB::transaction(function () use ($contactId, $data) {
            $contact = Contact::findOrFail($contactId);

            return ContactItem::create([
                'contact_id' => $contact->id,
                'type' => $data['type'],
                'value' => $data['value'],
            ]);
        });
    }

    public function updateContactItem(int $id, array $data): ContactItem
    {
        return DB::transaction(function () use ($id, $data) {
            $contactItem = ContactItem::findOrFail($id);
            $contactItem->update([
                'type' => $data['type'],
                'value' => $data['value'],
            ]);

            return $contactItem;
        });
    }

    public function deleteContactItem(int $id): void
    {
        ContactItem::where('id', $id)->delete();
    }

    public function contactItemsQuery(int $contactId, ?string $searchTerm = null): Builder
    {
        $query = ContactItem::query()
            ->select([
                'contact_items.id',
                'contact_items.contact_id',
                'contact_items.type as item_type',
                'contact_items.value as item_value',
            ])
            ->whereNull('contact_items.deleted_at')
            ->where('contact_items.contact_id', $contactId);

        if (!empty($searchTerm)) {
            $query->where(function ($q) use ($searchTerm) {
                $q->where('contact_items.type', 'like', '%' . $searchTerm . '%')
                    ->orWhere('contact_items.value', 'like', '%' . $searchTerm . '%');
            });
        }

        return $query;
    }

    public function contactItemsSummary(int $contactId): Collection
    {
        return ContactItem::query()
            ->where('contact_items.contact_id', $contactId)
            ->whereNull('contact_items.deleted_at')
            ->orderBy('contact_items.type')
            ->orderBy('contact_items.value')
            ->get(['contact_items.type', 'contact_items.value']);
    }

    public function findContactItem(int $id): ContactItem
    {
        return ContactItem::findOrFail($id);
    }

    public function contactsQuery(?string $searchTerm = null): Builder
    {
        $query = Contact::query()
            ->select([
                'id',
                'name',
                'client_id',
                'notes',
            ])
            ->withCount(['contactItems as contact_items_count' => function ($q) {
                $q->whereNull('contact_items.deleted_at');
            }]);

        if (!empty($searchTerm)) {
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', '%' . $searchTerm . '%')
                    ->orWhere('notes', 'like', '%' . $searchTerm . '%')
                    ->orWhereExists(function ($sub) use ($searchTerm) {
                        $sub->select(DB::raw(1))
                            ->from('contact_items')
                            ->whereColumn('contact_items.contact_id', 'contacts.id')
                            ->whereNull('contact_items.deleted_at')
                            ->where('contact_items.value', 'like', '%' . $searchTerm . '%');
                    });
            });
        }

        return $query;
    }
}
