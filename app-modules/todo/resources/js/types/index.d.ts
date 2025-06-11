export interface Todo {
    id: number;
    title: string;
    completed: boolean;
    due_date: string | null;
    user_id: number;
    user?: {
        id: number;
        name: string;
        email: string;
    };
}
