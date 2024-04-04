function revealPassword() {
    let input = document.getElementById('senha');

    if (input.type == "password") input.type = "text";
    else input.type = "password";
}

function setupCheckbox() {
    document.getElementById('keep-connected').checked = !document.getElementById('keep-connected').checked
}