import { onMounted, onUnmounted } from 'vue'

export function useKeyboardShortcuts(shortcuts = []) {
    const handleKeydown = (event) => {
        for (const shortcut of shortcuts) {
            const keys = shortcut.key.toLowerCase().split('+')
            const ctrl = keys.includes('ctrl')
            const shift = keys.includes('shift')
            const alt = keys.includes('alt')
            const key = keys.filter((k) => !['ctrl', 'shift', 'alt'].includes(k))[0]

            const match =
                (ctrl ? event.ctrlKey || event.metaKey : !event.ctrlKey && !event.metaKey) &&
                (shift ? event.shiftKey : !event.shiftKey) &&
                (alt ? event.altKey : !event.altKey) &&
                event.key.toLowerCase() === key

            if (match) {
                event.preventDefault()
                shortcut.action()
                break
            }
        }
    }

    onMounted(() => {
        document.addEventListener('keydown', handleKeydown)
    })

    onUnmounted(() => {
        document.removeEventListener('keydown', handleKeydown)
    })
}
