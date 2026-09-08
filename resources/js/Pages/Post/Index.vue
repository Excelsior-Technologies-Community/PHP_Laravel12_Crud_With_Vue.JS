<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, Link, router, useForm } from '@inertiajs/vue3'
import { ref, watch } from 'vue'

const props = defineProps({
    posts: {
        type: Array,
        default: () => [],
    },

    categories: {
        type: Array,
        default: () => [],
    },

    filters: {
        type: Object,
        default: () => ({
            search: '',
            category: '',
            status: '',
        }),
    },
})

const search = ref(props.filters.search || '')
const category = ref(props.filters.category || '')
const status = ref(props.filters.status || '')

let searchTimer = null

const applyFilters = () => {
    router.get(
        route('posts.index'),
        {
            search: search.value || undefined,
            category: category.value || undefined,
            status: status.value || undefined,
        },
        {
            preserveState: true,
            preserveScroll: true,
            replace: true,
        }
    )
}

watch(search, () => {
    clearTimeout(searchTimer)

    searchTimer = setTimeout(() => {
        applyFilters()
    }, 400)
})

watch([category, status], () => {
    applyFilters()
})

const clearFilters = () => {
    search.value = ''
    category.value = ''
    status.value = ''
}

const form = useForm({})

const deletePost = (id) => {
    if (confirm('Are you sure you want to delete this post?')) {
        form.delete(route('posts.destroy', id), {
            preserveScroll: true,
        })
    }
}

const statusClass = (postStatus) => {
    switch (postStatus) {
        case 'published':
            return 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400'

        case 'draft':
            return 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400'

        case 'archived':
            return 'bg-gray-200 text-gray-700 dark:bg-gray-700 dark:text-gray-300'

        default:
            return 'bg-gray-100 text-gray-700'
    }
}
</script>

<template>
    <Head title="Manage Posts" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">
                        Manage Posts
                    </h2>

                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                        Search, filter and manage your posts
                    </p>
                </div>

                <div class="flex gap-2">
                    <Link
                        :href="route('posts.statistics')"
                        class="px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg font-medium"
                    >
                        📊 Statistics
                    </Link>

                    <Link
                        :href="route('posts.create')"
                        class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium"
                    >
                        + Create Post
                    </Link>
                </div>
            </div>
        </template>

        <div class="py-10">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

                <!-- Filters -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-5 mb-6">

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

                        <!-- Search -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                🔎 Search Posts
                            </label>

                            <div class="relative">
                                <input
                                    v-model="search"
                                    type="text"
                                    placeholder="Search title or body..."
                                    class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white pl-4 pr-10"
                                />

                                <span class="absolute right-3 top-2.5 text-gray-400">
                                    🔍
                                </span>
                            </div>
                        </div>

                        <!-- Category -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                🏷️ Category
                            </label>

                            <select
                                v-model="category"
                                class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white"
                            >
                                <option value="">All Categories</option>

                                <option
                                    v-for="item in categories"
                                    :key="item.id"
                                    :value="item.id"
                                >
                                    {{ item.name }}
                                </option>
                            </select>
                        </div>

                        <!-- Status -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                📌 Status
                            </label>

                            <select
                                v-model="status"
                                class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white"
                            >
                                <option value="">All Statuses</option>
                                <option value="published">Published</option>
                                <option value="draft">Draft</option>
                                <option value="archived">Archived</option>
                            </select>
                        </div>

                    </div>

                    <div class="mt-4 flex justify-between items-center">
                        <p class="text-sm text-gray-500 dark:text-gray-400">
                            Showing {{ posts.length }} result<span v-if="posts.length !== 1">s</span>
                        </p>

                        <button
                            @click="clearFilters"
                            class="text-sm text-blue-600 hover:text-blue-800 dark:text-blue-400"
                        >
                            Clear Filters
                        </button>
                    </div>
                </div>

                <!-- Table -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm overflow-hidden">

                    <div class="overflow-x-auto">
                        <table class="w-full">

                            <thead>
                                <tr class="bg-gray-50 dark:bg-gray-700 border-b dark:border-gray-600">

                                    <th class="text-left px-6 py-4 text-sm font-semibold">
                                        ID
                                    </th>

                                    <th class="text-left px-6 py-4 text-sm font-semibold">
                                        Title
                                    </th>

                                    <th class="text-left px-6 py-4 text-sm font-semibold">
                                        Category
                                    </th>

                                    <th class="text-left px-6 py-4 text-sm font-semibold">
                                        Status
                                    </th>

                                    <th class="text-left px-6 py-4 text-sm font-semibold">
                                        Created
                                    </th>

                                    <th class="text-right px-6 py-4 text-sm font-semibold">
                                        Actions
                                    </th>

                                </tr>
                            </thead>

                            <tbody>

                                <tr
                                    v-for="post in posts"
                                    :key="post.id"
                                    class="border-b dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/50"
                                >

                                    <td class="px-6 py-4 text-sm">
                                        #{{ post.id }}
                                    </td>

                                    <td class="px-6 py-4">
                                        <div class="font-medium text-gray-900 dark:text-white">
                                            {{ post.title }}
                                        </div>

                                        <div class="text-sm text-gray-500 mt-1 max-w-md truncate">
                                            {{ post.body }}
                                        </div>
                                    </td>

                                    <td class="px-6 py-4">
                                        <span
                                            v-if="post.category"
                                            class="inline-flex px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400"
                                        >
                                            {{ post.category.name }}
                                        </span>

                                        <span
                                            v-else
                                            class="text-gray-400 text-sm"
                                        >
                                            No category
                                        </span>
                                    </td>

                                    <td class="px-6 py-4">
                                        <span
                                            class="inline-flex px-3 py-1 rounded-full text-xs font-semibold capitalize"
                                            :class="statusClass(post.status)"
                                        >
                                            {{ post.status }}
                                        </span>
                                    </td>

                                    <td class="px-6 py-4 text-sm text-gray-500">
                                        {{ new Date(post.created_at).toLocaleDateString() }}
                                    </td>

                                    <td class="px-6 py-4 text-right whitespace-nowrap">

                                        <Link
                                            :href="route('posts.edit', post.id)"
                                            class="inline-block px-3 py-1.5 bg-blue-500 hover:bg-blue-600 text-white rounded-lg text-sm mr-2"
                                        >
                                            Edit
                                        </Link>

                                        <button
                                            @click="deletePost(post.id)"
                                            :disabled="form.processing"
                                            class="px-3 py-1.5 bg-red-500 hover:bg-red-600 text-white rounded-lg text-sm disabled:opacity-50"
                                        >
                                            Delete
                                        </button>

                                    </td>

                                </tr>

                                <!-- Empty -->
                                <tr v-if="posts.length === 0">
                                    <td
                                        colspan="6"
                                        class="px-6 py-16 text-center"
                                    >
                                        <div class="text-5xl mb-4">
                                            🔍
                                        </div>

                                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                            No posts found
                                        </h3>

                                        <p class="text-gray-500 mt-1">
                                            Try changing your search or filters.
                                        </p>
                                    </td>
                                </tr>

                            </tbody>

                        </table>
                    </div>

                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>