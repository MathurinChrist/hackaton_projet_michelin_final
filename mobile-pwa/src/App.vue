<template>
  <div class="min-h-screen bg-white text-michelin-dark font-sans selection:bg-michelin-red selection:text-white pb-24">
    
    <!-- Top Header Mobile -->
    <header class="sticky top-0 bg-[#BA0B2F] h-[60px] flex items-center justify-between px-6 z-50 shadow-md">
      <div class="flex items-center gap-2">
        <span class="text-white font-black tracking-tighter text-lg italic">MICHELIN</span>
        <span class="text-white/80 font-light text-xs tracking-widest uppercase">Mobile</span>
      </div>
      <div class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center">
        <Menu class="w-5 h-5 text-white" />
      </div>
    </header>

    <!-- Main Content -->
    <main v-if="currentTab === 'explorer'" class="animate-fade-in">
      <!-- Shortened Hero for Mobile -->
      <section class="bg-michelin-gray p-8 pt-12 pb-16">
        <div class="flex items-center gap-2 bg-michelin-red/5 border border-michelin-red/10 rounded-full px-3 py-1 mb-6 w-fit">
          <div class="w-1 h-1 rounded-full bg-michelin-red animate-pulse"></div>
          <span class="text-[8px] text-michelin-red font-black uppercase tracking-widest">IA Expert Michelin</span>
        </div>
        <h1 class="text-4xl font-black font-serif text-michelin-dark leading-none tracking-tighter mb-4">
          L'autorité<br />réinventée.
        </h1>
        <p class="text-xs text-michelin-dark/40 font-medium leading-relaxed max-w-[240px]">
          Trouvez l'adresse parfaite validée par nos experts.
        </p>
      </section>

      <!-- Search & Filters -->
      <section class="px-6 -mt-6">
        <div class="bg-white rounded-2xl p-4 shadow-xl border border-michelin-border flex gap-3 focus-within:border-michelin-red transition-all">
          <SearchIcon class="w-5 h-5 text-michelin-dark/20" />
          <input 
            v-model="searchEntry" 
            type="text" 
            placeholder="Rechercher..." 
            class="flex-1 text-sm font-medium outline-none"
          />
        </div>
      </section>

      <!-- Results Grid (Single Column for Mobile) -->
      <section class="px-6 py-12">
        <div class="flex justify-between items-center mb-8">
          <h2 class="text-xl font-serif font-black uppercase tracking-tight">La Sélection</h2>
          <span class="text-[10px] font-black text-michelin-red">{{ filteredRestaurants.length }} Adresses</span>
        </div>

        <div class="space-y-6">
          <div v-for="resto in filteredRestaurants" :key="resto.id" 
               class="bg-white rounded-3xl border border-michelin-border p-6 shadow-sm active:scale-[0.98] transition-all">
            <div class="flex justify-between items-start mb-4">
              <span class="text-[8px] font-black tracking-widest text-michelin-red uppercase bg-michelin-red/5 px-3 py-1 rounded-full border border-michelin-red/10">
                {{ resto.vibe }}
              </span>
              <div class="flex gap-0.5 text-michelin-red">
                <Star v-for="s in (resto.stars || 0)" :key="s" class="w-3 h-3 fill-michelin-red" />
              </div>
            </div>
            <h4 class="text-lg font-bold font-serif mb-2">{{ resto.name }}</h4>
            <p class="text-[11px] text-michelin-dark/50 leading-relaxed mb-6 line-clamp-2 italic">"{{ resto.description }}"</p>
            <div class="flex items-center justify-between pt-4 border-t border-michelin-border">
              <span class="text-[9px] font-black text-michelin-dark/30 uppercase tracking-widest">{{ resto.location }}</span>
              <ArrowUpRight class="w-5 h-5 text-michelin-red" />
            </div>
          </div>
        </div>
      </section>
    </main>

    <FactCheck v-else-if="currentTab === 'factcheck'" />
    <Vibes v-else-if="currentTab === 'vibes'" />

    <!-- Bottom Nav -->
    <BottomNav v-model:tab="currentTab" />

    <!-- Install Prompt Mockup -->
    <div v-if="showInstallPrompt" class="fixed top-20 left-6 right-6 bg-michelin-dark text-white p-6 rounded-3xl shadow-2xl z-[200] animate-bounce-in">
       <div class="flex items-start gap-4 mb-4">
          <div class="w-12 h-12 bg-white rounded-2xl flex items-center justify-center flex-shrink-0">
             <span class="text-michelin-dark font-black text-xl italic">M</span>
          </div>
          <div>
             <h4 class="font-bold text-sm">Installer Michelin Verify</h4>
             <p class="text-[10px] text-white/50">Accédez à l'expertise Michelin en un clic depuis votre écran d'accueil.</p>
          </div>
       </div>
       <div class="flex gap-3">
          <button @click="showInstallPrompt = false" class="flex-1 py-3 bg-white/10 rounded-xl text-[10px] font-bold uppercase tracking-widest">Plus tard</button>
          <button class="flex-1 py-3 bg-michelin-red rounded-xl text-[10px] font-bold uppercase tracking-widest">Installer</button>
       </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { Menu, Search as SearchIcon, Star, ArrowUpRight } from 'lucide-vue-next'
