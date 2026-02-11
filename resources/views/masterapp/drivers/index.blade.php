@extends('masterapp.layouts.app')
@section('content')

<div class="content-header">
                    <div class="container-fluid">
                            <div class="row mb-2 align-items-center">
                                <div class="col-sm-6">
                                    <h1 class="m-0 text-dark">Drivers</h1>
                                </div>

                                <div class="col-sm-6 text-right">
                                    <a href="{{ route('masterapp.users.create') }}"
                                    class="btn btn-primary"
                                    style="width:150px;">
                                        <i class="fa fa-plus mr-1"></i> Add User
                                    </a>
                                </div>
                            </div>
                        </div>

                </div>

<section class="content">
    <div class="container-fluid">

        <div class="card">
            <div class="card-body">

                <table id="example2" class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            {{-- <th>Change Password</th> --}}
                            <th>Role</th>
                            <th>Wordpress User</th>
                            <th>Added Timestamp</th>
                            <th>Driver</th>
                            <th>Department</th>
                            <th>Publications</th>
                            <th>Contributor Status</th>
                            <th>Status</th>
                            <th>Status Notes</th>
                            <th>Active</th>
                            <th class="no-export no-vis">Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                    @foreach ($drivers as $driver)
                        <tr data-id="{{ $driver->id }}">

                            {{-- NAME --}}
                            <td>
                                <a href="{{ route('masterapp.entity.info', ['type' => 'users', 'id' => $driver->id]) }}"
                                   class="entity-link">
                                    {{ $driver->first_name }} {{ $driver->last_name }}
                                </a>
                            </td>

                            {{-- EMAIL --}}
                            <td>{{ $driver->email }}</td>

                            {{-- PHONE --}}
                            <td>{{ $driver->phone ?? '—' }}</td>

                             {{-- CHANGE PASSWORD (boolean) --}}
                            {{-- <td data-field="change_password">
                            {{ $driver->change_password ? "Yes" : "No" }}
                            </td> --}}

                            {{-- ROLES (multi) --}}
                            <td data-field="roles">
                            {{ $driver->roles->pluck("name")->implode(", ") ?: "NONE" }}
                                  </td>
                            
                            <td data-field="is_wordpress_user">
                            {{ $driver->is_wordpress_user ? "Yes" : "No" }}
                            </td>
                            <td>{{ $driver->created_at }}</td>

                            {{-- DRIVER (boolean) --}}
                            <td data-field="driver">
                            {{ $driver->driver ? "Yes" : "No" }}
                            </td>
            
                            {{-- DEPARTMENT --}}
                            <td>{{ $driver->department->name ?? 'N/A' }}</td>

                            {{-- PUBLICATIONS --}}
                            <td>
                                @forelse ($driver->publications as $publication)
                                    {{ $publication->name }}@if(!$loop->last), @endif
                                @empty
                                    <span class="text-muted">N/A</span>
                                @endforelse
                            </td>

                             <td>
                                      {{ $driver->contributor_status }}
                                  </td>
                            {{-- STATUS (INLINE EDIT) --}}
                            <td data-field="status_id">
                                <div class="status-container d-inline-block"
                                     data-id="{{ $driver->id }}">

                                    <span
                                        class="badge {{ $driver->status->badge_class ?? 'badge-secondary' }} status-badge"
                                        title="Click to change status"
                                        style="cursor:pointer;">
                                        {{ $driver->status->label ?? 'N/A' }}
                                    </span>

                                    <div class="status-select-wrapper mt-1" style="display:none;">
                                        <select class="form-control form-control-sm status-change-select">
                                            @foreach ($statusesList as $status)
                                                <option value="{{ $status->id }}"
                                                    {{ (int)$driver->status_id === (int)$status->id ? 'selected' : '' }}>
                                                    {{ $status->label }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </td>

                            {{-- STATUS NOTES (textarea) --}}
                                  <td data-field="status_notes">
                                      {{ $driver->status_notes }}
                                  </td>

                            {{-- ACTIVE --}}
                            {{-- <td class="text-center">
                                <span class="badge {{ $driver->active ? 'badge-success' : 'badge-danger' }}">
                                    {{ $driver->active ? 'Yes' : 'No' }}
                                </span>
                            </td> --}}
                             {{-- ajax toggle without page reload --}}
                                <td class="text-center">
                                    <div class="custom-control custom-switch">
                                        <input
                                            type="checkbox"
                                            class="custom-control-input js-toggle-active"
                                            id="activeSwitch{{ $driver->id }}"
                                            data-id="{{ $driver->id }}"
                                            {{ $driver->active ? 'checked' : '' }}
                                        >
                                        <label class="custom-control-label" for="activeSwitch{{ $driver->id }}"></label>
                                    </div>
                                </td>
                            {{-- ACTIONS --}}
                            <td class="no-export">
                                <div class="d-flex gap-2">

                                    {{-- VIEW --}}
                                    <a href="{{ route('masterapp.entity.info', ['type' => 'users', 'id' => $driver->id]) }}"
                                       class="action-icon"
                                       title="View Driver">
                                        <i class="fa fa-eye"></i>
                                    </a>

                                    {{-- EDIT (Users edit) --}}
                                    <a href="{{ route('masterapp.users.edit', $driver->id) }}"
                                       class="action-icon"
                                       title="Edit Driver">
                                        <i class="fa fa-edit"></i>
                                    </a>

                                    {{-- DELETE (destroy – future ready) --}}
                                    <button type="button"
                                        class="btn btn-link p-0 action-icon text-danger delete-item"
                                        data-url="{{ route('masterapp.users.destroy', $driver->id) }}"
                                        data-name="{{ $driver->first_name }} {{ $driver->last_name }}"
                                        title="Delete Driver">
                                        <i class="fa fa-trash"></i>
                                    </button>

                                </div>
                            </td>

                        </tr>
                    @endforeach
                    </tbody>
                </table>

            </div>
        </div>

    </div>
</section>

{{-- Generic Delete Modal --}}
@include('partials.generic-model')

<script src="{{ asset('js/jquery-3.6.0.min.js') }}"></script>
<script src="{{ asset('js/generic-delete-handler.js') }}"></script>


<script src="{{ asset('js/jquery-3.6.0.min.js') }}"></script>

<script>
$(function () {
     var dataTable=$('#example2').DataTable({
      "pageLength": 10,
      responsive: true,
      scrollX: false,
      autoWidth: false,
      lengthMenu: [[-1, 10, 50, 100], ["All", 10, 50, 100]],
      language: {
          lengthMenu: 'Show _MENU_',
          paginate: {
              next: '<i class="fa  fa-angle-double-right "></i>', 
              previous: '<i class="fa  fa-angle-double-left"></i>'
          },
          search: ''
      },

      dom: '<"top"Biplf>rt<"bottom bottomAlign"ip><"clear">',
      buttons: [
           {
            extend: 'print',
            text: '<i class="fa fa-print"></i> Print',
            className: 'btn btn-secondary',
            exportOptions: {
                columns: function (idx, data, node) {
                    const table = $('#example2').DataTable();
                    if ($(node).hasClass('no-vis')) return false;
                    return table.column(idx).visible();
                },
                format: { body: exportFormatter }
            },
            customize: function (win) {

                // Smaller font
                $(win.document.body).css('font-size', '8px');

                // Landscape + tight margins
                $(win.document.head).append(`
                    <style>
                        @page {
                            size: A4 landscape;
                            margin: 8mm;
                        }

                        table {
                            width: 100% !important;
                            table-layout: fixed;
                        }

                        th, td {
                            white-space: normal !important;
                            word-break: break-word;
                            overflow-wrap: break-word;
                            padding: 3px 4px !important;
                        }

                        th {
                            font-size: 8.5px;
                        }
                    </style>
                `);
            }
        },
        {
            extend: 'copyHtml5',
            text: '<i class="fa fa-copy"></i> Copy Data',
            className: 'btn btn-primary',
            exportOptions: {
                columns: exportVisibleColumns,
                format: { body: exportFormatter }
            }
        },

        {
            extend: 'excelHtml5',
            text: '<i class="fa fa-download"></i> Excel',
            className: 'btn btn-success',
            exportOptions: {
                columns: exportVisibleColumns,
                format: {
                    body: function (data, row, column, node) {

                        // Active toggle
                        if ($(node).find('.js-toggle-active').length) {
                            return $(node).find('.js-toggle-active').prop('checked')
                                ? 'Active'
                                : 'Inactive';
                        }

                        return $(node).text().trim();
                    }
                }
            }
        },

          // {
          //     extend: 'csvHtml5',
          //     text: '<i class="fa fa-download"></i> CSV',
          //     className: 'btn btn-info',
          //     exportOptions: {
          //         columns: [0, 1, 2, 3, 4, 5]
          //     }
          // },
        {
            extend: 'pdfHtml5',
            text: '<i class="fa fa-download"></i> PDF',
            className: 'btn btn-danger',
            orientation: 'landscape',
            pageSize: 'A4',
            exportOptions: {
                columns: exportVisibleColumns,
                format: { body: exportFormatter }
            },
            customize: function (doc) {

                const table = doc.content.find(c => c.table).table;
                const colCount = table.body[0].length;
                //    GLOBAL PDF STYLES
                doc.pageMargins = [6, 6, 6, 6];
                doc.defaultStyle.fontSize = 6;
                doc.styles.tableHeader.fontSize = 6.5;

                //    FORCE TABLE TO FIT PAGE
                table.widths = Array(colCount).fill((100 / colCount).toFixed(2) + '%');
                //    TEXT WRAPPING (CRITICAL)
                doc.styles.tableBodyEven = {
                    fontSize: 6,
                    margin: [0, 1, 0, 1]
                };
                doc.styles.tableBodyOdd = {
                    fontSize: 6,
                    margin: [0, 1, 0, 1]
                };
                //    HEADER STYLE
                table.body[0].forEach(cell => {
                    cell.fillColor = '#2c3e50';
                    cell.color = '#ffffff';
                    cell.alignment = 'left';
                });
            }
        },
        {
            extend: 'colvis',
            className: 'btn btn-warning',
            columns: ':not(.no-vis)'
        }
      ],
      columnDefs: [
        {
            targets: [2,4,6], // hidden initially
            visible: false
        },
        {
            targets: -1,
            orderable: false,
            searchable: true,
            className: 'no-vis'

            // targets: -1,
            // orderable: false,
            // searchable: false,
            // className: 'no-vis action-column'
        }
      ],
      fixedColumns: {
          rightColumns: 1
      },
      initComplete: function () {
          $('.dataTables_length').appendTo('.dataTables_wrapper .top');
          $('.dataTables_length').addClass('ml-2 d-flex align-items-center');
          var $topContainer = $('.top .dataTables_length').parent();
          $('.top .dataTables_length, .top .dataTables_paginate').wrapAll('<div class="length_pagination"></div>');
          var $topContaine1 = $('.length_pagination').parent();
          $('.top .dataTables_info, .top .length_pagination').wrapAll('<div class="show_page_align"></div>');
          var $topContaine2 = $('.dataTables_filter').parent();
          $(' .top .dt-buttons , .top .dataTables_filter').wrapAll('<div class=" btn_filter_align "></div>');
          // Set placeholder for search input and add search icon
          var $searchInput = $('.dataTables_filter input');
          $searchInput.attr('placeholder', 'Search..');
          // wrap input
          $searchInput.wrap('<div class="search-input-wrapper"></div>');
          // add class
          $searchInput.addClass('search-input');
          // ADD SEARCH ICON ELEMENT
          $searchInput.before('<i class="fa fa-search"></i>');
      }
  });

});
//  {{-- ajax toggle without page reload --}}
$(function () {

    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 5000,
        timerProgressBar: true,
        showClass: {
            popup: 'animate__animated animate__fadeInUp'
        },
        hideClass: {
            popup: 'animate__animated animate__fadeOutDown'
        }
    });

    //  EVENT DELEGATION (IMPORTANT)
    $(document).on('change', '.js-toggle-active', function () {

        const checkbox = $(this);
        const userId = checkbox.data('id');
        const isActive = checkbox.prop('checked');

        $.ajax({
            url: `{{ url('master-app/drivers') }}/${userId}/toggle-active`,
            type: 'PATCH',
            data: {
                _token: '{{ csrf_token() }}'
            },

            success: function () {
                Toast.fire({
                    icon: 'success',
                    title: isActive
                        ? 'Driver activated successfully'
                        : 'Driver deactivated successfully'
                });
            },

            error: function () {
                // rollback UI
                checkbox.prop('checked', !isActive);

                Toast.fire({
                    icon: 'error',
                    title: 'Failed to update driver status'
                });
            }
        });
    });

});
</script>
<script>
    //  * INLINE STATUS EDIT
    $(document).on('click', '.status-badge', function () {
        const $badge = $(this);
        const $container = $badge.closest('.status-container');

        $badge.hide();
        $container.find('.status-select-wrapper')
                  .show()
                  .find('select')
                  .focus();
    });

    $(document).on('change', '.status-change-select', function () {
        const $select = $(this);
        const $container = $select.closest('.status-container');
        const userId = $container.data('id');
        const statusId = $select.val();

        let url = "{{ route('masterapp.users.updateStatus', ':id') }}";
        url = url.replace(':id', userId);

        $.ajax({
            url: url,
            type: 'PATCH',
            data: {
                _token: '{{ csrf_token() }}',
                status_id: statusId
            },
            success: function (res) {

                const newLabel = $select.find('option:selected').text();
                const newClass = res.badge_class || 'badge-secondary';

                const $badge = $container.find('.status-badge');

                $badge
                    .removeClass(function (_, cls) {
                        return (cls.match(/badge-\S+/g) || []).join(' ');
                    })
                    .addClass(newClass)
                    .text(newLabel)
                    .show();

                $container.find('.status-select-wrapper').hide();
            },
            error: function () {
                resetStatusUI($container);
            }
        });
    });

    $(document).on('blur', '.status-change-select', function () {
        const $container = $(this).closest('.status-container');
        setTimeout(() => resetStatusUI($container), 200);
    });

    function resetStatusUI($container) {
        $container.find('.status-select-wrapper').hide();
        $container.find('.status-badge').show();
    }

    //  * DELETE HANDLER
    handleDelete();


