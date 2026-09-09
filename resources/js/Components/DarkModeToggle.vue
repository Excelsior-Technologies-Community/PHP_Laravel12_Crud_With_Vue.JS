<script setup>
import { onMounted, ref } from 'vue'

const isDark = ref(false)

const toggle = () => {
    isDark.value = !isDark.value

    if (isDark.value) {
        document.documentElement.classList.add('dark')
        localStorage.setItem('theme', 'dark')
    } else {
        document.documentElement.classList.remove('dark')
        localStorage.setItem('theme', 'light')
    }
}

onMounted(() => {
    const saved = localStorage.getItem('theme')

    if (saved === 'dark' || (!saved && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
        isDark.value = true
        document.documentElement.classList.add('dark')
    } else {
        isDark.value = false
        document.documentElement.classList.remove('dark')
    }
})
</script>

<template>
    <button
        type="button"
        @click="toggle"
        class="rounded-lg p-2 text-gray-700 transition hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700"
        :title="isDark ? 'Switch to light mode' : 'Switch to dark mode'"
    >
        <span v-if="isDark">☀️</span>
        <span v-else>🌙</span>
    </button>
</template>
