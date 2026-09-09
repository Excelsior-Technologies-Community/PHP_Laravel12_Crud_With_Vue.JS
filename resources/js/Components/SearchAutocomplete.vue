<script setup>
import { ref, watch } from 'vue'
import axios from 'axios'

const modelValue = defineModel({
    type: String,
    default: '',
})

const suggestions = ref([])
const showSuggestions = ref(false)
let timeout = null

watch(modelValue, (newVal) => {
    clearTimeout(timeout)

    if (!newVal || newVal.length < 2) {
        suggestions.value = []
        showSuggestions.value = false
        return
    }

    timeout = setTimeout(async () => {
        try {
            const response = await axios.get(route('posts.autocomplete'), {
                params: { q: newVal },
            })
            suggestions.value = response.data
            showSuggestions.value = true
        } catch {
            suggestions.value = []
        }
    }, 300)
})

const selectSuggestion = (post) => {
    modelValue.value = post.title
    showSuggestions.value = false
}

const close = () => {
    showSuggestions.value = false
}
</script>

<template>
    <div class="relative">
        <input
            :value="modelValue"
            @input="$event => modelValue = $event.target.value"
            @focus="showSuggestions = suggestions.length > 0"
            @blur="setTimeout(close, 200)"
            type="text"
            placeholder="Search..."
            class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white"
        />

        <ul
            v-if="showSuggestions && suggestions.length"
            class="absolute z-50 mt-1 max-h-60 w-full overflow-auto rounded-lg border border-gray-200 bg-white shadow-lg dark:border-gray-700 dark:bg-gray-800"
        >
            <li
                v-for="post in suggestions"
                :key="post.id"
                @mousedown="selectSuggestion(post)"
                class="cursor-pointer px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700"
            >
                {{ post.title }}
            </li>
        </ul>
    </div>
</template>
