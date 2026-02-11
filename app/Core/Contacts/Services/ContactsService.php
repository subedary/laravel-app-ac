<?php

namespace App\Core\Contacts\Services;

use App\Core\Contacts\Contracts\ContactsRepository;


class ContactsService
{
   
    public function __construct(
        private ContactsRepository $contacts
    ) {}

    public function create(array $data)
    {
        return $this->contacts->create($data);
    }

    public function get(int $id)
    {
        return $this->contacts->find($id);
    }

    public function update(int $id, array $data)
    {
        return $this->contacts->update($id, $data);
    }

    public function delete(int $id): void
    {
        $this->contacts->delete($id);
    }

    public function createContactItem(int $contactId, array $data)
    {
        return $this->contacts->createContactItem($contactId, $data);
    }

    public function updateContactItem(int $id, array $data)
    {
        return $this->contacts->updateContactItem($id, $data);
    }

    public function deleteContactItem(int $id): void
    {
        $this->contacts->deleteContactItem($id);
    }

    public function contactItemsQuery(int $contactId, ?string $searchTerm = null)
    {
        return $this->contacts->contactItemsQuery($contactId, $searchTerm);
    }

    public function contactItemsSummary(int $contactId)
    {
        return $this->contacts->contactItemsSummary($contactId);
    }

    public function findContactItem(int $id)
    {
        return $this->contacts->findContactItem($id);
    }

    public function contactsQuery(?string $searchTerm = null)
    {
        return $this->contacts->contactsQuery($searchTerm);
    }

}
