import type { PageProps } from '@inertiajs/core';
import { WorkspaceInfo } from '@workspace/resources/js/types';
import type { LucideIcon } from 'lucide-vue-next';
import type { Config } from 'ziggy-js';

export interface Auth {
    user: User;
}

export interface BreadcrumbItem {
    title: string;
    href: string;
}

export interface PageActionItem {
    label: string;
    icon?: LucideIcon;
    onClick?: () => void;
    disabled?: boolean;
}

export interface NavItem {
    title: string;
    href: string;
    icon?: LucideIcon;
    isActive?: boolean;
}

export interface SharedData extends PageProps {
    name: string;
    quote: { message: string; author: string };
    auth: Auth;
    ziggy: Config & { location: string };
    sidebarOpen: boolean;
    knownWorkspaces: WorkspaceInfo[];
}

export interface User {
    id: number;
    name: string;
    email: string;
    avatar?: string;
    email_verified_at: string | null;
    locale: 'de' | 'en';
    created_at: string;
    updated_at: string;
    workspace_id: number;
}

export type BreadcrumbItemType = BreadcrumbItem;
export type PageActionItemType = PageActionItem;
