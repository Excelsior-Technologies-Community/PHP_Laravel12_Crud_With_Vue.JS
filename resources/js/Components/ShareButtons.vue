<script setup>
import { ref } from 'vue'

const props = defineProps({
    post: {
        type: Object,
        required: true,
    },
})

const url = ref(typeof window !== 'undefined' ? window.location.origin + route('posts.show', props.post.slug || props.post.id) : '')
const title = ref(props.post.title)

const copyToClipboard = async () => {
    try {
        await navigator.clipboard.writeText(url.value)
        alert('Link copied to clipboard!')
    } catch {
        alert('Failed to copy link')
    }
}

const shareTwitter = () => {
    window.open(
        `https://twitter.com/intent/tweet?text=${encodeURIComponent(title.value)}&url=${encodeURIComponent(url.value)}`,
        '_blank'
    )
}

const shareFacebook = () => {
    window.open(
        `https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(url.value)}`,
        '_blank'
    )
}
</script>

<template>
    <div class="flex items-center gap-2">
        <button
            type="button"
            @click="copyToClipboard"
            class="rounded-lg bg-gray-100 px-3 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300"
            title="Copy link"
        >
            🔗 Copy
        </button>

        <button
            type="button"
            @click="shareTwitter"
            class="rounded-lg bg-blue-100 px-3 py-2 text-sm font-semibold text-blue-700 hover:bg-blue-200 dark:bg-blue-900/30 dark:text-blue-400"
        >
            🐦 Twitter
        </button>

        <button
            type="button"
            @click="shareFacebook"
            class="rounded-lg bg-indigo-100 px-3 py-2 text-sm font-semibold text-indigo-700 hover:bg-indigo-200 dark:bg-indigo-900/30 dark:text-indigo-400"
        >
            📘 Facebook
        </button>
    </div>
</template>
