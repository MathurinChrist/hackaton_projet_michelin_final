<template>
  <nav class="fixed bottom-0 left-0 right-0 bg-white border-t border-michelin-border pb-safe pt-2 px-4 shadow-lg z-[100] flex justify-around items-center h-[70px]">
    <button 
      v-for="tab in mobileTabs" 
      :key="tab.id"
      @click="$emit('update:tab', tab.id)"
      :class="[
        'flex flex-col items-center gap-1 transition-all',
        currentTab === tab.id ? 'text-michelin-red' : 'text-michelin-dark/40'
      ]"
    >
      <component :is="tab.icon" :class="['w-6 h-6', currentTab === tab.id ? 'fill-michelin-red/10' : '']" />
      <span class="text-[10px] font-bold uppercase tracking-widest">{{ tab.label }}</span>
    </button>
  </nav>
</template>

<script setup>
import { Compass, ShieldCheck, Sparkles } from 'lucide-vue-next'

defineProps({
  currentTab: String
})

defineEmits(['update:tab'])

const mobileTabs = [
  { id: 'explorer', label: 'Explorer', icon: Compass },
  { id: 'factcheck', label: 'Verify', icon: ShieldCheck },
  { id: 'vibes', label: 'Vibes', icon: Sparkles }
]
</script>

<style scoped>
.pb-safe {
  padding-bottom: env(safe-area-inset-bottom);
}
</style>
