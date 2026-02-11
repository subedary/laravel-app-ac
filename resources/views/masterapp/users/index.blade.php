@extends('masterapp.layouts.app')
@section('content')

            <div class="content-header">
                    <div class="container-fluid">
                            <div class="row mb-2 align-items-center">
                                <div class="col-sm-6">
                                    <h1 class="m-0 text-dark">Users</h1>
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
            <!-- Main content -->
            <section class="content">
                <div class="container-fluid">
                  <div class="row">
                    <div class="col-12">
                      <div class="card">
                        <div class="card-header">
                          
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                          <table id="example2" class="table table-bordered table-hover">
                            <thead>
                                  <th>Name</th>
                                  <th>Email</th>
                                  <th>Phone</th>
                                  {{-- <th>Change Password</th> --}}
                                  <th>Role</th>
                                  {{-- <th>Permissions</th> --}}
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
                            </thead>
                            <tbody>
                            @foreach ($users as $user)
                              <tr data-id="{{ $user->id }}">

                                  <td data-field="name">
                                  {{-- <a href="{{ route('masterapp.users.show', $user->id) }}"
                                      class="entity-link" > --}}
                                  <a href="{{ route('masterapp.entity.info', ['type' => 'users', 'id' => $user->id]) }}"
                                   class="entity-link" >
                                    {{ $user->first_name }}  {{ $user->last_name }}
                                  </a>
                                  </td>
                                  <td data-field="email">{{ $user->email }}</td>
                                  <td data-field="phone">{{ $user->phone }}</td>

                                  {{-- CHANGE PASSWORD (boolean) --}}
                                  {{-- <td data-field="change_password">
                                      {{ $user->change_password ? "Yes" : "No" }}
                                  </td> --}}

                                  {{-- ROLES (multi) --}}
                                  <td data-field="roles">
                                      {{ $user->roles->pluck("name")->implode(", ") ?: "NONE" }}
                                  </td>

                                  {{-- PERMISSIONS (multi) --}}
                                  {{-- <td data-field="permissions">
                                      {{ $user->getAllPermissions()->pluck("name")->implode(", ") ?: "NONE" }}
                                  </td> --}}
                                  <td data-field="is_wordpress_user">
                                      {{ $user->is_wordpress_user ? "Yes" : "No" }}
                                  </td>
                                  <td>{{ $user->created_at }}</td>

                                  {{-- DRIVER (boolean) --}}
                                  <td data-field="driver">
                                      {{ $user->driver ? "Yes" : "No" }}
                                  </td>
                                    <td data-field="department_id">
                                      {{ $user->department->name ?? "N/A" }}
                                  </td>
                                  {{-- <td data-field="publications">
                                    {{ $user->publication_users->publication_id ?? "N/A" }} --}}
                                    <td data-field="publications">
                                    @forelse ($user->publications as $publication)
                                        {{-- <span class="badge badge-info mr-1"> --}}
                                            {{ $publication->name }}

                                        {{-- </span> --}}
                                    @empty
                                        <span class="text-muted">N/A</span>
                                    @endforelse
                                </td>

                                  <td data-field="contributor_status">
                                      {{ $user->contributor_status }}
                                  </td>
                                  {{-- STATUS (select) --}}
                                    <td data-field="status_id">
                                        <div class="status-container d-inline-block"
                                            data-id="{{ $user->id }}">

                                            {{-- Status badge --}}
                                            <span
                                                class="badge {{ $user->status->badge_class ?? 'badge-secondary' }} status-badge"
                                                title="Click to change status"
                                                style="cursor: pointer;"
                                            >
                                                {{ $user->status->label ?? 'N/A' }}
                                            </span>

                                            {{-- Hidden select (inline edit) --}}
                                            <div class="status-select-wrapper mt-1" style="display: none;">
                                                <select class="form-control form-control-sm status-change-select">
                                                    @foreach ($statusesList as $status)
                                                        <option
                                                            value="{{ $status->id }}"
                                                            {{ (int) $user->status_id === (int) $status->id ? 'selected' : '' }}
                                                        >
                                                            {{ $status->label }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                <div class="status-spinner d-none ml-2">
                                                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                                </div>
                                            </div>

                                        </div>
                                    </td>

                                  {{-- STATUS NOTES (textarea) --}}
                                  <td data-field="status_notes">
                                      {{ $user->status_notes }}
                                  </td>
                                        
                                {{-- ajax toggle without page reload --}}
                                <td class="text-center">
                                    <div class="d-flex align-items-center justify-content-center">
                                        <div class="custom-control custom-switch">
                                            <input
                                                type="checkbox"
                                                class="custom-control-input js-toggle-active"
                                                id="activeSwitch{{ $user->id }}"
                                                data-id="{{ $user->id }}"
                                                {{ $user->active ? 'checked' : '' }}
                                            >
                                            <label class="custom-control-label" for="activeSwitch{{ $user->id }}"></label>
                                        </div>
                                        <div class="active-spinner d-none ml-2">
                                            <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                                        </div>
                                    </div>
                                </td>
                                    
                                    {{-- ACTIONS --}}
                                    <td data-field="actions" class="no-export">
                                        <div class="action-div d-flex gap-2">

                                            {{-- View --}}
                                            <a href="{{ route('masterapp.entity.info', ['type' => 'users', 'id' => $user->id]) }}"
                                                title="View user" class="action-icon entity-link">
                                                <i class="fa fa-eye" aria-hidden="true"></i>
                                            </a>

                                            {{-- Edit --}}
                                            <a href="{{ route('masterapp.users.edit', $user->id) }}"
                                                title="Edit user" class="action-icon">
                                                <i class="fa fa-edit" aria-hidden="true"></i>
                                            </a>
                                          
                                            {{-- <form class="d-inline js-delete-user"
                                                data-url="{{ route('masterapp.users.destroy', $user->id) }}">
                                                @csrf
                                                @method('DELETE')

                                                <button type="submit"
                                                        class="action-icon text-danger btn btn-link p-0"
                                                        title="Delete user" onclick=del_user($id)>
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </form> --}}
                                             {{-- @can('delete-users') --}}
                                        <button type="button"
                                            class="btn btn-link p-0 action-icon text-danger delete-item"
                                            data-url="{{ route('masterapp.users.destroy',  $user->id) }}"
                                            data-name="{{ $user->name }}"
                                            title="Delete User">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                        {{-- @endcan --}}
                                        </div>
                                    </td>
                              </tr>
                          @endforeach
                            </tbody>
                            <!-- <tfoot>
                            <tr>
                              <th>Rendering engine</th>
                              <th>Browser</th>
                              <th>Platform(s)</th>
                              <th>Engine version</th>
                              <th>CSS grade</th>
                            </tr> -->
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

<script src="{{ asset('js/generic-model-form.js') }}"></script>
<script src="{{ asset('js/ajax-form-handler.js') }}"></script>
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
            targets: [2, 4, 6], 
            // hidden initially
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

        // Show spinner
        checkbox.closest('td').find('.active-spinner').removeClass('d-none');

        $.ajax({
            url: `{{ url('master-app/users') }}/${userId}/toggle-active`,
            type: 'PATCH',
            data: {
                _token: '{{ csrf_token() }}'
            },

            success: function () {
                // Hide spinner
                checkbox.closest('td').find('.active-spinner').addClass('d-none');

                Toast.fire({
                    icon: 'success',
                    title: isActive
                        ? 'User activated successfully'
                        : 'User deactivated successfully'
                });
            },

            error: function () {
                // Hide spinner
                checkbox.closest('td').find('.active-spinner').addClass('d-none');

                // rollback UI
                checkbox.prop('checked', !isActive);

                Toast.fire({
                    icon: 'error',
                    title: 'Failed to update user status'
                });
            }
        });
    });

});


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

    $(document).on('click', '.status-badge', function () {
        const $badge = $(this);
        const $container = $badge.closest('.status-container');

        $badge.hide();
        $container
            .find('.status-select-wrapper')
            .show()
            .find('.status-change-select')
            .focus();
    });

    // Update status on change
    $(document).on('change', '.status-change-select', function () {
        const $select = $(this);
        const $container = $select.closest('.status-container');
        const userId = $container.data('id');
        const statusId = $select.val();

        // Show spinner
        $container.find('.status-spinner').removeClass('d-none');

        let url = "{{ route('masterapp.users.updateStatus', ':id') }}";
        url = url.replace(':id', userId);

        $.ajax({
            url: url,
            type: 'PATCH',
            data: {
                _token: '{{ csrf_token() }}',
                status_id: statusId
            },

            success: function (response) {
                // Hide spinner
                $container.find('.status-spinner').addClass('d-none');

                if (response.success) {

                    const newLabel = $select.find('option:selected').text();
                    const newClass = response.badge_class || 'badge-secondary';

                    const $badge = $container.find('.status-badge');

                    // Reset & update badge
                    $badge
                        .removeClass(function (_, cls) {
                            return (cls.match(/badge-\S+/g) || []).join(' ');
                        })
                        .addClass(newClass)
                        .text(newLabel)
                        .show();

                    $container.find('.status-select-wrapper').hide();

                    // Show success toast
                    Toast.fire({
                        icon: 'success',
                        title: 'User status updated successfully'
                    });
                }
            },

            error: function () {
                // Hide spinner
                $container.find('.status-spinner').addClass('d-none');

                alert('Failed to update status. Please try again.');
                resetStatusUI($container);
            }
        });
    });

    // Blur → revert UI (no change)
    $(document).on('blur', '.status-change-select', function () {
        const $container = $(this).closest('.status-container');

        // Delay prevents immediate blur after click
        setTimeout(() => {
            resetStatusUI($container);
        }, 200);
    });

    // Helper: Reset UI
    function resetStatusUI($container) {
        $container.find('.status-select-wrapper').hide();
        $container.find('.status-badge').show();
    }

});




