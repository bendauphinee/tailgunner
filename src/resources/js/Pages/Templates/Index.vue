<script setup>
import { ref, watch } from 'vue';
import { usePage } from '@inertiajs/vue3';

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

/*
 * Set up the current page in a way we can access,
 * and grab the templates so we can modify them when needed
 */
const page = usePage();
const templates = ref(page.props.templates);

// Set some variables for the add template form
const showDialog = ref(false);
const form = ref({
    title: '',
    errors: null
});

// Save the template
const createTemplate = () => {
    axios.post('/templates', { title: form.value.title })
        .then(response => {
            // Clean up the form
            showDialog.value = false;
            form.value.title = '';

            // Add the new template to the list
            templates.value.unshift(response.data.template);

            // Trigger our flash message
            flash.value = {
                message: response.data.message,
                data: response.data.template
            };
        })
        .catch(error => {
            form.value.errors = error.response.data.errors;
        });
};

// Check for and handle the flash message
const flash = ref(null);
const dismissFlash = () => {flash.value = null;};

watch(
    () => page.props.flash?.success,
    (newFlash) => {
        if (newFlash) {
            flash.value = newFlash;
        }
    },
    { immediate: true }
);
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
                    <button class="cancel_button" @click="showDialog = false">Cancel</button>
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
                            <button
                                class="edit"
                                @click="btnClick(`Edit Template ${flash.data.id}`)"
                            >Edit Template</button>
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
                                <td><a :href="`/templates/${template.id}`" class="text_link">{{ template.title }}</a></td>
                                <td>{{ template.description }}</td>
                                <td class="text-nowrap" :title="formatDate(template.created_at)">{{ relDate(template.created_at) }}</td>
                                <td class="text-nowrap" :title="formatDate(template.last_used)">{{ relDate(template.last_used) }}</td>
                                <td class="text-nowrap">
                                    {{ template.records }} Entries<br>
                                    <button @click="btnClick(`Add Record To Template ${template.id}`)">
                                        <font-awesome-icon :icon="['fas', 'plus']" />
                                        Add Record
                                    </button><br>
                                    <button @click="btnClick(`View Records For Template ${template.id}`)">
                                        <font-awesome-icon :icon="['fas', 'eye']" />
                                        View Records
                                    </button>
                                </td>
                                <td>
                                    <button @click="btnClick(`Delete Template ${template.id}`)">
                                        <font-awesome-icon :icon="['far', 'trash-can']" />
                                        Delete
                                    </button>
                                    <button @click="btnClick(`Clone Template ${template.id}`)">
                                        <font-awesome-icon :icon="['far', 'clone']" />
                                        Clone
                                    </button>
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
