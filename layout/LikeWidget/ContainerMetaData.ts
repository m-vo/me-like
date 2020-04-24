export class ContainerMetaData {
    private readonly _pathInfo: string;
    private readonly _pathConfirm: string;
    private readonly _tokenKeyUser: string;
    private readonly _tokenKeyConfirm: string;

    constructor(container: HTMLElement) {
        this._pathInfo = container.getAttribute('data-like');
        this._pathConfirm = container.getAttribute('data-like-confirm');
        this._tokenKeyUser = container.getAttribute('data-token-key-user');
        this._tokenKeyConfirm = container.getAttribute('data-token-key-confirm');
    }

    get tokenKeyConfirm(): string {
        return this._tokenKeyConfirm;
    }

    get tokenKeyUser(): string {
        return this._tokenKeyUser;
    }

    get pathConfirm(): string {
        return this._pathConfirm;
    }

    get pathInfo(): string {
        return this._pathInfo;
    }
}