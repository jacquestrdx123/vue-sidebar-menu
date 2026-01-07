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
        <component 
          v-if="item.icon" 
          :is="getIconComponent(item.icon)" 
          class="h-5 w-5 flex-shrink-0"
        />
        <span v-if="!isCollapsed" class="truncate">{{ item.label }}</span>
      </div>
      <ChevronDownIcon
        v-if="!isCollapsed && item.children"
        class="h-4 w-4 flex-shrink-0 transition-transform"
        :class="{ 'rotate-180': isExpanded }"
      />
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
      <component 
        v-if="item.icon" 
        :is="getIconComponent(item.icon)" 
        class="h-5 w-5 flex-shrink-0"
      />
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
import { ChevronDownIcon } from '@heroicons/vue/24/outline'
import { getIconComponent } from '../../utils/iconMapper.js'

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

</style>

