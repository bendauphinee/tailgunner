
import { ref } from 'vue';

const message = ref('');
const type = ref('');
const isVisible = ref(false);
let timeout;

export function useFlashMessage() {
    const showMessage = (text, messageType, duration = 3000) => {
        message.value = text;
        type.value = messageType;
        isVisible.value = true;

        if (timeout) clearTimeout(timeout);
        timeout = setTimeout(() => {
            hideMessage();
        }, duration);
    };

    const hideMessage = () => {
        isVisible.value = false;
    };

    return {
        message,
        type,
        isVisible,
        hideMessage,
        flashMessage: {
            success: (text) => showMessage(text, 'success'),
            error: (text) => showMessage(text, 'error'),
            warning: (text) => showMessage(text, 'warning'),
        }
    };
}