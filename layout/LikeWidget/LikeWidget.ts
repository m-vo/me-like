import { ContainerMetaData } from "./ContainerMetaData";
import { StatusData } from './StatusData';

export default class LikeWidget {
    static readonly USER_TOKEN_STORAGE_KEY: string = 'mvo_like_token';

    static confirmHandled: boolean = false;
    static acquiredUserToken: string | null = null;
    static emailAddress: string | null = null;

    // html containers / elements
    private readonly _container: HTMLElement;
    private readonly _counterElement: HTMLElement;
    private readonly _statusElement: HTMLElement;
    private readonly _formElement: HTMLFormElement;

    constructor(container: HTMLElement) {
        // get containers
        this._container = container;
        this._counterElement = container.querySelector<HTMLElement>('*[data-like-counter]');
        this._statusElement = container.querySelector<HTMLElement>('*[data-like-status]');
        this._formElement = container.querySelector<HTMLFormElement>('*[data-like-form]');
    }

    private static getFragmentToken(key: string, length: number = 64): string | null {
        const fragmentParams = new URLSearchParams(window.location.hash.substr(1));
        const token = fragmentParams.get(key);

        if (null === token || token.length !== length) {
            return null;
        }

        // remove fragment from url
        fragmentParams.delete(key);
        window.location.hash = fragmentParams.toString();

        return token;
    }

    public initialize(): void {
        // read meta data from container
        const metaData = new ContainerMetaData(this._container);

        // proxy confirm requests (once per page load)
        this.handleConfirm(metaData.tokenKeyConfirm, metaData.pathConfirm);

        // try to get user token
        if (null === LikeWidget.acquiredUserToken) {
            // ... from url
            LikeWidget.acquiredUserToken = LikeWidget.getFragmentToken(metaData.tokenKeyUser);

            if (null !== LikeWidget.acquiredUserToken) {
                localStorage.setItem(LikeWidget.USER_TOKEN_STORAGE_KEY, LikeWidget.acquiredUserToken);
            } else {
                // ... fallback to local storage
                LikeWidget.acquiredUserToken = localStorage.getItem(LikeWidget.USER_TOKEN_STORAGE_KEY);
            }
        }

        // query API + activate features
        this.fetchData(metaData.pathInfo).then((statusData: StatusData) => {
            // acquire new token from API if necessary
            if (null !== statusData.newToken && null === LikeWidget.acquiredUserToken) {
                LikeWidget.acquiredUserToken = statusData.newToken;
                localStorage.setItem(LikeWidget.USER_TOKEN_STORAGE_KEY, LikeWidget.acquiredUserToken);
            }

            this.updateUI(statusData);

            this.handleForm(statusData.pathAdd, () => {
                statusData.fakeUnconfirmed();
                this.updateUI(statusData);
            });
        });
    }

    private handleConfirm(tokenKey: string, path: string): void {
        if (LikeWidget.confirmHandled) {
            return;
        }

        const activationToken = LikeWidget.getFragmentToken(tokenKey);

        if (null === activationToken) {
            return;
        }

        // synchronous; do not care about the result
        (async () => {
            const formData = new FormData();
            formData.append('token', activationToken)

            await fetch(path, {
                method: 'post',
                body: formData,
            });
        })();

        LikeWidget.confirmHandled = true;
    }

    private async fetchData(pathInfo: string): Promise<StatusData> {
        const url = pathInfo + (null !== LikeWidget.acquiredUserToken ? `?token=${LikeWidget.acquiredUserToken}` : '');

        return await fetch(url)
            .then(response => response.json())
            .then(data => {
                return new StatusData(data);
            });
    }

    private updateUI(statusData: StatusData): void {
        // set count
        this._counterElement.innerHTML = `<span>${String(statusData.getEffectiveLikes())}</span>`;

        // set classes [.loading, .locked, .status--*]
        this._container.classList.remove('loading');

        if (statusData.isLocked()) {
            this._container.classList.add('locked');
            this._container.classList.remove('hide-form');
        }

        const setStatusClasses = (target: HTMLElement) => {
            target.classList.remove.apply(
                target.classList,
                Array.from(target.classList).filter(c => c.startsWith('status--'))
            );

            target.classList.add('status--' + statusData.getStatusAsText());
        }

        setStatusClasses(this._container);
        setStatusClasses(this._statusElement);
    }

    private handleForm(pathAdd: string, onSuccess: () => void): void {
        // show hidden form
        this._container.addEventListener('click', () => {
            if (!this._container.classList.contains('hide-form')) {
                return false;
            }

            this._container.classList.remove('hide-form');
            this._container.classList.add('show-form');

            const inputField = this._formElement.querySelector<HTMLInputElement>('input');
            if (null !== inputField) {
                if (null !== LikeWidget.emailAddress) {
                    inputField.value = LikeWidget.emailAddress;
                }

                inputField.focus();
            }

            return false;
        });

        // handle submit
        this._formElement.addEventListener('submit', (e) => {
            if (e.preventDefault) {
                e.preventDefault();
            }

            const formData = new FormData(this._formElement);
            formData.append('token', LikeWidget.acquiredUserToken)

            LikeWidget.emailAddress = <string>(formData.get('email'));

            fetch(pathAdd, {
                method: 'post',
                body: formData
            }).then(response => {
                this._container.classList.remove('show-form');
                this._container.classList.remove('sent-form');
                this._formElement.remove();

                if (204 === response.status) {
                    onSuccess();
                } else {
                    this._container.classList.add('error');
                }
            });

            return false;
        });
    }
}