import { useApp } from './composables/useApp'
import BottomNav from './components/layout/BottomNav.vue'
import FactCheck from './components/FactCheck.vue'
import Vibes from './components/Vibes.vue'

const {
    currentTab,
    restaurants,
    searchEntry,
    filteredRestaurants
} = useApp()

const showInstallPrompt = ref(true)

// Mock installation prompt disappear after 5s
setTimeout(() => { if(showInstallPrompt.value) showInstallPrompt.value = false }, 15000)
</script>

<style>
@import url('https://fonts.googleapis.com/css2?family=Noto+Sans:ital,wght@0,100..900;1,100..900&family=Noto+Serif:ital,wght@0,100..900;1,100..900&display=swap');

/* ── Variables ── */
:root {
  --michelin-red: #BA0B2F;
  --michelin-dark: #1a1a1a;
  --michelin-gray: #F5F5F5;
  --michelin-border: #E5E5E5;
  --font-sans: 'Noto Sans', system-ui, sans-serif;
  --font-serif: 'Noto Serif', Georgia, serif;
}

/* ── Base ── */
*, *::before, *::after { box-sizing: border-box; }

html {
  font-size: 16px;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
  text-rendering: optimizeLegibility;
}

body {
  font-family: var(--font-sans);
  font-weight: 400;
  font-size: 0.9375rem;
  line-height: 1.6;
  color: var(--michelin-dark);
  background: white;
  overscroll-behavior-y: contain;
}

/* ── Titres Mobile — Michelin Guide ── */

/* H1 — Grand titre mobile. Light, serré */
h1 {
  font-family: var(--font-sans);
  font-weight: 300;
  font-size: clamp(2rem, 9vw, 2.75rem);
  line-height: 1.02;
  letter-spacing: -0.03em;
  color: var(--michelin-dark);
  margin: 0;
}

/* H2 — Titre section */
h2 {
  font-family: var(--font-sans);
  font-weight: 400;
  font-size: clamp(1.25rem, 5vw, 1.75rem);
  line-height: 1.15;
  letter-spacing: -0.02em;
  color: var(--michelin-dark);
  margin: 0;
}

/* H3 — Titre de bloc */
h3 {
  font-family: var(--font-sans);
  font-weight: 600;
  font-size: 1rem;
  line-height: 1.25;
  letter-spacing: -0.005em;
  color: var(--michelin-dark);
  margin: 0;
}

/* H4 — Nom de restaurant */
h4 {
  font-family: var(--font-serif);
  font-weight: 400;
  font-size: 1.0625rem;
  line-height: 1.25;
  letter-spacing: -0.01em;
  color: var(--michelin-dark);
  margin: 0;
}

/* Corps */
p {
  font-family: var(--font-sans);
  font-weight: 400;
  font-size: 0.875rem;
  line-height: 1.65;
  color: rgba(26,26,26,0.6);
  margin: 0;
}

/* ── Animations ── */

.animate-fade-in {
  animation: fadeIn 0.4s ease-out forwards;
}

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(8px); }
  to { opacity: 1; transform: translateY(0); }
}

@keyframes bounce-in {
  0% { transform: scale(0.9); opacity: 0; }
  70% { transform: scale(1.05); opacity: 1; }
  100% { transform: scale(1); opacity: 1; }
}
.animate-bounce-in {
  animation: bounce-in 0.6s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
}
</style>

