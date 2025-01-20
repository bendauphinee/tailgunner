<script setup>
import { defineProps, onMounted, ref, watch } from 'vue';
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    template: Object
});

const isModified = ref(false);
const originalState = ref(null);

// Generic button click message function
const btnClick = (message) => {window.alert(message)}

const fieldTypes = ['integer', 'string', 'text', 'dropdown'];

// Watch for changes in template fields and parse extended_options if needed
const parseExtendedOptions = (fields) => {
    fields.forEach(field => {
        if (field.type === 'dropdown' && field.extended_options && typeof field.extended_options === 'string') {
            try {
                field.extended_options = JSON.parse(field.extended_options);
            } catch (e) {
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
                                <th>Field Label</th>
                                <th>Field Name</th>
                                <th>Data Type</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-if="!props.template.fields.length" class="text-gray-500 text-center py-8">
                                <td colspan="4">No fields found.</td>
                            </tr>
                            <tr v-for="field in props.template.fields" :key="field.id">
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
                                        class="w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                                    />
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
                                    <div v-if="field.type === 'dropdown'" class="mt-2">
                                        <div v-for="(option, index) in field.extended_options || []" :key="index" class="flex items-center gap-2 mb-2">
                                            <input
                                                type="text"
                                                v-model="field.extended_options[index]"
                                                class="w-full shadow-sm sm:text-sm border-gray-300 rounded-md"
                                                placeholder="Option value"
                                            />
                                            <button @click="btnClick(`Reorder option ${index}`)">
                                                <font-awesome-icon :icon="['fas', 'sort']" />
                                            </button>
                                            <button @click="btnClick(`Delete option ${index}`)">
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
                                    <button @click="btnClick(`Reorder field ${field.id}`)">
                                        <font-awesome-icon :icon="['fas', 'sort']" />
                                    </button>
                                    <button @click="btnClick(`Delete field ${field.id}`)">
                                        <font-awesome-icon :icon="['far', 'trash-can']" />
                                    </button>
                                </td>
                            </tr>
                            <tr class="text-gray-500 py-8">
                                <td colspan="4">
                                    <button
                                        class="float-right add_button"
                                        @click="btnClick(`Add Template Field To ${template.id}`)"
                                    >+ Add Field</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <hr class="border border-black m-4">
                    <div class="flex justify-end space-x-4">
                        <button
                            class="cancel_button"
                            @click="btnClick(`Cancel changes to template ${template.id}`)"
                        >Cancel Changes / Return To List</button>
                        <button
                            class="add_button"
                            type="submit"
                            @click="isModified ? btnClick(`Save template ${template.id}`) : btnClick(`No changes to template`)"
                        >Save Template Changes</button>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>