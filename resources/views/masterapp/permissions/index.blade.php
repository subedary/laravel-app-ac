@extends('masterapp.layouts.app')
@section('content')

<div class="content-header">
  <div class="container-fluid">
    <div class="row mb-2">
      <div class="col-sm-6">
        <h1 class="m-0 text-dark">Permissions</h1>
      </div>
      <div class="col-sm-6 d-flex justify-content-end add-new">

        <button type="button" class="btn btn-default ml-2" id="toggleFilterBtn">
          <i class="fa fa-filter"></i> Filter
        </button>
        &nbsp;
        @can('create-permission')
        <button type="button" class="btn btn-primary" id="addpermissionBtn"
          data-url="{{ route('masterapp.permissions.create') }}"
          data-title="Add New Permissions">
          <i class="fa fa-plus"></i> Add Permission
        </button>
        @endcan
      </div>
    </div>
  </div>
</div>

<!-- Main content -->
<section class="content">
  <div class="container-fluid">

    <!-- Search filter -->
    @include('masterapp.permissions._searchfilters')
    <!-- Search filter -->
    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-body">
            <table id="permissionstable" class="table table-bordered table-hover">
              <thead>
                <tr>
                  <th>Name</th>
                  <th>Display Name</th>
                  <th>slug</th>
                  <th>Guard Name</th>
                  <th>Module Name</th>
                  <th>Actions</th>
                </tr>
              </thead>
              <tbody>
                @foreach ($permissions as $permission)
                <tr data-id="{{ $permission->id }}">
                  <td data-field="name">{{ $permission->name }}</td>
                  <td data-field="email">{{ $permission->display_name }}</td>
                  <td data-field="email">{{ $permission->slug }}</td>
                  <td data-field="email">{{ $permission->guard_name }}</td>
                  <td data-field="module_name">{{ $permission->module->name }}</td>
                  <td data-field="actions">
                    <div class="action-div">
                      @can('edit-permission')
                      <button type="button" class="btn btn-link p-0 action-icon edit-item"
                        data-url="{{ route('masterapp.permissions.edit', ['permission' => $permission->id]) }}"
                        data-title="Edit permission"
                        title="Edit permission">
                        <i class="fa fa-edit"></i>
                      </button>
                      @endcan
                      @can('delete-permission')
                      <button type="button"
                        class="btn btn-link p-0 action-icon text-danger delete-item"
                        data-url="{{ route('masterapp.permissions.destroy', ['permission' => $permission->id]) }}"
                        data-name="{{ $permission->name }}"
                        title="Delete permission">
                        <i class="fa fa-trash"></i>
                      </button>
                      @endcan
                    </div>
                  </td>
                </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Generic Modal -->
@include('masterapp.partials.generic-model')

<script src="{{ asset('js/jquery-3.6.0.min.js') }}"></script> 


