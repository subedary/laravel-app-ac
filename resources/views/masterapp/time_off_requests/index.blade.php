@extends('masterapp.layouts.app')
@section('content')

    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark">Time Off Requests</h1>
                </div>
                <div class="col-sm-6 text-right">
                    <!-- Actions -->
                     @php
                        $exportParams = array_merge(request()->all(), ['type' => 'csv']);
                    @endphp
                    
                    <button type="button" class="btn btn-default ml-2" id="toggleFilterBtn">
                        <i class="fa fa-filter"></i> Filter
                    </button> 
                    
                    @if($canCreate)
                    <button type="button" class="btn btn-primary add-new ml-2" data-toggle="modal" data-target="#addRequestModal">
                        <i class="fa fa-plus"></i> Add Request
                    </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            
            <!-- Filters (Server-Side Logic for DataTable to Read) -->
            @php 
                $hasFilters = request()->hasAny(['id_from', 'id_to', 'date_from', 'date_to', 'status']);
                $displayFilter = $hasFilters ? 'block' : 'none';
            @endphp

            <div class="filter-wrapper" id="filterWrapper" style="display: {{ $displayFilter }};">
                <a href="{{ route('masterapp.time-off-requests.index') }}" class="close-filter-btn" title="Clear Filters & Close">
                    &times;
                </a>
                <form id="filterForm">
                    <div class="row align-items-end">
                        <div class="col-md-3">
                            <label class="font-weight-bold">ID Range</label>
                            <div class="input-group">
                                <input type="number" id="filter_id_from" name="id_from" class="form-control filter-input" value="{{ request('id_from') }}" placeholder="From">
                                <div class="input-group-prepend input-group-append">
                                    <span class="input-group-text border-left-0 border-right-0 bg-white">to</span>
                                </div>
                                <input type="number" id="filter_id_to" name="id_to" class="form-control filter-input" value="{{ request('id_to') }}" placeholder="To">
                            </div>
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
                            <label class="font-weight-bold">Status</label>
                            <select id="filter_status" name="status" class="form-control filter-input">
                                <option value="">All Statuses</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="denied" {{ request('status') == 'denied' ? 'selected' : '' }}>Denied</option>
                                <option value="approved_paid" {{ request('status') == 'approved_paid' ? 'selected' : '' }}>Approved (Paid)</option>
                                <option value="approved_unpaid" {{ request('status') == 'approved_unpaid' ? 'selected' : '' }}>Approved (Unpaid)</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <button type="button" id="applyFilterBtn" class="btn btn-primary btn-block"><i class="fa fa-filter"></i> Apply Filter</button> 
                        </div>
                    </div>
                    <div class="row mt-2">
                         <div class="col-md-12 text-right">
                            <a href="{{ route('masterapp.time-off-requests.index') }}" class="btn btn-link btn-sm text-secondary">Clear All Filters</a>
                         </div>
                    </div>
                </form>
            </div>

            <!-- Active Filters Badges -->
            <div id="activeFilters" class="mb-3" style="display:none;">
                <strong>Active Filters:</strong>
                <span id="activeFiltersList"></span>
            </div>

            <!-- Bulk Actions Toolbar -->
            @if($canChangeStatus)
            <div class="row bulk-actions-wrapper" id="bulkActionsToolbar">
                <div class="col-md-12 form-inline">
                    <label class="mr-3">Bulk Actions:</label>
                    <select class="form-control mr-4" id="bulkActionType" style="width: 180px;">
                        <option value="">Select Action</option>
                        <option value="change_status">Change Status</option>
                    </select>
                    
                    <select class="form-control mr-4" id="bulkActionStatus" style="display:none;" style="width: 200px;">
                        <option value="pending">Pending</option>
                        <option value="denied">Denied</option>
                        <option value="approved_paid">Approved (Paid)</option>
                        <option value="approved_unpaid">Approved (Unpaid)</option>
                    </select>

                    <button class="btn btn-primary" id="applyBulkAction">Apply</button>
                    <span class="ml-3" id="selectedCount">0 selected</span>
                </div>
            </div>
            @endif

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            @if(session('success'))
                                <div class="alert alert-success">{{ session('success') }}</div>
                            @endif
                            @if($errors->any())
                                <div class="alert alert-danger">
                                    <ul>
                                        @foreach($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            <table id="example2" class="table table-bordered table-hover">
                                <thead>
                                <tr>
                                    @if($canChangeStatus)
                                    <th width="40" class="text-center">
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-default btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="padding: 0px 5px;">
                                                <input type="checkbox" id="selectAllCheckbox" style="pointer-events: none;">
                                            </button>
                                            <div class="dropdown-menu">
                                                <a class="dropdown-item" href="#" id="actionSelectPage">Select This Page</a>
                                                <a class="dropdown-item" href="#" id="actionSelectAllGlobal">Select All Records</a>
                                                <div class="dropdown-divider"></div>
                                                <a class="dropdown-item" href="#" id="actionUnselectAll">Unselect All</a>
                                            </div>
                                        </div>
                                    </th>
                                    @else
                                    <th>#</th>
                                    @endif
                                    <th>Name</th>
                                    <th>Start Time</th>
                                    <th>End Time</th>
                                    <th>Added Timestamp</th>
                                    <th>Paid</th>
                                    <th>Status</th>
                                    <th>Notes</th>
                                    <th>Actions</th>
                                </tr>
                                </thead>
                                <tbody>
                                    <!-- Data loaded via AJAX -->
                                </tbody>
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

    <!-- Add Modal -->
    <div class="modal fade" id="addRequestModal" tabindex="-1" role="dialog" aria-labelledby="addRequestModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <form action="{{ route('masterapp.time-off-requests.store') }}" method="POST">
                @csrf
                <input type="hidden" name="submitted" value="1">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addRequestModalLabel">Add Time Off Request</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="user_id">User</label>
                                    <select name="user_id" id="user_id" class="form-control select2" required @if(!$canAdmin) disabled @endif>
                                        <option value="">Select User</option>
                                        @foreach($users as $user)
                                            <option value="{{ $user->id }}" {{ (isset($request) && $request->user_id == $user->id) || (!isset($request) && auth()->id() == $user->id) ? 'selected' : '' }}>
                                                {{ $user->first_name }} {{ $user->last_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @if(!$canAdmin)
                                        <input type="hidden" name="user_id" value="{{ auth()->id() }}">
                                    @endif
                        </div>
                        <div class="form-group">
                            <label for="start_time">Start Time</label>
                            <input type="datetime-local" class="form-control" name="start_time" required>
                        </div>
                        <div class="form-group">
                            <label for="end_time">End Time</label>
                            <input type="datetime-local" class="form-control" name="end_time" required>
                        </div>
                        <div class="form-group">
                            <label for="paid">Paid</label>
                            <select class="form-control" name="paid" {{ !$canChangeStatus ? 'disabled' : '' }}>
                                <option value="0">No</option>
                                <option value="1">Yes</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="status">Status</label>
                            <select class="form-control" name="status" required {{ !$canChangeStatus ? 'disabled' : '' }}>
                                <option value="pending" selected>Pending</option>
                                <option value="denied">Denied</option>
                                <option value="approved_paid">Approved (Paid)</option>
                                <option value="approved_unpaid">Approved (Unpaid)</option>
                            </select>
                             @if(!$canChangeStatus) <input type="hidden" name="status" value="pending"> @endif
                        </div>
                        <div class="form-group">
                            <label for="notes">Notes</label>
                            <textarea class="form-control" name="notes" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Modal -->
    <div class="modal fade" id="editRequestModal" tabindex="-1" role="dialog" aria-labelledby="editRequestModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <form id="editRequestForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editRequestModalLabel">Edit Time Off Request</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label for="edit_user_id">User</label>
                            <select name="user_id" id="edit_user_id" class="form-control select2" required @if(!$canAdmin) disabled @endif>
                                <option value="">Select User</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->first_name }} {{ $user->last_name }}</option>
                                @endforeach
                            </select>
                            @if(!$canAdmin)
                                        <input type="hidden" name="user_id" id="edit_user_id_hidden">
                                    @endif
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="edit_start_time">Start Time</label>
                                    <input type="datetime-local" name="start_time" id="edit_start_time" class="form-control" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="edit_end_time">End Time</label>
                                    <input type="datetime-local" name="end_time" id="edit_end_time" class="form-control" required>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="edit_paid">Paid</label>
                            <select class="form-control" id="edit_paid" name="paid" {{ ($canChangeStatus || $canAdmin) ? '' : 'disabled' }}>
                                <option value="0">No</option>
                                <option value="1">Yes</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="edit_status">Status</label>
                            <select class="form-control" id="edit_status" name="status" required {{ !$canChangeStatus ? 'disabled' : '' }}>
                                <option value="pending">Pending</option>
                                <option value="denied">Denied</option>
                                <option value="approved_paid">Approved (Paid)</option>
                                <option value="approved_unpaid">Approved (Unpaid)</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="edit_notes">Notes</label>
                            <textarea class="form-control" id="edit_notes" name="notes" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Update</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    
    <script src="{{ asset('js/jquery-3.6.0.min.js') }}"></script>
    <script>
        var canAdmin = @json($canAdmin);
        var canChangeStatus = @json($canChangeStatus);
        var canEdit = @json($canEdit);
        var canDelete = @json($canDelete);

        $(function () {
             // ...
             // --- URL Param Handling ---
             var urlParams = new URLSearchParams(window.location.search);
             
             // Initialize Inputs from URL
             if(urlParams.has('id_from')) $('#filter_id_from').val(urlParams.get('id_from'));
             if(urlParams.has('id_to')) $('#filter_id_to').val(urlParams.get('id_to'));
             if(urlParams.has('date_from')) $('#filter_date_from').val(urlParams.get('date_from'));
             if(urlParams.has('date_to')) $('#filter_date_to').val(urlParams.get('date_to'));
             if(urlParams.has('status')) $('#filter_status').val(urlParams.get('status'));

             // Calculate Initial Start for Pagination
             var initialPage = parseInt(urlParams.get('page')) || 1;
             var pageLength = 10; // Default
             var initialStart = (initialPage - 1) * pageLength;

             // Function to update URL
             function updateUrl() {
                 var params = new URLSearchParams();
                 
                 var id_from = $('#filter_id_from').val();
                 var id_to = $('#filter_id_to').val();
                 var date_from = $('#filter_date_from').val();
                 var date_to = $('#filter_date_to').val();
                 var status = $('#filter_status').val();

                 if(id_from) params.set('id_from', id_from);
                 if(id_to) params.set('id_to', id_to);
                 if(date_from) params.set('date_from', date_from);
                 if(date_to) params.set('date_to', date_to);
                 if(status) params.set('status', status);

                 // Page
                 var info = dataTable.page.info();
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

                 addBadge('ID From', $('#filter_id_from').val(), '#filter_id_from');
                 addBadge('ID To', $('#filter_id_to').val(), '#filter_id_to');
                 addBadge('Date From', $('#filter_date_from').val(), '#filter_date_from');
                 addBadge('Date To', $('#filter_date_to').val(), '#filter_date_to');
                 
                 var statusVal = $('#filter_status').val();
                 var statusText = $('#filter_status option:selected').text();
                 // If selected "All Statuses" (value empty), don't show badge
                 if(statusVal) { 
                     hasFilter = true;
                     var badge = $('<span class="badge badge-info ml-2 p-2" style="font-size: 100%;">Status: ' + statusText + ' <i class="fa fa-times cursor-pointer remove-filter" data-target="#filter_status" style="margin-left:5px;"></i></span>');
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
                 dataTable.page(0).draw(false); // Reset to page 1 on filter
                 updateUrl();
             });

             // Remove Filter Logic
             $(document).on('click', '.remove-filter', function() {
                 var target = $(this).data('target');
                 $(target).val(''); // Clear value
                 dataTable.page(0).draw(false);
                 updateUrl();
             });

             // --- DataTables Integration (Server-Side) ---
             var dataTable = $('#example2').DataTable({
                  "processing": true,
                  "serverSide": true,
                  "displayStart": initialStart, // Key for initial pagination
                  "ajax": {
                      "url": "{{ route('masterapp.time-off-requests.data') }}",
                      "data": function (d) {
                          d.id_from = $('#filter_id_from').val();
                          d.id_to = $('#filter_id_to').val();
                          d.date_from = $('#filter_date_from').val();
                          d.date_to = $('#filter_date_to').val();
                          d.status = $('#filter_status').val();
                      }
                  },
                  "pageLength": 10,
                  "createdRow": function( row, data, dataIndex ) {
                        $(row).attr('data-id', data.id);
                  },
                  "columns": [
                        @if($canChangeStatus)
                        { 
                            "data": "id",
                            "orderable": false,
                            "render": function(data, type, row) {
                                return '<input type="checkbox" class="row-checkbox" value="' + data + '">';
                            }
                        },
                        @else
                        {
                            "data": null,
                            "orderable": false, 
                            "render": function (data, type, row, meta) {
                                return meta.row + 1 + meta.settings._iDisplayStart;
                            }
                        },
                        @endif
                        { "data": "user_name" }, 
                        { "data": "start_time" },
                        { "data": "end_time" },
                        { "data": "added_timestamp" },
                        { "data": "paid" },
                        { 
                            "data": "status",
                            "render": function(data, type, row) {
                                var badgeClass = 'secondary';
                                var statusLabel = row.status_label || data;
                                
                                switch(data) {
                                    case 'approved_paid': badgeClass = 'success'; break;
                                    case 'approved_unpaid': badgeClass = 'primary'; break;
                                    case 'denied': badgeClass = 'danger'; break;
                                    case 'pending': badgeClass = 'warning'; break;
                                }

                                if (canChangeStatus) {
                                    return '<div class="status-container" data-id="' + row.id + '">' +
                                           '<span class="badge badge-' + badgeClass + ' status-badge" title="Click to edit">' + statusLabel + '</span>' +
                                           '<div class="status-select-wrapper">' +
                                           '<select class="form-control form-control-sm status-change-select">' +
                                           '<option value="pending" ' + (data == 'pending' ? 'selected' : '') + '>Pending</option>' +
                                           '<option value="denied" ' + (data == 'denied' ? 'selected' : '') + '>Denied</option>' +
                                           '<option value="approved_paid" ' + (data == 'approved_paid' ? 'selected' : '') + '>Approved (Paid)</option>' +
                                           '<option value="approved_unpaid" ' + (data == 'approved_unpaid' ? 'selected' : '') + '>Approved (Unpaid)</option>' +
                                           '</select></div></div>';
                                } else {
                                     return '<span class="badge badge-' + badgeClass + '">' + statusLabel + '</span>';
                                }
                            }
                        },
                        { "data": "notes" },
                        { 
                            "data": "actions",
                            "orderable": false,
                            "render": function(data, type, row) {
                                var actions = '<div class="action-div">';
                                var isPending = row.status === 'pending';
                                
                                // Edit: Show if allowed AND (isPending OR Admin)
                                if (canEdit && (isPending || canAdmin)) {
                                    actions += '<button type="button" class="btn btn-sm btn-info action-icon" ' +
                                       'data-toggle="modal" data-target="#editRequestModal" ' +
                                       'data-id="' + row.id + '" ' +
                                       'data-user_id="' + row.user_id + '" ' +
                                       'data-start_time="' + (row.start_time_raw || '') + '" ' +
                                       'data-end_time="' + (row.end_time_raw || '') + '" ' +
                                       'data-paid="' + (row.paid == 'Yes' ? 1 : 0) + '" ' +
                                       'data-status="' + row.status + '" ' +
                                       'data-notes="' + (row.notes ? row.notes.replace(/"/g, '&quot;') : '') + '" ' +
                                       'title="Edit request"><i class="fa fa-edit"></i></button>';
                                }

                                // Delete: Show if allowed AND (isPending OR Admin)
                                if (canDelete && (isPending || canAdmin)) {
                                    actions += '<button type="button" class="btn btn-sm btn-danger action-icon delete-btn" data-id="' + row.id + '" title="Delete request"><i class="fa fa-trash"></i></button>';
                                }
                                
                                actions += '</div>';
                                return actions;
                            }
                        }
                  ],
                  "responsive": true,
                  "scrollX": false,
                  "autoWidth": false,
                  "lengthMenu": [[10, 50, 100], [10, 50, 100]],
                  "language": {
                      "lengthMenu": 'Show _MENU_',
                      "paginate": {
                          "next": '<i class="fa  fa-angle-double-right "></i>', 
                          "previous": '<i class="fa  fa-angle-double-left"></i>'
                      },
                      "search": ''
                  },

                  dom: '<"top"Biplf>rt<"bottom bottomAlign"ip><"clear">',
                  buttons: [
                      {
                          extend: 'print',
                          text: '<i class="fa fa-print"></i> Print',
                          className: 'btn btn-secondary',
                          exportOptions: { columns: [1, 2, 3, 4, 5, 6, 7, 8] }
                      },
                      {
                          extend: 'copyHtml5',
                          text: '<i class="fa fa-copy"></i> Copy Data',
                          className: 'btn btn-primary',
                          exportOptions: { columns: [1, 2, 3, 4, 5, 6, 7, 8] }
                      },
                      {
                          extend: 'excelHtml5',
                          text: '<i class="fa fa-download"></i> Excel',
                          className: 'btn btn-success',
                          exportOptions: { columns: [1, 2, 3, 4, 5, 6, 7, 8] }
                      },
                      {
                          extend: 'pdfHtml5',
                          text: '<i class="fa fa-download"></i> PDF',
                          className: 'btn btn-danger',
                          exportOptions: { columns: [1, 2, 3, 4, 5, 6, 7, 8] }
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
                        searchable: false,
                        className: 'no-vis'
                    },
                    {
                        targets: 0,
                        className: 'no-vis'
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
                      $searchInput.attr('placeholder', 'Search..'); // Placeholder text for the search input field
                      $searchInput.after('<span class="input-group-text"><i class="fa fa-search"></i></span>'); // Add search icon
                  
                      // Initialize Badges based on initial URL params (since we manually set inputs above)
                      updateActiveFilterBadges();
                  }
              });

             // Update URL on Page Change
             dataTable.on('page.dt', function () {
                 // We need to wait for the redraw to complete slightly or just use the event
                 // Actually the event fires before draw. Let's use defer logic or just setTimeout 0
                 setTimeout(updateUrl, 0); 
             });

             // --- Click to Edit Status Logic ---
             $(document).on('click', '.status-badge', function() {
                 var container = $(this).closest('.status-container');
                 container.find('.status-badge').hide();
                 container.find('.status-select-wrapper').show();
                 container.find('.status-change-select').focus();
             });

             // Update Status on Change
             $(document).on('change', '.status-change-select', function() {
                 var select = $(this);
                 var container = select.closest('.status-container');
                 var id = container.data('id');
                 var status = select.val();
                 var url = "{{ route('masterapp.time-off-requests.updateStatus', ':id') }}";
                 url = url.replace(':id', id);

                 $.ajax({
                     url: url,
                     type: 'PATCH',
                     data: {
                         _token: '{{ csrf_token() }}',
                         status: status
                     },
                     success: function(response) {
                         if(response.success) {
                             dataTable.ajax.reload(null, false); // Reload table data without resetting paging
                         }
                     },
                     error: function(xhr) {
                         alert('Error updating status');
                     }
                 });
             });

             // Hide select on blur without change
             $(document).on('blur', '.status-change-select', function() {
                 var container = $(this).closest('.status-container');
                 setTimeout(function() {
                    container.find('.status-select-wrapper').hide();
                    container.find('.status-badge').show();
                 }, 200);
             });

             // --- Delete Action ---
             $(document).on('click', '.delete-btn', function() {
                 if(!confirm('Are you sure?')) return;
                 var id = $(this).data('id');
                 var url = "{{ route('masterapp.time-off-requests.destroy', ':id') }}";
                 url = url.replace(':id', id);
                 
                 $.ajax({
                     url: url,
                     type: 'POST', // DELETE method simulated via _method
                     data: {
                         _token: '{{ csrf_token() }}',
                         _method: 'DELETE'
                     },
                     success: function(response) {
                         dataTable.ajax.reload(null, false);
                     },
                     error: function(xhr) {
                         alert('Error deleting request');
                     }
                 });
             });


             // --- Bulk Actions Logic ---
             
             var selectAllGlobal = false;

             function updateSelectionUI() {
                 var totalOnPage = $('.row-checkbox').length;
                 var checkedOnPage = $('.row-checkbox:checked').length;
                 
                 // Update Header Checkbox State
                 if (checkedOnPage > 0 && checkedOnPage < totalOnPage) {
                    $('#selectAllCheckbox').prop('indeterminate', true);
                    $('#selectAllCheckbox').prop('checked', false);
                 } else if (checkedOnPage === totalOnPage && totalOnPage > 0) {
                    $('#selectAllCheckbox').prop('indeterminate', false);
                    $('#selectAllCheckbox').prop('checked', true);
                 } else {
                    $('#selectAllCheckbox').prop('indeterminate', false);
                    $('#selectAllCheckbox').prop('checked', selectAllGlobal); 
                 }

                 // Update Bulk Toolbar
                 var countText = '';
                 if (selectAllGlobal) {
                     var info = dataTable.page.info();
                     countText = 'All ' + info.recordsDisplay + ' records selected';
                 } else {
                     countText = checkedOnPage + ' selected';
                 }

                 if (checkedOnPage > 0 || selectAllGlobal) {
                     $('#bulkActionsToolbar').slideDown();
                     $('#selectedCount').text(countText);
                 } else {
                     $('#bulkActionsToolbar').slideUp();
                 }
             }

             // Dopdown Actions
             $('#actionSelectPage').click(function(e) {
                 e.preventDefault();
                 selectAllGlobal = false;
                 $('.row-checkbox').prop('checked', true);
                 updateSelectionUI();
             });

             $('#actionSelectAllGlobal').click(function(e) {
                 e.preventDefault();
                 selectAllGlobal = true;
                 $('.row-checkbox').prop('checked', true);
                 updateSelectionUI();
             });

             $('#actionUnselectAll').click(function(e) {
                 e.preventDefault();
                 selectAllGlobal = false;
                 $('.row-checkbox').prop('checked', false);
                 updateSelectionUI();
             });

             // Header Checkbox Click - Default to Toggle Page
             $(document).on('click', '#selectAllCheckbox', function() {
                 var isChecked = $(this).is(':checked');
                 selectAllGlobal = false; 
                 $('.row-checkbox').prop('checked', isChecked);
                 updateSelectionUI();
             });

             // Row Checkbox
             $(document).on('change', '.row-checkbox', function() {
                 if (!$(this).is(':checked') && selectAllGlobal) {
                     selectAllGlobal = false;
                 }
                 updateSelectionUI();
             });

             // Apply Bulk Action
             $('#applyBulkAction').click(function() {
                 var type = $('#bulkActionType').val();
                 var status = $('#bulkActionStatus').val();
                 var ids = [];
                 
                 // If not global, gather IDs
                 if (!selectAllGlobal) {
                     $('.row-checkbox:checked').each(function() {
                         ids.push($(this).val());
                     });

                     if (ids.length === 0) {
                         alert('Please select at least one record.');
                         return;
                     }
                 }

                 if (type == 'change_status') {
                     if (!status) {
                         alert('Please select a status.');
                         return;
                     }

                     // Gather active filters if global
                     var filters = {};
                     if (selectAllGlobal) {
                         filters = {
                             id_from: $('#filter_id_from').val(),
                             id_to: $('#filter_id_to').val(),
                             date_from: $('#filter_date_from').val(),
                             date_to: $('#filter_date_to').val(),
                             status: $('#filter_status').val()
                         };
                     }

                     $.ajax({
                         url: "{{ route('masterapp.time-off-requests.bulkUpdateStatus') }}",
                         type: 'POST',
                         data: {
                             _token: '{{ csrf_token() }}',
                             status: status,
                             ids: ids,
                             select_all: selectAllGlobal ? 1 : 0,
                             filters: filters
                         },
                         success: function(response) {
                             if(response.success) {
                                 dataTable.ajax.reload(null, false);
                                 selectAllGlobal = false;
                                 $('.row-checkbox').prop('checked', false);
                                 updateSelectionUI();
                             }
                         },
                         error: function(xhr) {
                             alert('Error updating statuses');
                         }
                     });
                 }
             });

             // Bulk Action Type UI
             $('#bulkActionType').change(function() {
                 var type = $(this).val();
                 if (type == 'change_status') {
                     $('#bulkActionStatus').show();
                 } else {
                     $('#bulkActionStatus').hide();
                 }
             });
             
             // Reset Checkboxes on Draw
             dataTable.on('draw', function() {
                 if (selectAllGlobal) {
                     $('.row-checkbox').prop('checked', true);
                 }
                 updateSelectionUI();
             });


             // Modal Edit Logic 
             $('#editRequestModal').on('show.bs.modal', function (event) {
                var button = $(event.relatedTarget);
                
                var id = button.data('id');
                var user_id = button.data('user_id');
                var notes = button.data('notes');
                var status = button.data('status');
                var paid = button.data('paid');
                var start_time = button.data('start_time');
                var end_time = button.data('end_time');
                
                var modal = $(this);

                modal.find('#edit_user_id').val(user_id);
                modal.find('#edit_start_time').val(start_time);
                modal.find('#edit_end_time').val(end_time);
                modal.find('#edit_paid').val(paid);
                modal.find('#edit_status').val(status);
                modal.find('#edit_notes').val(notes);
                
                var updateUrl = "{{ route('masterapp.time-off-requests.update', ':id') }}";
                updateUrl = updateUrl.replace(':id', id);
                modal.find('#editRequestForm').attr('action', updateUrl);
             });
        });
    </script>
@endsection