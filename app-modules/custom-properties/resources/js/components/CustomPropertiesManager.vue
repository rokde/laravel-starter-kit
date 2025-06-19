<script setup lang="ts">
import ConfirmButton from '@/components/ConfirmButton.vue';
import DataTable from '@/components/DataTable/DataTable.vue';
import { ITableOptions } from '@/components/DataTable/types';
import Heading from '@/components/Heading.vue';
import { Separator } from '@/components/ui/separator';
import { getI18n } from '@/i18n';
import CreateCustomPropertyForDefiner from '@customProperties/components/CreateCustomPropertyForDefiner.vue';
import { CustomPropertyDefinition, Definable } from '@customProperties/types';
import { router } from '@inertiajs/vue3';
import { onMounted, PropType, ref } from 'vue';

const { t } = getI18n();

const props = defineProps({
    definable: {
        type: Object as PropType<Definable>,
        required: true,
    },
});

const definitions = ref<CustomPropertyDefinition[]>([]);
const isLoading = ref<boolean>(true);
const error = ref<string | null>(null);
const fetchDefinitions = async () => {
    try {
        const url = route('custom-properties.index', {
            definable_type: props.definable.type,
            definable_id: props.definable.id,
        });
        const response = await fetch(url);
        if (!response.ok) {
            throw new Error('Error fetching custom properties. Please try again later. If the problem persists, please contact support.');
        }
        definitions.value = (await response.json()).data;
    } catch (e: any) {
        error.value = e.message;
    } finally {
        isLoading.value = false;
    }
};

const deleteDefinition = (id: number) => {
    router.delete(route('custom-properties.destroy', id), {
        preserveScroll: true,
        onSuccess: () => fetchDefinitions(),
    });
};

onMounted(() => fetchDefinitions());

const tableOptions: ITableOptions<CustomPropertyDefinition> = {
    key: 'id',
    withRowActions: true,
    columns: [
        {
            key: 'label',
            label: t('Label'),
            class: 'w-full',
        },
        {
            key: 'type',
            label: t('Type'),
            class: 'w-64',
        },
        {
            key: 'default_value',
            label: t('Default value'),
            class: 'w-64 text-right',
        },
        {
            key: 'rules',
            label: t('Rules'),
            class: 'w-64',
            value: (row) => row.rules?.join(', '),
        },
    ],
};
</script>

<template>
    <div class="flex flex-col space-y-6">
        <Heading :title="$t('Custom properties')" :description="$t('Manage your custom properties to extend your resources to your needs.')" />

        <DataTable :rows="definitions" :options="tableOptions">
            <template #label="{ row: definition }">
                {{ definition.label }}<br />
                <span class="text-muted-foreground font-mono text-xs">{{ definition.name }}</span>
            </template>

            <template #rowActions="{ row: definition }">
                <ConfirmButton
                    as="icon"
                    :title="$t('Delete property')"
                    :confirmation="
                        $t(
                            'Do you really want to remove the property? All property values will be removed on all items. This action can not be undone.',
                        )
                    "
                    @confirmed="deleteDefinition(definition.id)"
                />
            </template>
        </DataTable>

        <Separator class="my-6" />

        <CreateCustomPropertyForDefiner :definable="props.definable" @created="fetchDefinitions" />
    </div>
</template>
