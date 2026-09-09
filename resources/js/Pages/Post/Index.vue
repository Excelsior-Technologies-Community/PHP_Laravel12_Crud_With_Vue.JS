<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, Link, router } from '@inertiajs/vue3'
import { computed, ref, watch } from 'vue'

const props = defineProps({
    posts: {
        type: Object,
        required: true,
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
            sort: 'latest',
            per_page: 5,
            date_from: '',
            date_to: '',
            trash: false,
        }),
    },
})

/*
|--------------------------------------------------------------------------
| Filters
|--------------------------------------------------------------------------
*/

const search = ref(props.filters.search || '')
const category = ref(props.filters.category || '')
const status = ref(props.filters.status || '')
const sort = ref(props.filters.sort || 'latest')
const perPage = ref(Number(props.filters.per_page || 5))
const dateFrom = ref(props.filters.date_from || '')
const dateTo = ref(props.filters.date_to || '')
const showTrash = ref(Boolean(props.filters.trash))

/*
|--------------------------------------------------------------------------
| Selected Posts
|--------------------------------------------------------------------------
*/

const selectedPosts = ref([])

/*
|--------------------------------------------------------------------------
| Apply Filters
|--------------------------------------------------------------------------
*/

const applyFilters = () => {
    router.get(
        route('posts.index'),
        {
            search: search.value || undefined,
            category: category.value || undefined,
            status: status.value || undefined,
            sort: sort.value || undefined,
            per_page: perPage.value || undefined,
            date_from: dateFrom.value || undefined,
            date_to: dateTo.value || undefined,
            trash: showTrash.value ? 1 : undefined,
        },
        {
            preserveState: true,
            preserveScroll: true,
            replace: true,
        }
    )
}

/*
|--------------------------------------------------------------------------
| Search
|--------------------------------------------------------------------------
*/

let searchTimeout = null

watch(search, () => {
    clearTimeout(searchTimeout)

    searchTimeout = setTimeout(() => {
        applyFilters()
    }, 400)
})

/*
|--------------------------------------------------------------------------
| Other Filters
|--------------------------------------------------------------------------
*/

watch(
    [
        category,
        status,
        sort,
        perPage,
        dateFrom,
        dateTo,
        showTrash,
    ],
    () => {
        applyFilters()
    }
)

/*
|--------------------------------------------------------------------------
| Clear Filters
|--------------------------------------------------------------------------
*/

const clearFilters = () => {
    search.value = ''
    category.value = ''
    status.value = ''
    sort.value = 'latest'
    perPage.value = 5
    dateFrom.value = ''
    dateTo.value = ''
    showTrash.value = false

    applyFilters()
}

/*
|--------------------------------------------------------------------------
| Select All
|--------------------------------------------------------------------------
*/

const allSelected = computed(() => {
    return (
        props.posts.data.length > 0 &&
        selectedPosts.value.length === props.posts.data.length
    )
})

const toggleSelectAll = () => {
    if (allSelected.value) {
        selectedPosts.value = []
    } else {
        selectedPosts.value = props.posts.data.map((post) => post.id)
    }
}

/*
|--------------------------------------------------------------------------
| Delete Single Post
|--------------------------------------------------------------------------
*/

const deletePost = (id) => {
    if (!confirm('Are you sure you want to move this post to trash?')) {
        return
    }

    router.delete(route('posts.destroy', id), {
        preserveScroll: true,
        onSuccess: () => {
            selectedPosts.value = []
        },
    })
}

/*
|--------------------------------------------------------------------------
| Restore Post
|--------------------------------------------------------------------------
*/

const restorePost = (id) => {
    if (!confirm('Are you sure you want to restore this post?')) {
        return
    }

    router.post(
        route('posts.restore', id),
        {},
        {
            preserveScroll: true,
        }
    )
}

/*
|--------------------------------------------------------------------------
| Bulk Delete
|--------------------------------------------------------------------------
*/

const bulkDelete = () => {
    if (selectedPosts.value.length === 0) {
        alert('Please select at least one post.')
        return
    }

    if (
        !confirm(
            `Are you sure you want to move ${selectedPosts.value.length} post(s) to trash?`
        )
    ) {
        return
    }

    router.post(
        route('posts.bulk-delete'),
        {
            ids: selectedPosts.value,
        },
        {
            preserveScroll: true,
            onSuccess: () => {
                selectedPosts.value = []
            },
        }
    )
}

