import { ref } from 'vue';
import './useDraggable.css';

export function useDraggable(listId) {
    const currentDragIndex = ref(null);
    const dragTarget = ref(null);

    const handleDragStart = (index, event) => {
        if (!event.target.closest('.grip-handle')) return;
        currentDragIndex.value = index;
        dragTarget.value = listId;
        event.dataTransfer.effectAllowed = 'move';
    };

    const handleDragOver = (index, items, event) => {
        event.preventDefault();
        if (index !== currentDragIndex.value && dragTarget.value === listId) {
            const draggedItem = items[currentDragIndex.value];
            const remainingItems = items.filter((_, i) => i !== currentDragIndex.value);
            const reorderedItems = [
                ...remainingItems.slice(0, index),
                draggedItem,
                ...remainingItems.slice(index)
            ];
            currentDragIndex.value = index;
            return reorderedItems;
        }
        return items;
    };

    const handleDragEnd = () => {
        currentDragIndex.value = null;
        dragTarget.value = null;
    };

    const isDragging = (index) => {
        return dragTarget.value === listId && currentDragIndex.value === index;
    };

    const dragClasses = {
        handle: 'grip-handle',
        draggingItem: 'draggable-item dragging',
        draggingRow: 'draggable-row dragging'
    };

    return {
        isDragging,
        handleDragStart,
        handleDragOver,
        handleDragEnd,
        dragClasses
    };
}