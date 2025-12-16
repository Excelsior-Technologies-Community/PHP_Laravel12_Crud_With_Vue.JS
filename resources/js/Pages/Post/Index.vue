<script setup>
// Import authenticated layout (top navbar + auth layout)
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

// Import Inertia helpers
import { Head, Link, useForm } from '@inertiajs/vue3'

// Receive posts data from PostController
defineProps({
    posts: {
        type: Array,
        default: () => [],
    },
})

// Initialize form helper (used for delete request)
const form = useForm({})

// Delete post using Inertia DELETE request
const deletePost = (id) => {
    form.delete(`/posts/${id}`)
}
</script>

<template>
    <!-- Page title -->
    <Head title="Manage Posts" />

    <AuthenticatedLayout>
        <!-- Page header -->
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200">
                Manage Posts
            </h2>
        </template>

        <!-- Page content -->
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div
                    class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg"
                >
                    <div class="p-6 text-gray-900 dark:text-gray-100">

                        <!-- Create post button -->
                        <Link
                            href="/posts/create"
                            class="inline-block bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mb-4"
                        >
                            Create New Post
                        </Link>

                        <!-- Posts table -->
                        <table class="table-auto w-full border border-gray-300 dark:border-gray-700">
                            <thead>
                                <tr class="bg-gray-100 dark:bg-gray-700">
                                    <th class="border px-4 py-2">ID</th>
                                    <th class="border px-4 py-2">Title</th>
                                    <th class="border px-4 py-2">Body</th>
                                    <th class="border px-4 py-2 w-48">Action</th>
                                </tr>
                            </thead>

                            <tbody>
                                <!-- Loop through posts -->
                                <tr
                                    v-for="post in posts"
                                    :key="post.id"
                                    class="hover:bg-gray-50 dark:hover:bg-gray-600"
                                >
                                    <td class="border px-4 py-2">
                                        {{ post.id }}
                                    </td>

                                    <td class="border px-4 py-2">
                                        {{ post.title }}
                                    </td>

                                    <td class="border px-4 py-2">
                                        {{ post.body }}
                                    </td>

                                    <td class="border px-4 py-2">
                                        <!-- Edit post -->
                                        <Link
                                            :href="`/posts/${post.id}/edit`"
                                            class="bg-blue-500 hover:bg-blue-700 text-white px-3 py-1 rounded"
                                        >
                                            Edit
                                        </Link>

                                        <!-- Delete post -->
                                        <button
                                            @click="deletePost(post.id)"
                                            class="bg-red-500 hover:bg-red-700 text-white px-3 py-1 rounded ml-2"
                                        >
                                            Delete
                                        </button>
                                    </td>
                                </tr>

                                <!-- No data message -->
                                <tr v-if="posts.length === 0">
                                    <td colspan="4" class="text-center py-4 text-gray-500">
                                        No posts found
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
