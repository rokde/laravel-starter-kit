<script setup lang="ts">
import ConfirmButton from '@/components/ConfirmButton.vue';
import DatePicker from '@/components/DatePicker.vue';
import HeadingSmall from '@/components/HeadingSmall.vue';
import InputDescription from '@/components/InputDescription.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { getI18n } from '@/i18n';
import { slugify } from '@/lib/text-functions';
import { CustomPropertyDefinition, Definable } from '@customProperties/types';
import { useForm } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const { t } = getI18n();

interface RuleOption {
    value: string;
    label: string;
    display?: 'input';
    input?: string;
}

interface Props {
    definable: Definable;
}

const props = defineProps<Props>();

const emits = defineEmits<{
    (e: 'created'): void;
}>();

const form = useForm({
    name: '',
    label: '',
    type: 'text' as CustomPropertyDefinition['type'],
    rules: [],
    default_value: '',
    options: [],
});

const createDefinition = () => {
    form.transform((data) => ({
        ...data,
        rules: selectedRules.value.length > 0 ? selectedRules.value.map((rule: RuleOption) => rule.value) : undefined,
        options: data.options.length > 0 ? data.options : undefined,
        definable_type: props.definable.type,
        definable_id: props.definable.id,
    })).post(route('custom-properties.store'), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
            emits('created');
        },
    });
};

const updateInternalName = () => {
    form.name = slugify(form.label);
};

const resetDefaultValue = () => {
    form.default_value = '';
};

const types = [
    { value: 'text', label: t('Text') },
    { value: 'date', label: t('Date') },
    { value: 'number', label: t('Number') },
    { value: 'boolean', label: t('Checkbox') },
];

const selectedRules = ref<RuleOption[]>([]);
const rules: RuleOption[] = [
    { value: 'required', label: t('Required') },
    { value: 'numeric', label: t('Numeric') },
    { value: 'string', label: t('String') },
    { value: '-', label: t('Custom'), display: 'input' },
];

const currentRule = ref<RuleOption | null>(null);
const customRuleInput = ref('');

const selectableRules = computed(() =>
    rules.filter((rule) => rule.value === '-' || !selectedRules.value.some((selected) => selected.value === rule.value)),
);

const addRule = (rule: RuleOption) => {
    if (rule.display === 'input') {
        if (!customRuleInput.value) return;

        selectedRules.value.push({
            ...rule,
            value: customRuleInput.value,
            label: `${t('Custom')}: ${customRuleInput.value}`,
        });
        customRuleInput.value = '';
    } else {
        selectedRules.value.push(rule);
    }
    currentRule.value = null;
};

const removeRule = (ruleToRemove: RuleOption) => {
    selectedRules.value = selectedRules.value.filter((rule) => rule.value !== ruleToRemove.value);
    currentRule.value = null;
};
</script>

<template>
    <form @submit.prevent="createDefinition" class="space-y-6">
        <HeadingSmall :title="$t('Add custom property')" />

        <div class="grid gap-2">
            <Label for="label">{{ $t('Label') }}</Label>
            <Input id="label" v-model="form.label" type="text" class="block w-full" required @keyup="updateInternalName" />
            <InputDescription :description="$t('The label is used to identify the field in the UI.')" />
            <InputError :message="form.errors.label" />
        </div>

        <div class="grid gap-2">
            <Label for="name">{{ $t('Internal name') }}</Label>
            <Input id="name" v-model="form.name" type="text" class="block w-full" required pattern="[a-z0-9_]+" />
            <InputDescription
                :description="
                    $t('The internal name is used to reference the field in the API. It can only contain lowercase letters, numbers and underscores.')
                "
            />
            <InputError :message="form.errors.name" />
        </div>

        <div class="grid gap-2">
            <Label for="type">{{ $t('Type') }}</Label>
            <Select v-model="form.type" @update:model-value="resetDefaultValue" id="type">
                <SelectTrigger class="min-w-48">
                    <SelectValue class="p-2" />
                </SelectTrigger>
                <SelectContent>
                    <SelectItem v-for="type in types" :key="type.value" :value="type.value">
                        {{ type.label }}
                    </SelectItem>
                </SelectContent>
            </Select>
            <InputDescription
                :description="$t('The data type of the field configures the possible input element and a base validation of the provided value.')"
            />
            <InputError :message="form.errors.type" />
        </div>

        <div class="grid gap-2">
            <Label for="default_value">{{ $t('Default value') }}</Label>

            <Input v-if="form.type === 'text'" id="default_value" v-model="form.default_value" type="text" class="block w-full" />
            <Input v-if="form.type === 'number'" id="default_value" v-model="form.default_value" type="number" class="block w-48" />
            <DatePicker v-if="form.type === 'date'" id="default_value" v-model="form.default_value" clearable class="block w-full" />
            <Label v-if="form.type === 'boolean'" class="flex w-full items-center">
                <Checkbox id="default_value" v-model="form.default_value as any" />
                {{ (form.default_value as any as boolean) ? $t('Checked') : $t('Unchecked') }}
            </Label>
            <InputDescription
                :description="
                    $t('The default value is used for all items that do not have a value set. It will be the pre-set value on creating new items.')
                "
            />

            <InputError :message="form.errors.default_value" />
        </div>

        <div class="grid gap-2">
            <Label for="rules">{{ $t('Rules') }}</Label>

            <dl class="grid grid-cols-3">
                <template v-for="rule of selectedRules">
                    <dt>{{ rule.label }}</dt>
                    <dd class="font-mono">{{ rule.value }}</dd>
                    <dd class="text-right">
                        <ConfirmButton without-confirmation title="" confirmation="" as="icon" @confirmed="removeRule(rule)" />
                    </dd>
                </template>
            </dl>

            <div class="flex gap-2">
                <Select
                    :model-value="currentRule"
                    @update:model-value="
                        (rule: RuleOption) => {
                            currentRule = rule;
                            if (!rule.display) addRule(rule);
                        }
                    "
                >
                    <SelectTrigger class="min-w-48">
                        <SelectValue :placeholder="$t('Add a rule')" class="p-2" />
                    </SelectTrigger>
                    <SelectContent>
                        <SelectItem v-for="rule in selectableRules" :key="rule.value" :value="rule">
                            {{ rule.label }}
                        </SelectItem>
                    </SelectContent>
                </Select>

                <div v-if="currentRule?.display === 'input'" class="flex gap-2">
                    <Input
                        v-model="customRuleInput"
                        type="text"
                        :placeholder="$t('Enter custom rule')"
                        class="w-64"
                        @keydown.enter.stop="addRule(currentRule)"
                    />
                    <Button type="button" @click="addRule(currentRule)" :disabled="!customRuleInput">{{ $t('Add') }}</Button>
                </div>
            </div>
            <InputDescription
                :description="
                    $t(
                        'Rules provide form input validation to ensure that the value entered is valid. For example, you can require a value to be numeric or a string with a maximum length of 255 characters.',
                    )
                "
            />

            <InputError :message="form.errors.rules" />
        </div>

        <div class="flex items-center gap-4">
            <Button :disabled="form.processing">{{ $t('Save') }}</Button>

            <Transition
                enter-active-class="transition ease-in-out"
                enter-from-class="opacity-0"
                leave-active-class="transition ease-in-out"
                leave-to-class="opacity-0"
            >
                <p v-show="form.recentlySuccessful" class="text-sm text-neutral-600">{{ $t('Saved.') }}</p>
            </Transition>
        </div>
    </form>
</template>
