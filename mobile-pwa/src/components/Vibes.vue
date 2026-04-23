<template>
  <div class="px-6 py-12 animate-fade-in relative min-h-screen">
    <!-- Header -->
    <div class="mb-10 relative z-10">
        <div class="inline-flex items-center gap-2 bg-michelin-red/5 border border-michelin-red/10 rounded-full px-4 py-1 mb-6">
            <Sparkles class="w-2.5 h-2.5 text-michelin-red" />
            <span style="font-family:'Noto Sans',sans-serif; font-weight:700; font-size:9px; letter-spacing:0.18em; text-transform:uppercase; color:#BA0B2F;">IA ÉMOTIONNELLE</span>
        </div>
        <!-- H1 — Noto Sans 300 Light -->
        <div style="margin-bottom:1rem;">
          <span style="font-family:'Noto Sans',sans-serif; font-weight:300; font-size:clamp(2rem,9vw,2.75rem); line-height:1.0; letter-spacing:-0.035em; color:#1a1a1a;">Quelle est ta<br></span>
          <span style="font-family:'Noto Serif',serif; font-weight:400; font-size:clamp(2rem,9vw,2.75rem); line-height:1.0; letter-spacing:-0.03em; color:#BA0B2F; font-style:italic;">vibe ?</span>
        </div>
        <p style="font-family:'Noto Sans',sans-serif; font-weight:400; font-size:0.8125rem; color:rgba(26,26,26,0.45); line-height:1.6;">Laissez votre humeur guider votre prochaine table.</p>
    </div>

    <!-- Mood Selection Grid -->
    <div v-if="!vibeResults && !loading" class="grid grid-cols-2 gap-4 mb-16 relative z-10">
        <div 
            v-for="mood in moods" 
            :key="mood.id"
            @click="selectMood(mood)"
            class="group relative bg-white/60 backdrop-blur-sm rounded-[32px] p-6 border border-michelin-border shadow-sm active:scale-95 transition-all duration-300 hover:shadow-xl hover:bg-white"
        >
            <div style="font-size:1.75rem; margin-bottom:0.75rem; transition:transform 0.3s;" class="group-hover:scale-110">{{ mood.emoji }}</div>
            <!-- Mood label — Noto Sans 600 -->
            <div style="font-family:'Noto Sans',sans-serif; font-weight:600; font-size:0.8125rem; letter-spacing:0; color:#1a1a1a; margin-bottom:0.35rem;">{{ mood.label }}</div>
            <!-- Description — Noto Sans 400 small -->
            <p style="font-family:'Noto Sans',sans-serif; font-weight:400; font-size:0.75rem; color:rgba(26,26,26,0.4); line-height:1.5; margin:0;">{{ mood.description }}</p>
            
            <div class="absolute top-4 right-4 opacity-0 group-hover:opacity-100 transition-opacity">
                <Plus class="w-4 h-4 text-michelin-red" />
            </div>
        </div>
    </div>

    <!-- Custom Mood Input -->
    <div v-if="selectedMood?.id === 'custom' && !vibeResults && !loading" class="mb-16 animate-slide-up relative z-10">
        <div class="bg-white/80 backdrop-blur-xl border-2 border-michelin-border rounded-[32px] p-6 shadow-2xl shadow-michelin-red/5 mb-4 focus-within:border-michelin-red transition-all">
             <label class="text-[8px] font-black text-michelin-dark/20 uppercase tracking-widest block mb-4">Décrivez votre envie</label>
             <textarea 
                v-model="customVibeText"
                placeholder="Ex: Un dîner aux chandelles dans une cour intérieure avec des produits du potager..."
                class="w-full text-sm font-semibold outline-none h-32 resize-none bg-transparent placeholder:text-michelin-dark/10"
             ></textarea>
        </div>
        <button 
            @click="submitCustomVibe"
            class="w-full bg-michelin-dark text-white py-5 rounded-[24px] font-black text-xs uppercase tracking-[0.2em] shadow-xl active:scale-95 transition-all flex items-center justify-center gap-3"
        >
            <Sparkles class="w-4 h-4" />
            Planifier le moment
        </button>
    </div>

    <!-- Sophisticated Loading State -->
    <div v-if="loading" class="fixed inset-0 z-50 bg-white/90 backdrop-blur-md flex items-center justify-center p-12">
        <div class="text-center w-full max-w-[240px]">
            <div class="relative w-24 h-24 mx-auto mb-8">
                <div class="absolute inset-0 border-4 border-michelin-red/10 rounded-full"></div>
                <div class="absolute inset-0 border-4 border-t-michelin-red rounded-full animate-spin"></div>
                <div class="absolute inset-4 bg-michelin-red/5 rounded-full animate-pulse flex items-center justify-center">
                    <Heart class="w-6 h-6 text-michelin-red fill-michelin-red/20" />
                </div>
            </div>
            <h3 class="text-lg font-black text-michelin-dark tracking-tighter mb-2">{{ loadingText }}</h3>
            <div class="flex justify-center gap-1">
                <div v-for="i in 3" :key="i" class="w-1.5 h-1.5 rounded-full bg-michelin-red/20 animate-bounce" :style="{ animationDelay: i * 0.1 + 's' }"></div>
            </div>
        </div>
    </div>

    <!-- Premium Results View -->
    <div v-if="vibeResults && !loading" class="space-y-8 pb-20 animate-slide-up relative z-10">
        <!-- Concierge Intro Card — fond sombre Michelin -->
        <div class="bg-michelin-dark rounded-3xl p-8 text-white shadow-xl relative overflow-hidden">
            <div class="absolute top-[-20%] right-[-10%] w-[150px] h-[150px] bg-white/5 rounded-full blur-[40px]"></div>
            <div class="relative z-10">
                <div style="font-family:'Noto Sans',sans-serif; font-weight:700; font-size:9px; letter-spacing:0.18em; text-transform:uppercase; color:rgba(255,255,255,0.4); margin-bottom:1rem;">Verdict Conciergerie</div>
                <!-- Texte concierge — Noto Serif 400 italic -->
                <p style="font-family:'Noto Serif',serif; font-weight:400; font-size:0.9375rem; line-height:1.65; font-style:italic; color:rgba(255,255,255,0.9); margin:0;">
                   "{{ vibeResults.intro }}"
                </p>
            </div>
        </div>

        <!-- Restaurant Cards -->
        <div v-for="resto in vibeResults.results" :key="resto.id"
             class="bg-white rounded-[40px] border border-michelin-border p-8 shadow-sm group hover:shadow-2xl transition-all">
            <div class="flex justify-between items-start mb-6">
                <div class="flex gap-0.5">
                    <Star v-for="s in 3" :key="s" 
                          :class="['w-3.5 h-3.5', s <= (resto.stars || 0) ? 'text-michelin-red fill-michelin-red' : 'text-michelin-border']" />
                </div>
                <div :class="['text-[8px] font-black uppercase px-3 py-1.5 rounded-full border', 
                             resto.stars >= 3 ? 'bg-michelin-red text-white border-michelin-red' : 'bg-michelin-red/5 text-michelin-red border-michelin-red/10']">
                    {{ resto.stars >= 3 ? 'Incontournable' : 'Pépite Michelin' }}
                </div>
            </div>
            
            <!-- Nom restaurant — Noto Serif 400, pas font-bold -->
            <div style="font-family:'Noto Serif',serif; font-weight:400; font-size:1.1875rem; letter-spacing:-0.015em; color:#1a1a1a; margin:0.5rem 0 0.5rem;" class="uppercase">{{ resto.name }}</div>
            <!-- Description — Noto Sans 400 -->
            <p style="font-family:'Noto Sans',sans-serif; font-weight:400; font-size:0.8125rem; line-height:1.65; color:rgba(26,26,26,0.55); margin:0 0 1.5rem;">{{ resto.description }}</p>
            
            <div class="flex items-center justify-between pt-8 border-t border-michelin-border">
                <div class="flex items-center gap-2">
                    <MapPin class="w-3.5 h-3.5 text-michelin-dark/20" />
                    <span class="text-[10px] font-black text-michelin-dark/30 uppercase tracking-[0.1em]">{{ resto.location || 'Paris' }}</span>
                </div>
                <div class="w-12 h-12 rounded-full bg-michelin-gray flex items-center justify-center group-hover:bg-michelin-red transition-all">
                    <ArrowUpRight class="w-6 h-6 text-michelin-dark/20 group-hover:text-white" />
                </div>
            </div>
        </div>

        <button 
            @click="resetVibes" 
            class="w-full py-6 rounded-3xl bg-michelin-gray/50 text-[10px] font-black text-michelin-dark/30 uppercase tracking-widest text-center hover:bg-michelin-gray transition-all"
        >
            Nouvelle recherche émotionnelle
        </button>
    </div>
  </div>
</template>

<script setup>
import { useVibes } from '../composables/useVibes'
import { Star, ArrowUpRight, Sparkles, Plus, Heart, MapPin } from 'lucide-vue-next'

const {
    moods,
    selectedMood,
    loading,
    loadingText,
    vibeResults,
    customVibeText,
    selectMood,
    submitCustomVibe,
    resetVibes
} = useVibes()
</script>

<style scoped>
.animate-slide-up {
    animation: slideUp 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}

@keyframes slideUp {
    from { opacity: 0; transform: translateY(40px) scale(0.98); }
    to { opacity: 1; transform: translateY(0) scale(1); }
}
</style>
