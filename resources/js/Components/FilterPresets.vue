<script setup>
import { ref, onMounted } from 'vue'
import axios from 'axios'

const emit = defineEmits(['load'])

const showPresets = ref(false)
const presets = ref([])
const newPresetName = ref('')

const fetchPresets = async () => {
    try {
        const response = await axios.get(route('filter-presets.index'))
        presets.value = response.data
    } catch {
        presets.value = []
    }
}

onMounted(() => {
    fetchPresets()
})

const savePreset = async () => {
    if (!newPresetName.value.trim()) return

    try {
        await axios.post(route('filter-presets.store'), {
            name: newPresetName.value,
            filters: route().params || {},
        })

        newPresetName.value = ''
        await fetchPresets()
    } catch (error) {
        console.error('Failed to save preset:', error)
    }
}

const loadPreset = async (preset) => {
    try {
        const response = await axios.get(route('filter-presets.show', preset.id))
        emit('load', response.data.filters)
        showPresets.value = false
    } catch (error) {
        console.error('Failed to load preset:', error)
    }
}

const deletePreset = async (preset) => {
    if (!confirm('Delete this filter preset?')) return

    try {
        await axios.delete(route('filter-presets.destroy', preset.id))
        await fetchPresets()
    } catch (error) {
        console.error('Failed to delete preset:', error)
    }
}
</script>

<template>
    <div class="relative">
        <button
            type="button"
            @click="showPresets = !showPresets"
            class="rounded-lg bg-gray-100 px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300"
        >
            🎯 Filter Presets
        </button>

        <div
            v-if="showPresets"
            class="absolute right-0 z-50 mt-2 w-80 rounded-xl bg-white p-4 shadow-lg dark:bg-gray-800"
        >
            <h4 class="mb-3 text-sm font-semibold text-gray-900 dark:text-white">
                Saved Filters
            </h4>

            <!-- Save New Preset -->
            <div class="mb-4 flex gap-2">
                <input
                    v-model="newPresetName"
                    type="text"
                    placeholder="Preset name..."
                    class="flex-1 rounded-lg border-gray-300 text-sm dark:border-gray-700 dark:bg-gray-900 dark:text-white"
                />

                <button
                    type="button"
                    @click="savePreset"
                    :disabled="!newPresetName.trim()"
                    class="rounded-lg bg-blue-600 px-3 py-1 text-sm text-white hover:bg-blue-700 disabled:opacity-50"
                >
                    Save
                </button>
            </div>

            <!-- Presets List -->
            <div
                v-if="presets.length"
                class="space-y-2"
            >
                <div
                    v-for="preset in presets"
                    :key="preset.id"
                    class="flex items-center justify-between rounded-lg bg-gray-50 p-3 dark:bg-gray-700"
                >
                    <div>
                        <div class="text-sm font-medium text-gray-900 dark:text-white">
                            {{ preset.name }}
                        </div>

                        <div class="text-xs text-gray-500 dark:text-gray-400">
                            {{ new Date(preset.created_at).toLocaleDateString() }}
                        </div>
                    </div>

                    <div class="flex gap-2">
                        <button
                            type="button"
                            @click="loadPreset(preset)"
                            class="text-xs text-blue-600 hover:text-blue-800 dark:text-blue-400"
                        >
                            Load
                        </button>

                        <button
                            type="button"
                            @click="deletePreset(preset)"
                            class="text-xs text-red-600 hover:text-red-800 dark:text-red-400"
                        >
                            Delete
                        </button>
                    </div>
                </div>
            </div>

            <div
                v-else
                class="py-4 text-center text-sm text-gray-500 dark:text-gray-400"
            >
                No saved presets yet.
            </div>
        </div>
    </div>
</template>
