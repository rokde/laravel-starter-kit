export interface Todo {
    id: number;
    title: string;
    completed: boolean;
    user_id: number;
    user?: {
        id: number;
        name: string;
        email: string;
    };
}
