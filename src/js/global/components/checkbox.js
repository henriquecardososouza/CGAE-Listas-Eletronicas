function setCheckbox(id = undefined) {
    try {
        if (id) {
            const checkbox = document.getElementById(id);
    
            checkbox.checked = !checkbox.checked;
        }
    
        else {
            document.querySelectorAll('input[type="checkbox"]').forEach((checkbox) => {
                checkbox.checked = !checkbox.checked;
            });
        }
    } catch (error) { alert(error.message)}
}