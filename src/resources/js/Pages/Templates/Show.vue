<script setup>
import { defineProps, onMounted, ref, watch } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import { debounce } from 'lodash';
import { useFlashMessage } from '@/Composables/useFlashMessage';

import AppLayout from '@/Layouts/AppLayout.vue';
import { useDraggable } from '@/Composables/useDraggable';

// Generic button click message function
const btnClick = (message) => {window.alert(message)}

const props = defineProps({
    template: Object
});

const isModified = ref(false);
const originalState = ref(null);
const isSaving = ref(false);

const fieldTypes = ['integer', 'string', 'text', 'dropdown', 'checkbox'];

const removeOption = (field, index) => {
    field.extended_options.splice(index, 1);
};

const removeTemplateField = (index) => {
    props.template.fields.splice(index, 1);
};

// Watch for changes in template fields and parse extended_options if needed
const parseExtendedOptions = (fields) => {
    fields.forEach(field => {
        if (field.type === 'dropdown' || field.type === 'checkbox') {
            if (typeof field.extended_options === 'string') {
                try {
                    field.extended_options = JSON.parse(field.extended_options);
                } catch (e) {
                    field.extended_options = [];
                }
            } else if (!Array.isArray(field.extended_options)) {
                field.extended_options = [];
            }
        }
    });
};

// Initial parse
onMounted(() => {
    if (props.template.fields) {
        parseExtendedOptions(props.template.fields);

        // Store the initial state after parsing
        setTimeout(() => {
            originalState.value = JSON.stringify(props.template);
            isModified.value = false;
        }, 100);
    }
});

// Watch for changes in template data
watch(() => props.template, () => {
    if (originalState.value) {
        const currentState = JSON.stringify(props.template);
        isModified.value = currentState !== originalState.value;
    }
}, { deep: true });

const optionsDraggable = useDraggable('options');
const fieldsDraggable = useDraggable('fields');
const { dragClasses } = optionsDraggable;

const addTemplateField = () => {
    if (!props.template.fields) {
        props.template.fields = [];
    }

    props.template.fields.push({
        label: '',
        name: '',
        type: 'string',
        extended_options: [],
    });
};

const fieldErrors = ref([]);

const validateFieldNames = () => {
    const names = props.template.fields.map(f => f.name?.trim().toLowerCase());
    const duplicates = names.filter((name, index) => names.indexOf(name) !== index);

    fieldErrors.value = Array(props.template.fields.length).fill(null);

    if (duplicates.length > 0) {
        props.template.fields.forEach((field, index) => {
            if (duplicates.includes(field.name?.trim().toLowerCase())) {
                fieldErrors.value[index] = 'Duplicate field name';
            }
        });
        return true;
    }
    return false;
};

const debouncedValidateFieldNames = debounce(validateFieldNames, 100);

// Watch for field name changes
watch(() => props.template.fields, () => {
    debouncedValidateFieldNames();
}, { deep: true });

const hasDuplicateFieldNames = () => {
    return validateFieldNames();
};

const { flashMessage } = useFlashMessage();

const saveTemplate = () => {
    if (!isModified.value) {
        flashMessage.warning('No changes to template');
        return;
    }

    if (hasDuplicateFieldNames()) {
        flashMessage.error('Cannot save template: Duplicate field names found');
        return;
    }

    isSaving.value = true;

    // Create a deep copy to avoid modifying the display data
    const cleanTemplate = JSON.parse(JSON.stringify(props.template));

    cleanTemplate.title = cleanTemplate.title.trim();
    cleanTemplate.description = cleanTemplate.description?.trim() || null;

    cleanTemplate.fields = cleanTemplate.fields.filter(field => {
        const trimmedLabel = field.label?.trim() || '';
        const trimmedName = field.name?.trim() || '';
        const isFieldPopulated = trimmedLabel && trimmedName;

        if (isFieldPopulated) {
            field.label = trimmedLabel;
            field.name = trimmedName;

            if (field.type === 'dropdown' || field.type === 'checkbox') {
                // Clean and stringify the options
                field.extended_options = JSON.stringify(
                    field.extended_options
                        .map(opt => opt?.trim())
                        .filter(opt => opt)
                );
            }

            return true;
        }

        return false;
    });

    router.put(`/templates/${props.template.id}`, cleanTemplate, {
        onSuccess: () => {
            // Re-parse the extended_options after successful save
            parseExtendedOptions(props.template.fields);
            flashMessage.success('Template changes saved successfully');

            // Update the original state and reset the modification status
            originalState.value = JSON.stringify(props.template);
            isModified.value = false;

            // Clear loading state
            isSaving.value = false;
        },
        onError: () => {
            flashMessage.error('Failed to save template changes');
            isSaving.value = false;
        }
    });
};

const handleCancel = () => {
    if (!isModified.value || (isModified.value && confirm('You have unsaved changes. Are you sure you want to leave?'))) {
        router.visit(route('templates.index'));
    }
};

const handleFieldNameInput = (field) => {
    // Replace spaces with underscores
    field.name = field.name.replace(/\s+/g, '_');
    debouncedValidateFieldNames();
};
</script>

