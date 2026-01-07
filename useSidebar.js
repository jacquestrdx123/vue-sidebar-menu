import { ref, watch } from 'vue'

const isCollapsed = ref(false)

export function useSidebar() {
  const toggleSidebar = () => {
    isCollapsed.value = !isCollapsed.value
    localStorage.setItem('sidebarCollapsed', isCollapsed.value.toString())
    updateBodyClass()
  }

  const loadSidebarState = () => {
    const saved = localStorage.getItem('sidebarCollapsed')
    if (saved !== null) {
      isCollapsed.value = saved === 'true'
    }
    
    // Auto-collapse on mobile
    if (window.innerWidth < 1024) {
      isCollapsed.value = true
    }
    
    updateBodyClass()
  }

  const updateBodyClass = () => {
    if (isCollapsed.value) {
      document.body.classList.add('sidebar-collapsed')
      document.body.classList.remove('sidebar-expanded')
    } else {
      document.body.classList.add('sidebar-expanded')
      document.body.classList.remove('sidebar-collapsed')
    }
  }

  return {
    isCollapsed,
    toggleSidebar,
    loadSidebarState
  }
}

