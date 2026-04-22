<template>
  <div class="px-6 py-12 animate-fade-in relative overflow-hidden">
    <!-- Sophisticated Background Elements -->
    <div class="fixed top-[-10%] right-[-10%] w-[300px] h-[300px] bg-michelin-red/5 rounded-full blur-[100px] pointer-events-none"></div>
    <div class="fixed bottom-[-10%] left-[-10%] w-[250px] h-[250px] bg-michelin-dark/5 rounded-full blur-[80px] pointer-events-none"></div>

    <!-- Hero Mobile -->
    <div class="mb-10 text-center relative z-10">
        <!-- Badge label — Noto Sans 700, 10px, tracked -->
        <div class="inline-flex items-center gap-2 bg-michelin-red/5 border border-michelin-red/10 rounded-full px-3 py-1 mb-6">
          <div class="w-1 h-1 rounded-full bg-michelin-red animate-pulse"></div>
          <span style="font-family:'Noto Sans',sans-serif; font-weight:700; font-size:9px; letter-spacing:0.18em; text-transform:uppercase; color:#BA0B2F;">Authenticité certifiée</span>
        </div>
        <!-- H1 — Noto Sans 300 Light, très compact, pas de font-black -->
        <div style="margin-bottom:0.75rem;">
          <div style="font-family:'Noto Sans',sans-serif; font-weight:300; font-size:clamp(2rem,9vw,2.5rem); line-height:1; letter-spacing:-0.04em; color:#1a1a1a;">MICHELIN</div>
          <div style="font-family:'Noto Sans',sans-serif; font-weight:400; font-size:clamp(2rem,9vw,2.5rem); line-height:1; letter-spacing:-0.04em; color:#BA0B2F; font-style:italic;">VERIFY</div>
        </div>
        <!-- Sous-titre — Noto Sans 400, 13px -->
        <p style="font-family:'Noto Sans',sans-serif; font-weight:400; font-size:0.8125rem; line-height:1.6; color:rgba(26,26,26,0.5); max-width:220px; margin:0 auto;">
          L'expertise de nos inspecteurs au service de votre curiosité digitale.
        </p>
    </div>

    <!-- Search Input -->
    <div class="mb-12 relative z-10">
        <div class="bg-white/80 backdrop-blur-xl border-2 border-michelin-border rounded-[24px] p-2 flex shadow-2xl shadow-michelin-red/5 focus-within:border-michelin-red transition-all duration-500 group">
             <input 
                v-model="viralUrl"
                type="text" 
                placeholder="Collez un lien TikTok ou Instagram..."
                style="font-family:'Noto Sans',sans-serif; font-weight:400; font-size:0.875rem;"
                class="flex-1 px-5 py-4 outline-none bg-transparent placeholder:text-michelin-dark/20"
             />
             <button 
                @click="analyzeUrl"
                :disabled="loading"
                class="bg-michelin-red text-white w-14 h-14 rounded-[18px] flex items-center justify-center transition-all active:scale-90 shadow-lg shadow-michelin-red/30 disabled:opacity-50"
             >
                <Search v-if="!loading" class="w-6 h-6" />
                <Loader2 v-else class="w-6 h-6 animate-spin" />
             </button>
        </div>
    </div>

    <!-- Loading / Scanner State -->
    <div v-if="loading" class="fixed inset-0 z-[100] bg-white flex flex-center items-center justify-center p-10 animate-fade-in">
        <div class="w-full max-w-[280px] text-center">
            <div class="relative w-48 h-64 mx-auto mb-10 bg-michelin-gray rounded-3xl overflow-hidden border border-michelin-border shadow-inner">
                <!-- Mascot / Thinking Silhouette -->
                <div class="absolute inset-0 flex items-center justify-center opacity-10">
                    <img src="https://upload.wikimedia.org/wikipedia/fr/b/bd/Bibendum_Michelin.svg" class="w-32 animate-pulse-slow" />
                </div>
                
                <!-- Scanner Beam -->
                <div class="scanner-beam absolute left-0 right-0 h-1 bg-gradient-to-r from-transparent via-michelin-red to-transparent shadow-[0_0_15px_#BA0B2F] z-20"></div>
                
                <!-- Data Particles Mockup -->
                <div class="absolute inset-x-2 bottom-4 h-32 flex flex-col gap-2">
                    <div v-for="i in 5" :key="i" class="h-1 bg-michelin-dark/5 rounded-full overflow-hidden">
                        <div class="h-full bg-michelin-red/20" :style="{ width: Math.random() * 80 + '%', transition: 'width 0.5s ease' }"></div>
                    </div>
                </div>
            </div>
            
            <div style="font-family:'Noto Sans',sans-serif; font-weight:600; font-size:1.0625rem; letter-spacing:-0.01em; color:#1a1a1a; margin-bottom:0.5rem;">Analyse en cours...</div>
            <div style="font-family:'Noto Sans',sans-serif; font-weight:700; font-size:9px; letter-spacing:0.18em; text-transform:uppercase; color:#BA0B2F;" class="animate-pulse">{{ currentStepText }}</div>
        </div>
    </div>

    <!-- Analysis Result Mobile -->
    <div v-if="analysis && !loading" class="space-y-6 mb-12 animate-slide-up relative z-10">
        <div class="bg-white rounded-[40px] border border-michelin-border overflow-hidden shadow-2xl relative">
             <!-- Premium Header -->
             <div class="absolute top-6 left-6">
                <div class="w-3 h-3 rounded-full" :style="{ backgroundColor: analysis.advisoryColor }"></div>
             </div>
             
             <div class="pt-12 pb-8 px-8 text-center border-b border-michelin-border">
                <div class="relative inline-block mb-6">
                    <div class="text-michelin-red text-8xl font-black tracking-tighter drop-shadow-sm">
                        {{ analysis.score }}
                    </div>
                    <div class="absolute -right-6 top-2 text-xs font-black text-michelin-dark/20 ring-1 ring-michelin-border px-2 py-1 rounded-md">
                        /10
                    </div>
                </div>
                
                <h2 class="text-2xl font-black uppercase mb-2 tracking-tight text-michelin-dark">{{ analysis.name }}</h2>
                <div class="flex items-center justify-center gap-1 mb-6">
                    <Star v-for="s in 3" :key="s" 
                          :class="['w-4 h-4', s <= (analysis.stars || 0) ? 'text-michelin-red fill-michelin-red' : 'text-michelin-border']" />
                </div>
                
                <p class="text-[11px] font-bold uppercase tracking-widest px-4 py-2 rounded-full border border-michelin-border inline-block bg-michelin-gray">
                    {{ analysis.advisory }}
                </p>
             </div>
             
             <div class="p-8 bg-michelin-gray/30">
                <blockquote class="text-[13px] font-medium leading-relaxed text-michelin-dark/80 italic mb-10 text-center px-2">
                    "{{ analysis.description }}"
                </blockquote>
                
                <div :class="[
                    'p-6 rounded-[24px] flex items-center justify-between shadow-lg',
                    analysis.score >= 7.5 ? 'bg-michelin-red' : 'bg-michelin-dark'
                ]">
                    <div class="flex items-center gap-4">
                        <div class="w-10 h-10 rounded-full bg-white/20 flex items-center justify-center">
                            <ShieldCheck v-if="analysis.score >= 7.5" class="w-5 h-5 text-white" />
                            <AlertTriangle v-else class="w-5 h-5 text-white" />
                        </div>
                        <div class="text-white">
                            <div class="text-[8px] font-black opacity-60 uppercase tracking-widest">Recommandation</div>
                            <div class="text-base font-black italic">{{ analysis.score >= 7.5 ? 'CERTIFIÉ' : 'DÉCONSEILLÉ' }}</div>
                        </div>
                    </div>
                    <button @click="analysis = null" class="w-10 h-10 rounded-full border border-white/20 flex items-center justify-center">
                        <X class="w-4 h-4 text-white" />
                    </button>
                </div>
             </div>
        </div>
        
        <button 
            @click="analysis = null"
            class="w-full py-5 rounded-2xl border-2 border-michelin-dark/5 text-[10px] font-black uppercase tracking-widest text-michelin-dark/40"
        >
            Nouvelle analyse
        </button>
    </div>

    <!-- Recent History Mobile -->
    <div v-if="!analysis && !loading" class="relative z-10">
        <div class="flex items-center justify-between mb-8">
            <!-- Section label — Noto Sans 700 10px tracked uppercase -->
            <div style="font-family:'Noto Sans',sans-serif; font-weight:700; font-size:9px; letter-spacing:0.18em; text-transform:uppercase; color:rgba(26,26,26,0.35);">Dernières vérifications</div>
            <div class="h-[1px] flex-1 mx-4 bg-michelin-border"></div>
        </div>
        
        <div class="space-y-3">
            <div v-for="item in recentAnalyses" :key="item.id" 
                 @click="recallAnalysis(item)"
                 class="bg-white border border-michelin-border p-5 rounded-2xl flex justify-between items-center active:scale-[0.97] transition-all group hover:shadow-md">
                 <div class="flex items-center gap-5">
                    <div class="w-12 h-12 rounded-xl bg-michelin-gray flex flex-col items-center justify-center">
                        <!-- Score — Noto Serif 400 -->
                        <span style="font-family:'Noto Serif',serif; font-weight:400; font-size:1rem; color:#BA0B2F;">{{ item.score }}</span>
                        <span style="font-family:'Noto Sans',sans-serif; font-weight:700; font-size:7px; letter-spacing:0.14em; text-transform:uppercase; color:rgba(186,11,47,0.4);">SCORE</span>
                    </div>
                    <div>
                        <!-- Restaurant name — Noto Sans 600 -->
                        <div style="font-family:'Noto Sans',sans-serif; font-weight:600; font-size:0.875rem; letter-spacing:-0.005em; color:#1a1a1a;">{{ item.name }}</div>
                        <!-- Platform — Noto Sans 400 small -->
                        <div style="font-family:'Noto Sans',sans-serif; font-weight:400; font-size:0.75rem; color:rgba(26,26,26,0.4); margin-top:1px;">{{ item.platform }}</div>
                    </div>
                 </div>
                 <ChevronRight class="w-5 h-5 text-michelin-border group-hover:text-michelin-red transition-colors" />
            </div>
        </div>
    </div>
  </div>
