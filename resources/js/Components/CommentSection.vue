<script setup>
import { ref, onMounted, watch } from 'vue'
import axios from 'axios'

const props = defineProps({
    postId: {
        type: Number,
        required: true,
    },
    comments: {
        type: Array,
        default: () => [],
    },
})

const body = ref('')
const parentId = ref(null)
const replyingTo = ref(null)
const localComments = ref([...props.comments])
const displayedCount = ref(10)
const hasMore = ref(props.comments.length > 10)

const visibleComments = ref([])

const updateVisibleComments = () => {
    visibleComments.value = localComments.value.slice(0, displayedCount.value)
    hasMore.value = displayedCount.value < localComments.value.length
}

onMounted(() => {
    updateVisibleComments()
})

watch(() => props.comments, (newComments) => {
    localComments.value = [...newComments]
    updateVisibleComments()
}, { deep: true })

const submit = async () => {
    if (!body.value.trim()) return

    try {
        const response = await axios.post(
            route('posts.comments.store', props.postId),
            {
                body: body.value,
                parent_id: parentId.value,
            }
        )

        const newComment = response.data.comment
        localComments.value.push(newComment)
        body.value = ''
        parentId.value = null
        replyingTo.value = null
        updateVisibleComments()
    } catch (error) {
        console.error('Failed to post comment:', error)
    }
}

const deleteComment = async (commentId) => {
    if (!confirm('Delete this comment?')) return

    try {
        await axios.delete(route('posts.comments.destroy', commentId))
        localComments.value = localComments.value.filter((c) => c.id !== commentId)
        updateVisibleComments()
    } catch (error) {
        console.error('Failed to delete comment:', error)
    }
}

const startReply = (comment) => {
    parentId.value = comment.id
    replyingTo.value = comment
    body.value = `@${comment.user.name} `
}

const loadMore = () => {
    displayedCount.value += 10
    updateVisibleComments()
}
</script>

<template>
    <div class="border-t pt-8 dark:border-gray-700">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
            💬 Comments ({{ localComments.length }})
        </h3>

        <!-- Comment Form -->
        <form @submit.prevent="submit" class="mb-6">
            <div
                v-if="replyingTo"
                class="mb-2 flex items-center justify-between rounded-lg bg-blue-50 p-3 dark:bg-blue-900/20"
            >
                <span class="text-sm text-blue-700 dark:text-blue-300">
                    Replying to <strong>{{ replyingTo.user.name }}</strong>
                </span>

                <button
                    type="button"
                    @click="replyingTo = null; parentId = null; body = ''"
                    class="text-xs text-blue-500 hover:text-blue-700"
                >
                    Cancel
                </button>
            </div>

            <textarea
                v-model="body"
                rows="3"
                placeholder="Write a comment..."
                class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white"
            ></textarea>

            <div class="mt-2 flex justify-end">
                <button
                    type="submit"
                    :disabled="!body.value.trim()"
                    class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-700 disabled:opacity-50"
                >
                    Post Comment
                </button>
            </div>
        </form>

        <!-- Comments List -->
        <div class="space-y-4">
            <div
                v-for="comment in visibleComments"
                :key="comment.id"
                class="rounded-lg bg-gray-50 p-4 dark:bg-gray-700/50"
            >
                <div class="flex items-start justify-between">
                    <div class="flex items-center gap-3">
                        <div
                            class="flex h-8 w-8 items-center justify-center rounded-full bg-blue-100 text-sm font-semibold text-blue-700 dark:bg-blue-900/30 dark:text-blue-400"
                        >
                            {{ comment.user.name.charAt(0).toUpperCase() }}
                        </div>

                        <div>
                            <div class="text-sm font-semibold text-gray-900 dark:text-white">
                                {{ comment.user.name }}
                            </div>

                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                {{ new Date(comment.created_at).toLocaleString() }}
                            </div>
                        </div>
                    </div>

                    <div class="flex gap-2">
                        <button
                            type="button"
                            @click="startReply(comment)"
                            class="text-xs text-blue-600 hover:text-blue-800 dark:text-blue-400"
                        >
                            Reply
                        </button>

                        <button
                            v-if="comment.user_id === $page.props.auth.user.id"
                            type="button"
                            @click="deleteComment(comment.id)"
                            class="text-xs text-red-600 hover:text-red-800 dark:text-red-400"
                        >
                            Delete
                        </button>
                    </div>
                </div>

                <p class="mt-2 text-sm text-gray-700 dark:text-gray-300">
                    {{ comment.body }}
                </p>
            </div>

            <div
                v-if="localComments.length === 0"
                class="py-8 text-center text-sm text-gray-500 dark:text-gray-400"
            >
                No comments yet. Be the first to comment!
            </div>

            <div
                v-if="hasMore"
                class="py-4 text-center"
            >
                <button
                    type="button"
                    @click="loadMore"
                    class="rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-100 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200 dark:hover:bg-gray-700"
                >
                    Load More Comments
                </button>
            </div>
        </div>
    </div>
</template>
