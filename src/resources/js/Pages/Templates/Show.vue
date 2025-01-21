<script setup>
import { defineProps, onMounted, ref, watch } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    template: Object
});

const isModified = ref(false);
const originalState = ref(null);

// Generic button click message function
const btnClick = (message) => {window.alert(message)}

const fieldTypes = ['integer', 'string', 'text', 'dropdown'];

const removeOption = (field, index) => {
    field.extended_options.splice(index, 1);
};

const removeTemplateField = (index) => {
    props.template.fields.splice(index, 1);
};

// Watch for changes in template fields and parse extended_options if needed
const parseExtendedOptions = (fields) => {
    fields.forEach(field => {
        if (field.type === 'dropdown') {
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

const currentDragIndex = ref(null);
const currentFieldDragIndex = ref(null);

const handleDragStart = (index, field, event) => {
    currentDragIndex.value = index;
    event.dataTransfer.effectAllowed = 'move';
};

const handleDragOver = (index, field, event) => {
    event.preventDefault();
    if (index !== currentDragIndex.value) {
        const items = field.extended_options;
        const draggedItem = items[currentDragIndex.value];
        const remainingItems = items.filter((_, i) => i !== currentDragIndex.value);
        const reorderedItems = [
            ...remainingItems.slice(0, index),
            draggedItem,
            ...remainingItems.slice(index)
        ];
        field.extended_options = reorderedItems;
        currentDragIndex.value = index;
    }
};

const handleDragEnd = () => {
    currentDragIndex.value = null;
};

const handleFieldDragStart = (index, event) => {
    currentFieldDragIndex.value = index;
    event.dataTransfer.effectAllowed = 'move';
};

const handleFieldDragOver = (index, event) => {
    event.preventDefault();
    if (index !== currentFieldDragIndex.value) {
        const fields = props.template.fields;
        const draggedField = fields[currentFieldDragIndex.value];
        const remainingFields = fields.filter((_, i) => i !== currentFieldDragIndex.value);
        props.template.fields = [
            ...remainingFields.slice(0, index),
            draggedField,
            ...remainingFields.slice(index)
        ];
        currentFieldDragIndex.value = index;
    }
};

const handleFieldDragEnd = () => {
    currentFieldDragIndex.value = null;
};

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

// Add watch for field names
watch(() => props.template.fields, () => {
    validateFieldNames();
}, { deep: true });

const hasDuplicateFieldNames = () => {
    return validateFieldNames();
};

const saveTemplate = () => {
    if (!isModified.value) {
        btnClick('No changes to template');
        return;
    }

    if (hasDuplicateFieldNames()) {
        btnClick('Cannot save template: Duplicate field names found');
        return;
    }

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

            if (field.type === 'dropdown') {
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
            btnClick(`Template ${props.template.id} saved successfully`);
            originalState.value = JSON.stringify(props.template);
            isModified.value = false;
        },
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
    validateFieldNames();
};
</script>

<template>
    <AppLayout :title="template.title">
        <template #header>
            <div class="template_header">
                <h2>
                    <Link :href="route('templates.index')" class="text_link">Template</Link>: {{ template.title }}
                </h2>
                <p v-if="template.description" class="max-w-[70%]">{{ template.description }}</p>
                <p v-else class="max-w-[70%]">No description available.</p>
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
                                draggable="true"
                                :class="{ 'dragging': currentFieldDragIndex === index }"
                                @dragstart="handleFieldDragStart(index, $event)"
                                @dragover.prevent="handleFieldDragOver(index, $event)"
                                @dragend="handleFieldDragEnd">
                                <td>
                                    <font-awesome-icon :icon="['fas', 'grip-vertical']" class="cursor-move" />
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
                                    <div v-if="field.type === 'dropdown'" class="mt-2 options-container">
                                        <div v-for="(, index) in field.extended_options || []"
                                             :key="index"
                                             class="option-row flex items-center gap-2 mb-2 px-2"
                                             :class="{ 'dragging': currentDragIndex === index }"
                                             draggable="true"
                                             @dragstart="handleDragStart(index, field, $event)"
                                             @dragover.prevent="handleDragOver(index, field, $event)"
                                             @dragend="handleDragEnd"
                                            >
                                            <font-awesome-icon :icon="['fas', 'grip-vertical']" class="cursor-move" />
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
                            class="add_button"
                            type="submit"
                            @click="saveTemplate"
                        >Save Template Changes</button>
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

/* Create an options container to isolate drag styles */
.options-container .option-row.dragging {
    opacity: 0.5;
    background: #7dd3fc;
    border-radius: 0.375rem;
}

.option-row.shift-up {
    transform: translateY(-42px);
}

.option-row.shift-down {
    transform: translateY(42px);
}

/* Explicitly target only direct tr.dragging */
> table > tbody > tr.dragging {
    opacity: 0.5;
    background: #7dd3fc;
}

> table > tbody > tr.dragging td {
    background: transparent;
}

.border-red-500 {
    border-color: rgb(239 68 68);
}
</style>