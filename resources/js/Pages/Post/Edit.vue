<script setup>
// Import layout
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

// Import Inertia helpers
import { Head, useForm, Link } from '@inertiajs/vue3'

// Receive post data from controller
const props = defineProps({
    post: Object,
})

// Bind existing post data to form
const form = useForm({
    title: props.post.title,
    body: props.post.body,
})

// Submit update request
const submit = () => {
    form.put(`/posts/${props.post.id}`)
}
</script>

<template>
    <Head title="Edit Post" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200">
                Edit Post
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div
                    class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg"
                >
                    <div class="p-6 text-gray-900 dark:text-gray-100">

                        <!-- Back button -->
                        <Link
                            href="/posts"
                            class="inline-block bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded mb-4"
                        >
                            Back
                        </Link>

                        <!-- Edit form -->
                        <form @submit.prevent="submit">

                            <div class="mb-4">
                                <label class="block mb-2 font-medium">Title</label>

                                <input
                                    type="text"
                                    v-model="form.title"
                                    class="w-full rounded border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white"
                                />
                            </div>

                            <div class="mb-4">
                                <label class="block mb-2 font-medium">Body</label>

                                <textarea
                                    v-model="form.body"
                                    rows="4"
                                    class="w-full rounded border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white"
                                ></textarea>
                            </div>

                            <button
                                type="submit"
                                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded"
                            >
                                Update Post
                            </button>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
