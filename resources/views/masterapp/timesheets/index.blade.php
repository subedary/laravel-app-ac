@extends('masterapp.layouts.app')
@section('content')

{{-- HEADER --}}
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2 align-items-center">
            <div class="col-sm-6">
                <h1 class="m-0 text-dark">Timesheets</h1>
            </div>

            <div class="col-sm-6 d-flex justify-content-end add-new">
                <button type="button" class="btn btn-default ml-2" id="toggleFilterBtn">
                    <i class="fa fa-filter"></i> Filter
                </button>

                <button type="button"
                    class="btn btn-primary add-new ml-2"
                    data-toggle="modal"
                    data-target="#addTimesheetModal">
                <i class="fa fa-plus"></i> Add Timesheet
            </button>
            </div>
        </div>
    </div>
</div>

{{-- CONTENT --}}
<section class="content">
    <div class="container-fluid">

        {{-- Filters (Server-Side Logic for DataTable to Read) --}}
        @php
            $hasFilters = request()->hasAny(['user_id', 'date_from', 'date_to', 'type']);
            $displayFilter = $hasFilters ? 'block' : 'none';
        @endphp

        <div class="filter-wrapper" id="filterWrapper" style="display: {{ $displayFilter }};">
            <a href="#" class="close-filter-btn" id="toggleFilterclear" title="Clear Filters & Close">
                &times;
            </a>
            <form id="filterForm">
                <div class="row align-items-end">
                    <div class="col-md-3">
                        <label class="font-weight-bold">User</label>
                        <select id="filter_user_id" name="user_id" class="form-control filter-input">
                            <option value="">All Users</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                    {{ $user->first_name }} {{ $user->last_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="font-weight-bold">Date Range (Start Time)</label>
                        <div class="input-group">
                            <input type="date" id="filter_date_from" name="date_from" class="form-control filter-input" value="{{ request('date_from') }}">
                            <div class="input-group-prepend input-group-append">
                                <span class="input-group-text border-left-0 border-right-0 bg-white">to</span>
                            </div>
                            <input type="date" id="filter_date_to" name="date_to" class="form-control filter-input" value="{{ request('date_to') }}">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <label class="font-weight-bold">Type</label>
                        <select id="filter_type" name="type" class="form-control filter-input">
                            <option value="">All Types</option>
                            <option value="normal_paid" {{ request('type') == 'normal_paid' ? 'selected' : '' }}>Normal Paid</option>
                            <option value="absent_unpaid" {{ request('type') == 'absent_unpaid' ? 'selected' : '' }}>Absent (Unpaid)</option>
                            <option value="holiday_paid" {{ request('type') == 'holiday_paid' ? 'selected' : '' }}>Holiday Paid</option>
                            <option value="sick_paid" {{ request('type') == 'sick_paid' ? 'selected' : '' }}>Sick Paid</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <button type="button" id="applyFilterBtn" class="btn btn-primary btn-block"><i class="fa fa-filter"></i> Apply Filter</button>
                    </div>
                </div>
                <div class="row mt-2">
                     <div class="col-md-12 text-right">
                        <a href="{{ route('masterapp.timesheets.index') }}" class="btn btn-link btn-sm text-secondary">Clear All Filters</a>
                     </div>
                </div>
            </form>
        </div>

        {{-- Active Filters Badges --}}
        <div id="activeFilters" class="mb-3" style="display:none;">
            <strong>Active Filters:</strong>
            <span id="activeFiltersList"></span>
        </div>

        <div class="row">
            <div class="col-12">

                <div class="card">
                    <div class="card-body">

                        <table id="dataTable"
                               class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>User</th>
                                    <th>Start Time</th>
                                    <th>End Time</th>
                                    <th>Hours</th>
                                    <th>Clock In Mode</th>
                                    <th>Type</th>
                                    <th>Notes</th>
                                    <th class="no-export no-vis">Actions</th>
                                </tr>
                            </thead>

                            <tbody>
                                {{-- Loaded via AJAX --}}
                            </tbody>
                        </table>

                    </div>
                </div>

            </div>
        </div>
    </div>
</section>

{{-- Add Timesheet Modal --}}
<div class="modal fade" id="addTimesheetModal" tabindex="-1">
    <div class="modal-dialog">
                <form id="addTimesheetForm" action="{{ route('masterapp.timesheets.store') }}" method="POST">
            @csrf

            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Timesheet Entry</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>

                <div class="modal-body">

                    {{-- USER --}}
                    <div class="form-group">
                        <label>User<span class="text-danger">*</span></label>
                        <select name="user_id" class="form-control" required>
                            <option value="">Select User</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}">
                                    {{ $user->first_name }} {{ $user->last_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- START TIME --}}
                    <div class="form-group">
                        <label>Start Time<span class="text-danger">*</span></label>
                        <input type="datetime-local"
                               name="start_time"
                               class="form-control"
                               required>
                    </div>

                    {{-- END TIME --}}
                    <div class="form-group">
                        <label>End Time<span class="text-danger">*</span></label>
                        <input type="datetime-local"
                               name="end_time"
                               class="form-control">
                    </div>

                    {{-- CLOCK IN MODE --}}
                    <div class="form-group">
                        <label>Clock In Mode</label>
                        <select name="clock_in_mode" class="form-control" required>
                            <option value="office">Office</option>
                            <option value="remote">Remote</option>
                            <option value="out_of_office">Out of Office</option>
                            <option value="do_not_disturb">Do Not Disturb</option>
                        </select>
                    </div>

                    {{-- TYPE --}}
                    <div class="form-group">
                        <label>Type</label>
                        <select name="type" class="form-control" required>
                            <option value="normal_paid">Normal Paid</option>
                            <option value="absent_unpaid">Absent (Unpaid)</option>
                            <option value="holiday_paid">Holiday Paid</option>
                            <option value="sick_paid">Sick Paid</option>
                        </select>
                    </div>

                    {{-- NOTES --}}
                    <div class="form-group">
                        <label>Notes</label>
                        <textarea name="notes"
                                  class="form-control"
                                  rows="3"></textarea>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button"
                            class="btn btn-secondary"
                            data-dismiss="modal">
                        Close
                    </button>

                    <button type="submit" class="btn btn-primary">
                        <span id="btn-add-text">Save</span>
                        <span id="btn-add-spinner" class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Edit Timesheet Modal --}}
