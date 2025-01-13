<script setup>
import { ref, watch } from 'vue';
import { router, usePage } from '@inertiajs/vue3';

import AppLayout from '@/Layouts/AppLayout.vue';

import dayjs from 'dayjs';
import relativeTime from 'dayjs/plugin/relativeTime';
import advancedFormat from 'dayjs/plugin/advancedFormat';

dayjs.extend(relativeTime);
dayjs.extend(advancedFormat);

defineProps({
    templates: Object
});

const relDate = (date) => dayjs(date).fromNow();
const formatDate = (date) => dayjs(date).format('YYYY-MM-DD HH:mm:ss Z');

// Generic button click message function
const btnClick = (message) => {window.alert(message)}

// Handle the add template form
const showDialog = ref(false);
const form = ref({
    title: '',
    errors: null
});

// Save the template
const createTemplate = async () => {
    router.post('/templates',
        { title: form.value.title },
        {
            onSuccess: () => {
                showDialog.value = false;
                form.value.title = '';
            },
            onError: (errors) => {
                form.value.errors = errors;
            }
        });
}

// Check for and handle the flash message
const page = usePage();
const flash = ref(null);

watch(
    () => page.props.flash?.success,
    (newFlash) => {
        if (newFlash) {
            flash.value = newFlash;
        }
    },
    { immediate: true }
);

const dismissFlash = () => {
    flash.value = null;
};
</script>

<template>
    <div v-if="showDialog" class="dialog-overlay">
        <div class="dialog">
            <h3 class="title">Create New Template</h3>
            <form @submit.prevent="createTemplate">
                <input
                    v-model="form.title"
                    type="text"
                    placeholder="Template Name"
                    id="name"
                    class="w-full p-2 border rounded mb-4"
                    :class="{ 'border-red-500': form.errors?.title }"
                />
                <p v-if="form.errors?.title" class="text-red-500 text-sm mb-2">
                    {{ form.errors.title }}
                </p>
                <div class="flex justify-end gap-2">
                    <button @click="showDialog = false" class="cancel_button">Cancel</button>
                    <button
                        class="add_button"
                        type="submit"
                        :disabled="form.processing"
                    >Save</button>
                </div>
            </form>
        </div>
    </div>

    <AppLayout title="Templates">
        <template #header>
            <div class="template_header">
                <h2>
                    Templates
                </h2>
                <button class="add_button" @click="showDialog = true">
                    Add New Template
                </button>
            </div>
        </template>

        <div class="py-1">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-x-auto shadow-xl sm:rounded-lg">
                    <div v-if="flash" class="ilf_success">
                        <span class="block sm:inline">
                            Successfully created new template "{{ flash.data.title }}"
                            <button class="edit" @click="btnClick(`Edit Template ${flash.data.id}`)">Edit Template</button>
                        </span>
                        <button @click="dismissFlash" class="close">
                            <span class="text-2xl">&times;</span>
                        </button>
                    </div>

                    <table v-if="templates.length" class="style01">
                        <thead>
                            <tr>
                                <th>Template Name</th>
                                <th>Description</th>
                                <th>Created</th>
                                <th>Last Used</th>
                                <th>Records</th>
                                <th>Template</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="template in templates" :key="template.id">
                                <td>{{ template.title }}</td>
                                <td>{{ template.description }}</td>
                                <td class="text-nowrap" :title="formatDate(template.created_at)">{{ relDate(template.created_at) }}</td>
                                <td class="text-nowrap" :title="formatDate(template.last_used)">{{ relDate(template.last_used) }}</td>
                                <td class="text-nowrap">
                                    {{ template.records }} Entries<br>
                                    <button @click="btnClick(`Add Record To Template ${template.id}`)">
                                        Add Record
                                    </button><br>
                                    <button @click="btnClick(`View Records For Template ${template.id}`)">
                                        View Records
                                    </button>
                                </td>
                                <td>
                                    <button @click="btnClick(`Edit Template ${template.id}`)">Edit</button>
                                    <button @click="btnClick(`Delete Template ${template.id}`)">Delete</button>
                                    <button @click="btnClick(`Clone Template ${template.id}`)">Clone</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <div v-else class="text-gray-500 text-center py-8">
                        No templates found.
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
