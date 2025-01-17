<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
</script>

<template>
    <AppLayout title="Dashboard">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                Dashboard
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                    <div class="p-6 lg:p-8 bg-white dark:bg-gray-800 dark:bg-gradient-to-bl dark:from-gray-700/50 dark:via-transparent border-b border-gray-200 dark:border-gray-700">
                        <h1 class="text-2xl font-medium text-gray-900 dark:text-white">
                            Welcome to your business planner!
                        </h1>

                        <p class="mt-6 text-gray-500 dark:text-gray-400 leading-relaxed">
                            Tailgunner gives you the tools you need to collect and organize your data, to help your business succeed!
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
