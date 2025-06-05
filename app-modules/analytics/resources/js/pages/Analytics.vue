<script setup lang="ts">
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';
import { DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuTrigger } from '@/components/ui/dropdown-menu';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { getI18n } from '@/i18n';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';
import { Analytic } from '@analytics/types';
import { Head, router } from '@inertiajs/vue3';
import { ArrowDownAzIcon, ArrowDownUpIcon, ArrowUpZaIcon, XIcon } from 'lucide-vue-next';
import { localePercent } from '../../../../../resources/js/lib/number-functions';

const { t } = getI18n();

interface Props {
    analytics: Analytic[];
    sort: {
        column: null | string;
        direction: null | 'asc' | 'desc';
    };
}

const props = defineProps<Props>();

const breadcrumbItems: BreadcrumbItem[] = [
    {
        title: t('Analytics'),
        href: route('analytics.index'),
    },
];

const doSort = (column: string, direction: undefined | 'asc' | 'desc'): void => {
    let field: string | undefined = undefined;
    if (direction === 'asc') {
        field = column;
    } else if (direction === 'desc') {
        field = `-${column}`;
    }

    router.reload({ data: { sort: field } });
};
</script>

<template>
    <AppLayout :breadcrumbs="breadcrumbItems">
        <Head :title="$t('Analytics')" />

        <div class="mx-auto my-8 flex w-full max-w-5xl flex-col">
            <Heading :title="$t('Analytics')" />

            <div class="flex flex-col gap-y-4">
                <div class="rounded-md border">
                    <Table>
                        <TableHeader>
                            <TableRow>
                                <TableHead class="w-fit">
                                    <div class="flex items-center space-x-2">
                                        <DropdownMenu>
                                            <DropdownMenuTrigger as-child>
                                                <Button variant="ghost" size="sm" class="data-[state=open]:bg-accent -ml-3 h-8 text-sm">
                                                    <span>{{ $t('Name') }}</span>
                                                    <ArrowUpZaIcon
                                                        v-if="props.sort.column === 'name' && props.sort.direction === 'desc'"
                                                        class="ml-2 size-4"
                                                    />
                                                    <ArrowDownAzIcon
                                                        v-else-if="props.sort.column === 'name' && props.sort.direction === 'asc'"
                                                        class="ml-2 size-4"
                                                    />
                                                    <ArrowDownUpIcon v-else class="text-muted-foreground ml-2 size-4" />
                                                </Button>
                                            </DropdownMenuTrigger>
                                            <DropdownMenuContent align="start">
                                                <DropdownMenuItem @click="doSort('name', 'asc')">
                                                    <ArrowDownAzIcon class="text-muted-foreground/70 mr-2 h-3.5 w-3.5" />
                                                    {{ $t('Ascending') }}
                                                </DropdownMenuItem>
                                                <DropdownMenuItem @click="doSort('name', 'desc')">
                                                    <ArrowUpZaIcon class="text-muted-foreground/70 mr-2 h-3.5 w-3.5" />
                                                    {{ $t('Descending') }}
                                                </DropdownMenuItem>
                                                <DropdownMenuItem @click="doSort('name', undefined)">
                                                    <XIcon class="text-muted-foreground/70 mr-2 h-3.5 w-3.5" />
                                                    {{ $t('Clear sorting') }}
                                                </DropdownMenuItem>
                                            </DropdownMenuContent>
                                        </DropdownMenu>
                                    </div>
                                </TableHead>
                                <TableHead class="w-20 text-right">
                                    <div class="flex items-center space-x-2">
                                        <DropdownMenu>
                                            <DropdownMenuTrigger as-child>
                                                <Button variant="ghost" size="sm" class="data-[state=open]:bg-accent -ml-3 h-8 text-sm">
                                                    <span>{{ $t('Impressions') }}</span>
                                                    <ArrowUpZaIcon
                                                        v-if="props.sort.column === 'impressions' && props.sort.direction === 'desc'"
                                                        class="ml-2 size-4"
                                                    />
                                                    <ArrowDownAzIcon
                                                        v-else-if="props.sort.column === 'impressions' && props.sort.direction === 'asc'"
                                                        class="ml-2 size-4"
                                                    />
                                                    <ArrowDownUpIcon v-else class="text-muted-foreground ml-2 size-4" />
                                                </Button>
                                            </DropdownMenuTrigger>
                                            <DropdownMenuContent align="start">
                                                <DropdownMenuItem @click="doSort('impressions', 'asc')">
                                                    <ArrowDownAzIcon class="text-muted-foreground/70 mr-2 h-3.5 w-3.5" />
                                                    {{ $t('Ascending') }}
                                                </DropdownMenuItem>
                                                <DropdownMenuItem @click="doSort('impressions', 'desc')">
                                                    <ArrowUpZaIcon class="text-muted-foreground/70 mr-2 h-3.5 w-3.5" />
                                                    {{ $t('Descending') }}
                                                </DropdownMenuItem>
                                                <DropdownMenuItem @click="doSort('impressions', undefined)">
                                                    <XIcon class="text-muted-foreground/70 mr-2 h-3.5 w-3.5" />
                                                    {{ $t('Clear sorting') }}
                                                </DropdownMenuItem>
                                            </DropdownMenuContent>
                                        </DropdownMenu>
                                    </div>
                                </TableHead>
                                <TableHead colspan="2">
                                    <div class="flex items-center justify-end space-x-2">
                                        <DropdownMenu>
                                            <DropdownMenuTrigger as-child>
                                                <Button variant="ghost" size="sm" class="data-[state=open]:bg-accent -ml-3 h-8 text-sm">
                                                    <span>{{ $t('Hovers') }}</span>
                                                    <ArrowUpZaIcon
                                                        v-if="props.sort.column === 'hovers' && props.sort.direction === 'desc'"
                                                        class="ml-2 size-4"
                                                    />
                                                    <ArrowDownAzIcon
                                                        v-else-if="props.sort.column === 'hovers' && props.sort.direction === 'asc'"
                                                        class="ml-2 size-4"
                                                    />
                                                    <ArrowDownUpIcon v-else class="text-muted-foreground ml-2 size-4" />
                                                </Button>
                                            </DropdownMenuTrigger>
                                            <DropdownMenuContent align="start">
                                                <DropdownMenuItem @click="doSort('hovers', 'asc')">
                                                    <ArrowDownAzIcon class="text-muted-foreground/70 mr-2 h-3.5 w-3.5" />
                                                    {{ $t('Ascending') }}
                                                </DropdownMenuItem>
                                                <DropdownMenuItem @click="doSort('hovers', 'desc')">
                                                    <ArrowUpZaIcon class="text-muted-foreground/70 mr-2 h-3.5 w-3.5" />
                                                    {{ $t('Descending') }}
                                                </DropdownMenuItem>
                                                <DropdownMenuItem @click="doSort('hovers', undefined)">
                                                    <XIcon class="text-muted-foreground/70 mr-2 h-3.5 w-3.5" />
                                                    {{ $t('Clear sorting') }}
                                                </DropdownMenuItem>
                                            </DropdownMenuContent>
                                        </DropdownMenu>
                                    </div>
                                </TableHead>
                                <TableHead colspan="2">
                                    <div class="flex items-center justify-end space-x-2">
                                        <DropdownMenu>
                                            <DropdownMenuTrigger as-child>
                                                <Button variant="ghost" size="sm" class="data-[state=open]:bg-accent -ml-3 h-8 text-sm">
                                                    <span>{{ $t('Clicks') }}</span>
                                                    <ArrowUpZaIcon
                                                        v-if="props.sort.column === 'clicks' && props.sort.direction === 'desc'"
                                                        class="ml-2 size-4"
                                                    />
                                                    <ArrowDownAzIcon
                                                        v-else-if="props.sort.column === 'hovers' && props.sort.direction === 'asc'"
                                                        class="ml-2 size-4"
                                                    />
                                                    <ArrowDownUpIcon v-else class="text-muted-foreground ml-2 size-4" />
                                                </Button>
                                            </DropdownMenuTrigger>
                                            <DropdownMenuContent align="start">
                                                <DropdownMenuItem @click="doSort('clicks', 'asc')">
                                                    <ArrowDownAzIcon class="text-muted-foreground/70 mr-2 h-3.5 w-3.5" />
                                                    {{ $t('Ascending') }}
                                                </DropdownMenuItem>
                                                <DropdownMenuItem @click="doSort('clicks', 'desc')">
                                                    <ArrowUpZaIcon class="text-muted-foreground/70 mr-2 h-3.5 w-3.5" />
                                                    {{ $t('Descending') }}
                                                </DropdownMenuItem>
                                                <DropdownMenuItem @click="doSort('clicks', undefined)">
                                                    <XIcon class="text-muted-foreground/70 mr-2 h-3.5 w-3.5" />
                                                    {{ $t('Clear sorting') }}
                                                </DropdownMenuItem>
                                            </DropdownMenuContent>
                                        </DropdownMenu>
                                    </div>
                                </TableHead>
                            </TableRow>
                        </TableHeader>
                        <TableBody>
                            <TableRow v-for="analytic in props.analytics" :key="analytic.id">
                                <TableCell class="font-mono select-all">{{ analytic.name }}</TableCell>
                                <TableCell class="text-right slashed-zero tabular-nums">{{ analytic.impressions }}</TableCell>
                                <TableCell class="text-right slashed-zero tabular-nums">{{ analytic.hovers }}</TableCell>
                                <TableCell class="text-muted-foreground w-8 text-right text-xs slashed-zero tabular-nums">
                                    {{ localePercent(analytic.hovers / analytic.impressions) }}
                                </TableCell>
                                <TableCell class="text-right slashed-zero tabular-nums">{{ analytic.clicks }}</TableCell>
                                <TableCell class="text-muted-foreground w-8 text-right text-xs slashed-zero tabular-nums">
                                    {{ localePercent(analytic.clicks / analytic.impressions) }}
                                </TableCell>
                            </TableRow>
                            <TableRow v-if="props.analytics.length <= 0">
                                <TableCell colspan="3">
                                    {{ $t('No analytics yet.') }}
                                </TableCell>
                            </TableRow>
                        </TableBody>
                    </Table>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
