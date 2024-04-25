let actualPage = 1;
let maxPages = 1;

window.addEventListener("DOMContentLoaded", createTable);

function createTable (itens = []) {
    setPagination();
}

function setFilter() {

    createTable();
    openModal("filter");
}

function setOrder() {

    createTable();
}

function cleanFilter() {
    createTable();
    openModal("filter");
}

function setPagination() {
    let btn;

    if (actualPage <= 1) {
        btn = document.getElementById("btn-previous");
        btn.classList.add("disabled");
        btn.disabled = true;
    }

    else {
        btn = document.getElementById("btn-previous");
        btn.classList.remove("disabled");
        btn.disabled = false;
    }

    if (actualPage >= maxPages) {
        btn = document.getElementById("btn-next");
        btn.classList.add("disabled");
        btn.disabled = true;
    }

    else {
        btn = document.getElementById("btn-next");
        btn.classList.remove("disabled");
        btn.disabled = false;
    }
}

function changePage(num) {
    actualPage += num;
}