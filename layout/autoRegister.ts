import LikeWidget from "./LikeWidget/LikeWidget";

// auto register like widgets
document.addEventListener('DOMContentLoaded', () => {
    const containers = document.querySelectorAll<HTMLElement>('*[data-like]');

    if (null === containers) {
        return;
    }

    containers.forEach(e => {
        new LikeWidget(e).initialize();
    });
});
