export interface Workspace {
    id: number;
    name: string;
}

export interface Role {
    key: string;
    name: string;
    description: string;
}

export interface Invitation {
    id: number;
    email: string;
    role: string;
    created_at: string;
}
