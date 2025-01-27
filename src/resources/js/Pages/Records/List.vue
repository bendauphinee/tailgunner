<script setup>
import { Head, Link } from '@inertiajs/vue3'
import AppLayout from '@/Layouts/AppLayout.vue'

// Generic button click message function
const btnClick = (message) => {window.alert(message)}

const props = defineProps({
    template: Object,
    records: Object,
});
</script>

<template>
  <AppLayout>
    <Head :title="template.title" />

    <template #header>
        <div class="template_header">
            <div class="w-full flex justify-between items-center">
                <h2><Link :href="route('templates.show', { template: template.id })" class="text_link">Template</Link>: {{ template.title }}</h2>
                <div>
                  <button @click="btnClick(`Add Record To Template ${template.id}`)">
                      <font-awesome-icon :icon="['fas', 'plus']" />
                      Add Record
                  </button>
                </div>
            </div>
        </div>
    </template>

    <div class="py-1">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 overflow-x-auto shadow-xl sm:rounded-lg p-4 border-2 border-black">
          <table class="style01">
              <thead>
                <tr>
                    <th
                      v-for="field in template.fields"
                      :key="field.id"
                    >
                      {{ field.label }}
                    </th>
                    <th></th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="record in records" :key="record.id">
                    <td
                      v-for="field in template.fields"
                      :key="field.id"
                    >
                      {{ record[field.name] }}
                    </td>
                    <td>
                        <button
                        >
                            Edit
                        </button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
      </div>
    </AppLayout>
</template>