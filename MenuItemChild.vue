<template>
  <div class="menu-item-child-wrapper">
    <!-- Child with sub-children (nested group) -->
    <div v-if="hasChildren" class="relative">
      <button
        @click="toggleExpanded"
        @mouseenter="handleMouseEnter"
        @mouseleave="handleMouseLeave"
        :class="[
          'menu-item-button w-full',
          `level-${level}`,
          item.active ? 'active' : ''
        ]"
      >
        <MaterialIcon :icon="item.icon" />
        <span class="menu-item-label flex-1 text-left text-xs font-medium">{{ item.label }}</span>
        <MaterialIcon 
          v-if="level === 2"
          icon="chevron_right"
          class="text-sm"
        />
        <MaterialIcon 
          v-else
          :icon="isExpanded ? 'expand_more' : 'chevron_right'" 
          class="text-sm"
        />
      </button>

      <!-- Sub-menu popup for level 2 items -->
      <Teleport v-if="level === 2 && showSubMenu" to="body">
        <div
          ref="subMenuRef"
          :style="subMenuStyle"
          class="submenu-popup bg-white dark:bg-gray-800 rounded-lg shadow-xl border border-gray-200 dark:border-gray-700 py-2 min-w-[200px] z-50"
          @mouseenter="handleMouseEnter"
          @mouseleave="handleMouseLeave"
        >
          <ul>
            <li v-for="child in item.children" :key="child.key">
              <a
                :href="child.url"
                @click.prevent="$emit('select', child)"
                :class="[
                  'submenu-item flex items-center gap-3 px-4 py-2 text-sm hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors',
                  child.active ? 'bg-[#BAF504] text-black hover:bg-[#BAF504] dark:hover:bg-[#BAF504] font-bold' : 'text-black dark:text-white'
                ]"
              >
                <MaterialIcon :icon="child.icon" class="text-base" />
                <span>{{ child.label }}</span>
              </a>
            </li>
          </ul>
        </div>
      </Teleport>

      <!-- Accordion children for level 3+ -->
      <Transition v-if="level > 2" name="accordion">
        <ul v-if="isExpanded" class="menu-children">
          <li v-for="child in item.children" :key="child.key">
            <a
              :href="child.url"
              @click.prevent="$emit('select', child)"
              :class="[
                'menu-item-button w-full',
                `level-${level + 1}`,
                child.active ? 'active' : ''
              ]"
            >
              <MaterialIcon :icon="child.icon" />
              <span class="menu-item-label flex-1 text-left text-xs">{{ child.label }}</span>
            </a>
          </li>
        </ul>
      </Transition>
    </div>

    <!-- Child without sub-children (simple link) -->
    <a
      v-else
      :href="item.url"
      @click.prevent="$emit('select', item)"
      :class="[
        'menu-item-button w-full',
        `level-${level}`,
        item.active ? 'active' : ''
      ]"
    >
      <MaterialIcon :icon="item.icon" />
      <span class="menu-item-label flex-1 text-left text-xs font-medium">{{ item.label }}</span>
    </a>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import MaterialIcon from './MaterialIcon.vue'

const props = defineProps({
  item: {
    type: Object,
    required: true
  },
  level: {
    type: Number,
    default: 2
  }
})

defineEmits(['select'])

const isExpanded = ref(false)
const showSubMenu = ref(false)
const subMenuRef = ref(null)
const subMenuStyle = ref({})
let hoverTimeout = null

const hasChildren = computed(() => {
  return props.item.children && props.item.children.length > 0
})

const toggleExpanded = (event) => {
  if (props.level === 2 && hasChildren.value) {
    // For level 2, show popup
    showSubMenu.value = !showSubMenu.value
    if (showSubMenu.value) {
      const rect = event.currentTarget.getBoundingClientRect()
      subMenuStyle.value = {
        position: 'fixed',
        top: `${rect.top}px`,
        left: `${rect.right + 8}px`,
      }
    }
  } else {
    // For level 3+, toggle accordion
    isExpanded.value = !isExpanded.value
  }
}

