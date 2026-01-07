<template>
  <div class="menu-item-wrapper">
    <!-- Item with children (expandable) -->
    <div v-if="hasChildren">
      <!-- Collapsed mode: Show popup menu on hover -->
      <template v-if="isCollapsed">
        <div @mouseenter="showCollapsedMenu" @mouseleave="hideCollapsedMenu" class="relative">
          <button
            :class="[
              'menu-item-button w-full justify-center',
              'level-1',
              item.active ? 'active' : ''
            ]"
          >
            <MaterialIcon :icon="item.icon" />
          </button>
          
          <!-- Collapsed Popup Menu -->
          <Teleport to="body">
            <div
              v-if="showPopupMenu"
              ref="popupMenuRef"
              :style="popupMenuStyle"
              @mouseenter="showCollapsedMenu"
              @mouseleave="hideCollapsedMenu"
              class="collapsed-popup-menu bg-white dark:bg-gray-800 rounded-lg shadow-xl border border-gray-200 dark:border-gray-700 py-2 min-w-[250px] z-50"
            >
              <div class="px-4 py-2 font-semibold text-sm text-gray-900 dark:text-white border-b border-gray-200 dark:border-gray-700">
                {{ item.label }}
              </div>
              <ul class="py-1">
                <li v-for="child in item.children" :key="child.key">
                  <MenuItemChild
                    :item="child"
                    :level="2"
                    @select="handleSelect"
                  />
                </li>
              </ul>
            </div>
          </Teleport>
        </div>
      </template>

      <!-- Expanded mode: Expandable accordion -->
      <template v-else>
        <button
          @click="toggleExpanded"
          :class="[
            'menu-item-button w-full',
            `level-${level}`,
            item.active ? 'active' : '',
            isExpanded ? 'expanded' : ''
          ]"
        >
          <MaterialIcon :icon="item.icon" />
          <span class="menu-item-label flex-1 text-left text-xs">{{ item.label }}</span>
          <MaterialIcon 
            :icon="isExpanded ? 'expand_more' : 'chevron_right'" 
            class="text-sm transition-transform"
          />
          <button
            v-if="level === 1"
            @click.stop="handleFavoriteToggle"
            :class="[
              'favorite-button p-1.5 rounded transition-colors ml-1',
              isFavorited ? 'text-yellow-500 hover:text-yellow-600' : 'text-gray-400 hover:text-yellow-500'
            ]"
            :title="isFavorited ? 'Remove from favorites' : 'Add to favorites'"
          >
            <MaterialIcon :icon="isFavorited ? 'star' : 'star_outline'" class="text-lg" />
          </button>
        </button>

        <!-- Children (accordion) -->
        <Transition name="accordion">
          <ul v-if="isExpanded" class="menu-children">
            <li v-for="child in item.children" :key="child.key">
              <MenuItemChild
                :item="child"
                :level="level + 1"
                @select="handleSelect"
              />
            </li>
          </ul>
        </Transition>
      </template>
    </div>

    <!-- Item without children (simple link) -->
    <div v-else>
      <template v-if="isCollapsed">
        <div @mouseenter="showTooltip" @mouseleave="hideTooltip" class="relative">
          <a
            :href="item.url"
            @click.prevent="handleSelect(item)"
            :class="[
              'menu-item-button w-full justify-center',
              `level-${level}`,
              item.active ? 'active' : ''
            ]"
          >
            <MaterialIcon :icon="item.icon" />
          </a>
          
          <!-- Tooltip -->
          <Teleport to="body">
            <div
              v-if="showItemTooltip"
              :style="tooltipStyle"
              class="tooltip bg-gray-900 dark:bg-gray-700 text-white text-sm px-3 py-2 rounded-lg shadow-lg z-50 whitespace-nowrap"
            >
              {{ item.label }}
            </div>
          </Teleport>
        </div>
      </template>
      <template v-else>
        <a
          :href="item.url"
          @click.prevent="handleSelect(item)"
          :class="[
            'menu-item-button w-full',
            `level-${level}`,
            item.active ? 'active' : ''
          ]"
        >
          <MaterialIcon :icon="item.icon" />
          <span class="menu-item-label flex-1 text-left text-xs">{{ item.label }}</span>
          <button
            v-if="level === 1"
            @click.stop="handleFavoriteToggle"
            :class="[
              'favorite-button p-1.5 rounded transition-colors ml-1',
              isFavorited ? 'text-yellow-500 hover:text-yellow-600' : 'text-gray-400 hover:text-yellow-500'
            ]"
            :title="isFavorited ? 'Remove from favorites' : 'Add to favorites'"
          >
            <MaterialIcon :icon="isFavorited ? 'star' : 'star_outline'" class="text-lg" />
          </button>
        </a>
      </template>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch, onMounted, onBeforeUnmount } from 'vue'
import MaterialIcon from './MaterialIcon.vue'
import MenuItemChild from './MenuItemChild.vue'
import { useFavoriteMenus } from '@/Composables/useFavoriteMenus.js'

const props = defineProps({
  item: {
    type: Object,
    required: true
  },
  level: {
    type: Number,
    default: 1
  },
  isCollapsed: {
    type: Boolean,
    default: false
  }
})

const emit = defineEmits(['select'])

// Favorite menu functionality
const { isFavorited: checkIsFavorited, toggleFavorite } = useFavoriteMenus()