</script>

@php
    $successMessage = session()->pull('success');
@endphp

<script>
document.addEventListener('DOMContentLoaded', () => {

    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 2000,
        timerProgressBar: true,
        showClass: {
            popup: 'animate__animated animate__fadeInUp'
        },
        hideClass: {
            popup: 'animate__animated animate__fadeOutDown'
        }
    });

    //  . Normal redirect success (non-AJAX)
    @if ($successMessage)
        Toast.fire({
            icon: 'success',
            title: @json($successMessage)
        });
    @endif

    //  . AJAX redirect success (?created=1)
    const params = new URLSearchParams(window.location.search);

    if (params.get('created') === '1') {
        Toast.fire({
            icon: 'success',
            title: params.get('message') || 'User created successfully'
        });

        //  remove query params so it never repeats
        window.history.replaceState({}, document.title, window.location.pathname);
    }

});

</script>
<script>
    handleDelete();
// $(function () {

//     const Toast = Swal.mixin({
//         toast: true,
//         position: 'top-end',
//         showConfirmButton: false,
//         timer: 2000,
//         timerProgressBar: true,
//         showClass: {
//             popup: 'animate__animated animate__fadeInUp'
//         },
//         hideClass: {
//             popup: 'animate__animated animate__fadeOutDown'
//         }
//     });

