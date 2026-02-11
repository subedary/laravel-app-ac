document.addEventListener("submit", function (e) {

    if (!e.target.matches("#driverFormEdit")) return;
    e.preventDefault();

    const form = e.target;
    const url = form.action;
    const token = document.querySelector('meta[name="csrf-token"]').content;

    const quillEditor = document.querySelector("#status_notes_editor")?._quill;
    if (quillEditor) {
        document.getElementById("status_notes_input").value = quillEditor.root.innerHTML;
    }

    let formData = new FormData(form);
    formData.append("_method", "PUT");

    Swal.fire({ title: "Updating...", didOpen: () => Swal.showLoading() });

   fetch(url, {
    method: "POST",
    headers: {
        "X-CSRF-TOKEN": token,
        "X-Requested-With": "XMLHttpRequest"
    },
    body: formData,
})
.then(async (res) => {
    if (!res.ok) {
        const errorJson = await res.json();
        return Promise.reject(errorJson);
    }
    return res.json();
})
.then((data) => {
    table.ajax.reload(null, false); //  refresh table without losing page
    Swal.fire("Updated!", data.message, "success");
    bootstrap.Modal.getInstance(document.getElementById("userModal")).hide();
    setTimeout(() => location.reload(), 400);
})
.catch((err) => {
    console.error("Update failed:", err);

    if (err.errors) {
        // Validation error
        let firstError = Object.values(err.errors)[0][0];
        Swal.fire("Validation Error", firstError, "warning");
    } else {
        Swal.fire("Error", "Unexpected error occurred", "error");
    }
});
});