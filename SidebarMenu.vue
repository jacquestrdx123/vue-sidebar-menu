<template>
  <div class="sidebar-menu-container">
    <!-- Sidebar -->
    <aside
      :class="[
        'sidebar',
        isCollapsed ? 'sidebar-collapsed' : 'sidebar-expanded',
        'bg-white dark:bg-gray-900 border-r border-gray-200 dark:border-gray-800 transition-all duration-300',
        isCollapsed ? 'w-16' : 'w-[250px]'
      ]"
      :data-collapsed="isCollapsed"
    >
      <!-- Branding Section -->
      <div class="branding-section border-b border-gray-200 dark:border-gray-800 px-4 py-3 flex items-center justify-between">
        <div class="branding-logo flex items-center gap-3 overflow-hidden">
          <img 
            src="/images/CIBAlogo.png" 
            alt="CIBA Logo" 
            class="flex-shrink-0 h-10 w-10 object-contain"
          />
        </div>
        <button
          @click="toggleSidebar"
          class="toggle-button p-2 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg transition-colors flex-shrink-0"
          :title="isCollapsed ? 'Expand sidebar' : 'Collapse sidebar'"
        >
          <span class="material-icons text-gray-600 dark:text-gray-400">
            {{ isCollapsed ? 'chevron_right' : 'chevron_left' }}
          </span>
        </button>
      </div>

      <!-- Search Bar -->
      <div v-if="!isCollapsed" class="search-section border-b border-gray-200 dark:border-gray-800 p-3">
        <div class="relative">
          <span class="material-icons absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-lg">
            search
          </span>
          <input
            v-model="searchQuery"
            type="text"
            placeholder="Search menu..."
            class="w-full pl-10 pr-10 py-2 text-sm bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-ciba-green focus:border-transparent text-gray-900 dark:text-white placeholder-gray-500"
            style="padding-left: 2.75rem !important;"
          />
          <button
            v-if="searchQuery"
            @click="clearSearch"
            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300"
          >
            <span class="material-icons text-lg">close</span>
          </button>
        </div>
      </div>

      <!-- Menu Items -->
      <nav class="menu-nav">
        <div ref="scrollContainer" class="menu-scroll-container">
          <!-- Expanded Favorite Children -->
          <ExpandedFavoriteChildren />
          
          <!-- No Results Message -->
          <div v-if="searchQuery && filteredItems.length === 0" class="p-4 text-center">
            <span class="material-icons text-gray-400 dark:text-gray-600 text-4xl mb-2">search_off</span>
            <p class="text-sm text-gray-500 dark:text-gray-400">No menu items found</p>
            <button @click="clearSearch" class="mt-2 text-xs text-ciba-green hover:underline">
              Clear search
            </button>
          </div>
          
          <!-- Menu Items -->
          <ul v-else class="space-y-1 p-2">
            <template v-for="item in filteredItems" :key="item.key">
              <li>
                <!-- Top Level Item -->
                <MenuItem
                  :item="item"
                  :level="1"
                  :is-collapsed="isCollapsed"
                  @select="onSelect"
                />
              </li>
            </template>
          </ul>
        </div>
      </nav>
    </aside>

    <!-- Mobile Overlay -->
    <Transition name="overlay">
      <div
        v-if="isMobileMenuOpen"
        @click="closeMobileMenu"
        class="mobile-overlay fixed inset-0 bg-black bg-opacity-50 z-30 lg:hidden"
      />
    </Transition>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onBeforeUnmount, watch, nextTick } from 'vue'
import { usePage, router } from '@inertiajs/vue3'
import { useSidebar } from '@/Composables/useSidebar.js'
import MenuItem from './MenuItem.vue'
import ExpandedFavoriteChildren from './ExpandedFavoriteChildren.vue'

const page = usePage()
const { isCollapsed, toggleSidebar: toggle, loadSidebarState } = useSidebar()
const isMobileMenuOpen = ref(false)
const searchQuery = ref('')
const scrollContainer = ref(null)
const SCROLL_STORAGE_KEY = 'vue-admin-sidebar-scroll'

const items = computed(() => {
  // Use the 'menu' prop from MenuWebService (static menu definition)
  const shared = page?.props?.menu
  if (!shared) return []

  const list = Array.isArray(shared) 
    ? shared 
    : Object.entries(shared).map(([key, value]) => ({ key, ...value }))

  const processChildren = (children) => {
    if (!children) return null
    
    const childArray = Object.entries(children).map(([childKey, childValue]) => ({
      key: childKey,
      label: childValue.label ?? childValue.title ?? '',
      icon: childValue.icon ?? null,
      url: childValue.url ?? childValue.href ?? '#',
      active: childValue.active ?? false,
      children: childValue.children ? processChildren(childValue.children) : null,
    }))
    
    // Return null if array is empty instead of empty array
    return childArray.length > 0 ? childArray : null
  }

  return list
    .map((item) => ({
      key: item.key,
      label: item.label ?? item.title ?? '',
      icon: item.icon ?? 'folder',
      url: item.url ?? item.href ?? '#',
      active: item.active ?? false,
      children: item.children ? processChildren(item.children) : null,
    }))
    .filter((item) => {
      // Filter out groups that have no children (empty arrays)
      if (item.url === '#' && (!item.children || item.children.length === 0)) {
        return false
      }
      return true
    })
})

