<template>
  <div v-if="favoriteItems.length > 0 && !isCollapsed" class="p-2 border-b border-gray-200 dark:border-gray-800">
    <div class="px-3 py-2 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
      Favorites
    </div>
    <ul class="space-y-1">
      <li v-for="(item, index) in favoriteItems" :key="index">
        <a
          @click="handleClick(item)"
          :href="item.url"
          :class="[
            'w-full flex items-center gap-3 px-3 py-2 text-sm rounded-lg transition-colors',
            item.active
              ? 'bg-ciba-green text-white'
              : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800',
          ]"
        >
          <component 
            v-if="item.icon" 
            :is="getIconComponent(item.icon)" 
            class="h-5 w-5 flex-shrink-0"
          />
          <span class="truncate">{{ item.label }}</span>
        </a>
      </li>
    </ul>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { usePage } from '@inertiajs/vue3'
import { getIconComponent } from '../../utils/iconMapper.js'

const props = defineProps({
  isCollapsed: {
    type: Boolean,
    default: false,
  },
})

const emit = defineEmits(['select'])

const page = usePage()

const favoriteItems = computed(() => {
  const favorites = page?.props?.favoriteMenuItems
  if (!favorites || !Array.isArray(favorites)) {
    return []
  }
  return favorites.sort((a, b) => (a.order || 0) - (b.order || 0))
})

const handleClick = (item) => {
  emit('select', item)
}
</script>

<style scoped>
</style>

