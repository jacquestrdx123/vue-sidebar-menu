<template>
  <Transition name="slide-down">
    <div
      v-if="activeFavorite && hasChildren"
      class="expanded-favorite-children bg-ciba-green/10 dark:bg-ciba-green/5 border-b border-ciba-green/20 dark:border-ciba-green/10 p-3 mb-2"
    >
      <div class="flex items-center justify-between mb-2">
        <h3 class="text-sm font-semibold text-gray-900 dark:text-white flex items-center gap-2">
          <MaterialIcon :icon="activeFavorite.icon" size="sm" />
          {{ activeFavorite.label }}
        </h3>
        <button
          @click="handleClose"
          class="p-1 text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white rounded transition-colors"
          title="Close"
        >
          <MaterialIcon icon="close" size="sm" />
        </button>
      </div>

      <ul class="space-y-1">
        <li v-for="child in childrenArray" :key="child.key || child.label">
          <a
            :href="child.url"
            @click.prevent="handleChildClick(child)"
            :class="[
              'flex items-center gap-3 px-3 py-2 rounded-lg text-sm transition-colors',
              child.active
                ? 'bg-ciba-green text-black font-semibold'
                : 'text-gray-700 dark:text-gray-300 hover:bg-white/50 dark:hover:bg-gray-800/50'
            ]"
          >
            <MaterialIcon v-if="child.icon" :icon="child.icon" size="sm" />
            <span>{{ child.label }}</span>
          </a>
        </li>
      </ul>
    </div>
  </Transition>
</template>

<script setup>
import { computed, onMounted, onBeforeUnmount } from 'vue'
import { router } from '@inertiajs/vue3'
import MaterialIcon from './MaterialIcon.vue'
import { useFavoriteMenus } from '@/Composables/useFavoriteMenus.js'

const { activeFavorite, clearActiveFavorite } = useFavoriteMenus()

const hasChildren = computed(() => {
  if (!activeFavorite.value?.children) {
    return false
  }
  
  const children = activeFavorite.value.children
  return (Array.isArray(children) && children.length > 0) ||
    (!Array.isArray(children) && Object.keys(children).length > 0)
})

const childrenArray = computed(() => {
  if (!activeFavorite.value?.children) {
    return []
  }
  
  const children = activeFavorite.value.children
  
  // If already an array, return as is
  if (Array.isArray(children)) {
    return children
  }
  
  // If object, convert to array of values
  return Object.entries(children).map(([key, value]) => ({
    key,
    ...value
  }))
})

const handleChildClick = (child) => {
  if (child.url && child.url !== '#') {
    router.visit(child.url)
    clearActiveFavorite()
  }
}

const handleClose = () => {
  clearActiveFavorite()
}

// Close when clicking outside
const handleClickOutside = (event) => {
  if (!activeFavorite.value) return
  
  const element = event.target.closest('.expanded-favorite-children')
  const favoriteBar = event.target.closest('.favorite-menu-bar')
  
  if (!element && !favoriteBar) {
    clearActiveFavorite()
  }
}

onMounted(() => {
  document.addEventListener('click', handleClickOutside)
})

onBeforeUnmount(() => {
  document.removeEventListener('click', handleClickOutside)
})
</script>

<style scoped>
.expanded-favorite-children {
  border-radius: 8px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.slide-down-enter-active,
.slide-down-leave-active {
  transition: all 0.3s ease;
}

.slide-down-enter-from {
  opacity: 0;
  transform: translateY(-10px);
  max-height: 0;
}

.slide-down-enter-to {
  opacity: 1;
  transform: translateY(0);
  max-height: 500px;
}

.slide-down-leave-from {
  opacity: 1;
  transform: translateY(0);
  max-height: 500px;
}

.slide-down-leave-to {
  opacity: 0;
  transform: translateY(-10px);
  max-height: 0;
}
</style>

