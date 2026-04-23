<template>
  <div
    class="min-h-[80vh] flex flex-col items-center justify-center py-32 px-12 text-center bg-[#0a0a0a]"
  >
    <div class="relative">
      <div
        class="absolute -inset-20 bg-michelin-red/10 blur-[100px] rounded-full"
      ></div>
      <div class="relative z-10">
        <div
          class="flex items-center gap-[7px] bg-michelin-red/[0.12] border border-michelin-red/[0.35] rounded-full px-5 py-2 mb-10 mx-auto w-fit"
        >
          <div
            class="w-1.5 h-1.5 rounded-full bg-michelin-red animate-pulse"
          ></div>
          <span
            class="text-[11px] text-[#FF5555] tracking-widest font-bold uppercase"
            >Module {{ currentTab }}</span
          >
        </div>
        <h1
          class="text-5xl md:text-7xl font-bold leading-[1.1] mb-8 tracking-tight max-w-4xl"
        >
          Bientôt disponible en version
          <span class="text-michelin-red not-italic font-serif">Bêta</span>.
        </h1>
        <p class="text-lg text-white/40 mb-12 max-w-xl mx-auto leading-relaxed">
          Nous finalisons l'expérience pour la génération 25-30 ans. Nos
          inspecteurs digitaux vérifient les derniers buzz.
        </p>
        <button
          @click="goToExplorer"
          class="bg-michelin-red text-white text-[12px] font-bold tracking-widest px-12 py-5 rounded-full hover:shadow-[0_12px_24px_rgba(204,0,0,0.2)] transition-all"
        >
          RETOUR À L'EXPLORATION
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from "vue";
import { useRoute, useRouter } from "vue-router";

const router = useRouter();
const route = useRoute();

const currentTab = computed(() => {
  const tabParam = route.params.tab;
  return typeof tabParam === "string" ? tabParam : "explorer";
});

const goToExplorer = async () => {
  await router.push("/");
};
</script>
