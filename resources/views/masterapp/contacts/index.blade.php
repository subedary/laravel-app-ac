@extends('masterapp.layouts.app')
@section('content')

<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0 text-dark">Contacts</h1>
      </div>
      <div class="col-sm-6 d-flex justify-content-end add-new">

       <button type="button" class="btn btn-default ml-2" id="toggleFilterBtn">
          <i class="fa fa-filter"></i> Filter
        </button>
        &nbsp;
          @can('create-contact')
        <button type="button" class="btn btn-primary" id="addModuleBtn"
          data-url="{{ route('masterapp.contacts.create') }}"
          data-title="Add New Contact">
          <i class="fa fa-plus"></i> Add Contact
        </button>
          @endcan
      </div>
    </div>
  </div>
</div>
<!-- Main content -->
<section class="content">
  <div class="container-fluid">

   @include('masterapp.contacts._searchfilters')
    <div class="row">
      <div class="col-12">
        <div class="card">

          <!-- /.card-header -->
          <div class="card-body">
            <table id="dataTable" class="table table-bordered table-hover">
              <thead>
                <!-- <th><input type="checkbox" id="selectAll"></th> -->
                        <th>Name</th>
                        <th>Notes</th>
                        <th>Items</th>
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

@push('styles')
<style>
  .contact-items-count {
    cursor: pointer;
  }

  #dataTable {
    border-collapse: collapse;
  }

  #dataTable th,
  #dataTable td {
    border: 1px solid #dee2e6 !important;
  }

  .popover .table-bordered th,
  .popover .table-bordered td {
    border: 1px solid #dee2e6 !important;
  }
</style>
@endpush

@push('scripts')

 <script src="{{ asset('js/permission-checkboxes.js') }}" defer></script>