const isFavorited = computed(() => {
  return props.item.key ? checkIsFavorited(props.item.key) : false
})

const handleFavoriteToggle = async () => {
  if (props.item.key) {
    await toggleFavorite(props.item.key)
  }
}

const isExpanded = ref(false)
const showPopupMenu = ref(false)
const showItemTooltip = ref(false)
const popupMenuRef = ref(null)
const popupMenuStyle = ref({})
const tooltipStyle = ref({})
let hideTimeout = null

const hasChildren = computed(() => {
  return props.item.children && props.item.children.length > 0
})

// Helper function to check if any child is active
const hasActiveChild = (item) => {
  if (!item.children) return false
  return item.children.some(child => {
    if (child.active) return true
    return hasActiveChild(child)
  })
}

// Auto-expand if item or any child is active, or if forceExpanded is set (search)
watch(() => props.item, (newItem) => {
  if (newItem.active || hasActiveChild(newItem) || newItem.forceExpanded) {
    isExpanded.value = true
  }
}, { immediate: true, deep: true })

const toggleExpanded = () => {
  isExpanded.value = !isExpanded.value
}

const showCollapsedMenu = (event) => {
  if (hideTimeout) {
    clearTimeout(hideTimeout)
  }
  showPopupMenu.value = true
  const rect = event.currentTarget.getBoundingClientRect()
  popupMenuStyle.value = {
    position: 'fixed',
    top: `${rect.top}px`,
    left: `${rect.right + 8}px`,
  }
}

const hideCollapsedMenu = () => {
  hideTimeout = setTimeout(() => {
    showPopupMenu.value = false
  }, 200)
}

const showTooltip = (event) => {
  const rect = event.currentTarget.getBoundingClientRect()
  tooltipStyle.value = {
    position: 'fixed',
    top: `${rect.top + (rect.height / 2) - 16}px`,
    left: `${rect.right + 8}px`,
  }
  setTimeout(() => {
    showItemTooltip.value = true
  }, 500)
}

const hideTooltip = () => {
  showItemTooltip.value = false
}

const handleSelect = (selectedItem) => {
  showPopupMenu.value = false
  showItemTooltip.value = false
  emit('select', selectedItem)
}
</script>

<style scoped>
.menu-item-button {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 12px 16px;
  border-radius: 8px;
  transition: all 0.2s ease;
  cursor: pointer;
  border: none;
  background: transparent;
  text-decoration: none;
  color: #000000 !important;
  box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
}

.dark .menu-item-button {
  color: #ffffff !important;
}

.menu-item-label {
  color: inherit !important;
}

.menu-item-button :deep(.material-icons-wrapper),
.menu-item-button :deep(.material-icons) {
  color: inherit !important;
}

.menu-item-button.level-1 {
  font-weight: 600;
  min-height: 48px;
}

.menu-item-button.level-2 {
  font-weight: 500;
  min-height: 40px;
  padding-left: 32px;
  background-color: rgba(0, 0, 0, 0.02);
}

.dark .menu-item-button.level-2 {
  background-color: rgba(255, 255, 255, 0.04);
}

.menu-item-button.level-3 {
  font-weight: 400;
  min-height: 36px;
  padding-left: 48px;
}

.menu-item-button:hover:not(.active) {
  background-color: rgba(186, 245, 4, 0.1);
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.08);
  transform: translateY(-1px);
}

.menu-item-button.active {
  background-color: #BAF504 !important;
  color: #000000 !important;
  font-weight: 700 !important;
  box-shadow: 0 4px 6px rgba(186, 245, 4, 0.3), 0 2px 4px rgba(0, 0, 0, 0.1);
}

.dark .menu-item-button.active {
  background-color: #BAF504 !important;
  color: #000000 !important;
}

.menu-item-button.active .menu-item-label,
.menu-item-button.active :deep(.material-icons-wrapper),
.menu-item-button.active :deep(.material-icons) {
  color: #000000 !important;
}

.menu-item-button.expanded {
  border-bottom-left-radius: 0;
  border-bottom-right-radius: 0;
  padding-bottom: 8px;
}

.menu-children {
  list-style: none;
  padding: 0;
  margin: 0;
  margin-top: 0;
}

.menu-children > li:first-child .menu-item-button {
  padding-top: 10px;
}

.menu-children > li:last-child .menu-item-button {
  padding-bottom: 12px;
  border-bottom-left-radius: 8px;
  border-bottom-right-radius: 8px;
}

.accordion-enter-active,
.accordion-leave-active {
  transition: all 0.3s ease;
  overflow: hidden;
}

.accordion-enter-from,
.accordion-leave-to {
  max-height: 0;
  opacity: 0;
}

.accordion-enter-to,
.accordion-leave-from {
  max-height: 1000px;
  opacity: 1;
}

.collapsed-popup-menu {
  animation: slideIn 0.2s ease;
}

.tooltip {
  animation: fadeIn 0.2s ease;
  pointer-events: none;
}

@keyframes slideIn {
  from {
    opacity: 0;
    transform: translateX(-8px);
  }
  to {
    opacity: 1;
    transform: translateX(0);
  }
}

@keyframes fadeIn {
  from {
    opacity: 0;
  }
  to {
    opacity: 1;
  }
}

/* Justify center when collapsed */
.menu-item-button.justify-center {
  justify-content: center;
  padding-left: 0;
  padding-right: 0;
}
</style>

