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
/*
document.getElementById("btn-nav-collase").addEventListener('mousedown', () => {
    const div = document.getElementById("nav-items-collapse");
    
    div.style.display = div.style.display == "grid" ? "none" : "grid";
})*/