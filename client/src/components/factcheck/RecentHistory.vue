<template>
    <section class="max-w-5xl mx-auto px-6 mt-16">
      <div class="flex items-center justify-between mb-8">
        <h2 class="text-2xl font-serif font-bold text-michelin-dark">Historique Récent</h2>
        <span class="text-[11px] font-bold text-michelin-red uppercase tracking-widest cursor-pointer hover:underline">Voir tout →</span>
      </div>
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        <div v-for="item in recentAnalyses" :key="item.id" 
             @click="$emit('select', item)"
             class="bg-white border border-michelin-border rounded-2xl p-7 cursor-pointer hover:shadow-lg hover:-translate-y-1 transition-all group">
          <div class="flex justify-between items-start mb-5">
            <div class="text-michelin-red text-3xl font-black">{{ item.score }}</div>
            <ArrowUpRight class="w-5 h-5 text-michelin-dark/10 group-hover:text-michelin-red transition-all" />
          </div>
          <h4 class="text-[15px] font-bold text-michelin-dark uppercase tracking-tight leading-tight">{{ item.name.replace(/\*\*/g, '') }}</h4>
        </div>
      </div>
    </section>
</template>

<script setup>
import { ArrowUpRight } from 'lucide-vue-next'

defineProps({
    recentAnalyses: Array
})

defineEmits(['select'])
</script>