<script>

     $.fn.dataTable.ext.errMode = 'none';

     
     $(document).ready(function() {


         $(document).ajaxError(function(event, xhr, settings, thrownError) {
            // Store the route URL in a variable for clarity
            const moduleDataRoute = "{{ route('masterapp.contacts.data') }}?debug_counts=1";

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
                url: "{{ route('masterapp.contacts.data') }}",
                columns: [
                    { data: 'name', name: 'name' },
                    { data: 'Notes', name: 'notes' },
                    { data: 'ItemsCount', name: 'contact_items_count' },
                    { data: 'actions', name: 'actions', orderable: false, searchable: false }
                ],
            },
            buttons:[
            ],
          
            filterInputs: [
                {
                    id: 'customSearchInput',
                    name: 'search',          
                    type: 'text'
                },
               
            ],
           
            endpoints: {
                create: "{{ route('masterapp.contacts.create') }}",
                edit: "",
                delete: ""
            },
        });

    function escapeHtml(value) {
        return String(value)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function disposePopover(el) {
        if (typeof bootstrap !== 'undefined' && bootstrap.Popover && typeof bootstrap.Popover.getInstance === 'function') {
            const instance = bootstrap.Popover.getInstance(el);
            if (instance) instance.dispose();
            return;
        }

        if (typeof $ !== 'undefined' && $.fn && $.fn.popover) {
            $(el).popover('dispose');
        }
    }

    function initPopover(el) {
        const content = el.getAttribute('data-bs-content') || el.getAttribute('data-content') || 'Loading...';

        if (typeof bootstrap !== 'undefined' && bootstrap.Popover) {
            new bootstrap.Popover(el, {
                html: true,
                sanitize: false,
                trigger: 'hover',
                container: 'body',
                content
            });
            return;
        }

        if (typeof $ !== 'undefined' && $.fn && $.fn.popover) {
            $(el).popover({
                html: true,
                sanitize: false,
                trigger: 'hover',
                container: 'body',
                content
            });
        }
    }

    function updatePopoverContent(el, html) {
        el.setAttribute('data-bs-content', html);
        el.setAttribute('data-content', html);

        if (typeof bootstrap !== 'undefined' && bootstrap.Popover && typeof bootstrap.Popover.getInstance === 'function') {
            const instance = bootstrap.Popover.getInstance(el);
            if (instance) {
                if (typeof instance.setContent === 'function') {
                    instance.setContent({ '.popover-body': html });
                } else {
                    instance.update();
                }
            }
            return;
        }

        if (typeof $ !== 'undefined' && $.fn && $.fn.popover) {
            $(el).popover('dispose');
            $(el).popover({
                html: true,
                sanitize: false,
                trigger: 'hover',
                container: 'body',
                content: html
            });
        }
    }

    function bindPopoverLoad(el) {
        const handler = function () {
            if (el.dataset.loaded === '1' || el.dataset.loading === '1') {
                return;
            }

            el.dataset.loading = '1';

            const url = el.dataset.url;
            Ajax.get(url)
                .then((res) => {
                    const items = res.items || [];
                    const html = buildItemsTable(items);

                    updatePopoverContent(el, html);
                    el.dataset.loaded = '1';
                    el.dataset.loading = '0';
                })
                .catch(() => {
                    const html = '<div class="text-muted">Failed to load items</div>';
                    updatePopoverContent(el, html);
                    el.dataset.loaded = '1';
                    el.dataset.loading = '0';
                });
        };

        if (typeof $ !== 'undefined' && $.fn) {
            $(el).off('shown.bs.popover.contactItems').on('shown.bs.popover.contactItems', handler);
            return;
        }

        el.removeEventListener('shown.bs.popover', handler);
        el.addEventListener('shown.bs.popover', handler);
    }

    function initItemPopovers() {
        const elements = document.querySelectorAll('.contact-items-count');
        elements.forEach((el) => {
            disposePopover(el);
            initPopover(el);
            bindPopoverLoad(el);
        });
    }

    $('#dataTable').on('draw.dt', function () {
        initItemPopovers();
    });

    $('#dataTable').on('processing.dt', function (e, settings, processing) {
        if (!processing) {
            $('#dataTable_processing').hide();
        }
    });

    initItemPopovers();

    function buildItemsTable(items) {
        if (!items.length) {
            return '<div class="text-muted">No items</div>';
        }

        const rows = items.map((item) => {
            const type = escapeHtml(item.type);
            const value = escapeHtml(item.value);
            return `<tr><td>${type}</td><td>${value}</td></tr>`;
        }).join('');

        return `
            <table class="table table-bordered table-sm mb-0" style="border-collapse: collapse;">
                <thead>
                    <tr><th>Type</th><th>Value</th></tr>
                </thead>
                <tbody>${rows}</tbody>
            </table>
        `;
    }

    $(document).on('click', '.contact-items-count', function (e) {
        e.preventDefault();
        const el = this;
        const tr = $(el).closest('tr');
        const table = $('#dataTable').DataTable();
        const row = table.row(tr);

        if (row.child.isShown()) {
            row.child.hide();
            tr.removeClass('shown');
            return;
        }

        row.child('<div class="text-muted">Loading...</div>').show();
        tr.addClass('shown');

        const url = el.dataset.url;
        Ajax.get(url)
            .then((res) => {
                const items = res.items || [];
                row.child(buildItemsTable(items)).show();
            })
            .catch(() => {
                row.child('<div class="text-muted">Failed to load items</div>').show();
            });
    });

});


// Filter Toggle
    $('#toggleFilterBtn').click(function() {
      $('#filterWrapper').slideToggle();
    });

    $('#toggleFilterclear').click(function() {
      $('#filterWrapper').slideToggle();
    });

    $('#clearSearchBtn').on('click', function () {
      $('#customSearchInput').val('');
      if ($.fn.DataTable.isDataTable('#dataTable')) {
        $('#dataTable').DataTable().ajax.reload(null, false);
      }
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
        reloadOnSuccess: false,
        onSuccess: function () {
            if ($.fn.DataTable.isDataTable('#dataTable')) {
                $('#dataTable').DataTable().ajax.reload(null, false);
            }
        },
        
    });
});

 $(document).ready(function() {
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

     // for delete functionality
    handleDelete();
});
</script>
@endpush
