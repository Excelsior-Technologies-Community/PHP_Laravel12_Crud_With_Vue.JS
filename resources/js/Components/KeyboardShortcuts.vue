<script setup>
import { useKeyboardShortcuts } from '@/Composables/useKeyboardShortcuts'

const props = defineProps({
    shortcuts: {
        type: Array,
        default: () => [],
    },
})

useKeyboardShortcuts(props.shortcuts)
</script>

<template>
    <slot />
</template>