</template>

<script setup>
import { useFactCheck } from '../composables/useFactCheck'
import { Search, Star, ShieldCheck, AlertTriangle, X, ChevronRight, Loader2 } from 'lucide-vue-next'

const {
    viralUrl,
    loading,
    analysis,
    recentAnalyses,
    currentStepText,
    analyzeUrl,
    recallAnalysis
} = useFactCheck()
</script>

<style scoped>
.animate-slide-up {
    animation: slideUp 0.6s cubic-bezier(0.165, 0.84, 0.44, 1) forwards;
}

.scanner-beam {
    animation: scan 2s infinite ease-in-out;
}

.animate-pulse-slow {
    animation: pulseSlow 3s infinite ease-in-out;
}

@keyframes slideUp {
    from { opacity: 0; transform: translateY(40px); }
    to { opacity: 1; transform: translateY(0); }
}

@keyframes scan {
    0% { top: 0%; box-shadow: 0 0 10px #BA0B2F; }
    50% { top: 98%; box-shadow: 0 0 20px #BA0B2F; }
    100% { top: 0%; box-shadow: 0 0 10px #BA0B2F; }
}

@keyframes pulseSlow {
    0%, 100% { opacity: 0.05; transform: scale(1); }
    50% { opacity: 0.15; transform: scale(1.05); }
}
</style>

