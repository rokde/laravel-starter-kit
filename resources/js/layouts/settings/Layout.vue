<script setup lang="ts">
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';
import { getI18n } from '@/i18n';
import ContentSplitLayout from '@/layouts/content/ContentSplitLayout.vue';
import { type NavItem } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';

const { t } = getI18n();

const sidebarNavItems: NavItem[] = [
    {
        title: t('Profile'),
        href: '/settings/profile',
    },
    {
        title: t('Password'),
        href: '/settings/password',
    },
    {
        title: t('Appearance'),
        href: '/settings/appearance',
    },
    {
        title: t('Locale'),
        href: '/settings/locale',
    },
    {
        title: t('Notifications'),
        href: '/settings/notifications',
    },
    {
        title: t('passkeys::passkeys.passkeys'),
        href: '/settings/passkeys',
    },
];

const page = usePage();

const currentPath = page.props.ziggy?.location ? new URL(page.props.ziggy.location).pathname : '';
</script>

<template>
    <div class="px-4 py-6">
        <Heading :title="$t('Settings')" :description="$t('Manage your profile and account settings')" />

        <ContentSplitLayout>
            <template #sidebar>
                <nav class="flex flex-col space-y-1 space-x-0">
                    <Button
                        v-for="item in sidebarNavItems"
                        :key="item.href"
                        variant="ghost"
                        :class="['w-full justify-start', { 'bg-muted': currentPath === item.href }]"
                        as-child
                    >
                        <Link :href="item.href" class="overflow-hidden">
                            {{ item.title }}
                        </Link>
                    </Button>
                </nav>
            </template>

            <template #default>
                <slot />
            </template>
        </ContentSplitLayout>
    </div>
</template>
