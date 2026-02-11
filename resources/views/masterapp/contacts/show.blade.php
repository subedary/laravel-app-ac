@extends('masterapp.layouts.app')
@section('content')

<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0 text-dark">Contact Details</h1>
      </div>
      <div class="col-sm-6 d-flex justify-content-end">
        <a href="{{ route('masterapp.contacts.index') }}" class="btn btn-secondary">Back</a>
      </div>
    </div>
  </div>
</div>

<section class="content">
  <div class="container-fluid">
    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-body">
            <div class="mb-3">
              <div class="text-muted">Name</div>
              <div class="fw-semibold">{{ $contact->name }}</div>
            </div>
            <div class="mb-3">
              <div class="text-muted">Notes</div>
              <div class="fw-semibold">{{ $contact->notes ?? '-' }}</div>
            </div>
          </div>
        </div>
      </div>
    </div>



    <div class="row">
      <div class="col-12">
        <div class="card">

          <!-- /.card-header -->
          <div class="card-body">
            <table id="dataTable" class="table table-bordered table-hover">
              <thead>
                <!-- <th><input type="checkbox" id="selectAll"></th> -->
                        <th>Type</th>
                        <th>Value</th>
                        <th>Actions</th>
              </thead>
             

              </tfoot>
            </table>
          </div>
          <!-- /.card-body -->
        </div>
        <!-- /.card -->
      </div>
      <!-- /.col -->
    </div>
    <!-- /.row -->


  </div>
</section>

<!-- Generic Modal -->
@include('partials.generic-model')
@endsection

@push('scripts')

 <script src="{{ asset('js/permission-checkboxes.js') }}" defer></script>
<script>

     $.fn.dataTable.ext.errMode = 'none';

     
     $(document).ready(function() {


         $(document).ajaxError(function(event, xhr, settings, thrownError) {
            // Store the route URL in a variable for clarity
            const moduleDataRoute = "{{ route('masterapp.contacts.items', ['id' => $contact->id]) }}";

            const isDataTableRequest =
                settings.url.indexOf(moduleDataRoute) !== -1 &&
                (!settings.type || settings.type.toUpperCase() === 'GET');

            if (isDataTableRequest) {
                // Ignore validation/auth/session errors so the table stays visible
                if ([400, 401, 419, 422].includes(xhr.status)) {
                    return;
                }
              
                let errorMessage = '';
                let alertClass = 'alert-danger'; // Default to danger

                // Customize the message and alert type based on the status code
                if (xhr.status === 403) {
                    errorMessage = 'You do not have permission to view  data.';
                } else if (xhr.status === 404) {
                    errorMessage = 'The data endpoint could not be found.';
                    alertClass = 'alert-warning';
                } else if (xhr.status >= 500) {
                    errorMessage = 'A server error occurred. Please try again later.';
                } else {
                    errorMessage = 'An unknown error occurred while loading the data.';
                }

                // Create the HTML for our custom error message div
                const errorHtml = `
                    <div class="alert ${alertClass} text-center m-3" role="alert">
                        <i class="fa fa-exclamation-triangle me-2"></i>
                        <strong>Error:44</strong> ${errorMessage}
                    </div>
                `;

                // Replace the DataTable's content with our error message
                $('#dataTable_wrapper').html(errorHtml);
            }
        });

     ModalFormManager.init();

    CRUDManager.init({
            resource: 'Contact Items',
            serverSide: {
                url: "{{ route('masterapp.contacts.items', ['id' => $contact->id]) }}",
                columns: [
                    { data: 'item_type', name: 'item_type' },
                    { data: 'item_value', name: 'item_value' },
                    { data: 'actions', name: 'actions', orderable: false, searchable: false }
                ],
            },
            buttons: [
                 @can('create-contact-item')
                {
                    text: '<i class="fa fa-plus"></i> Add Item',
                    className: 'btn btn-info',
                    action: 'create',
                    requireSelection: false,
                },
                @endcan
               
                {
                    extend: 'colvis',
                    className: 'btn btn-warning',
                    text: '<i class="fa fa-columns"></i> Columns'
                }
            ],
          
            filterInputs: [
                {
                    id: 'customSearchInput',
                    name: 'search',          
                    type: 'text'
                },
               
            ],
           
            endpoints: {
                create: "{{ route('masterapp.contact-items.create', ['id' => $contact->id]) }}",
                edit: "",
                delete: ""
            },
        });

        const $buttons = $('#dataTable_wrapper .dt-buttons');
        if ($buttons.length && $buttons.find('#customSearchInput').length === 0) {
            $buttons.append(`
                <div class="ms-2 d-inline-block align-middle">
                    <input type="text" id="customSearchInput" class="form-control" style="height: 38px;" placeholder="Search..." />
                </div>
            `);
        }

});


