$(document).ready(function () {

    if ($.fn.DataTable.isDataTable('#driversTable')) {
        $('#driversTable').DataTable().clear().destroy();
    }

    const table = $('#driversTable').DataTable({
        layout: { topStart: ['pageLength', 'buttons'], topEnd: null },
        paging: true,
        searching: true,
        ordering: true,
        pageLength: 10,
        lengthMenu: [[10, 25, 50, 100, 250, 500, -1], [10, 25, 50, 100, 250, 500, "All"]],
        columnDefs: [{ orderable: false, targets: 0 }],
        dom: 'lBfrtip',
        buttons: [
            {
                text: '<i class="fa fa-edit"></i> Edit',
                className: 'btn btn-primary buttons-edit',
                enabled: false,
                action: () => {
                    const id = getSelected().first().data('id');
                    if (id) openDriverModal(`/drivers/${id}/edit`, 'Edit Driver');
                }
            },
            {
                text: '<i class="fa fa-trash"></i> Delete',
                className: 'btn btn-danger buttons-del',
                enabled: false,
                action: () => bulkDelete()
            },
            { extend: 'colvis', text: '<i class="fa fa-columns"></i> Columns' },
            { extend: 'copy', text: '<i class="fa fa-copy"></i> Copy' },
            { extend: 'excel', text: '<i class="fa fa-file-excel"></i> Excel' },
            { extend: 'pdf', text: '<i class="fa fa-file-pdf"></i> PDF' }
        ]
    });

    function getSelected() {
        return $('#driversTable tbody .row-check:checked').closest('tr');
    }

    function updateButtons() {
        const count = getSelected().length;
        table.button('.buttons-edit').enable(count === 1);
        table.button('.buttons-del').enable(count > 0);
    }

    $('#driversTable').on('change', '.row-check', function () {
        $(this).closest('tr').toggleClass('selected-row', $(this).is(':checked'));
        updateButtons();
    });

    $('#selectAll').on('change', function () {
        $('.row-check').prop('checked', $(this).prop('checked')).trigger('change');
    });

    function openDriverModal(url, title) {
        $('#userModalTitle').text(title);
        $('#userModalBody').html(`
            <div class="text-center py-4">
                <div class="spinner-border text-primary"></div>
                <p class="fw-semibold mt-2">Loading...</p>
            </div>
        `);
        const modal = new bootstrap.Modal(document.getElementById('userModal'));
        modal.show();
        $.get(url, html => $('#userModalBody').html(html));
    }

    function bulkDelete() {
        const ids = getSelected().map((_, row) => $(row).data('id')).get();
        if (!ids.length) return;

        Swal.fire({
            title: "Delete Selected Drivers?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonText: "Yes, delete",
            confirmButtonColor: "#d33"
        }).then(res => {
            if (!res.isConfirmed) return;

            fetch("/drivers/bulk-delete", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr('content')
                },
                body: JSON.stringify({ ids }),
            })
            .then(res => res.json())
            .then(data => {
                ids.forEach(id => {
                    table.row($(`tr[data-id="${id}"]`)).remove().draw();
                });

                Swal.fire({
                    icon: "success",
                    title: "Deleted!",
                    text: data.message,
                    timer: 1400,
                    showConfirmButton: false
                });
            });
        });
    }

});
