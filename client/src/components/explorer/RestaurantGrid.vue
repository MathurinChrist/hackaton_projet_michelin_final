<template>
    <section class="bg-michelin-gray/30 py-24 px-6 md:px-12 lg:px-20">
        <div class="max-w-7xl mx-auto">
          <div class="flex justify-between items-end mb-16 px-4">
            <div>
                <h2 class="text-4xl font-serif font-bold tracking-tight mb-2 uppercase">La Sélection.</h2>
                <p class="text-sm text-michelin-dark/40 font-medium">Bases certifiées & découvertes communautaires.</p>
            </div>
            <div class="text-[10px] font-black text-michelin-red uppercase tracking-widest">{{ filteredRestaurants.length }} Adresses trouvées</div>
          </div>
          
          <div v-if="filteredRestaurants.length === 0" class="py-20 text-center">
              <p class="text-michelin-dark/30 font-serif text-2xl italic">Aucun établissement ne correspond à votre recherche...</p>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-10">
            <div v-for="resto in filteredRestaurants" :key="resto.id" 
                 class="bg-white rounded-[40px] border border-michelin-border p-10 group hover:shadow-2xl hover:-translate-y-2 transition-all duration-500 overflow-hidden relative">
              
              <div v-if="resto.source === 'michelin'" class="absolute -top-4 -right-4 w-24 h-24 bg-michelin-red/5 rounded-full flex items-center justify-center -rotate-12 group-hover:bg-michelin-red/10 transition-colors">
                  <Award class="w-8 h-8 text-michelin-red/20" />
              </div>

              <div class="relative z-10 h-full flex flex-col justify-between">
                <div>
                   <div class="flex justify-between items-start mb-8">
                    <span :style="{ backgroundColor: resto.accentColor + '10', color: resto.accentColor }" class="text-[9px] font-black tracking-widest px-4 py-2 rounded-full uppercase border border-current opacity-60">
                      {{ resto.vibe }}
                    </span>
                    <div class="flex gap-1 text-michelin-red">
                        <Star v-for="s in (resto.stars || 0)" :key="s" class="w-4 h-4 fill-michelin-red" />
                    </div>
                  </div>
                  
                  <h4 class="text-2xl font-bold mb-3 font-serif group-hover:text-michelin-red transition-colors">{{ (resto.name || '').replace(/\*\*/g, '') }}</h4>
                  <p class="text-sm text-michelin-dark/50 font-medium leading-relaxed mb-10 line-clamp-2 italic">"{{ (resto.description || '').replace(/\*\*/g, '') }}"</p>
                </div>

                <div class="flex items-center justify-between pt-8 border-t border-michelin-gray">
                   <div>
                      <div class="text-[10px] font-black tracking-widest uppercase text-michelin-dark/30 mb-1">{{ resto.location }}</div>
                      <div class="text-[11px] font-bold text-michelin-dark/60 tracking-wider">Expertise {{ resto.source === 'michelin' ? 'Certifiée' : 'Agent' }}</div>
                   </div>
                   <div class="w-12 h-12 rounded-2xl bg-michelin-gray flex items-center justify-center group-hover:bg-michelin-red group-hover:text-white transition-all">
                      <ArrowUpRight class="w-5 h-5 cursor-pointer" />
                   </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>
</template>

<script setup>
import { Star, Award, ArrowUpRight } from 'lucide-vue-next'

defineProps({
    filteredRestaurants: Array
})
</script>
