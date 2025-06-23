<script setup lang="ts">
import ConfirmButton from '@/components/ConfirmButton.vue';
import DatePicker from '@/components/DatePicker.vue';
import HeadingSmall from '@/components/HeadingSmall.vue';
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from '@/components/ui/select';
import { getI18n } from '@/i18n';
import { CustomPropertyDefinition, Definable } from '@customProperties/types';
import { useForm } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import { Separator } from '@/components/ui/separator';

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
    label: '',
    type: 'text' as CustomPropertyDefinition['type'],
    rules: [],
    default_value: '',
    property_options: {
        decimal_places: 0,
        suffix: '',
        sort: '-',
    },
    display_options: {
        display: 'value',
    },
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
            selectedRules.value = [];
            customRuleInput.value = '';
            emits('created');
        },
    });
};

const resetDefaultValue = () => {
    form.reset('property_options', 'default_value', 'display_options');
    selectedRules.value = [];
    customRuleInput.value = '';
};

const types = [
    { value: 'text', label: t('Text') },
    { value: 'date', label: t('Date') },
    { value: 'number', label: t('Number') },
    { value: 'color', label: t('Color') },
    { value: 'boolean', label: t('Checkbox') },
    { value: 'select', label: t('Select') },
];

const rulesForTypes: { [key: string]: RuleOption[] } = {
    text: [
        { value: 'required', label: t('Required') },
        { value: 'email', label: t('Email') },
        { value: 'url', label: t('Url') },
        { value: '-', label: t('Custom'), display: 'input' },
    ],
    date: [],
    number: [],
    color: [],
    boolean: [],
    select: [],
};

const selectedRules = ref<RuleOption[]>([]);
const currentRule = ref<RuleOption | null>(null);
const customRuleInput = ref('');

const selectableRules = computed(() =>
    rulesForTypes[form.type]?.filter(
        (rule: RuleOption) => rule.value === '-' || !selectedRules.value.some((selected) => selected.value === rule.value),
    ),
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

const hasDefaultValue = computed<boolean>(() => {
    return ['text', 'date', 'number', 'color'].includes(form.type);
});
</script>

<template>
    <form @submit.prevent="createDefinition" class="space-y-6">
        <HeadingSmall :title="$t('Add custom property')" />

        <div class="grid grid-cols-2 gap-2">
            <div class="grid gap-2 self-start">
                <Label
                    for="type"
                    :title="$t('The type of the field configures the possible input element and a base validation of the provided value.')"
                    >{{ $t('Type') }}</Label
                >
                <Select v-model="form.type" @update:model-value="resetDefaultValue" id="type" required>
                    <SelectTrigger class="min-w-48">
                        <SelectValue class="p-2" />
                    </SelectTrigger>
                    <SelectContent>
                        <SelectItem v-for="type in types" :key="type.value" :value="type.value">
                            {{ type.label }}
                        </SelectItem>
                    </SelectContent>
                </Select>
                <InputError :message="form.errors.type" />
            </div>

            <!-- Property Options -->
            <div v-if="form.type === 'number'" class="grid gap-2">
                <div class="grid gap-2">
                    <Label for="decimal_places">{{ $t('Decimal places') }}</Label>
                    <Input
                        id="decimal_places"
                        v-model="form.property_options.decimal_places"
                        type="number"
                        min="0"
                        max="10"
                        step="1"
                        class="block w-48"
                        required
                    />
                    <InputError :message="form.errors.property_options" />
                </div>
                <div class="grid gap-2">
                    <Label for="suffix">{{ $t('Suffix') }}</Label>
                    <Input
                        id="suffix"
                        v-model="form.property_options.suffix"
                        type="text"
                        min="0"
                        max="10"
                        class="block w-48"
                        :placeholder="$t('% or USD')"
                    />
                    <InputError :message="form.errors.property_options" />
                </div>
            </div>
            <div v-if="form.type === 'select'" class="grid gap-2">
                <div class="grid gap-2">
                    <Label for="sort">{{ $t('Sort order') }}</Label>
                    <Select v-model="form.property_options.sort">
                        <SelectTrigger class="min-w-48">
                            <SelectValue class="p-2" />
                        </SelectTrigger>
                        <SelectContent>
                            <SelectItem value="-">{{ $t('No sort order') }}</SelectItem>
                            <SelectItem value="asc">{{ $t('Sort in ascending order') }}</SelectItem>
                            <SelectItem value="desc">{{ $t('Sort in descending order') }}</SelectItem>
                        </SelectContent>
                    </Select>
                    <InputError :message="form.errors.property_options" />
                </div>
            </div>
        </div>

        <Separator v-if="form.type" class="my-6" />

        <div v-if="form.type" class="grid gap-2">
            <Label for="label" :title="$t('The label is used to identify the field in the UI.')" required>{{ $t('Label') }}</Label>
            <Input id="label" v-model="form.label" type="text" class="block w-full" required />
            <InputError :message="form.errors.label" />
        </div>

        <div v-if="form.type && hasDefaultValue" class="grid gap-2">
            <Label
                for="default_value"
                :title="
                    $t('The default value is used for all items that do not have a value set. It will be the pre-set value on creating new items.')
                "
                >{{ $t('Default value') }}</Label
            >

            <Input v-if="form.type === 'text'" id="default_value" v-model="form.default_value" type="text" class="block w-full" />
            <Input v-if="form.type === 'number'" id="default_value" v-model="form.default_value" type="number" class="block w-48" />
            <Input v-if="form.type === 'color'" id="default_value" v-model="form.default_value" type="color" class="block w-48" />
            <DatePicker v-if="form.type === 'date'" id="default_value" v-model="form.default_value" clearable class="block w-full" />

            <InputError :message="form.errors.default_value" />
        </div>

        <div v-if="form.type && selectableRules.length > 0" class="grid gap-2">
            <Label
                for="rules"
                :title="
                    $t(
                        'Rules provide form input validation to ensure that the value entered is valid. For example, you can require a value to be numeric or a string with a maximum length of 255 characters.',
                    )
                "
                >{{ $t('Rules') }}</Label
            >

            <dl class="grid grid-cols-3">
                <template v-for="rule of selectedRules" :key="rule.value">
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

            <InputError :message="form.errors.rules" />
        </div>

        <!-- Display Options -->
        <div v-if="form.type === 'number'" class="grid gap-2">
            <div class="grid gap-2">
                <Label for="display">{{ $t('Display') }}</Label>
                <Select v-model="form.display_options.display">
                    <SelectTrigger class="min-w-48">
                        <SelectValue class="p-2" />
                    </SelectTrigger>
                    <SelectContent>
                        <SelectItem value="value">{{ $t('Value') }}</SelectItem>
                        <SelectItem value="progress">{{ $t('Progress') }}</SelectItem>
                        <SelectItem value="progress-with-value">{{ $t('Progress with value') }}</SelectItem>
                        <SelectItem value="ring">{{ $t('Ring') }}</SelectItem>
                        <SelectItem value="ring-with-value">{{ $t('Ring with value') }}</SelectItem>
                    </SelectContent>
                </Select>
                <InputError :message="form.errors.property_options" />
            </div>
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
