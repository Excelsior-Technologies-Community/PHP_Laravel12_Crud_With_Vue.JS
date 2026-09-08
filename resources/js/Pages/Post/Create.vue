<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, useForm, Link } from '@inertiajs/vue3'

defineProps({
    categories: {
        type: Array,
        default: () => [],
    },
})

const form = useForm({
    title: '',
    body: '',
    category_id: '',
    status: 'published',
})

const submit = () => {
    form.post(route('posts.store'))
}
</script>

<template>
    <Head title="Create Post" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">
                        Create Post
                    </h2>
                    <p class="text-sm text-gray-500 mt-1">
                        Create a new content post
                    </p>
                </div>
            </div>
        </template>

        <div class="py-10">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

                <div class="bg-white dark:bg-gray-800 shadow-sm rounded-xl">
                    <div class="p-6">

                        <div class="flex justify-between items-center mb-6">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                Post Details
                            </h3>

                            <Link
                                :href="route('posts.index')"
                                class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg"
                            >
                                Back
                            </Link>
                        </div>

                        <form @submit.prevent="submit" class="space-y-6">

                            <!-- Title -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Title
                                </label>

                                <input
                                    v-model="form.title"
                                    type="text"
                                    placeholder="Enter post title"
                                    class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white"
                                />

                                <p
                                    v-if="form.errors.title"
                                    class="text-red-500 text-sm mt-1"
                                >
                                    {{ form.errors.title }}
                                </p>
                            </div>

                            <!-- Category -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Category
                                </label>

                                <select
                                    v-model="form.category_id"
                                    class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white"
                                >
                                    <option value="">Select Category</option>

                                    <option
                                        v-for="category in categories"
                                        :key="category.id"
                                        :value="category.id"
                                    >
                                        {{ category.name }}
                                    </option>
                                </select>

                                <p
                                    v-if="form.errors.category_id"
                                    class="text-red-500 text-sm mt-1"
                                >
                                    {{ form.errors.category_id }}
                                </p>
                            </div>

                            <!-- Status -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Status
                                </label>

                                <select
                                    v-model="form.status"
                                    class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white"
                                >
                                    <option value="published">Published</option>
                                    <option value="draft">Draft</option>
                                    <option value="archived">Archived</option>
                                </select>

                                <p
                                    v-if="form.errors.status"
                                    class="text-red-500 text-sm mt-1"
                                >
                                    {{ form.errors.status }}
                                </p>
                            </div>

                            <!-- Body -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Body
                                </label>

                                <textarea
                                    v-model="form.body"
                                    rows="7"
                                    placeholder="Write your post content..."
                                    class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white"
                                ></textarea>

                                <p
                                    v-if="form.errors.body"
                                    class="text-red-500 text-sm mt-1"
                                >
                                    {{ form.errors.body }}
                                </p>
                            </div>

                            <!-- Submit -->
                            <div class="flex justify-end">
                                <button
                                    type="submit"
                                    :disabled="form.processing"
                                    class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-lg disabled:opacity-50"
                                >
                                    {{ form.processing ? 'Saving...' : 'Save Post' }}
                                </button>
                            </div>

                        </form>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>