<script setup>
import { ref, watch } from 'vue'
import axios from 'axios'

const props = defineProps({
    postId: {
        type: Number,
        required: true,
    },
    bookmarked: {
        type: Boolean,
        default: false,
    },
})

const isBookmarked = ref(props.bookmarked)

watch(() => props.bookmarked, (val) => {
    isBookmarked.value = val
})

const toggle = async () => {
    try {
        const response = await axios.post(route('posts.bookmark', props.postId))
        isBookmarked.value = response.data.bookmarked
    } catch (error) {
        console.error('Failed to toggle bookmark:', error)
    }
}
</script>

<template>
    <button
        type="button"
        @click="toggle"
        class="flex items-center gap-2 rounded-lg px-4 py-2 text-sm font-semibold transition"
        :class="isBookmarked ? 'bg-yellow-100 text-yellow-700 hover:bg-yellow-200 dark:bg-yellow-900/30 dark:text-yellow-400' : 'bg-gray-100 text-gray-700 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300'"
    >
        <span>{{ isBookmarked ? '🔖' : '📑' }}</span>
        <span>{{ isBookmarked ? 'Bookmarked' : 'Bookmark' }}</span>
    </button>
</template>
