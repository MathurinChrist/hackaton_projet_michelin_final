<template>
  <div
    class="min-h-screen bg-white text-michelin-dark font-sans selection:bg-michelin-red selection:text-white"
  >
    <Navbar
      :currentTab="currentTab"
      :tabs="tabs"
      :iaChildren="iaChildren"
      :showIADropdown="showIADropdown"
      @update:tab="currentTab = $event"
      @update:dropdown="showIADropdown = $event"
    />

    <main v-if="currentTab === 'explorer'">
      <ExplorerHero :restaurants="restaurants" :iconMap="iconMap" />

      <ExplorerFilters
        v-model:searchEntry="searchEntry"
        v-model:filterStars="filterStars"
      />

      <RestaurantGrid :filteredRestaurants="filteredRestaurants" />

      <!-- KPI Section -->
      <section class="max-w-7xl mx-auto px-6 md:px-12 py-32">
        <div
          class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-[1px] bg-michelin-border border border-michelin-border rounded-[40px] overflow-hidden"
        >
          <div
            v-for="(kpi, i) in kpis"
            :key="i"
            class="bg-white p-12 group hover:bg-michelin-gray transition-all"
          >
            <div class="flex items-center gap-4 mb-6">
              <div
                class="w-12 h-12 rounded-2xl flex items-center justify-center bg-michelin-red/5 text-michelin-red font-bold"
              >
                <component :is="kpi.icon" class="w-5 h-5" />
              </div>
              <span
                class="text-[10px] font-black text-michelin-dark/30 tracking-widest uppercase"
                >{{ kpi.label }}</span
              >
            </div>
            <div class="text-5xl font-bold mb-3 font-serif">
              {{ kpi.value }}
            </div>
            <div class="text-sm text-michelin-dark/20 font-medium">
              {{ kpi.sub }}
            </div>
          </div>
        </div>
      </section>
    </main>

    <FactCheck v-else-if="currentTab === 'factcheck'" />
    <Vibes v-else-if="currentTab === 'vibes'" />
    <Snack v-else-if="currentTab === 'snack'" />

    <!-- Footer -->
    <footer
      class="bg-white py-20 px-6 md:px-12 lg:px-20 border-t border-michelin-border"
    >
      <div
        class="max-w-7xl mx-auto flex flex-col md:flex-row justify-between items-center gap-12"
      >
        <div
          class="text-[12px] font-bold tracking-widest uppercase text-michelin-dark"
        >
          Michelin Guide
        </div>
        <div
          class="text-[10px] text-michelin-dark/30 font-black tracking-widest uppercase"
        >
          © 2025 MICHELIN GUIDE · CONCEPT MVP
        </div>
        <div class="flex gap-4">
          <Instagram
            class="w-5 h-5 text-michelin-dark/20 hover:text-michelin-red transition-colors"
          />
          <Youtube
            class="w-5 h-5 text-michelin-dark/20 hover:text-michelin-red transition-colors"
          />
        </div>
      </div>
    </footer>
  </div>
</template>

<script setup>
import { computed, markRaw } from "vue";
import {
  Check,
  Flame,
  Globe,
  Award,
  Instagram,
  Youtube,
} from "lucide-vue-next";
import { useApp } from "./composables/useApp";

// Components
import Navbar from "./components/layout/Navbar.vue";
import ExplorerHero from "./components/explorer/ExplorerHero.vue";
import ExplorerFilters from "./components/explorer/ExplorerFilters.vue";
import RestaurantGrid from "./components/explorer/RestaurantGrid.vue";
import FactCheck from "./components/FactCheck.vue";
import Vibes from "./components/Vibes.vue";
import Snack from "./components/Snack.vue";

const {
  currentTab,
  restaurants,
  showIADropdown,
  searchEntry,
  filterStars,
  tabs,
  iaChildren,
  iconMap,
  filteredRestaurants,
} = useApp();

const kpis = computed(() => {
  return [
    {
      label: "Fact-Check",
      value: "2 847",
      sub: "Tendances analysées",
      icon: markRaw(Check),
    },
    {
      label: "Mood Match",
      value: "94%",
      sub: "Satisfaction vibe",
      icon: markRaw(Flame),
    },
    { label: "Passport", value: "128k", sub: "Inscrits", icon: markRaw(Globe) },
    {
      label: "Badges",
      value: "19k",
      sub: "Cette semaine",
      icon: markRaw(Award),
    },
  ];
});
</script>

<style>
@import url("https://fonts.googleapis.com/css2?family=Noto+Sans:ital,wght@0,100..900;1,100..900&family=Noto+Serif:ital,wght@0,100..900;1,100..900&display=swap");
body {
  font-family: "Noto Sans", system-ui, sans-serif;
  background: white;
  color: #1a1a1a;
  -webkit-font-smoothing: antialiased;
  text-rendering: optimizeLegibility;
}
</style>
