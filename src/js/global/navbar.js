// We need to keep track of faded in elements so we can apply fade out later in CSS
document.addEventListener('animationstart', function (e) {
    if (e.animationName === 'fade-in') {
        e.target.classList.add('did-fade-in');
    }
  });

document.addEventListener('animationend', function (e) {
    if (e.animationName === 'fade-out') {
        e.target.classList.remove('did-fade-in');
    }
});

window.addEventListener("resize", function() {
    var windowWidth = window.innerWidth;

    if (windowWidth > 1100) {
        if (document.querySelector("div.nav-items-aux").style.display == "block") openNavbar();
    }
});

function openNavbar() {
    const items = document.querySelector("div.nav-items-aux");
    const display = items.style.display == "block" ? "none" : "block";

    if (display == "block") {
        items.style.display = display;
        setTimeout(() => { document.querySelector("div.nav-items-aux").style.overflowY = "visible"; }, 200);
    }

    else {
        items.style.overflowY = "hidden";
        items.style.animation = "shrink-to-top 0.2s ease-out";

        setTimeout(() => { 
            document.querySelector("div.nav-items-aux").style.display = "none";
            items.style.animation = "grow-from-top 0.2s ease-out";
        }, 200);
    }
}