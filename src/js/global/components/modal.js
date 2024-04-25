var modal = undefined;
var background = undefined;
var newDisplay = undefined;

/**
 * Abre ou fecha um modal específico
 * @param {String} modalID ID do modal a ser aberto/fechado
 * @returns {void}
 */
const openModal = (modalID = undefined) => {
    modal = document.getElementById(modalID ? modalID : "modal");
    background = document.getElementById((modalID ? modalID : "modal") + "-background");
    newDisplay = modal.style.display == "block" ? "none" : "block";
    
    if (newDisplay == "none") {
        modal.style.animation = "fade-out 0.2s linear";
        background.style.animation = "fade-out 0.2s linear";
        setTimeout(changeModalDisplay, 200);
    }
    
    else {
        changeModalDisplay();
    }
}

/**
 * Altera o display da div de modal e de sua div de background
 * @returns {void}
 */
const changeModalDisplay = () => {
    modal.style.display = newDisplay;
    background.style.display = newDisplay;
    modal.style.animation = "fade-in 0.2s linear";
    background.style.animation = "fade-in 0.2s linear";
}