const handleMouseEnter = () => {
  if (hoverTimeout) {
    clearTimeout(hoverTimeout)
  }
}

const handleMouseLeave = () => {
  if (props.level === 2) {
    hoverTimeout = setTimeout(() => {
      showSubMenu.value = false
    }, 300)
  }
}
</script>

<style scoped>
.menu-item-button {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 10px 16px;
  border-radius: 0;
  transition: all 0.2s ease;
  cursor: pointer;
  border: none;
  background: transparent;
  color: #000000 !important;
  text-decoration: none;
  box-shadow: 0 1px 2px rgba(0, 0, 0, 0.04);
}

.dark .menu-item-button {
  color: #ffffff !important;
}

.menu-item-button.level-2:not(.active) {
  font-weight: 500;
  min-height: 40px;
  padding-left: 32px;
  background-color: rgba(0, 0, 0, 0.02);
  color: rgba(0, 0, 0, 0.9) !important;
}

.dark .menu-item-button.level-2:not(.active) {
  background-color: rgba(255, 255, 255, 0.04);
  color: rgba(255, 255, 255, 0.9) !important;
}

.menu-item-button.level-2.active {
  min-height: 40px;
  padding-left: 32px;
}

.menu-item-button.level-3:not(.active) {
  font-weight: 400;
  min-height: 36px;
  padding-left: 48px;
  color: rgba(0, 0, 0, 0.85) !important;
}

.dark .menu-item-button.level-3:not(.active) {
  color: rgba(255, 255, 255, 0.85) !important;
}

.menu-item-button.level-3.active {
  min-height: 36px;
  padding-left: 48px;
}

.menu-item-button.level-2:not(.active) :deep(.material-icons-wrapper),
.menu-item-button.level-2:not(.active) :deep(.material-icons) {
  opacity: 1;
}

.menu-item-button.level-3:not(.active) :deep(.material-icons-wrapper),
.menu-item-button.level-3:not(.active) :deep(.material-icons) {
  opacity: 0.9;
}

.menu-item-button:hover:not(.active) {
  background-color: rgba(186, 245, 4, 0.1);
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.08);
  transform: translateY(-1px);
}

.menu-item-button.level-1.active {
  background-color: #BAF504 !important;
  color: #000000 !important;
  font-weight: 700 !important;
  box-shadow: 0 4px 6px rgba(186, 245, 4, 0.3), 0 2px 4px rgba(0, 0, 0, 0.1);
}

.menu-item-button.level-2.active {
  background-color: rgba(186, 245, 4, 0.7) !important;
  color: #000000 !important;
  font-weight: 600 !important;
  box-shadow: 0 3px 5px rgba(186, 245, 4, 0.25), 0 1px 3px rgba(0, 0, 0, 0.08);
}

.dark .menu-item-button.level-2.active {
  background-color: rgba(186, 245, 4, 0.7) !important;
  color: #000000 !important;
}

.menu-item-button.level-3.active {
  background-color: rgba(186, 245, 4, 0.7) !important;
  color: #000000 !important;
  font-weight: 600 !important;
  box-shadow: 0 2px 4px rgba(186, 245, 4, 0.2), 0 1px 2px rgba(0, 0, 0, 0.06);
}

.dark .menu-item-button.level-3.active {
  background-color: rgba(186, 245, 4, 0.7) !important;
  color: #000000 !important;
}

.menu-item-button.active :deep(.material-icons-wrapper),
.menu-item-button.active :deep(.material-icons) {
  opacity: 1 !important;
}

.dark .menu-item-button.level-1.active {
  background-color: #BAF504 !important;
  color: #000000 !important;
}

.menu-children {
  list-style: none;
  padding: 0;
  margin: 0;
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

.submenu-popup {
  animation: slideIn 0.2s ease;
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
</style>