// Filtered items based on search query
const filteredItems = computed(() => {
  if (!searchQuery.value) {
    return items.value
  }

  const query = searchQuery.value.toLowerCase()

  const filterChildren = (children) => {
    if (!children) return null
    
    const filtered = children.filter(child => 
      child.label.toLowerCase().includes(query)
    )
    
    return filtered.length > 0 ? filtered : null
  }

  return items.value
    .map(item => {
      const matchesLabel = item.label.toLowerCase().includes(query)
      const filteredChildren = item.children ? filterChildren(item.children) : null
      
      // Include group if it matches OR if it has matching children
      if (matchesLabel || filteredChildren) {
        return {
          ...item,
          children: filteredChildren,
          // Auto-expand groups with matching children during search
          forceExpanded: !matchesLabel && filteredChildren
        }
      }
      
      return null
    })
    .filter(item => item !== null)
})

const clearSearch = () => {
  searchQuery.value = ''
}

const onSelect = (item) => {
  if (item && item.url && item.url !== '#') {
    router.visit(item.url)
  }
  // Close mobile menu after selection
  if (window.innerWidth < 1024) {
    isMobileMenuOpen.value = false
  }
}

const toggleSidebar = () => {
  toggle()
}

const closeMobileMenu = () => {
  isMobileMenuOpen.value = false
}

const persistScrollPosition = () => {
  if (!scrollContainer.value) {
    return
  }

  sessionStorage.setItem(SCROLL_STORAGE_KEY, String(scrollContainer.value.scrollTop))
}

const restoreScrollPosition = () => {
  if (!scrollContainer.value) {
    return
  }

  const stored = sessionStorage.getItem(SCROLL_STORAGE_KEY)
  if (stored === null) {
    return
  }

  requestAnimationFrame(() => {
    scrollContainer.value.scrollTop = Number(stored)
  })
}

// Load saved preference on mount
onMounted(() => {
  loadSidebarState()

  restoreScrollPosition()

  if (scrollContainer.value) {
    scrollContainer.value.addEventListener('scroll', persistScrollPosition, { passive: true })
  }
  
  // Handle window resize
  window.addEventListener('resize', handleResize)
})

onBeforeUnmount(() => {
  window.removeEventListener('resize', handleResize)

  if (scrollContainer.value) {
    scrollContainer.value.removeEventListener('scroll', persistScrollPosition)
  }
})

const handleResize = () => {
  if (window.innerWidth < 1024) {
    isCollapsed.value = true
    document.body.classList.add('sidebar-collapsed')
    document.body.classList.remove('sidebar-expanded')
  }
}

watch(
  () => page.url,
  () => {
    nextTick(() => {
      restoreScrollPosition()
    })
  }
)

// Expose toggle function for parent components
defineExpose({
  toggleSidebar,
  isCollapsed
})
</script>

<style scoped>
.sidebar-menu-container {
  display: contents;
}

.sidebar {
  position: fixed;
  top: 0;
  left: 0;
  height: 100vh;
  z-index: 40;
  display: flex;
  flex-direction: column;
}

.sidebar-expanded {
  width: 250px;
}

.sidebar-collapsed {
  width: 64px;
}

.branding-section {
  flex-shrink: 0;
  min-height: 60px;
}

.menu-nav {
  flex: 1;
  min-height: 0;
  overflow: hidden;
  position: relative;
}

.menu-scroll-container {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  overflow-y: auto;
  overflow-x: hidden;
}

.menu-scroll-container::-webkit-scrollbar {
  width: 6px;
}

.menu-scroll-container::-webkit-scrollbar-track {
  background: transparent;
}

.menu-scroll-container::-webkit-scrollbar-thumb {
  background: rgba(156, 163, 175, 0.3);
  border-radius: 3px;
}

.menu-scroll-container::-webkit-scrollbar-thumb:hover {
  background: rgba(156, 163, 175, 0.5);
}

.toggle-button {
  opacity: 0.7;
}

.toggle-button:hover {
  opacity: 1;
}

.sidebar-collapsed .toggle-button {
  margin-left: auto;
  margin-right: auto;
}

.mobile-overlay {
  backdrop-filter: blur(2px);
}

/* Transitions */
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.2s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}

.overlay-enter-active,
.overlay-leave-active {
  transition: opacity 0.3s ease;
}

.overlay-enter-from,
.overlay-leave-to {
  opacity: 0;
}

.material-icons {
  font-family: 'Material Icons';
  font-weight: normal;
  font-style: normal;
  font-size: 24px;
  display: inline-block;
  line-height: 1;
  text-transform: none;
  letter-spacing: normal;
  word-wrap: normal;
  white-space: nowrap;
  direction: ltr;
}

/* Responsive */
@media (max-width: 1023px) {
  .sidebar {
    transform: translateX(-100%);
  }
  
  .sidebar.mobile-open {
    transform: translateX(0);
  }
}
</style>

