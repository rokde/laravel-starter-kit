export interface Notification {
    id: string;
    type: string;
    group: string;
    title: string;
    url: string | null;
    data: {
        [key: string]: unknown;
    };
    read: boolean;
    created_at: string;
}
