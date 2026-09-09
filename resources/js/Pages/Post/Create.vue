<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Head, useForm, Link } from '@inertiajs/vue3'
import { ref, watch } from 'vue'
import { useKeyboardShortcuts } from '@/Composables/useKeyboardShortcuts'

defineProps({
    categories: {
        type: Array,
        default: () => [],
    },
    tags: {
        type: Array,
        default: () => [],
    },
})

const form = useForm({
    title: '',
    body: '',
    excerpt: '',
    slug: '',
    featured_image: null,
    category_id: '',
    status: 'published',
    tags: [],
})

const previewUrl = ref(null)

watch(
    () => form.title,
    (newTitle) => {
        if (!form.slug) {
            form.slug = newTitle
                .toLowerCase()
                .replace(/[^a-z0-9]+/g, '-')
                .replace(/^-+|-+$/g, '')
        }
    }
)

const handleFileChange = (event) => {
    const file = event.target.files[0]
    if (file) {
        form.featured_image = file
        previewUrl.value = URL.createObjectURL(file)
    }
}

const submit = () => {
    form.post(route('posts.store'), {
        forceFormData: true,
    })
}

useKeyboardShortcuts([
    { key: 'n', ctrl: true, action: () => { } },
    { key: '/', action: () => { } },
])
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

                            <!-- Slug -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Slug
                                </label>

                                <input
                                    v-model="form.slug"
                                    type="text"
                                    placeholder="post-slug"
                                    class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white"
                                />

                                <p
                                    v-if="form.errors.slug"
                                    class="text-red-500 text-sm mt-1"
                                >
                                    {{ form.errors.slug }}
                                </p>
                            </div>

                            <!-- Excerpt -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Excerpt
                                </label>

                                <textarea
                                    v-model="form.excerpt"
                                    rows="3"
                                    placeholder="Short description..."
                                    class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white"
                                ></textarea>

                                <p
                                    v-if="form.errors.excerpt"
                                    class="text-red-500 text-sm mt-1"
                                >
                                    {{ form.errors.excerpt }}
                                </p>
                            </div>

                            <!-- Featured Image -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Featured Image
                                </label>

                                <input
                                    type="file"
                                    accept="image/*"
                                    @change="handleFileChange"
                                    class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white"
                                />

                                <div
                                    v-if="previewUrl"
                                    class="mt-4"
                                >
                                    <img
                                        :src="previewUrl"
                                        alt="Preview"
                                        class="h-48 w-full rounded-lg object-cover"
                                    />
                                </div>

                                <p
                                    v-if="form.errors.featured_image"
                                    class="text-red-500 text-sm mt-1"
                                >
                                    {{ form.errors.featured_image }}
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

                            <!-- Tags -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Tags
                                </label>

                                <select
                                    v-model="form.tags"
                                    multiple
                                    class="w-full rounded-lg border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-white"
                                >
                                    <option
                                        v-for="tag in tags"
                                        :key="tag.id"
                                        :value="tag.id"
                                    >
                                        {{ tag.name }}
                                    </option>
                                </select>

                                <p class="text-xs text-gray-500 mt-1">
                                    Hold Ctrl (Windows) or Cmd (Mac) to select multiple.
                                </p>

                                <p
                                    v-if="form.errors.tags"
                                    class="text-red-500 text-sm mt-1"
                                >
                                    {{ form.errors.tags }}
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