// Filter Toggle
    $('#toggleFilterBtn').click(function() {
      $('#filterWrapper').slideToggle();
    });

     $('#toggleFilterclear').click(function() {
      $('#filterWrapper').slideToggle();
    });

    $('#addModuleBtn').on('click', function() {
      const url = $(this).data('url');
      const title = $(this).data('title');
      ModalFormManager.openModal(url, title);
      
    });

    $(document).on('click', '.edit-item', function(e) {
      const url = $(this).data('url');
      const title = $(this).data('title');
      ModalFormManager.openModal(url, title);
      
    });

// Your form submission handlers remain the same and are correct.
 $(document).ready(function() {
    handleAjaxForm("#form-create-contact", {
        loadingIndicator: 'button',
        buttonTextSelector: '#btn-text',
        buttonSpinnerSelector: '#btn-spinner',
        modalToClose: "#genericModal",
        successTitle: "Contact Created!",
        reloadOnSuccess: true,
        
    });
});

 $(document).ready(function() {
    handleAjaxForm("#form-create-contact-item", {
        loadingIndicator: 'button',
        buttonTextSelector: '#btn-text',
        buttonSpinnerSelector: '#btn-spinner',
        modalToClose: "#genericModal",
        closeModalOnSuccess: true,
        successTitle: "Contact Item Created!",
        reloadOnSuccess: false,
        onSuccess: function () {
            if ($.fn.DataTable.isDataTable('#dataTable')) {
                $('#dataTable').DataTable().ajax.reload(null, false);
            }
        }
    });

    handleAjaxForm("#form-edit-contact", {
        loadingIndicator: 'button',
        buttonTextSelector: '#btn-text',
        buttonSpinnerSelector: '#btn-edit-spinner',
        modalToClose: "#genericModal",
        closeModalOnSuccess: true,
        successTitle: "Contact Updated!",
        reloadOnSuccess: false,
        onSuccess: function () {
            if ($.fn.DataTable.isDataTable('#dataTable')) {
                $('#dataTable').DataTable().ajax.reload(null, false);
            }
        }
    });

    handleAjaxForm("#form-edit-contact-item", {
        loadingIndicator: 'button',
        buttonTextSelector: '#btn-text',
        buttonSpinnerSelector: '#btn-edit-spinner',
        modalToClose: "#genericModal",
        closeModalOnSuccess: true,
        successTitle: "Contact Item Updated!",
        reloadOnSuccess: false,
        onSuccess: function () {
            if ($.fn.DataTable.isDataTable('#dataTable')) {
                $('#dataTable').DataTable().ajax.reload(null, false);
            }
        }
    });

     // for delete functionality
    handleDelete();
});

function formatPhoneInput($input) {
    const type = $('#type').val();
    if (type !== 'phone') {
        return;
    }

    let value = ($input.val() || '').replace(/\D/g, '').slice(0, 10);
    const parts = [];
    if (value.length > 0) parts.push(value.slice(0, 3));
    if (value.length > 3) parts.push(value.slice(3, 6));
    if (value.length > 6) parts.push(value.slice(6, 10));
    $input.val(parts.join('-'));
}

$(document).on('input', '[data-phone-mask]', function () {
    formatPhoneInput($(this));
});

$(document).on('change', '#type', function () {
    formatPhoneInput($('#value'));
});

$(document).on('shown.bs.modal', '#genericModal', function () {
    $(this).find('[data-phone-mask]').each(function () {
        formatPhoneInput($(this));
    });
});
</script>
@endpush
