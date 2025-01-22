<script setup>
import { computed } from 'vue';
import { useFlashMessage } from '@/Composables/useFlashMessage';

const { message, type, isVisible, hideMessage } = useFlashMessage();

const classes = computed(() => ({
    'fixed top-4 right-4 p-4 rounded shadow-lg z-50 transition-opacity duration-1000': true,
    'bg-green-500 text-white': type.value === 'success',
    'bg-red-500 text-white': type.value === 'error',
    'bg-yellow-500 text-white': type.value === 'warning',
    'opacity-0': !isVisible.value,
    'opacity-100': isVisible.value,
}));
</script>

<template>
    <div v-show="isVisible" :class="classes">
        <div class="flex items-center">
            <span class="mr-2 text-xl">{{ message }}</span>
            <button @click="hideMessage" class="text-white hover:text-gray-200 bg-transparent">
                <font-awesome-icon :icon="['fas', 'times']" />
            </button>
        </div>
    </div>
</template>