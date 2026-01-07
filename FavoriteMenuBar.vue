<template>
  <div v-if="favoriteMenus.length > 0" class="favorite-menu-bar">
    <div
      v-for="favorite in favoriteMenus"
      :key="favorite.menu_key"
      class="favorite-menu-item-wrapper"
    >
      <button
        @click="handleFavoriteClick(favorite)"
        :class="[
          'favorite-menu-item flex items-center gap-2 px-3 py-2 rounded-lg transition-all relative',
          activeFavorite?.menu_key === favorite.menu_key 
            ? 'bg-ciba-green text-black font-semibold shadow-lg shadow-ciba-green/30' 
            : 'bg-white/10 text-white hover:bg-white/20 shadow-md hover:shadow-lg'
        ]"
        :title="favorite.label"
      >
        <MaterialIcon :icon="favorite.icon" size="sm" />
        <span class="text-sm whitespace-nowrap">{{ favorite.label }}</span>
      </button>
      <button
        @click.stop="handleRemoveFavorite(favorite.menu_key)"
        class="favorite-remove-button"
        :title="'Remove ' + favorite.label + ' from favorites'"
      >
        <MaterialIcon icon="close" size="xs" />
      </button>
    </div>
    
    <!-- Clear All Button -->
    <button
      @click="handleClearAll"
      class="clear-all-button flex items-center gap-2 px-3 py-2 rounded-lg transition-all bg-red-600/80 text-white hover:bg-red-700 shadow-md hover:shadow-lg"
      title="Clear all favorites"
    >
      <MaterialIcon icon="delete_sweep" size="sm" />
      <span class="text-sm whitespace-nowrap">Clear All</span>
    </button>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { router } from '@inertiajs/vue3'
import MaterialIcon from './MaterialIcon.vue'
import { useFavoriteMenus } from '@/Composables/useFavoriteMenus.js'

const { favoriteMenus, activeFavorite, setActiveFavorite, removeFavorite, clearAllFavorites } = useFavoriteMenus()

const handleClearAll = async () => {
  if (confirm('Are you sure you want to clear all favorites?')) {
    await clearAllFavorites()
  }
}

const handleFavoriteClick = (favorite) => {
  // Scroll sidebar menu to top
  const menuScrollContainer = document.querySelector('.menu-scroll-container')
  if (menuScrollContainer) {
    menuScrollContainer.scrollTo({
      top: 0,
      behavior: 'smooth'
    })
  }
  
  // Check if children exists and has keys (could be object or array)
  const hasChildren = favorite.children && (
    (Array.isArray(favorite.children) && favorite.children.length > 0) ||
    (!Array.isArray(favorite.children) && Object.keys(favorite.children).length > 0)
  )
  
  // If the menu item has children, toggle the expanded view
  if (hasChildren) {
    setActiveFavorite(favorite)
  } else {
    // If no children, navigate to the URL
    if (favorite.url && favorite.url !== '#') {
      router.visit(favorite.url)
    }
  }
}

const handleRemoveFavorite = async (menuKey) => {
  await removeFavorite(menuKey)
}
</script>

<style scoped>
.favorite-menu-bar {
  display: flex;
  flex-wrap: wrap;
  gap: 8px;
}

.favorite-menu-item-wrapper {
  position: relative;
  flex-shrink: 0;
}

.favorite-menu-item {
  flex-shrink: 0;
}

.favorite-remove-button {
  position: absolute;
  top: -6px;
  right: -6px;
  width: 20px;
  height: 20px;
  background: #ef4444;
  border: 2px solid #1f2937;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  cursor: pointer;
  opacity: 0;
  transition: all 0.2s ease;
  padding: 0;
}

.favorite-menu-item-wrapper:hover .favorite-remove-button {
  opacity: 1;
}

.favorite-remove-button:hover {
  background: #dc2626;
  transform: scale(1.1);
}

.clear-all-button {
  flex-shrink: 0;
}
</style>

