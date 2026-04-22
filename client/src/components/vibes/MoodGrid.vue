<template>
    <div class="grid grid-cols-2 lg:grid-cols-6 gap-4 mb-12">
        <div 
            v-for="mood in moods" 
            :key="mood.id"
            @click="$emit('select', mood)"
            :class="[
                'relative rounded-[24px] p-6 cursor-pointer transition-all duration-500 overflow-hidden group border-2',
                selectedMood?.id === mood.id 
                    ? 'border-michelin-red shadow-2xl shadow-michelin-red/10 scale-[1.02]'
                    : 'border-transparent bg-white shadow-sm hover:shadow-xl hover:-translate-y-1'
            ]"
        >
            <div class="text-3xl mb-4">{{ mood.emoji }}</div>
            <h3 class="text-xs font-black uppercase tracking-tight mb-2">{{ mood.label }}</h3>
            <p class="text-[9px] text-michelin-dark/40 font-medium leading-relaxed line-clamp-2">{{ mood.description }}</p>
            
            <div v-if="selectedMood?.id === mood.id" class="absolute top-3 right-3 w-5 h-5 bg-michelin-red rounded-full flex items-center justify-center">
                <Check class="w-2.5 h-2.5 text-white" />
            </div>
        </div>
    </div>
</template>

<script setup>
import { Check } from 'lucide-vue-next'

defineProps({
    moods: Array,
    selectedMood: Object
})

defineEmits(['select'])
</script>
