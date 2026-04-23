<template>
  <div
    class="bg-[#FBFBFB] min-h-screen font-sans text-michelin-dark pb-20 selection:bg-michelin-red selection:text-white"
  >
    <main class="max-w-6xl mx-auto px-6 pt-16">
      <!-- Header -->
      <div class="text-center mb-16">
        <div
          class="inline-flex items-center gap-2 bg-michelin-red/5 border border-michelin-red/10 rounded-full px-4 py-1 mb-8"
        >
          <div
            class="w-1.5 h-1.5 rounded-full bg-michelin-red animate-pulse"
          ></div>
          <span
            class="text-[9px] text-michelin-red font-black uppercase tracking-widest"
            >IA Émotionnelle Michelin</span
          >
        </div>

        <h1
          class="text-5xl md:text-6xl font-serif font-black text-michelin-dark mb-4 tracking-tighter"
        >
          Quelle est ta <span class="text-michelin-red italic">vibe ?</span>
        </h1>
        <p
          class="text-michelin-dark/40 text-sm max-w-lg mx-auto leading-relaxed"
        >
          Oublie les filtres. Dis-nous ton humeur, on trouve l'adresse.
        </p>
      </div>

      <!-- Refactored Components -->
      <MoodGrid
        :moods="moods"
        :selectedMood="selectedMood"
        @select="selectMood"
      />

      <CustomVibeInput
        v-if="selectedMood?.id === 'custom'"
        v-model="customVibeText"
        @submit="submitCustomVibe"
      />

      <!-- Loading State -->
      <div v-if="loading" class="max-w-3xl mx-auto text-center py-20">
        <div
          class="w-20 h-20 mx-auto mb-8 rounded-full border-t-2 border-michelin-red animate-spin"
        ></div>
        <h3 class="text-2xl font-serif font-bold mb-3 text-michelin-dark">
          Le concierge réfléchit...
        </h3>
        <p class="text-sm text-michelin-dark/40">{{ loadingText }}</p>
      </div>

      <!-- Results Section -->
      <VibeResults
        v-if="vibeResults && !loading"
        :vibeResults="vibeResults"
        :selectedMood="selectedMood"
        @reset="resetVibes"
      />

      <!-- Empty State -->
      <div
        v-if="!loading && !vibeResults && !selectedMood"
        class="max-w-xl mx-auto text-center py-20"
      >
        <div class="text-6xl mb-8">🍷</div>
        <h3 class="text-2xl font-serif font-bold text-michelin-dark/30 mb-4">
          Choisis ton ambiance
        </h3>
        <p class="text-sm text-michelin-dark/20">
          Clique sur une vibe ci-dessus et laisse le concierge Michelin faire le
          reste.
        </p>
      </div>
    </main>
  </div>
</template>

<script setup>
import { useVibes } from "../composables/useVibes";
import MoodGrid from "./vibes/MoodGrid.vue";
import CustomVibeInput from "./vibes/CustomVibeInput.vue";
import VibeResults from "./vibes/VibeResults.vue";

const {
  moods,
  selectedMood,
  loading,
  loadingText,
  vibeResults,
  customVibeText,
  selectMood,
  submitCustomVibe,
  resetVibes,
} = useVibes();
</script>

<style scoped>
@keyframes fade-up {
  from {
    opacity: 0;
    transform: translateY(20px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}
:deep(.animate-fade-up) {
  animation: fade-up 0.6s ease-out both;
}
</style>
