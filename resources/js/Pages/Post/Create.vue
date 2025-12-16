<script setup>
// Import layout
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

// Import Inertia helpers
import { Head, useForm, Link } from '@inertiajs/vue3'

// Initialize form fields
const form = useForm({
    title: '',
    body: '',
})

// Submit form to store post
const submit = () => {
    form.post('/posts')
}
</script>

<template>
    <Head title="Create Post" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200">
                Create Post
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

                        <!-- Create form -->
                        <form @submit.prevent="submit">

                            <!-- Title field -->
                            <div class="mb-4">
                                <label class="block mb-2 font-medium">Title</label>

                                <input
                                    type="text"
                                    v-model="form.title"
                                    class="w-full rounded border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white"
                                />

                                <!-- Validation error -->
                                <div v-if="form.errors.title" class="text-red-500 text-sm mt-1">
                                    {{ form.errors.title }}
                                </div>
                            </div>

                            <!-- Body field -->
                            <div class="mb-4">
                                <label class="block mb-2 font-medium">Body</label>

                                <textarea
                                    v-model="form.body"
                                    rows="4"
                                    class="w-full rounded border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white"
                                ></textarea>

                                <div v-if="form.errors.body" class="text-red-500 text-sm mt-1">
                                    {{ form.errors.body }}
                                </div>
                            </div>

                            <!-- Submit button -->
                            <button
                                type="submit"
                                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded"
                                :disabled="form.processing"
                            >
                                Save Post
                            </button>

                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
