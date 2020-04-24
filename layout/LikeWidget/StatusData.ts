import { IAPIStatusData } from "./IAPIStatusData";

export class StatusData {
    private readonly _likes: number;
    private readonly _pathAdd: string;
    private readonly _newToken: string | null;
    private _status: boolean | null;

    constructor(data: IAPIStatusData) {
        this._likes = data.likes;
        this._newToken = data.newToken;
        this._pathAdd = data.pathAdd;
        this._status = data.status;
    }

    public getEffectiveLikes(): number {
        // correct for unconfirmed like
        return this._likes + (false === this._status ? 1 : 0);
    }

    get pathAdd(): string {
        return this._pathAdd;
    }

    get newToken(): string | null {
        return this._newToken;
    }

    public getStatusAsText(): string {
        if (true === this._status) {
            return 'confirmed';
        }

        if (false === this._status) {
            return 'unconfirmed';
        }

        return 'no-information';
    }

    public isLocked(): boolean {
        // consider a like as 'locked' if it has
        // been counted or waits to be confirmed
        return null !== this._status;
    }

    public fakeUnconfirmed(): void {
        this._status = false;
    }
}
