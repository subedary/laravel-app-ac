<div class="mb-3">
    <label for="name" class="form-label">Name</label>
    <input type="text" id="name" name="name" value="{{ isset($contact) ? $contact->name : '' }}" class="form-control" required>
</div>
@if (isset($contact))
    <input type="hidden" name="id" value="{{ $contact->id }}">
@endif

<div class="mb-3">
    <label for="contact_name" class="form-label">Notes</label>
    <input type="text" id="notes" name="notes" value="{{ isset($contact) ? $contact->notes : '' }}" class="form-control">
</div>