<script>
  // Declare dataTable in a broader scope so it's accessible to other functions
  var dataTable;

  $(function() {
    dataTable = $('#permissionstable').DataTable({
      "pageLength": 10,
      responsive: true,
      scrollX: false,
      autoWidth: false,
      lengthMenu: [
        [-1, 10, 50, 100],
        ["All", 10, 50, 100]
      ],
      language: {
        lengthMenu: 'Show _MENU_',
        paginate: {
          next: '<i class="fa  fa-angle-double-right "></i>',
          previous: '<i class="fa  fa-angle-double-left"></i>'
        }
        // Removed search: '' since we're using custom search
      },
      dom: '<"top"Bipl>rt<"bottom bottomAlign"ip><"clear">', // Removed 'f' to hide default search
      buttons: [],
      fixedColumns: {
        rightColumns: 1
      },
      initComplete: function() {
        $('.dataTables_length').appendTo('.dataTables_wrapper .top');
        $('.dataTables_length').addClass('ml-2 d-flex align-items-center');
        var $topContainer = $('.top .dataTables_length').parent();
        $('.top .dataTables_length, .top .dataTables_paginate').wrapAll('<div class="length_pagination"></div>');
        var $topContaine1 = $('.length_pagination').parent();
        $('.top .dataTables_info, .top .length_pagination').wrapAll('<div class="show_page_align"></div>');

        // Setup custom filters after DataTable initialization
        setupCustomFilters();
      }
    });
  });

  function upsertPermissionRow(permission) {
    if (!permission || !permission.id || !dataTable) {
      return;
    }

    const rowData = [
      permission.name || '',
      permission.display_name || '',
      permission.slug || '',
      permission.guard_name || '',
      permission.module_name || '',
      permission.actions_html || ''
    ];

    const $existingRow = $('#permissionstable tbody tr[data-id="' + permission.id + '"]');
    if ($existingRow.length) {
      const row = dataTable.row($existingRow);
      row.data(rowData).draw(false);
      $(row.node()).attr('data-id', permission.id);
    } else {
      const newRow = dataTable.row.add(rowData).draw(false);
      $(newRow.node()).attr('data-id', permission.id);
    }
  }

  // Define setupCustomFilters function once
  function setupCustomFilters() {
    // Custom search input
    $('#customSearchInput').on('keyup change', function() {
      dataTable.search(this.value).draw();
    });

    // Module filter dropdown
    $('#moduleFilter').on('change', function() {
      var moduleValue = this.value;

      // If module filter is selected, apply custom filtering
      if (moduleValue) {
        // Custom search function to filter by module
        dataTable.columns().search('').draw(); // Clear all column searches first

        // Find the column index that contains module data
        // You mentioned it's the 5th column, so index is 4 (0-indexed)
        var moduleColumnIndex = 4; // Adjust based on your table structure

        // Apply search to the module column
        dataTable.column(moduleColumnIndex).search(moduleValue).draw();
      } else {
        // If no module selected, clear the module column filter
        dataTable.columns().search('').draw();
      }
    });

    // Clear all filters link
    $('#clearFiltersBtn').on('click', function(e) {
      e.preventDefault();
      $('#customSearchInput').val('');
      $('#moduleFilter').val('');
      dataTable.search('').columns().search('').draw();
    });

    // Filter Toggle
    $('#toggleFilterBtn').click(function() {
      $('#filterWrapper').slideToggle();
    });

     $('#toggleFilterclear').click(function() {
      $('#filterWrapper').slideToggle();
    });

    
  }

  $(document).ready(function() {
    // 1. Initialize the manager
    ModalFormManager.init();

    // 2. Bind click events for both Add and Edit buttons
    $('#addpermissionBtn').on('click', function() {
      const url = $(this).data('url');
      const title = $(this).data('title');
      ModalFormManager.openModal(url, title);
    });

    $(document).on('click', '.edit-item', function(e) {
      const url = $(this).data('url');
      const title = $(this).data('title');
      ModalFormManager.openModal(url, title);
    });

    // function for handle add form
    handleAjaxForm("#form-permission", {
      loadingIndicator: 'button',
      buttonTextSelector: '#btn-text',
      buttonSpinnerSelector: '#btn-spinner',
      modalToClose: "#genericModal",
      reloadOnSuccess: false,
      onSuccess: function (res) {
        if (typeof dataTable !== 'undefined') {
          upsertPermissionRow(res?.permission);
        }
      }
    });

    // form for edit form
    handleAjaxForm("#form-edit-permission", {
      loadingIndicator: 'button',
      buttonTextSelector: '#btn-edit-text',
      buttonSpinnerSelector: '#btn-edit-spinner',
      successTitle: "Module Updated!",
      reloadOnSuccess: false,
      onSuccess: function (res) {
        if (typeof dataTable !== 'undefined') {
          upsertPermissionRow(res?.permission);
        }
      }
    });

    // for delete functionality
    handleDelete();
  });
</script>

@endsection
