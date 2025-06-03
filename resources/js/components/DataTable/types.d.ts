export interface ITableOptions<T> {
    key: keyof T;
    caption?: string;
    withRowSelection?: boolean;
    withRowActions?: boolean;
    withToolbar?: boolean;
    withTermSearch?: boolean;
    withPagination?: boolean;
    columns: ITableColumnOption<T>[];
    rowsPerPage?: number;
}

export interface ITableColumnOption<T> {
    key: keyof T | string;
    value?: string;
    label: string;
    class?: string;
    sortable?: boolean;
    hideable?: boolean;
}

export interface ITableFacetFilterOption<T> {
    key: keyof T;
    label: string;
    displaySelectedItems?: number;
    options: ITableFacetFilterOptionItem[];
}

export interface ITableFacetFilterOptionItem {
    label: string;
    value: unknown;
    group?: string;
    count: number;
    icon?: Component;
}

export interface IPaginatedMeta {
    current_page: number;
    from: number;
    last_page: number;
    links: {
        url: string | null;
        label: string;
        active: boolean;
    }[];
    path: string;
    per_page: number;
    to: number;
    total: number;
}

export interface ISortEntry {
    field: string;
    direction: 'asc' | 'desc';
}

export interface IQuery {
    sort: ISortEntry[];
    filter: {
        term?: string | null;
        facets?: IFilterFacetSelected;
    };
}

export interface IFilterFacetSelected extends Object {
    [key: string]: string[] | number[] | undefined;
}
