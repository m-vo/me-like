import LikeWidget from "./LikeWidget/LikeWidget";
import ResetLink from "./LikeWidget/ResetLink";

function initializeLikeWidgets() {
    const containers = document.querySelectorAll<HTMLElement>('*[data-like]');

    if (null === containers) {
        return;
    }

    containers.forEach(e => {
        new LikeWidget(e).initialize();
    });
}

function initializeResetLinks() {
    const elements = document.querySelectorAll<HTMLElement>('*[data-like-reset]');

    if (null === elements) {
        return;
    }

    elements.forEach(e => {
        new ResetLink(e).initialize();
    });
}

// automatically find and initialize elements
document.addEventListener('DOMContentLoaded', () => {
    initializeLikeWidgets();
    initializeResetLinks();
});
