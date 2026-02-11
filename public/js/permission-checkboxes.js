function selectAll(element) {
    let parentId = element.id;
    if (element.checked) {
        document
            .querySelectorAll(`.child[data-parent="${parentId}"]`)
            .forEach((child) => {
                child.checked = element.checked;
            });
    } else {
        document
            .querySelectorAll(`.child[data-parent="${parentId}"]`)
            .forEach((child) => {
                child.checked = element.checked;
            });
    }
}

function checkAllBox() {
    $(".child").each(function (index, element) {
        let parentId = this.dataset.parent;
        let parentCheckbox = document.getElementById(parentId);
        let allChildren = document.querySelectorAll(
            `.child[data-parent="${parentId}"]`
        );
        let allChecked = Array.from(allChildren).every((chk) => chk.checked);

        parentCheckbox.checked = allChecked;
    });
}
