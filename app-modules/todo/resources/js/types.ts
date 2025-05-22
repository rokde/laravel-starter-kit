☺export interface Todo {
    id: number;
    title: string;
    completed: boolean;
    workspace_id: number;
    user_id: number;
    user?: {
        id: number;
        name: string;
        email: string;
    };
    created_at: string;
    updated_at: string;
}