//     //  EVENT DELEGATION (IMPORTANT)
//     $(document).on('submit', '.js-delete-user', function (e) {
//         e.preventDefault();

//         const $form = $(this);
//         const url = $form.data('url');
//         const token = $form.find('input[name="_token"]').val();

//         Swal.fire({
//             title: 'Are you sure?',
//             text: 'This user will be deleted.',
//             icon: 'warning',
//             showCancelButton: true,
//             confirmButtonColor: '#d33',
//             cancelButtonColor: '#6c757d',
//             confirmButtonText: 'Yes',
//             cancelButtonText: 'Cancel',
//             position: 'top-center'
//         }).then((result) => {

//             if (!result.isConfirmed) return;

//             $.ajax({
//                 url: url,
//                 type: 'POST',
//                 data: {
//                     _token: token,
//                     _method: 'DELETE'
//                 },
//                 dataType: 'json',

//                 success: function (res) {
//                     Toast.fire({
//                         icon: 'success',
//                         title: res.message || 'User deleted successfully'
//                     });

//                     $form.closest('tr').fadeOut(300, function () {
//                         $(this).remove();
//                     });
//                 },

//                 error: function () {
//                     Toast.fire({
//                         icon: 'error',
//                         title: 'Failed to delete user'
//                     });
//                 }
//             });
//         });
//     });

// });

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

