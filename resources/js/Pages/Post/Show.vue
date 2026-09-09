<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, Link, router } from '@inertiajs/vue3'
import { ref, onMounted } from 'vue'
import axios from 'axios'
import LikeButton from '@/Components/LikeButton.vue'
import BookmarkButton from '@/Components/BookmarkButton.vue'
import ShareButtons from '@/Components/ShareButtons.vue'
import CommentSection from '@/Components/CommentSection.vue'

defineProps({
    post: {
        type: Object,
        required: true,
    },
    userLiked: {
        type: Boolean,
        default: false,
    },
    userBookmarked: {
        type: Boolean,
        default: false,
    },
    comments: {
        type: Array,
        default: () => [],
    },
})

const trackView = async () => {
    try {
        await axios.post(route('posts.view', post.slug || post.id))
    } catch {
    }
}

onMounted(() => {
    trackView()
})
</script>

<template>
    <Head :title="post.title" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-xl font-semibold text-gray-800 dark:text-gray-200">
                        {{ post.title }}
                    </h2>
                    <p class="text-sm text-gray-500 mt-1">
                        {{ post.category?.name || 'Uncategorized' }} · {{ post.status }}
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
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">

                <div class="bg-white dark:bg-gray-800 shadow-sm rounded-xl overflow-hidden">
                    <!-- Featured Image -->
                    <div
                        v-if="post.featured_image"
                        class="h-64 w-full"
                    >
                        <img
                            :src="`/storage/${post.featured_image}`"
                            :alt="post.title"
                            class="h-full w-full object-cover"
                            loading="lazy"
                        />
                    </div>

                    <div class="p-6">
                        <!-- Meta -->
                        <div class="flex flex-wrap items-center gap-3 mb-6">
                            <span
                                class="rounded-full px-3 py-1 text-xs font-semibold"
                                :class="{
                                    'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400': post.status === 'published',
                                    'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400': post.status === 'draft',
                                    'bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300': post.status === 'archived',
                                }"
                            >
                                {{ post.status }}
                            </span>

                            <span class="text-sm text-gray-500 dark:text-gray-400">
                                By {{ post.user?.name || 'Unknown' }}
                            </span>

                            <span class="text-sm text-gray-500 dark:text-gray-400">
                                {{ new Date(post.created_at).toLocaleDateString() }}
                            </span>
                        </div>

                        <!-- Excerpt -->
                        <p
                            v-if="post.excerpt"
                            class="text-lg text-gray-600 dark:text-gray-300 mb-6 italic"
                        >
                            {{ post.excerpt }}
                        </p>

                        <!-- Body -->
                        <div
                            class="prose dark:prose-invert max-w-none mb-8"
                            v-html="post.body.replace(/\n/g, '<br>')"
                        ></div>

                        <!-- Tags -->
                        <div
                            v-if="post.tags?.length"
                            class="flex flex-wrap gap-2 mb-8"
                        >
                            <Link
                                v-for="tag in post.tags"
                                :key="tag.id"
                                :href="route('posts.index', { tag: tag.id })"
                                class="rounded-full bg-blue-100 px-3 py-1 text-xs font-semibold text-blue-700 hover:bg-blue-200 dark:bg-blue-900/30 dark:text-blue-400"
                            >
                                #{{ tag.name }}
                            </Link>
                        </div>

                        <!-- Engagement -->
                        <div class="flex flex-wrap items-center gap-4 border-t pt-6 dark:border-gray-700">
                            <LikeButton
                                :post-id="post.id"
                                :liked="userLiked"
                                :likes-count="post.likes_count"
                            />

                            <BookmarkButton
                                :post-id="post.id"
                                :bookmarked="userBookmarked"
                            />

                            <ShareButtons :post="post" />

                            <span class="text-sm text-gray-500 dark:text-gray-400">
                                👁 {{ post.views_count }} views
                            </span>
                        </div>

                        <!-- Comments -->
                        <CommentSection
                            :post-id="post.id"
                            :comments="comments"
                            class="mt-8"
                        />
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>