/*
|--------------------------------------------------------------------------
| Export CSV
|--------------------------------------------------------------------------
*/

const exportCsv = () => {
    const params = new URLSearchParams()

    if (search.value) {
        params.append('search', search.value)
    }

    if (category.value) {
        params.append('category', category.value)
    }

    if (status.value) {
        params.append('status', status.value)
    }

    if (sort.value) {
        params.append('sort', sort.value)
    }

    if (dateFrom.value) {
        params.append('date_from', dateFrom.value)
    }

    if (dateTo.value) {
        params.append('date_to', dateTo.value)
    }

    window.location.href =
        route('posts.export') + '?' + params.toString()
}

/*
|--------------------------------------------------------------------------
| Pagination
|--------------------------------------------------------------------------
*/

const pages = computed(() => {
    const totalPages = Number(props.posts.last_page || 1)

    return Array.from(
        { length: totalPages },
        (_, index) => index + 1
    )
})

const goToPage = (page) => {
    if (
        page < 1 ||
        page > props.posts.last_page ||
        page === props.posts.current_page
    ) {
        return
    }

    router.get(
        route('posts.index'),
        {
            page: page,
            search: search.value || undefined,
            category: category.value || undefined,
            status: status.value || undefined,
            sort: sort.value || undefined,
            per_page: perPage.value || undefined,
            date_from: dateFrom.value || undefined,
            date_to: dateTo.value || undefined,
            trash: showTrash.value ? 1 : undefined,
        },
        {
            preserveState: true,
            preserveScroll: true,
            replace: true,
        }
    )
}

/*
|--------------------------------------------------------------------------
| Status Class
|--------------------------------------------------------------------------
*/

const statusClass = (postStatus) => {
    switch (postStatus) {
        case 'published':
            return 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400'

        case 'draft':
            return 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400'

        case 'archived':
            return 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300'

        default:
            return 'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300'
    }
}
</script>

