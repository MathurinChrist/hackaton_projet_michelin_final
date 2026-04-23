<template>
    <div class="max-w-5xl mx-auto">
        <!-- Concierge Introduction -->
        <div class="bg-white rounded-[40px] border border-michelin-border p-12 mb-16 shadow-sm">
            <div class="flex items-start gap-8">
                <div class="w-16 h-16 rounded-2xl bg-michelin-red/5 flex items-center justify-center flex-shrink-0">
                    <MessageCircle class="w-7 h-7 text-michelin-red" />
                </div>
                <div>
                    <h4 class="text-[9px] font-black text-michelin-red uppercase tracking-[0.4em] mb-4">Recommandation du Concierge</h4>
                    <p class="text-xl font-semibold text-michelin-dark leading-relaxed">
                        {{ vibeResults.intro }}
                    </p>
                    <div class="mt-6 text-[10px] font-black text-michelin-dark/20 uppercase tracking-widest">
                        {{ vibeResults.count }} adresses sélectionnées pour {{ selectedMood?.id === 'custom' ? 'votre envie "' + vibeResults.vibe + '"' : 'votre humeur "' + selectedMood?.label + '"' }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Restaurant Results Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mb-20">
            <div 
                v-for="(resto, index) in vibeResults.results" 
                :key="resto.id"
                :style="{ animationDelay: (index * 0.1) + 's' }"
                class="bg-white rounded-[36px] border border-michelin-border p-10 group hover:shadow-2xl hover:-translate-y-2 transition-all duration-500 animate-fade-up"
            >
                <!-- Stars + Match Badge -->
                <div class="flex justify-between items-start mb-8">
                    <div class="flex gap-1">
                        <Star v-for="s in (resto.stars || 0)" :key="s" class="w-4 h-4 fill-michelin-red text-michelin-red" />
                    </div>
                    <div class="flex items-center gap-1.5 bg-michelin-red/5 px-3 py-1.5 rounded-full">
                        <Flame class="w-3 h-3 text-michelin-red" />
                        <span class="text-[8px] font-black text-michelin-red uppercase tracking-widest">Match {{ getMatchLabel(resto.matchStrength) }}</span>
                    </div>
                </div>

                <!-- Score -->
                <div class="text-michelin-red text-5xl font-serif font-black mb-2 tracking-tighter">
                    {{ resto.score }}
                </div>

                <!-- Name & Description -->
                <h4 class="text-xl font-bold font-serif text-michelin-dark mb-4 group-hover:text-michelin-red transition-colors uppercase tracking-tight">
                    {{ resto.name }}
                </h4>
                <p class="text-sm text-michelin-dark/50 font-medium leading-relaxed line-clamp-3 italic mb-8">
                    "{{ resto.description }}"
                </p>

                <!-- Footer -->
                <div class="flex items-center justify-between pt-6 border-t border-michelin-border/50">
                    <span class="text-[9px] font-black text-michelin-dark/20 uppercase tracking-widest">Certifié Michelin</span>
                    <ArrowUpRight class="w-5 h-5 text-michelin-dark/10 group-hover:text-michelin-red transition-colors" />
                </div>
            </div>
        </div>

        <!-- Reset Button -->
        <div class="text-center pb-10">
            <button @click="$emit('reset')" class="text-[10px] font-black text-michelin-dark/30 uppercase tracking-widest hover:text-michelin-red transition-colors">
                ← Changer d'humeur
            </button>
        </div>
    </div>
</template>

<script setup>
import { Star, Flame, MessageCircle, ArrowUpRight } from 'lucide-vue-next'

defineProps({
    vibeResults: Object,
    selectedMood: Object
})

defineEmits(['reset'])

const getMatchLabel = (strength) => {
    if (strength >= 4) return 'Parfait'
    if (strength >= 3) return 'Fort'
    if (strength >= 2) return 'Bon'
    return 'Ok'
}
</script>
