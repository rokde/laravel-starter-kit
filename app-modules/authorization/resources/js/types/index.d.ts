export interface Role {
    id: number;
    name: string;
    permissions: string[];
}

export interface Permission {
    id: string | number;
    resource: string;
    action: string;
    description: string;
}