<template>
    <Head title="Posts" />

    <AuthenticatedLayout>
        <template #header>
            <div
                class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"
            >
                <div>
                    <h2
                        class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200"
                    >
                        Posts
                    </h2>

                    <p
                        class="mt-1 text-sm text-gray-500 dark:text-gray-400"
                    >
                        Manage your posts
                    </p>
                </div>

                <div class="flex flex-wrap gap-2">
                    <Link
                        :href="route('posts.create')"
                        class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-blue-700"
                    >
                        + Create Post
                    </Link>

                    <button
                        type="button"
                        @click="exportCsv"
                        class="rounded-lg bg-green-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-green-700"
                    >
                        Export CSV
                    </button>
                </div>
            </div>
        </template>

        <div class="py-8">
            <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">

                <!-- Filters -->
                <div
                    class="rounded-xl bg-white p-5 shadow-sm dark:bg-gray-800"
                >
                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-4">

                        <!-- Search -->
                        <div>
                            <label
                                class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300"
                            >
                                Search
                            </label>

                            <input
                                v-model="search"
                                type="text"
                                placeholder="Search title or body..."
                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                            />
                        </div>

                        <!-- Category -->
                        <div>
                            <label
                                class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300"
                            >
                                Category
                            </label>

                            <select
                                v-model="category"
                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                            >
                                <option value="">
                                    All Categories
                                </option>

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
                            <label
                                class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300"
                            >
                                Status
                            </label>

                            <select
                                v-model="status"
                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                            >
                                <option value="">
                                    All Status
                                </option>

                                <option value="published">
                                    Published
                                </option>

                                <option value="draft">
                                    Draft
                                </option>

                                <option value="archived">
                                    Archived
                                </option>
                            </select>
                        </div>

                        <!-- Sorting -->
                        <div>
                            <label
                                class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300"
                            >
                                Sort
                            </label>

                            <select
                                v-model="sort"
                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                            >
                                <option value="latest">
                                    Newest First
                                </option>

                                <option value="oldest">
                                    Oldest First
                                </option>

                                <option value="title_asc">
                                    Title A-Z
                                </option>

                                <option value="title_desc">
                                    Title Z-A
                                </option>
                            </select>
                        </div>

                        <!-- Per Page -->
                        <div>
                            <label
                                class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300"
                            >
                                Per Page
                            </label>

                            <select
                                v-model="perPage"
                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                            >
                                <option :value="5">5</option>
                                <option :value="10">10</option>
                                <option :value="25">25</option>
                                <option :value="50">50</option>
                            </select>
                        </div>

                        <!-- Date From -->
                        <div>
                            <label
                                class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300"
                            >
                                Date From
                            </label>

                            <input
                                v-model="dateFrom"
                                type="date"
                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                            />
                        </div>

                        <!-- Date To -->
                        <div>
                            <label
                                class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300"
                            >
                                Date To
                            </label>

                            <input
                                v-model="dateTo"
                                type="date"
                                class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
                            />
                        </div>

                        <!-- Trash -->
                        <div class="flex items-end">
                            <label
                                class="flex cursor-pointer items-center gap-2"
                            >
                                <input
                                    v-model="showTrash"
                                    type="checkbox"
                                    class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500"
                                />

                                <span
                                    class="text-sm font-medium text-gray-700 dark:text-gray-300"
                                >
                                    Show Trash
                                </span>
                            </label>
                        </div>
                    </div>

                    <!-- Clear Filters -->
                    <div class="mt-4 flex justify-end">
                        <button
                            type="button"
                            @click="clearFilters"
                            class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600"
                        >
                            Clear Filters
                        </button>
                    </div>
                </div>

                <!-- Bulk Actions -->
                <div
                    v-if="selectedPosts.length > 0 && !showTrash"
                    class="flex flex-col gap-3 rounded-xl bg-blue-50 p-4 dark:bg-blue-900/20 sm:flex-row sm:items-center sm:justify-between"
                >
                    <div
                        class="text-sm font-medium text-blue-700 dark:text-blue-300"
                    >
                        {{ selectedPosts.length }} post(s) selected
                    </div>

                    <button
                        type="button"
                        @click="bulkDelete"
                        class="rounded-lg bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-700"
                    >
                        Move Selected to Trash
                    </button>
                </div>

                <!-- Posts Table -->
                <div
                    class="overflow-hidden rounded-xl bg-white shadow-sm dark:bg-gray-800"
                >
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <!-- Select -->
                                    <th
                                        v-if="!showTrash"
                                        class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-300"
                                    >
                                        <input
                                            type="checkbox"
                                            :checked="allSelected"
                                            @change="toggleSelectAll"
                                            class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                        />
                                    </th>

                                    <th
                                        class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-300"
                                    >
                                        ID
                                    </th>

                                    <th
                                        class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-300"
                                    >
                                        Title
                                    </th>

                                    <th
                                        class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-300"
                                    >
                                        Category
                                    </th>

                                    <th
                                        class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-300"
                                    >
                                        Status
                                    </th>

                                    <th
                                        class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-300"
                                    >
                                        Created
                                    </th>

                                    <th
                                        class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-300"
                                    >
                                        Actions
                                    </th>
                                </tr>
                            </thead>

                            <tbody
                                class="divide-y divide-gray-200 dark:divide-gray-700"
                            >
                                <tr
                                    v-for="post in posts.data"
                                    :key="post.id"
                                    class="hover:bg-gray-50 dark:hover:bg-gray-700/50"
                                >
                                    <!-- Checkbox -->
                                    <td
                                        v-if="!showTrash"
                                        class="px-6 py-4"
                                    >
                                        <input
                                            v-model="selectedPosts"
                                            :value="post.id"
                                            type="checkbox"
                                            class="rounded border-gray-300 text-blue-600 focus:ring-blue-500"
                                        />
                                    </td>

                                    <!-- ID -->
                                    <td
                                        class="whitespace-nowrap px-6 py-4 text-sm font-medium text-gray-900 dark:text-gray-100"
                                    >
                                        #{{ post.id }}
                                    </td>

                                    <!-- Title -->
                                    <td class="px-6 py-4">
                                        <div
                                            class="max-w-xs truncate text-sm font-semibold text-gray-900 dark:text-gray-100"
                                        >
                                            {{ post.title }}
                                        </div>

                                        <div
                                            class="mt-1 max-w-xs truncate text-xs text-gray-500 dark:text-gray-400"
                                        >
                                            {{ post.body }}
                                        </div>
                                    </td>

                                    <!-- Category -->
                                    <td
                                        class="whitespace-nowrap px-6 py-4 text-sm text-gray-600 dark:text-gray-300"
                                    >
                                        {{ post.category?.name || 'No category' }}
                                    </td>

                                    <!-- Status -->
                                    <td class="whitespace-nowrap px-6 py-4">
                                        <span
                                            class="rounded-full px-2.5 py-1 text-xs font-semibold"
                                            :class="statusClass(post.status)"
                                        >
                                            {{ post.status }}
                                        </span>
                                    </td>

                                    <!-- Created -->
                                    <td
                                        class="whitespace-nowrap px-6 py-4 text-sm text-gray-500 dark:text-gray-400"
                                    >
                                        {{
                                            new Date(
                                                post.created_at
                                            ).toLocaleDateString()
                                        }}
                                    </td>

                                    <!-- Actions -->
                                    <td
                                        class="whitespace-nowrap px-6 py-4 text-right"
                                    >
                                        <div
                                            class="flex justify-end gap-2"
                                        >
                                            <!-- Edit -->
                                            <Link
                                                v-if="!showTrash"
                                                :href="
                                                    route(
                                                        'posts.edit',
                                                        post.id
                                                    )
                                                "
                                                class="rounded-lg bg-blue-100 px-3 py-1.5 text-xs font-semibold text-blue-700 hover:bg-blue-200 dark:bg-blue-900/30 dark:text-blue-400"
                                            >
                                                Edit
                                            </Link>

                                            <!-- Delete -->
                                            <button
                                                v-if="!showTrash"
                                                type="button"
                                                @click="deletePost(post.id)"
                                                class="rounded-lg bg-red-100 px-3 py-1.5 text-xs font-semibold text-red-700 hover:bg-red-200 dark:bg-red-900/30 dark:text-red-400"
                                            >
                                                Delete
                                            </button>

                                            <!-- Restore -->
                                            <button
                                                v-if="showTrash"
                                                type="button"
                                                @click="restorePost(post.id)"
                                                class="rounded-lg bg-green-100 px-3 py-1.5 text-xs font-semibold text-green-700 hover:bg-green-200 dark:bg-green-900/30 dark:text-green-400"
                                            >
                                                Restore
                                            </button>
                                        </div>
                                    </td>
                                </tr>

                                <!-- Empty -->
                                <tr v-if="posts.data.length === 0">
                                    <td
                                        :colspan="showTrash ? 6 : 7"
                                        class="px-6 py-12 text-center"
                                    >
                                        <div
                                            class="text-sm font-medium text-gray-500 dark:text-gray-400"
                                        >
                                            No posts found.
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div
                        v-if="
                            posts.data.length > 0 &&
                            posts.last_page > 1
                        "
                        class="flex flex-col gap-4 border-t p-5 dark:border-gray-700 sm:flex-row sm:items-center sm:justify-between"
                    >
                        <!-- Page Information -->
                        <div
                            class="text-sm text-gray-500 dark:text-gray-400"
                        >
                            Showing
                            <strong>{{ posts.from }}</strong>
                            -
                            <strong>{{ posts.to }}</strong>
                            of
                            <strong>{{ posts.total }}</strong>

                            <span class="mx-1">|</span>

                            Page
                            <strong>{{ posts.current_page }}</strong>
                            of
                            <strong>{{ posts.last_page }}</strong>
                        </div>

                        <!-- Numeric Pagination -->
                        <div
                            class="flex flex-wrap items-center gap-2"
                        >
                            <button
                                v-for="page in pages"
                                :key="page"
                                type="button"
                                @click="goToPage(page)"
                                class="flex h-10 w-10 items-center justify-center rounded-lg border text-sm font-semibold transition"
                                :class="
                                    page === posts.current_page
                                        ? 'border-blue-600 bg-blue-600 text-white'
                                        : 'border-gray-300 bg-white text-gray-700 hover:bg-gray-100 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700'
                                "
                            >
                                {{ page }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>