<?php
namespace App\Core\Contacts\Contracts;

use App\Models\Contact;
use App\Models\ContactItem;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

interface ContactsRepository
{
    public function find(int $id): Contact;

    public function create(array $data): Contact;

    public function update(int $id, array $data): Contact;

     public function delete(int $id): void;

    public function createContactItem(int $contactId, array $data): ContactItem;

    public function updateContactItem(int $id, array $data): ContactItem;

    public function deleteContactItem(int $id): void;

    public function contactItemsQuery(int $contactId, ?string $searchTerm = null): Builder;

    public function contactItemsSummary(int $contactId): Collection;

    public function findContactItem(int $id): ContactItem;

    public function contactsQuery(?string $searchTerm = null): Builder;
}
