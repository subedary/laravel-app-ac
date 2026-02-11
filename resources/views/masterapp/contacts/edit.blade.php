<section class="content">
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-10">
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('masterapp.contacts.update', $contact->id) }}" method="POST" id="form-edit-contact">
                            @csrf
                            @method('PUT')
                            @include('masterapp.contacts.form')

                            <div class="alert alert-danger d-none" id="form-error-message"></div>

                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                 <button type="button" class="btn btn-secondary" data-dismiss="modal" data-bs-dismiss="modal">Cancel</button>
                                <button type="submit" class="btn btn-success" id="submit-btn">
                                    <span id="btn-text">Update Contact</span>
                                    <span id="btn-edit-spinner" class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
