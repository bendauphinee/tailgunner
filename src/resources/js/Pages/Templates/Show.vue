<script setup>
import { defineProps } from 'vue';
import { Link } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

defineProps({
    template: Object
});

// Generic button click message function
const btnClick = (message) => {window.alert(message)}
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
                            <tr v-if="!template.fields.length" class="text-gray-500 text-center py-8">
                                <td colspan="4">No fields found.</td>
                            </tr>
                            <tr v-for="field in template.fields" :key="field.id">
                                <td>{{ field.label }}</td>
                                <td>{{ field.name }}</td>
                                <td>{{ field.type }}</td>
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
                            @click="btnClick(`Save template ${template.id}`)"
                        >Save Template Changes</button>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>