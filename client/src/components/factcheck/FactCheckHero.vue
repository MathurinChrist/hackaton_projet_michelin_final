<template>
    <section class="bg-white border-b border-michelin-border">
      <div class="max-w-6xl mx-auto px-6 pt-16 pb-12">
        <div class="flex items-start justify-between gap-8">
          <div class="flex-1 max-w-xl">
            <h1 class="mb-4">
              <span class="text-[52px] md:text-[64px] font-black text-michelin-dark leading-none block tracking-tight">MICHELIN</span>
              <span class="text-[52px] md:text-[64px] font-black text-michelin-red leading-none block tracking-tight">VERIFY</span>
            </h1>
            <p class="text-michelin-dark/50 text-sm leading-relaxed max-w-md">
              Michelin Verify analyse les vidéos TikTok pour vous donner un verdict fiable, basé sur l'expertise du Guide Michelin.
            </p>
          </div>

          <div class="hidden md:flex items-start gap-6 relative">
            <div class="bg-white border border-michelin-border rounded-2xl p-5 shadow-sm mt-4 max-w-[200px]">
              <div class="flex items-center gap-2 mb-2">
                <div class="w-5 h-5 rounded-full bg-michelin-red flex items-center justify-center">
                  <Check class="w-3 h-3 text-white" />
                </div>
                <span class="text-[12px] font-bold text-michelin-dark">Je vérifie pour vous.</span>
              </div>
              <p class="text-[11px] text-michelin-dark/40 leading-relaxed">Indépendant, rigoureux,<br/>100% Michelin.</p>
            </div>
            
            <img 
              src="@/assets/Mascotte2.webp" 
              alt="Mascotte Michelin" 
              :class="[
                'w-[200px] h-auto object-contain transition-all duration-500',
                loading ? 'animate-mascot-thinking' : 'animate-mascot-idle'
              ]"
            />
          </div>
        </div>
        
        <div class="mt-10 max-w-2xl">
          <div class="bg-white rounded-full border-2 border-michelin-border flex items-center p-1.5 focus-within:border-michelin-red transition-colors">
            <div class="w-10 h-10 rounded-full bg-michelin-gray flex items-center justify-center ml-2">
              <LinkIcon class="w-4 h-4 text-michelin-dark/30" />
            </div>
            <input 
              :value="modelValue"
              @input="$emit('update:modelValue', $event.target.value)"
              @keyup.enter="$emit('analyze')"
              type="text" 
              placeholder="Collez l'URL TikTok ou Instagram ici..." 
              class="flex-1 px-4 py-3 bg-transparent outline-none text-sm font-medium placeholder:text-michelin-dark/30"
            />
            <button 
              @click="$emit('analyze')"
              :disabled="loading"
              class="bg-michelin-red text-white font-bold text-[13px] tracking-wider px-8 py-3.5 rounded-full hover:bg-[#A70A2A] transition-all uppercase"
            >
              {{ loading ? 'Analyse...' : 'ANALYSER' }}
            </button>
          </div>
          <p class="mt-3 text-[11px] text-michelin-dark/30 flex items-center gap-1.5">
            <Shield class="w-3 h-3" /> Aucune donnée personnelle collectée
          </p>
        </div>
      </div>
    </section>
</template>

<script setup>
import { Check, Link as LinkIcon, Shield } from 'lucide-vue-next'

defineProps({
    modelValue: String,
    loading: Boolean
})

defineEmits(['update:modelValue', 'analyze'])
</script>

<style scoped>
@keyframes mascot-idle {
    0%, 100% { transform: translateY(0px); }
    50% { transform: translateY(-8px); }
}
.animate-mascot-idle {
    animation: mascot-idle 3s ease-in-out infinite;
}

@keyframes mascot-thinking {
    0% { transform: translateY(0px) rotate(0deg) scale(1); }
    15% { transform: translateY(-12px) rotate(-3deg) scale(1.03); }
    30% { transform: translateY(0px) rotate(2deg) scale(1); }
    45% { transform: translateY(-10px) rotate(-2deg) scale(1.02); }
    60% { transform: translateY(0px) rotate(1deg) scale(1); }
    75% { transform: translateY(-8px) rotate(-1deg) scale(1.01); }
    100% { transform: translateY(0px) rotate(0deg) scale(1); }
}
.animate-mascot-thinking {
    animation: mascot-thinking 1.5s ease-in-out infinite;
    filter: drop-shadow(0 10px 20px rgba(178, 11, 36, 0.15));
}
</style>