</script>

{{-- SUCCESS TOAST --}}
@php $successMessage = session('success'); @endphp
@if ($successMessage)
<script>
Swal.fire({
    toast: true,
    position: 'top-end',
    icon: 'success',
    title: @json($successMessage),
    showConfirmButton: false,
    timer: 2000,
    timerProgressBar: true
});

</script>
@endif
<script>
//  {{-- export formatter function --}}
function exportFormatter(data, row, column, node) {

    //  ACTIVE TOGGLE (checkbox switch)
    if ($(node).find('.js-toggle-active').length) {
        return $(node).find('.js-toggle-active').prop('checked')
            ? 'Active'
            : 'Inactive';
    }

    //  STATUS DROPDOWN (ONLY selected value)
    const select = $(node).find('select');
    if (select.length) {
        return select.find('option:selected').text().trim();
    }

    // BADGES / SPANS (Status labels)
    if ($(node).find('.badge').length) {
        return $(node).find('.badge').text().trim();
    }

    //  DEFAULT: clean text
    return $('<div>').html(data).text().trim();
}

//  Helper to temporarily disable responsive, perform action, then re-enable (fixes export issues)

function exportVisibleColumns(idx, data, node) {
    const table = $('#example2').DataTable();

    // Exclude action column
    if ($(node).hasClass('no-vis')) {
        return false;
    }

    // Export only columns enabled via Column Visibility
    return table.column(idx).visible();
}
</script>
@endsection
