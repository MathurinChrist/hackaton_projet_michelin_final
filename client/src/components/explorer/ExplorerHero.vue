<template>
    <section class="grid grid-cols-1 lg:grid-cols-[1.2fr_1fr] min-h-[600px] border-b border-michelin-border">
        <div class="py-20 px-6 md:py-24 md:px-12 lg:px-20 flex flex-col justify-center bg-michelin-gray">
          <!-- Badge — label Michelin style -->
          <div class="flex items-center gap-[7px] bg-michelin-red/5 border border-michelin-red/20 rounded-full px-4 py-1.5 mb-10 w-fit">
            <div class="w-1.5 h-1.5 rounded-full bg-michelin-red animate-pulse"></div>
            <span style="font-size:10px; font-weight:700; letter-spacing:0.14em; text-transform:uppercase; color:#BA0B2F;">Nouvelle expérience · IA embarquée</span>
          </div>
          
          <!-- H1 : Michelin Guide style — Noto Sans Light, tracking serré -->
          <h1 style="font-family:'Noto Sans',sans-serif; font-weight:300; font-size:clamp(2.75rem,5vw,4.25rem); line-height:1.02; letter-spacing:-0.035em; margin:0 0 1.5rem; color:#1a1a1a;">
            L'autorité<br>gastronomique<br><span style="color:#BA0B2F; font-style:italic; font-weight:400;">réinventée</span>.
          </h1>
          
          <!-- Corps de texte — 15px Regular, 1.65 ligne -->
          <p style="font-family:'Noto Sans',sans-serif; font-size:0.9375rem; font-weight:400; line-height:1.65; color:rgba(26,26,26,0.5); max-width:460px; margin:0 0 3rem;">
            Finis les pièges Instagram. Trouve l'adresse qui te ressemble — validée par un siècle d'expertise.
          </p>
          
          <div class="flex flex-wrap gap-5 items-center mb-20">
            <button style="background:#BA0B2F; color:white; font-family:'Noto Sans',sans-serif; font-weight:700; font-size:0.6875rem; letter-spacing:0.16em; text-transform:uppercase; padding:1rem 2.5rem; border-radius:9999px; border:none; cursor:pointer; transition:opacity 0.2s;" onmouseover="this.style.opacity='0.85'" onmouseout="this.style.opacity='1'">
              Explorer maintenant
            </button>
          </div>
          
          <!-- Stats — chiffre en Noto Serif 400, label en label-michelin -->
          <div class="grid grid-cols-2 sm:grid-cols-4 gap-12 pt-12 border-t border-michelin-border">
            <div v-for="stat in stats" :key="stat.label">
              <div style="font-family:'Noto Serif',serif; font-weight:400; font-size:1.875rem; letter-spacing:-0.02em; color:#1a1a1a; line-height:1;">{{ stat.value }}</div>
              <div style="font-family:'Noto Sans',sans-serif; font-weight:700; font-size:0.5625rem; letter-spacing:0.16em; text-transform:uppercase; color:rgba(26,26,26,0.35); margin-top:0.35rem;">{{ stat.label }}</div>
            </div>
          </div>
        </div>
        
        <div class="relative overflow-hidden bg-white border-l border-michelin-border hidden lg:block">
          <div class="grid grid-cols-2 grid-rows-2 h-full gap-[1px] bg-michelin-border">
            <div v-for="(card, i) in dynamicHeroCards" :key="i" 
                 class="group p-10 flex flex-col justify-end relative overflow-hidden transition-all duration-700 cursor-pointer bg-white hover:bg-michelin-gray">
              <div class="relative z-10">
                <div class="flex justify-between items-start mb-6">
                   <!-- Card category label -->
                   <div style="font-family:'Noto Sans',sans-serif; font-weight:700; font-size:0.5625rem; letter-spacing:0.16em; text-transform:uppercase; color:rgba(26,26,26,0.35);">{{ card.category }}</div>
                   <div class="w-10 h-10 rounded-full flex items-center justify-center bg-michelin-gray border border-michelin-border group-hover:scale-110 transition-all">
                      <component :is="card.icon" class="w-4 h-4 text-michelin-red" />
                   </div>
                </div>
                
                <span style="display:inline-block; font-family:'Noto Sans',sans-serif; font-weight:700; font-size:0.5625rem; letter-spacing:0.14em; text-transform:uppercase; padding:0.3rem 0.75rem; border-radius:9999px; margin-bottom:0.75rem; background:rgba(186,11,47,0.05); color:#BA0B2F; border:1px solid rgba(186,11,47,0.12);">
                  {{ card.tag }}
                </span>
                <div class="text-michelin-red text-xs mb-2 tracking-[3px]">
                    <span v-for="s in 5" :key="s" :class="s <= (card.stars || 5) ? 'opacity-100' : 'opacity-20'">★</span>
                </div>
                <!-- Card title — Noto Serif 400 -->
                <div style="font-family:'Noto Serif',serif; font-weight:400; font-size:1.0625rem; letter-spacing:-0.01em; color:#1a1a1a; margin-bottom:0.2rem; transition:transform 0.2s;" class="group-hover:translate-x-1">{{ card.name }}</div>
                <!-- Card meta — Noto Sans Regular small -->
                <div style="font-family:'Noto Sans',sans-serif; font-weight:400; font-size:0.8125rem; color:rgba(26,26,26,0.45);">{{ card.meta }}</div>
              </div>
            </div>
          </div>
        </div>
      </section>
</template>


<script setup>
import { computed, markRaw } from 'vue'
import { Check, Flame, Leaf, Award } from 'lucide-vue-next'

const props = defineProps({
    restaurants: Array,
    iconMap: Object
})

const stats = [
    { label: 'Restaurants vérifiés', value: '35k+' },
    { label: 'Pays couverts', value: '140' },
    { label: 'Anti-buzz', value: '100%' },
    { label: 'Explorateurs actifs', value: '128k' }
]

const fallbackHeroCards = [
  { category: 'Fact-check', tag: 'Vérifié', name: 'Guy Savoy', meta: 'Gastronomique · €€€€€', accent: '#CC0000', icon: markRaw(Check) },
  { category: 'Mood match', tag: 'Romantique', name: 'Septime', meta: 'Bistronomie · €€€', accent: '#534AB7', icon: markRaw(Flame) },
  { category: 'Éco', tag: 'Jardin', name: 'Saturne', meta: 'Naturel · €€€', accent: '#1D9E75', icon: markRaw(Leaf) },
  { category: 'Passeport', tag: 'Badge', name: 'Frenchie', meta: 'Moderne · €€€€', accent: '#BA7517', icon: markRaw(Award) },
]

const dynamicHeroCards = computed(() => {
  const data = props.restaurants.slice(0, 4)
  if (data.length < 4) return fallbackHeroCards
  
  return data.map(r => ({
    category: r.source === 'michelin' ? 'Certifié' : 'Community',
    tag: r.status,
    name: r.name,
    meta: `${r.location} · ${r.price}`,
    accent: r.accentColor,
    icon: props.iconMap[r.icon] || markRaw(Check)
  }))
})
</script>
