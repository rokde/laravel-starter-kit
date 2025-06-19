export interface CustomPropertyDefinition {
    id: number;
    name: string;
    label: string;
    type: 'text' | 'date' | 'number' | 'boolean';
    rules: string[] | null;
    default_value: string | null;
    options: string[] | null;
}

export interface Definable {
    type: string;
    id: number | string;
}
