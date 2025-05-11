<script setup lang="ts">
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';
import { Separator } from '@/components/ui/separator';
import { getI18n } from '@/i18n';
import { type NavItem } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';

const { t } = getI18n();

const sidebarNavItems: NavItem[] = [
    {
        title: t('Workspace'),
        href: '/workspaces/current',
    },
    {
        title: t('Members'),
        href: '/workspaces/current/members',
    },
    {
        title: t('Invitations'),
        href: '/workspaces/current/invitations',
    },
];

const page = usePage();

const currentPath = page.props.ziggy?.location ? new URL(page.props.ziggy.location).pathname : '';
</script>

<template>
    <div class="px-4 py-6">
        <Heading :title="$t('Workspace')" :description="$t('Manage your workspace and the acting members')" />

        <div class="flex flex-col space-y-8 md:space-y-0 lg:flex-row lg:space-y-0 lg:space-x-12">
            <aside class="w-full max-w-xl lg:w-48">
                <nav class="flex flex-col space-y-1 space-x-0">
                    <Button
                        v-for="item in sidebarNavItems"
                        :key="item.href"
                        variant="ghost"
                        :class="['w-full justify-start', { 'bg-muted': currentPath === item.href }]"
                        as-child
                    >
                        <Link :href="item.href">
                            {{ item.title }}
                        </Link>
                    </Button>
                </nav>
            </aside>

            <Separator class="my-6 md:hidden" />

            <div class="flex-1 md:max-w-2xl">
                <section class="max-w-xl space-y-12">
                    <div class="flex flex-col space-y-6">
                        <slot />
                    </div>
                </section>
            </div>
        </div>
    </div>
</template>
