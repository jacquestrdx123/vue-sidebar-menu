<template>
  <div>
    <button
      v-if="item.url === '#' || item.children"
      @click="toggleExpanded"
      :class="[
        'w-full flex items-center gap-3 px-3 py-2 text-sm rounded-lg transition-colors',
        isCollapsed ? 'justify-center' : 'justify-between',
        item.active
          ? 'bg-ciba-green text-white'
          : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800',
      ]"
    >
      <div class="flex items-center gap-3 flex-1 min-w-0">
        <span v-if="item.icon" class="material-icons text-lg flex-shrink-0">
          {{ item.icon }}
        </span>
        <span v-if="!isCollapsed" class="truncate">{{ item.label }}</span>
      </div>
      <span
        v-if="!isCollapsed && item.children"
        class="material-icons text-sm flex-shrink-0 transition-transform"
        :class="{ 'rotate-180': isExpanded }"
      >
        expand_more
      </span>
    </button>

    <a
      v-else
      @click="handleClick"
      :href="item.url"
      :class="[
        'w-full flex items-center gap-3 px-3 py-2 text-sm rounded-lg transition-colors',
        isCollapsed ? 'justify-center' : '',
        item.active
          ? 'bg-ciba-green text-white'
          : 'text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800',
      ]"
    >
      <span v-if="item.icon" class="material-icons text-lg flex-shrink-0">
        {{ item.icon }}
      </span>
      <span v-if="!isCollapsed" class="truncate">{{ item.label }}</span>
    </a>

    <Transition name="slide">
      <ul
        v-if="item.children && isExpanded && !isCollapsed"
        class="ml-4 mt-1 space-y-1 border-l border-gray-200 dark:border-gray-700 pl-2"
      >
        <li v-for="(child, childKey) in item.children" :key="childKey">
          <MenuItem
            :item="{ ...child, key: childKey }"
            :level="level + 1"
            :is-collapsed="isCollapsed"
            @select="$emit('select', $event)"
          />
        </li>
      </ul>
    </Transition>
  </div>
</template>

<script setup>
import { ref, watch } from 'vue'
import MenuItem from './MenuItem.vue'

const props = defineProps({
  item: {
    type: Object,
    required: true,
  },
  level: {
    type: Number,
    default: 1,
  },
  isCollapsed: {
    type: Boolean,
    default: false,
  },
})

const emit = defineEmits(['select'])

const isExpanded = ref(props.item.active || false)

// Auto-expand if item has active child or is active
watch(
  () => props.item.active,
  (active) => {
    if (active) {
      isExpanded.value = true
    }
  },
  { immediate: true }
)

// Auto-expand during search if forceExpanded is set
watch(
  () => props.item.forceExpanded,
  (forceExpanded) => {
    if (forceExpanded) {
      isExpanded.value = true
    }
  }
)

const toggleExpanded = () => {
  if (props.item.children) {
    isExpanded.value = !isExpanded.value
  } else if (props.item.url && props.item.url !== '#') {
    handleClick()
  }
}

const handleClick = () => {
  emit('select', props.item)
}
</script>

<style scoped>
.slide-enter-active,
.slide-leave-active {
  transition: all 0.3s ease;
  max-height: 1000px;
  overflow: hidden;
}

.slide-enter-from,
.slide-leave-to {
  max-height: 0;
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
</style>

