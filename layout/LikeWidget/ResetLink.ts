import LikeWidget from "./LikeWidget";

export default class ResetLink {
    private readonly _target: HTMLElement;

    constructor(target: HTMLElement) {
        this._target = target;
    }

    public initialize(): void {
        this._target.addEventListener('click', (e) => {
            if (e.preventDefault) {
                e.preventDefault();
            }

            localStorage.removeItem(LikeWidget.USER_TOKEN_STORAGE_KEY);
            location.reload();

            return false;
        });
    }
}