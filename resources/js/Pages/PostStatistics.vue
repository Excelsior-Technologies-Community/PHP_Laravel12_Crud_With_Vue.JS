<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, Link } from '@inertiajs/vue3'

defineProps({
    stats: {
        type: Object,
        required: true,
    },

    categoryStatistics: {
        type: Array,
        default: () => [],
    },
})
</script>

<template>
    <Head title="Post Statistics" />

    <AuthenticatedLayout>

        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">
                        📊 Post Statistics
                    </h2>

                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                        Overview of your content management system
                    </p>
                </div>

                <Link
                    :href="route('posts.index')"
                    class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg"
                >
                    ← Back to Posts
                </Link>
            </div>
        </template>

        <div class="py-10">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

                <!-- Stat Cards -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">

                    <!-- Total -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 border-l-4 border-blue-500">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-500">
                                    Total Posts
                                </p>

                                <h3 class="text-3xl font-bold text-gray-900 dark:text-white mt-2">
                                    {{ stats.total }}
                                </h3>
                            </div>

                            <div class="text-4xl">
                                📝
                            </div>
                        </div>
                    </div>

                    <!-- Published -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 border-l-4 border-green-500">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-500">
                                    Published
                                </p>

                                <h3 class="text-3xl font-bold text-green-600 mt-2">
                                    {{ stats.published }}
                                </h3>
                            </div>

                            <div class="text-4xl">
                                ✅
                            </div>
                        </div>
                    </div>

                    <!-- Draft -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 border-l-4 border-yellow-500">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-500">
                                    Drafts
                                </p>

                                <h3 class="text-3xl font-bold text-yellow-600 mt-2">
                                    {{ stats.draft }}
                                </h3>
                            </div>

                            <div class="text-4xl">
                                ✏️
                            </div>
                        </div>
                    </div>

                    <!-- Archived -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm p-6 border-l-4 border-gray-500">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-500">
                                    Archived
                                </p>

                                <h3 class="text-3xl font-bold text-gray-600 mt-2">
                                    {{ stats.archived }}
                                </h3>
                            </div>

                            <div class="text-4xl">
                                📦
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Category Statistics -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm">

                    <div class="p-6 border-b dark:border-gray-700">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                            Posts by Category
                        </h3>

                        <p class="text-sm text-gray-500 mt-1">
                            Distribution of posts across categories
                        </p>
                    </div>

                    <div class="p-6">

                        <div
                            v-if="categoryStatistics.length"
                            class="space-y-6"
                        >

                            <div
                                v-for="category in categoryStatistics"
                                :key="category.name"
                            >

                                <div class="flex justify-between mb-2">

                                    <span class="font-medium text-gray-700 dark:text-gray-300">
                                        {{ category.name }}
                                    </span>

                                    <span class="font-semibold text-gray-900 dark:text-white">
                                        {{ category.count }}
                                    </span>

                                </div>

                                <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-3">

                                    <div
                                        class="bg-blue-600 h-3 rounded-full transition-all duration-500"
                                        :style="{
                                            width: `${stats.total > 0 ? Math.max((category.count / stats.total) * 100, category.count > 0 ? 3 : 0) : 0}%`
                                        }"
                                    ></div>

                                </div>

                            </div>

                        </div>

                        <div
                            v-else
                            class="text-center py-10 text-gray-500"
                        >
                            No category data available.
                        </div>

                    </div>
                </div>

                <!-- Status Overview -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-8">

                    <div class="bg-green-50 dark:bg-green-900/20 rounded-xl p-6">
                        <div class="text-sm text-green-700 dark:text-green-400">
                            Published Rate
                        </div>

                        <div class="text-3xl font-bold text-green-700 dark:text-green-400 mt-2">
                            {{ stats.total ? Math.round((stats.published / stats.total) * 100) : 0 }}%
                        </div>
                    </div>

                    <div class="bg-yellow-50 dark:bg-yellow-900/20 rounded-xl p-6">
                        <div class="text-sm text-yellow-700 dark:text-yellow-400">
                            Draft Rate
                        </div>

                        <div class="text-3xl font-bold text-yellow-700 dark:text-yellow-400 mt-2">
                            {{ stats.total ? Math.round((stats.draft / stats.total) * 100) : 0 }}%
                        </div>
                    </div>

                    <div class="bg-gray-100 dark:bg-gray-700 rounded-xl p-6">
                        <div class="text-sm text-gray-600 dark:text-gray-300">
                            Archived Rate
                        </div>

                        <div class="text-3xl font-bold text-gray-700 dark:text-gray-200 mt-2">
                            {{ stats.total ? Math.round((stats.archived / stats.total) * 100) : 0 }}%
                        </div>
                    </div>

                </div>

            </div>
        </div>

    </AuthenticatedLayout>
</template>