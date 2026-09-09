<script setup>
import { ref, watch } from 'vue'
import axios from 'axios'

const props = defineProps({
    postId: {
        type: Number,
        required: true,
    },
    liked: {
        type: Boolean,
        default: false,
    },
    likesCount: {
        type: Number,
        default: 0,
    },
})

const isLiked = ref(props.liked)
const count = ref(props.likesCount)

watch(() => props.liked, (val) => {
    isLiked.value = val
})

watch(() => props.likesCount, (val) => {
    count.value = val
})

const toggle = async () => {
    try {
        const response = await axios.post(route('posts.like', props.postId))
        isLiked.value = response.data.liked
        count.value = response.data.likes_count
    } catch (error) {
        console.error('Failed to toggle like:', error)
    }
}
</script>

<template>
    <button
        type="button"
        @click="toggle"
        class="flex items-center gap-2 rounded-lg px-4 py-2 text-sm font-semibold transition"
        :class="isLiked ? 'bg-red-100 text-red-700 hover:bg-red-200 dark:bg-red-900/30 dark:text-red-400' : 'bg-gray-100 text-gray-700 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300'"
    >
        <span>{{ isLiked ? '❤️' : '🤍' }}</span>
        <span>{{ count }}</span>
    </button>
</template>
