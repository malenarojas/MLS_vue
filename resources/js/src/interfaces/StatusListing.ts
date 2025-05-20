export interface StatusListing {
    id: number;
    name: string;
    isFinal?: number;
    transitionsFrom?: StatusListingTransition[];
}

export interface StatusListingTransition {
    id: number;
    name: string;
    isFinal?: number;
}

export interface Pivot {
    fromStatusId: number;
    toStatusId:   number;
}