<div class="modal fade" id="editTimesheetModal" tabindex="-1">
  <div class="modal-dialog modal-lg" style="width:500px;">
    <form id="editTimesheetForm" method="POST">
      @csrf
      @method('PUT')

      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Edit Timesheet</h5>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>

        <div class="modal-body">

          <input type="hidden" id="edit_id">

          {{-- USER --}}
          <div class="form-group">
            <label>User<span class="text-danger">*</span></label>
            <select name="user_id" id="edit_user_id" class="form-control" required><span class="text-danger">*</span></label>
              @foreach($users as $user)
                <option value="{{ $user->id }}">
                  {{ $user->first_name }} {{ $user->last_name }}
                </option>
              @endforeach
            </select>
          </div>

          {{-- START --}}
          <div class="form-group">
            <label>Start Time<span class="text-danger">*</span></label>
            <input type="datetime-local" name="start_time" id="edit_start_time" class="form-control" required>
          </div>

          {{-- END --}}
          <div class="form-group">
            <label>End Time</label>
            <input type="datetime-local" name="end_time" id="edit_end_time" class="form-control">
          </div>

          {{-- CLOCK MODE --}}
          <div class="form-group">
            <label>Clock In Mode</label>
            <select name="clock_in_mode" id="edit_clock_in_mode" class="form-control">
              <option value="office">Office</option>
              <option value="remote">Remote</option>
              <option value="out_of_office">Out of Office</option>
              <option value="do_not_disturb">Do Not Disturb</option>
            </select>
          </div>

          {{-- TYPE --}}
          <div class="form-group">
            <label>Type</label>
            <select name="type" id="edit_type" class="form-control">
              <option value="normal_paid">Normal Paid</option>
              <option value="absent_unpaid">Absent (Unpaid)</option>
              <option value="holiday_paid">Holiday Paid</option>
            </select>
          </div>

          {{-- NOTES --}}
          <div class="form-group">
            <label>Notes</label>
            <textarea name="notes" id="edit_notes" class="form-control"></textarea>
          </div>

        </div>

        <div class="modal-footer">
          <button class="btn btn-secondary" data-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">
            <span id="btn-edit-text">Update</span>
            <span id="btn-edit-spinner" class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
          </button>
        </div>
      </div>
    </form>
  </div>
</div>


{{-- GENERIC MODAL --}}
@include('partials.generic-model')

{{-- SCRIPTS --}}
<script src="{{ asset('js/jquery-3.6.0.min.js') }}"></script>
<script src="{{ asset('js/generic-model-form.js') }}"></script>
<script src="{{ asset('js/ajax-form-handler.js') }}"></script>
<script src="{{ asset('js/generic-delete-handler.js') }}"></script>

