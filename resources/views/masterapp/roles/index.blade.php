@extends('masterapp.layouts.app')
@section('content')

<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0 text-dark">Roles</h1>
      </div>
      <div class="col-sm-6 d-flex justify-content-end add-new">

       <button type="button" class="btn btn-default ml-2" id="toggleFilterBtn">
          <i class="fa fa-filter"></i> Filter
        </button>
        &nbsp;
          @can('create-role')
        <button type="button" class="btn btn-primary" id="addModuleBtn"
          data-url="{{ route('masterapp.roles.create') }}"
          data-title="Add New Role">
          <i class="fa fa-plus"></i> Add Role
        </button>
          @endcan
      </div>
    </div>
  </div>
</div>
<!-- Main content -->
<section class="content">
  <div class="container-fluid">

   @include('masterapp.roles._searchfilters')
    <div class="row">
      <div class="col-12">
        <div class="card">

          <!-- /.card-header -->
          <div class="card-body">
            <table id="dataTable" class="table table-bordered table-hover">
              <thead>
                <!-- <th><input type="checkbox" id="selectAll"></th> -->
                        <th> Role Name</th>
                         <th>Department</th>
                        <th>Permissions</th>

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
            const moduleDataRoute = "{{ route('masterapp.roles.data') }}";

            if (settings.url.indexOf(moduleDataRoute) !== -1) {
              
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
            resource: 'Roles',
            serverSide: {
                url: "{{ route('masterapp.roles.data') }}",
                columns: [
                    { data: 'name', name: 'name' },
                    { data: 'department', name: 'department.name' },
                    { data: 'permissions', name: 'permissions', orderable: false, searchable: false },
                    { data: 'actions', name: 'actions', orderable: false, searchable: false }
                ],
            },
            buttons:[],
          
            filterInputs: [
                {
                    id: 'customSearchInput',
                    name: 'search',          
                    type: 'text'
                },
                {
                    id: 'moduleFilter',     
                    name: 'department_id',  
                    type: 'select'
                }
            ],
           
            endpoints: {
                create: "{{ route('masterapp.roles.create') }}",
                edit: "",
                delete: ""
            },
        });

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

    $('#clearFiltersBtn').on('click', function(e) {
      e.preventDefault();
      $('#customSearchInput').val('').trigger('input');
      $('#moduleFilter').val('').trigger('change');
      if ($.fn.DataTable.isDataTable('#dataTable')) {
          $('#dataTable').DataTable().ajax.reload(null, false);
      }
    });

    $(document).on('click', '.edit-item', function(e) {
      const url = $(this).data('url');
      const title = $(this).data('title');
      ModalFormManager.openModal(url, title);
      
    });

// Your form submission handlers remain the same and are correct.
 $(document).ready(function() {
    handleAjaxForm("#form-create-role", {
        loadingIndicator: 'button',
        buttonTextSelector: '#btn-text',
        buttonSpinnerSelector: '#btn-spinner',
        modalToClose: "#genericModal",
        successTitle: "Role Created!",
        reloadOnSuccess: false,
        onSuccess: function () {
            if ($.fn.DataTable.isDataTable('#dataTable')) {
                $('#dataTable').DataTable().ajax.reload(null, false);
            }
        }
    });
});

 $(document).ready(function() {
    handleAjaxForm("#form-edit-role", {
        loadingIndicator: 'button',
        buttonTextSelector: '#btn-text',
        buttonSpinnerSelector: '#btn-spinner',
        modalToClose: "#genericModal",
        successTitle: "Role Updated!",
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
</script>
@endpush
