<script setup lang="ts">
import ConfirmButton from '@/components/ConfirmButton.vue';
import Heading from '@/components/Heading.vue';
import { Button } from '@/components/ui/button';
import { DropdownMenu, DropdownMenuContent, DropdownMenuItem, DropdownMenuTrigger } from '@/components/ui/dropdown-menu';
import { Progress } from '@/components/ui/progress';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';
import { getI18n } from '@/i18n';
import AppLayout from '@/layouts/AppLayout.vue';
import ContentLayout from '@/layouts/content/ContentLayout.vue';
import { localePercent } from '@/lib/number-functions';
import type { BreadcrumbItem } from '@/types';
import { Analytic, Flow } from '@analytics/types';
import { Head, router } from '@inertiajs/vue3';
import { ArrowDownAzIcon, ArrowDownUpIcon, ArrowUpZaIcon, XIcon } from 'lucide-vue-next';

const { t } = getI18n();

interface Props {
    analytics: Analytic[];
    sort: {
        column: null | string;
        direction: null | 'asc' | 'desc';
    };
    flows: Flow[];
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

        <ContentLayout>
            <div class="flex items-start justify-between">
                <Heading :title="$t('Analytics')" />

                <ConfirmButton
                    as="icon"
                    :disabled="props.analytics.length < 1"
                    :title="$t('Flush analytics')"
                    :confirmation="$t('Do you want to flush all analytics?')"
                    @confirmed="router.delete(route('analytics.destroy'), { preserveScroll: true, onSuccess: () => router.reload() })"
                />
            </div>

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
                                {{ localePercent(analytic.hovers / analytic.impressions, 2) }}
                            </TableCell>
                            <TableCell class="text-right slashed-zero tabular-nums">{{ analytic.clicks }}</TableCell>
                            <TableCell class="text-muted-foreground w-8 text-right text-xs slashed-zero tabular-nums">
                                {{ localePercent(analytic.clicks / analytic.impressions, 2) }}
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

            <Heading :title="$t('Flows')" class="mt-8" />

            <div class="rounded-md border">
                <Table>
                    <TableHeader>
                        <TableRow>
                            <TableHead class="w-32">{{ $t('Name') }}</TableHead>
                            <TableHead class="w-fit">{{ $t('Flow') }}</TableHead>
                        </TableRow>
                    </TableHeader>
                    <TableBody>
                        <TableRow v-for="flow in props.flows" :key="flow.name">
                            <TableCell class="w-64">{{ flow.name }}</TableCell>
                            <TableCell class="w-fit">
                                <template v-for="(step, index) in flow.steps" :key="step.name + index">
                                    <div class="flex w-full items-center">
                                        <div class="w-2/3 font-mono">
                                            <span class="text-muted-foreground text-xs">({{ index + 1 }})</span>
                                            {{ step.name }}
                                        </div>

                                        <div class="flex w-1/3 items-center">
                                            <Progress :model-value="step.clicks ? (step.clicks / flow.clicks) * 100 : 0" class="w-32 shrink-0" />
                                            <span class="grow text-right slashed-zero tabular-nums">{{ step.clicks }}</span>
                                        </div>
                                    </div>
                                </template>
                            </TableCell>
                        </TableRow>
                    </TableBody>
                </Table>
            </div>
        </ContentLayout>
    </AppLayout>
</template>