<script>
$(function () {
     // --- URL Param Handling ---
     var urlParams = new URLSearchParams(window.location.search);

     // Initialize Inputs from URL
     if(urlParams.has('user_id')) $('#filter_user_id').val(urlParams.get('user_id'));
     if(urlParams.has('date_from')) $('#filter_date_from').val(urlParams.get('date_from'));
     if(urlParams.has('date_to')) $('#filter_date_to').val(urlParams.get('date_to'));
     if(urlParams.has('type')) $('#filter_type').val(urlParams.get('type'));

     // Calculate Initial Start for Pagination
     var initialPage = parseInt(urlParams.get('page')) || 1;
     var pageLength = 10; // Default
     var initialStart = (initialPage - 1) * pageLength;

     // Function to update URL
     function updateUrl() {
         var params = new URLSearchParams();

         var user_id = $('#filter_user_id').val();
         var date_from = $('#filter_date_from').val();
         var date_to = $('#filter_date_to').val();
         var type = $('#filter_type').val();

         if(user_id) params.set('user_id', user_id);
         if(date_from) params.set('date_from', date_from);
         if(date_to) params.set('date_to', date_to);
         if(type) params.set('type', type);

         // Page
         var info = table.page.info();
         var currentPage = info.page + 1;
         if (currentPage > 1) params.set('page', currentPage);

         var newUrl = window.location.pathname + '?' + params.toString();
         history.pushState(null, '', newUrl);

         updateActiveFilterBadges();
     }

     // Function to render Active Filter Badges
     function updateActiveFilterBadges() {
         var container = $('#activeFilters');
         var list = $('#activeFiltersList');
         list.empty();
         var hasFilter = false;

         function addBadge(label, value, inputId) {
             if(value) {
                 hasFilter = true;
                 var badge = $('<span class="badge badge-info ml-2 p-2" style="font-size: 100%;">' + label + ': ' + value + ' <i class="fa fa-times cursor-pointer remove-filter" data-target="' + inputId + '" style="margin-left:5px;"></i></span>');
                 list.append(badge);
             }
         }

         var userVal = $('#filter_user_id').val();
         var userText = $('#filter_user_id option:selected').text();
         if(userVal) {
             hasFilter = true;
             var badge = $('<span class="badge badge-info ml-2 p-2" style="font-size: 100%;">User: ' + userText + ' <i class="fa fa-times cursor-pointer remove-filter" data-target="#filter_user_id" style="margin-left:5px;"></i></span>');
             list.append(badge);
         }

         addBadge('Date From', $('#filter_date_from').val(), '#filter_date_from');
         addBadge('Date To', $('#filter_date_to').val(), '#filter_date_to');

         var typeVal = $('#filter_type').val();
         var typeText = $('#filter_type option:selected').text();
         if(typeVal) {
             hasFilter = true;
             var badge = $('<span class="badge badge-info ml-2 p-2" style="font-size: 100%;">Type: ' + typeText + ' <i class="fa fa-times cursor-pointer remove-filter" data-target="#filter_type" style="margin-left:5px;"></i></span>');
             list.append(badge);
         }

         if(hasFilter) container.show();
         else container.hide();
     }

     // Filter Toggle
     $('#toggleFilterBtn').click(function() {
         $('#filterWrapper').slideToggle();
     });

     // Apply Filter Button
     $('#applyFilterBtn').click(function() {
         table.page(0).draw(false); // Reset to page 1 on filter
         updateUrl();
     });

      $('#toggleFilterclear').click(function() {
      $('#filterWrapper').slideToggle();
    });

     // Remove Filter Logic
     $(document).on('click', '.remove-filter', function() {
         var target = $(this).data('target');
         $(target).val(''); // Clear value
         table.page(0).draw(false);
         updateUrl();
     });

    const table = $('#dataTable').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        autoWidth: false,
        pageLength: 10,
        displayStart: initialStart, // Key for initial pagination

        ajax: {
            url: "{{ route('masterapp.timesheets.data') }}",
            type: "GET",
            data: function (d) {
                d.user_id = $('#filter_user_id').val();
                d.date_from = $('#filter_date_from').val();
                d.date_to = $('#filter_date_to').val();
                d.type = $('#filter_type').val();
            }
        },

        columns: [
            { data: 'user', name: 'user' },
            { data: 'start_time', name: 'start_time' },
            { data: 'end_time', name: 'end_time' },
            { data: 'hours', name: 'hours' },
            { data: 'clock_in_mode', name: 'clock_in_mode' },
            { data: 'type', name: 'type' },
            { data: 'notes', name: 'notes' },
            { data: 'actions', name: 'actions', orderable: false, searchable: false }
        ],

        dom: '<"top"Biplf>rt<"bottom bottomAlign"ip><"clear">',
        buttons: [],

        language: {
            lengthMenu: 'Show _MENU_',
            paginate: {
                next: '<i class="fa fa-angle-double-right"></i>',
                previous: '<i class="fa fa-angle-double-left"></i>'
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
                columns: exportVisibleColumns
            },
            customize: function (win) {

                $(win.document.body).css('font-size', '9px');

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
                            padding: 4px !important;
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
                columns: exportVisibleColumns
            }
        },
        {
            extend: 'excelHtml5',
            text: '<i class="fa fa-download"></i> Excel',
            className: 'btn btn-success',
            exportOptions: {
                columns: exportVisibleColumns
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
                columns: exportVisibleColumns
            },
            customize: function (doc) {

                const table = doc.content.find(c => c.table).table;
                const colCount = table.body[0].length;

                /* Page + font */
                doc.pageMargins = [15, 12, 15, 12];
                doc.defaultStyle.fontSize = 8;
                doc.styles.tableHeader.fontSize = 9;

                /*  Best fit for small column counts */
                table.widths = Array(colCount).fill('*');

                /* Wrapping & spacing */
                doc.styles.tableBodyEven = {
                    margin: [0, 3, 0, 3]
                };
                doc.styles.tableBodyOdd = {
                    margin: [0, 3, 0, 3]
                };

                /* Header styling */
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
            targets: [], // hidden initially
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

            $('.top .dataTables_length, .top .dataTables_paginate')
                .wrapAll('<div class="length_pagination"></div>');

            $('.top .dataTables_info, .top .length_pagination')
                .wrapAll('<div class="show_page_align"></div>');

            $('.top .dt-buttons, .top .dataTables_filter')
                .wrapAll('<div class="btn_filter_align"></div>');

            const $searchInput = $('.dataTables_filter input');
            $searchInput.attr('placeholder', 'Search..');
            // wrap input
            $searchInput.wrap('<div class="search-input-wrapper"></div>');
            // add class
            $searchInput.addClass('search-input');
            // ADD SEARCH ICON ELEMENT
            $searchInput.before('<i class="fa fa-search"></i>');

            // Initialize Badges based on initial URL params
            updateActiveFilterBadges();
        }
    });

    // Update URL on Page Change
    table.on('page.dt', function () {
        // We need to wait for the redraw to complete slightly or just use the event
        // Actually the event fires before draw. Let's use defer logic or just setTimeout 0
        setTimeout(updateUrl, 0);
    });
});

