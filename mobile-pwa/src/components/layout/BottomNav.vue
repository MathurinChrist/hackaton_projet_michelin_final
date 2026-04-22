<template>
  <nav class="fixed bottom-0 left-0 right-0 bg-white/95 backdrop-blur-lg border-t border-michelin-border pb-safe pt-2 px-4 shadow-[0_-4px_20px_rgba(0,0,0,0.06)] z-[100] flex justify-around items-center h-[70px]">
    <button 
      v-for="tab in mobileTabs" 
      :key="tab.id"
      @click="$emit('update:modelValue', tab.id)"
      :class="[
        'flex flex-col items-center gap-1 transition-all duration-300 relative',
        tab.id === modelValue ? 'text-[#BA0B2F] scale-110' : 'text-[#1a1a1a]/35 scale-100'
      ]"
    >
      <!-- Active indicator dot -->
      <div v-if="tab.id === modelValue" class="absolute -top-2 w-5 h-[3px] rounded-full bg-[#BA0B2F] transition-all"></div>
      <component :is="tab.icon" :class="['w-6 h-6 transition-all duration-300', tab.id === modelValue ? 'stroke-[2.5]' : 'stroke-[1.5]']" />
      <span :class="['text-[9px] font-bold uppercase tracking-[0.15em] transition-all', tab.id === modelValue ? 'opacity-100' : 'opacity-60']">
        {{ tab.label }}
      </span>
    </button>
  </nav>
</template>

<script setup>
import { Compass, ShieldCheck, Sparkles } from 'lucide-vue-next'

defineProps({
  modelValue: String
})

defineEmits(['update:modelValue'])

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
