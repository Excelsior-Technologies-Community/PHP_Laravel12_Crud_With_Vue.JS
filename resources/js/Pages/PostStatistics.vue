<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, Link } from '@inertiajs/vue3'
import { ref, onMounted } from 'vue'
import axios from 'axios'

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

const viewsData = ref([])
const days = ref(30)

const fetchViewsAnalytics = async () => {
    try {
        const response = await axios.get(route('posts.views-analytics'), {
            params: { days: days.value },
        })
        viewsData.value = response.data
    } catch (error) {
        console.error('Failed to fetch views analytics:', error)
    }
}

onMounted(() => {
    fetchViewsAnalytics()
})
</script>

<template>
    <Head title="Post Statistics" />

    <AuthenticatedLayout>
        <!-- Header -->
        <template #header>
            <div
                class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"
            >
                <div>
                    <h2
                        class="text-xl font-semibold text-gray-800 dark:text-gray-200"
                    >
                        📊 Post Statistics
                    </h2>

                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        Overview of your content management system
                    </p>
                </div>

                <Link
                    :href="route('posts.index')"
                    class="rounded-lg bg-gray-600 px-4 py-2 font-medium text-white hover:bg-gray-700"
                >
                    ← Back to Posts
                </Link>
            </div>
        </template>

        <div class="py-10">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

                <!-- ===================================================== -->
                <!-- Statistics Cards -->
                <!-- ===================================================== -->

                <div
                    class="mb-8 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-5"
                >

                    <!-- Total -->
                    <div
                        class="rounded-xl border-l-4 border-blue-500 bg-white p-6 shadow-sm dark:bg-gray-800"
                    >
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    Total Posts
                                </p>

                                <h3
                                    class="mt-2 text-3xl font-bold text-gray-900 dark:text-white"
                                >
                                    {{ stats.total }}
                                </h3>
                            </div>

                            <div class="text-4xl">
                                📝
                            </div>
                        </div>
                    </div>

                    <!-- Published -->
                    <div
                        class="rounded-xl border-l-4 border-green-500 bg-white p-6 shadow-sm dark:bg-gray-800"
                    >
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    Published
                                </p>

                                <h3
                                    class="mt-2 text-3xl font-bold text-green-600 dark:text-green-400"
                                >
                                    {{ stats.published }}
                                </h3>
                            </div>

                            <div class="text-4xl">
                                ✅
                            </div>
                        </div>
                    </div>

                    <!-- Draft -->
                    <div
                        class="rounded-xl border-l-4 border-yellow-500 bg-white p-6 shadow-sm dark:bg-gray-800"
                    >
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    Drafts
                                </p>

                                <h3
                                    class="mt-2 text-3xl font-bold text-yellow-600 dark:text-yellow-400"
                                >
                                    {{ stats.draft }}
                                </h3>
                            </div>

                            <div class="text-4xl">
                                ✏️
                            </div>
                        </div>
                    </div>

                    <!-- Archived -->
                    <div
                        class="rounded-xl border-l-4 border-gray-500 bg-white p-6 shadow-sm dark:bg-gray-800"
                    >
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    Archived
                                </p>

                                <h3
                                    class="mt-2 text-3xl font-bold text-gray-600 dark:text-gray-300"
                                >
                                    {{ stats.archived }}
                                </h3>
                            </div>

                            <div class="text-4xl">
                                📦
                            </div>
                        </div>
                    </div>

                    <!-- Deleted -->
                    <div
                        class="rounded-xl border-l-4 border-red-500 bg-white p-6 shadow-sm dark:bg-gray-800"
                    >
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    Deleted
                                </p>

                                <h3
                                    class="mt-2 text-3xl font-bold text-red-600 dark:text-red-400"
                                >
                                    {{ stats.deleted ?? 0 }}
                                </h3>
                            </div>

                            <div class="text-4xl">
                                🗑️
                            </div>
                        </div>
                    </div>

                </div>


                <!-- ===================================================== -->
                <!-- Status Summary -->
                <!-- ===================================================== -->

                <div
                    class="mb-8 rounded-xl bg-white p-6 shadow-sm dark:bg-gray-800"
                >
                    <div class="mb-6">
                        <h3
                            class="text-lg font-semibold text-gray-900 dark:text-white"
                        >
                            📌 Status Overview
                        </h3>

                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                            Distribution of active posts by status
                        </p>
                    </div>

                    <div class="grid grid-cols-1 gap-6 md:grid-cols-3">

                        <!-- Published Rate -->
                        <div
                            class="rounded-xl bg-green-50 p-6 dark:bg-green-900/20"
                        >
                            <div
                                class="text-sm font-medium text-green-700 dark:text-green-400"
                            >
                                Published Rate
                            </div>

                            <div
                                class="mt-2 text-3xl font-bold text-green-700 dark:text-green-400"
                            >
                                {{
                                    stats.total
                                        ? Math.round(
                                              (stats.published / stats.total) *
                                                  100
                                          )
                                        : 0
                                }}%
                            </div>

                            <div
                                class="mt-3 h-2 overflow-hidden rounded-full bg-green-200 dark:bg-green-900"
                            >
                                <div
                                    class="h-2 rounded-full bg-green-500 transition-all duration-500"
                                    :style="{
                                        width: `${
                                            stats.total
                                                ? Math.min(
                                                      (stats.published /
                                                          stats.total) *
                                                          100,
                                                      100
                                                  )
                                                : 0
                                        }%`,
                                    }"
                                ></div>
                            </div>
                        </div>

                        <!-- Draft Rate -->
                        <div
                            class="rounded-xl bg-yellow-50 p-6 dark:bg-yellow-900/20"
                        >
                            <div
                                class="text-sm font-medium text-yellow-700 dark:text-yellow-400"
                            >
                                Draft Rate
                            </div>

                            <div
                                class="mt-2 text-3xl font-bold text-yellow-700 dark:text-yellow-400"
                            >
                                {{
                                    stats.total
                                        ? Math.round(
                                              (stats.draft / stats.total) * 100
                                          )
                                        : 0
                                }}%
                            </div>

                            <div
                                class="mt-3 h-2 overflow-hidden rounded-full bg-yellow-200 dark:bg-yellow-900"
                            >
                                <div
                                    class="h-2 rounded-full bg-yellow-500 transition-all duration-500"
                                    :style="{
                                        width: `${
                                            stats.total
                                                ? Math.min(
                                                      (stats.draft /
                                                          stats.total) *
                                                          100,
                                                      100
                                                  )
                                                : 0
                                        }%`,
                                    }"
                                ></div>
                            </div>
                        </div>

                        <!-- Archived Rate -->
                        <div
                            class="rounded-xl bg-gray-100 p-6 dark:bg-gray-700"
                        >
                            <div
                                class="text-sm font-medium text-gray-600 dark:text-gray-300"
                            >
                                Archived Rate
                            </div>

                            <div
                                class="mt-2 text-3xl font-bold text-gray-700 dark:text-gray-200"
                            >
                                {{
                                    stats.total
                                        ? Math.round(
                                              (stats.archived / stats.total) *
                                                  100
                                          )
                                        : 0
                                }}%
                            </div>

                            <div
                                class="mt-3 h-2 overflow-hidden rounded-full bg-gray-300 dark:bg-gray-600"
                            >
                                <div
                                    class="h-2 rounded-full bg-gray-500 transition-all duration-500"
                                    :style="{
                                        width: `${
                                            stats.total
                                                ? Math.min(
                                                      (stats.archived /
                                                          stats.total) *
                                                          100,
                                                      100
                                                  )
                                                : 0
                                        }%`,
                                    }"
                                ></div>
                            </div>
                        </div>

                    </div>
                </div>


                <!-- ===================================================== -->
                <!-- Category Statistics -->
                <!-- ===================================================== -->

                <div
                    class="rounded-xl bg-white shadow-sm dark:bg-gray-800"
                >
                    <!-- Header -->
                    <div
                        class="border-b p-6 dark:border-gray-700"
                    >
                        <div
                            class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between"
                        >
                            <div>
                                <h3
                                    class="text-lg font-semibold text-gray-900 dark:text-white"
                                >
                                    📂 Posts by Category
                                </h3>

                                <p
                                    class="mt-1 text-sm text-gray-500 dark:text-gray-400"
                                >
                                    Distribution of active posts across categories
                                </p>
                            </div>

                            <Link
                                :href="route('posts.index')"
                                class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700"
                            >
                                Manage Posts
                            </Link>
                        </div>
                    </div>

                    <!-- Category Data -->
                    <div class="p-6">

                        <div
                            v-if="categoryStatistics.length"
                            class="space-y-6"
                        >

                            <div
                                v-for="category in categoryStatistics"
                                :key="category.name"
                            >

                                <!-- Category Name + Count -->
                                <div
                                    class="mb-2 flex items-center justify-between"
                                >
                                    <span
                                        class="font-medium text-gray-700 dark:text-gray-300"
                                    >
                                        {{ category.name }}
                                    </span>

                                    <span
                                        class="rounded-full bg-blue-100 px-3 py-1 text-xs font-semibold text-blue-700 dark:bg-blue-900/30 dark:text-blue-400"
                                    >
                                        {{ category.count }}
                                        {{
                                            category.count === 1
                                                ? 'Post'
                                                : 'Posts'
                                        }}
                                    </span>
                                </div>

                                <!-- Progress -->
                                <div
                                    class="h-3 w-full overflow-hidden rounded-full bg-gray-200 dark:bg-gray-700"
                                >
                                    <div
                                        class="h-3 rounded-full bg-blue-600 transition-all duration-500"
                                        :style="{
                                            width: `${
                                                stats.total > 0
                                                    ? Math.max(
                                                          (category.count /
                                                              stats.total) *
                                                              100,
                                                          category.count > 0
                                                              ? 3
                                                              : 0
                                                      )
                                                    : 0
                                            }%`,
                                        }"
                                    ></div>
                                </div>

                                <!-- Percentage -->
                                <div
                                    class="mt-1 text-right text-xs text-gray-500 dark:text-gray-400"
                                >
                                    {{
                                        stats.total
                                            ? Math.round(
                                                  (category.count /
                                                      stats.total) *
                                                      100
                                              )
                                            : 0
                                    }}%
                                </div>

                            </div>

                        </div>

                        <!-- Empty Category -->
                        <div
                            v-else
                            class="py-12 text-center"
                        >
                            <div class="mb-4 text-5xl">
                                📂
                            </div>

                            <h3
                                class="text-lg font-semibold text-gray-900 dark:text-white"
                            >
                                No category data available
                            </h3>

                            <p
                                class="mt-1 text-sm text-gray-500 dark:text-gray-400"
                            >
                                Create posts with categories to see the
                                category statistics.
                            </p>

                            <Link
                                :href="route('posts.create')"
                                class="mt-4 inline-block rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white hover:bg-blue-700"
                            >
                                + Create Post
                            </Link>
                        </div>

                    </div>
                </div>


                <!-- ===================================================== -->
                <!-- Quick Summary -->
                <!-- ===================================================== -->

                <div class="mt-8 grid grid-cols-1 gap-6 md:grid-cols-2">

                    <!-- Active Posts -->
                    <div
                        class="rounded-xl bg-blue-50 p-6 dark:bg-blue-900/20"
                    >
                        <div class="flex items-center justify-between">
                            <div>
                                <p
                                    class="text-sm font-medium text-blue-700 dark:text-blue-400"
                                >
                                    Active Posts
                                </p>

                                <h3
                                    class="mt-2 text-3xl font-bold text-blue-700 dark:text-blue-400"
                                >
                                    {{
                                        (stats.total ?? 0)
                                    }}
                                </h3>

                                <p
                                    class="mt-2 text-sm text-blue-600 dark:text-blue-300"
                                >
                                    Posts currently available in your
                                    content management system.
                                </p>
                            </div>

                            <div class="text-5xl">
                                📚
                            </div>
                        </div>
                    </div>

                    <!-- Deleted Posts -->
                    <div
                        class="rounded-xl bg-red-50 p-6 dark:bg-red-900/20"
                    >
                        <div class="flex items-center justify-between">
                            <div>
                                <p
                                    class="text-sm font-medium text-red-700 dark:text-red-400"
                                >
                                    Posts in Trash
                                </p>

                                <h3
                                    class="mt-2 text-3xl font-bold text-red-700 dark:text-red-400"
                                >
                                    {{
                                        stats.deleted ?? 0
                                    }}
                                </h3>

                                <p
                                    class="mt-2 text-sm text-red-600 dark:text-red-300"
                                >
                                    Soft-deleted posts that can be restored.
                                </p>
                            </div>

                            <div class="text-5xl">
                                🗑️
                            </div>
                        </div>

                        <Link
                            :href="route('posts.index', { trash: 1 })"
                            class="mt-4 inline-block rounded-lg bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700"
                        >
                            View Trash
                        </Link>
                    </div>

                </div>


                <!-- ===================================================== -->
                <!-- Views Analytics Chart -->
                <!-- ===================================================== -->

                <div
                    class="rounded-xl bg-white p-6 shadow-sm dark:bg-gray-800"
                >
                    <div
                        class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between"
                    >
                        <div>
                            <h3
                                class="text-lg font-semibold text-gray-900 dark:text-white"
                            >
                                📈 Post Views Analytics
                            </h3>

                            <p
                                class="mt-1 text-sm text-gray-500 dark:text-gray-400"
                            >
                                Views over the last {{ days }} days
                            </p>
                        </div>

                        <select
                            v-model="days"
                            @change="fetchViewsAnalytics"
                            class="rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                        >
                            <option :value="7">Last 7 days</option>
                            <option :value="30">Last 30 days</option>
                            <option :value="90">Last 90 days</option>
                        </select>
                    </div>

                    <div
                        v-if="viewsData.length"
                        class="flex items-end gap-1 overflow-x-auto pb-2"
                    >
                        <div
                            v-for="item in viewsData"
                            :key="item.date"
                            class="flex flex-1 min-w-[40px] flex-col items-center gap-1"
                        >
                            <div
                                class="w-full rounded-t-lg bg-blue-500 transition-all duration-300 hover:bg-blue-600"
                                :style="{
                                    height: `${Math.max((item.views / Math.max(...viewsData.map(d => d.views), 1)) * 200, 4)}px`,
                                }"
                                :title="`${item.views} views on ${item.date}`"
                            ></div>

                            <span
                                class="text-xs text-gray-500 dark:text-gray-400"
                            >
                                {{ new Date(item.date).toLocaleDateString(undefined, { month: 'short', day: 'numeric' }) }}
                            </span>
                        </div>
                    </div>

                    <div
                        v-else
                        class="py-12 text-center"
                    >
                        <div class="mb-4 text-5xl">📊</div>

                        <h3
                            class="text-lg font-semibold text-gray-900 dark:text-white"
                        >
                            No views data available
                        </h3>

                        <p
                            class="mt-1 text-sm text-gray-500 dark:text-gray-400"
                        >
                            Views will appear here once posts are being viewed.
                        </p>
                    </div>
                </div>


                <!-- ===================================================== -->
                <!-- Footer Actions -->
                <!-- ===================================================== -->

                <div
                    class="mt-8 flex flex-wrap justify-center gap-3"
                >
                    <Link
                        :href="route('posts.index')"
                        class="rounded-lg bg-blue-600 px-5 py-2.5 font-medium text-white hover:bg-blue-700"
                    >
                        📝 Manage Posts
                    </Link>

                    <Link
                        :href="route('posts.create')"
                        class="rounded-lg bg-green-600 px-5 py-2.5 font-medium text-white hover:bg-green-700"
                    >
                        + Create New Post
                    </Link>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>

