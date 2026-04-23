<template>
    <section class="max-w-5xl mx-auto px-6 -mt-1">
      <div class="bg-white rounded-3xl border border-michelin-border shadow-lg overflow-hidden mt-10">
        <div class="grid grid-cols-1 lg:grid-cols-12">
          
          <!-- Gauche : Score -->
          <div class="lg:col-span-4 p-10 flex flex-col items-center justify-center text-center border-b lg:border-b-0 lg:border-r border-michelin-border">
            
            <div v-if="loading" class="py-10 flex flex-col items-center">
              <div class="w-16 h-16 rounded-full border-t-2 border-michelin-red animate-spin mb-6"></div>
              <p class="text-sm font-bold text-michelin-dark/60">{{ currentStepText }}</p>
            </div>

            <div v-else-if="analysis" class="w-full">
              <div class="text-michelin-red text-[80px] font-black leading-none mb-1 tracking-tighter">
                {{ analysis.score }}<span class="text-[18px] text-michelin-dark/20 font-medium ml-1">/10</span>
              </div>
              <div class="w-12 h-[2px] bg-michelin-red mx-auto my-5"></div>
              <h2 class="text-2xl font-black text-michelin-dark uppercase tracking-tight mb-1">{{ analysis.name.replace(/\*\*/g, '') }}</h2>
              <p class="text-[11px] text-michelin-dark/40 uppercase tracking-widest font-medium mb-6">Cuisine française · Paris</p>
              
              <div class="inline-flex items-center gap-2 bg-michelin-red/5 border border-michelin-red/20 rounded-full px-5 py-2.5">
                <Star class="w-4 h-4 text-michelin-red fill-michelin-red" />
                <span class="text-[11px] font-bold text-michelin-red uppercase tracking-wider">Validé par Michelin</span>
              </div>
              <p class="mt-4 text-[10px] text-michelin-dark/25">Score basé sur nos critères officiels ⓘ</p>
            </div>
          </div>

          <!-- Droite : Expertise -->
          <div class="lg:col-span-8 p-10 bg-[#fafafa]">
            
            <div v-if="loading" class="space-y-4">
              <div class="h-4 bg-michelin-dark/5 rounded-full w-32 animate-pulse"></div>
              <div class="h-5 bg-michelin-dark/5 rounded-full w-full animate-pulse"></div>
              <div class="h-5 bg-michelin-dark/5 rounded-full w-4/5 animate-pulse"></div>
              <div class="h-5 bg-michelin-dark/5 rounded-full w-3/5 animate-pulse"></div>
              <div class="h-16 bg-michelin-dark/5 rounded-2xl w-full animate-pulse mt-6"></div>
            </div>

            <div v-else-if="analysis" class="space-y-8">
              <div>
                <h5 class="text-[10px] font-black text-michelin-dark/30 uppercase tracking-[0.3em] mb-4">Expertise Michelin</h5>
                <p class="text-[15px] text-michelin-dark leading-[1.8] font-medium">
                  {{ analysis.description.replace(/\*\*/g, '') }}
                </p>
              </div>

              <div :class="[
                'rounded-2xl p-5 flex items-center gap-5',
                analysis.score >= 7 ? 'bg-michelin-red' : 'bg-michelin-dark'
              ]">
                <div class="w-10 h-10 rounded-full bg-white/20 flex items-center justify-center flex-shrink-0">
                  <Check class="w-5 h-5 text-white" />
                </div>
                <div>
                  <div class="text-[9px] text-white/50 uppercase tracking-widest font-bold">Verdict Michelin</div>
                  <div class="text-white font-black text-lg uppercase tracking-wide">{{ analysis.score >= 7 ? 'RECOMMANDÉ' : 'PRUDENCE' }}</div>
                  <div class="text-[11px] text-white/40">Basé sur les critères du Guide Michelin.</div>
                </div>
              </div>

              <div class="flex gap-4">
                <button @click="$emit('share')" class="flex-1 bg-white border border-michelin-border py-4 rounded-xl text-[11px] font-bold uppercase tracking-wider hover:border-michelin-red transition-all flex items-center justify-center gap-2 text-michelin-dark/60">
                  <Share2 class="w-4 h-4" /> Partager
                </button>
                <button @click="$emit('copy')" class="flex-1 bg-michelin-red text-white py-4 rounded-xl text-[11px] font-bold uppercase tracking-wider hover:bg-[#A70A2A] transition-all flex items-center justify-center gap-2">
                  <Download class="w-4 h-4" /> {{ linkCopied ? 'Copié !' : 'Exporter le rapport' }}
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
</template>

<script setup>
import { Star, Check, Share2, Download } from 'lucide-vue-next'

defineProps({
    loading: Boolean,
    analysis: Object,
    currentStepText: String,
    linkCopied: Boolean
})

defineEmits(['share', 'copy'])
</script>

<style scoped>
@keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
.animate-spin { animation: spin 1s linear infinite; }
</style>
