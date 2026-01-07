import { ref, onMounted } from 'vue'

const STORAGE_KEY = 'vue-admin-sidebar-collapsed'
const isCollapsed = ref(false)

export function useSidebar() {
  const loadSidebarState = () => {
    if (typeof window !== 'undefined') {
      const stored = localStorage.getItem(STORAGE_KEY)
      if (stored !== null) {
        isCollapsed.value = stored === 'true'
      } else {
        // Default to collapsed on mobile
        isCollapsed.value = window.innerWidth < 1024
      }
    }
  }

  const toggleSidebar = () => {
    isCollapsed.value = !isCollapsed.value
    if (typeof window !== 'undefined') {
      localStorage.setItem(STORAGE_KEY, String(isCollapsed.value))
    }
  }

  onMounted(() => {
    loadSidebarState()
  })

  return {
    isCollapsed,
    toggleSidebar,
    loadSidebarState,
  }
}

