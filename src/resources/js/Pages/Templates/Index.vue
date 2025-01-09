<script setup>
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

const btnClick = (message) => {window.alert(message)}
</script>

<template>
    <AppLayout title="Templates">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Templates
            </h2>
        </template>

        <div class="py-1">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-x-auto shadow-xl sm:rounded-lg">
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