// OPEN CREATE MODAL
$('#addTimesheetBtn').on('click', function () {
        ModalFormManager.openModal(
            $(this).data('url'),
            $(this).data('title')
        );
});

    // AJAX FORM HANDLING
handleAjaxForm('#form-timesheets', {
        modalToClose: '#genericModal',
        reloadOnSuccess: true
});

  // DELETE HANDLER
handleDelete();

//Edit Timesheet Js
$(document).on('click', '.js-edit-timesheet', function () {
    const btn   = $(this);
    const url   = btn.data('url');
    const modal = $('#editTimesheetModal');

    $.get(url, function (data) {

        modal.find('#edit_user_id').val(data.user_id);
        modal.find('#edit_start_time').val(data.start_time);
        modal.find('#edit_end_time').val(data.end_time);
        modal.find('#edit_clock_in_mode').val(data.clock_in_mode);
        modal.find('#edit_type').val(data.type);
        modal.find('#edit_notes').val(data.notes);

        modal.find('#editTimesheetForm')
            .attr('action', '/master-app/timesheets/' + data.id);

        modal.modal('show');
    });
});

handleAjaxForm("#addTimesheetForm", {
    loadingIndicator: 'button',
    buttonTextSelector: '#btn-add-text',
    buttonSpinnerSelector: '#btn-add-spinner',
    modalToClose: "#addTimesheetModal",
    reloadOnSuccess: true,
    successTitle: "Timesheet Created!"
});

handleAjaxForm("#editTimesheetForm", {
    loadingIndicator: 'button',
    buttonTextSelector: '#btn-edit-text',
    buttonSpinnerSelector: '#btn-edit-spinner',
    modalToClose: "#editTimesheetModal",
    reloadOnSuccess: true,
    successTitle: "Timesheet Updated!"
});

// Export only visible columns (excluding action column)
function exportVisibleColumns(idx, data, node) {
    const table = $('#dataTable').DataTable();

    // Exclude action column
    if ($(node).hasClass('no-export') || $(node).hasClass('no-vis')) {
        return false;
    }

    // Export only columns enabled via Column Visibility
    return table.column(idx).visible();
}


</script>
@endsection
