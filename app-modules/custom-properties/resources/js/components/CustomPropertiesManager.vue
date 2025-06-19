<script setup lang="ts">
import ConfirmButton from '@/components/ConfirmButton.vue';
import HeadingSmall from '@/components/HeadingSmall.vue';
import CreateCustomPropertyForDefiner from '@customProperties/components/CreateCustomPropertyForDefiner.vue';
import { CustomPropertyDefinition, Definable } from '@customProperties/types';
import { router } from '@inertiajs/vue3';
import { onMounted, PropType, ref } from 'vue';

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
</script>

<template>
    <div class="flex flex-col space-y-6">
        <HeadingSmall :title="$t('Custom Properties')" :description="$t('Manage your custom properties to extend your resources to your needs.')" />

        <div class="mt-6 flow-root">
            <div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
                    <table v-if="definitions.length > 0" class="min-w-full divide-y divide-gray-300 dark:divide-gray-700">
                        <thead>
                            <tr>
                                <th scope="col" class="py-3.5 pr-3 pl-4 text-left text-sm font-semibold text-gray-900 sm:pl-0 dark:text-gray-100">
                                    Label
                                </th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-gray-100">
                                    Interner Name
                                </th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-gray-100">Typ</th>
                                <th scope="col" class="relative py-3.5 pr-4 pl-3 sm:pr-0"><span class="sr-only">Löschen</span></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
                            <tr v-for="definition in definitions" :key="definition.id">
                                <td class="py-4 pr-3 pl-4 text-sm font-medium whitespace-nowrap text-gray-900 sm:pl-0 dark:text-gray-100">
                                    {{ definition.label }}
                                </td>
                                <td class="px-3 py-4 text-sm whitespace-nowrap text-gray-500 dark:text-gray-400">
                                    <code>{{ definition.name }}</code>
                                </td>
                                <td class="px-3 py-4 text-sm whitespace-nowrap text-gray-500 dark:text-gray-400">{{ definition.type }}</td>
                                <td class="relative py-4 pr-4 pl-3 text-right text-sm font-medium whitespace-nowrap sm:pr-0">
                                    <ConfirmButton
                                        :title="$t('Delete property')"
                                        :confirmation="
                                            $t(
                                                'Do you really want to remove the property? All property values will be removed on all items. This action can not be undone.',
                                            )
                                        "
                                        @confirmed="deleteDefinition(definition.id)"
                                    />
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <p v-else class="text-sm text-gray-500 dark:text-gray-400">Noch keine benutzerdefinierten Felder angelegt.</p>
                </div>
            </div>
        </div>

        <CreateCustomPropertyForDefiner :definable="props.definable" @created="fetchDefinitions" />
    </div>
</template>
