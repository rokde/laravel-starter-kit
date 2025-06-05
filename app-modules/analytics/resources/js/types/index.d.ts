export interface Analytic {
    id: number;
    name: string;
    impressions: number;
    hovers: number;
    clicks: number;
}

export interface Flow {
    name: string;
    steps: Array<{
        name: string;
        clicks: number;
    }>;
    clicks: number;
}