<template>
    <AppLayout :title="template.title">
        <template #header>
            <div class="template_header">
                <div class="w-full flex justify-between items-center">
                    <h2><Link :href="route('templates.index')" class="text_link">Template</Link>: {{ template.title }}</h2>
                    <div>
                        {{ template.records }} Entries
                        <button @click="btnClick(`Add Record To Template ${template.id}`)">
                            <font-awesome-icon :icon="['fas', 'plus']" />
                            Add Record
                        </button>
                        <button @click="btnClick(`View Records For Template ${template.id}`)">
                            <font-awesome-icon :icon="['fas', 'eye']" />
                            View Records
                        </button>
                    </div>
                </div>
            </div>
        </template>

        <div class="py-1">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-x-auto shadow-xl sm:rounded-lg p-4 border-2 border-black">
                    <div class="mb-4 flex items-center">
                        <label for="template-title" class="block text-sm font-medium text-gray-700 w-40">Template Title</label>
                        <input v-model="template.title" type="text" id="template-title" class="mt-1 block w-1/2 shadow-sm sm:text-sm border-gray-300 rounded-md" />
                    </div>
                    <div class="mb-4 flex items-left">
                        <label for="template-description" class="block text-sm font-medium text-gray-700 w-40">Template Description</label>
                        <textarea v-model="template.description" id="template-description" rows="4"
                            class="mt-1 block w-1/2 shadow-sm sm:text-sm border-gray-300 rounded-md"></textarea>
                    </div>
                    <hr class="border border-black m-4">
                    <table class="style01">
                        <thead>
                            <tr>
                                <th></th>
                                <th>Field Label</th>
                                <th>Field Name</th>
                                <th>Data Type</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-if="!props.template.fields.length" class="text-gray-500 text-center py-8">
                                <td colspan="5">No fields found.</td>
                            </tr>
                            <tr v-for="(field, index) in props.template.fields"
                                :key="index"
                                :class="{ [dragClasses.draggingRow]: fieldsDraggable.isDragging(index) }">
                                <td>
                                    <div :class="dragClasses.handle" draggable="true"
                                        @dragstart="fieldsDraggable.handleDragStart(index, $event)"
                                        @dragover.prevent="(e) => props.template.fields = fieldsDraggable.handleDragOver(index, props.template.fields, e)"
                                        @dragend="fieldsDraggable.handleDragEnd">
                                        <font-awesome-icon :icon="['fas', 'grip-vertical']" />
                                    </div>
                                </td>
                                <td>
                                    <input
                                        type="text"
                                        v-model="field.label"
                                        class="w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                                    />
                                </td>
                                <td>
                                    <input
                                        type="text"
                                        v-model="field.name"
                                        @input="handleFieldNameInput(field)"
                                        :class="{ 'border-red-500': fieldErrors[index] }"
                                        class="w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                                    />
                                    <div v-if="fieldErrors[index]" class="text-red-500 text-sm mt-1">
                                        {{ fieldErrors[index] }}
                                    </div>
                                </td>
                                <td>
                                    <select
                                        v-model="field.type"
                                        class="w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                                    >
                                        <option v-for="type in fieldTypes" :key="type" :value="type">
                                            {{ type }}
                                        </option>
                                    </select>
                                    <div v-if="field.type === 'dropdown' || field.type === 'checkbox'" class="mt-2 options-container">
                                        <div v-for="(option, index) in field.extended_options || []"
                                             :key="index"
                                             class="option-row flex items-center gap-2 mb-2 px-2"
                                             :class="{ [dragClasses.draggingItem]: optionsDraggable.isDragging(index) }">
                                            <div :class="dragClasses.handle" draggable="true"
                                                @dragstart="optionsDraggable.handleDragStart(index, $event)"
                                                @dragover.prevent="(e) => field.extended_options = optionsDraggable.handleDragOver(index, field.extended_options, e)"
                                                @dragend="optionsDraggable.handleDragEnd">
                                                <font-awesome-icon :icon="['fas', 'grip-vertical']" class="cursor-move" />
                                            </div>
                                            <input
                                                type="text"
                                                v-model="field.extended_options[index]"
                                                class="w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                                                placeholder="Option value"
                                            />
                                            <button @click="removeOption(field, index)">
                                                <font-awesome-icon :icon="['far', 'trash-can']" />
                                            </button>
                                        </div>
                                        <button
                                            @click="field.extended_options = [...(field.extended_options || []), '']"
                                            class="add_button float-right"
                                        >
                                            + Add Option
                                        </button>
                                    </div>
                                </td>
                                <td>
                                    <button @click="removeTemplateField(index)">
                                        <font-awesome-icon :icon="['far', 'trash-can']" />
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr class="text-gray-500 py-8">
                                <td colspan="5">
                                    <button
                                        class="float-right add_button"
                                        @click="addTemplateField"
                                    >+ Add Field</button>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                    <hr class="border border-black m-4">
                    <div class="flex justify-end space-x-4">
                        <button
                            class="cancel_button"
                            @click="handleCancel"
                        >Cancel Changes / Return To List</button>
                        <button
                            class="save_button"
                            type="submit"
                            @click="saveTemplate"
                            :disabled="!isModified || isSaving"
                        >
                            <span v-if="!isModified">
                                <font-awesome-icon :icon="['fas', 'circle-check']" class="text-green-400" />
                                No Changes To Save
                            </span>
                            <span v-else-if="isSaving">
                                <font-awesome-icon :icon="['fas', 'spinner']" class="animate-spin" />
                                Saving...
                            </span>
                            <span v-else>
                                <font-awesome-icon :icon="['fas', 'triangle-exclamation']" class="text-yellow-400" />
                                Save Template Changes
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<style scoped>
.option-row {
    transition: transform 0.2s ease;
    position: relative;
    cursor: default;
}
</style>
