<template>
  <header>
    <!-- Navbar -->
    <nav
      class="flex items-center justify-between px-6 md:px-12 h-[70px] border-b border-michelin-border bg-michelin-dark sticky top-0 z-[100]"
    >
      <div class="flex items-center gap-3">
        <div class="flex items-end gap-[3px]">
          <div class="w-[11px] h-[11px] rounded-full bg-michelin-red"></div>
          <div class="w-2 h-2 rounded-full bg-michelin-red opacity-70"></div>
          <div
            class="w-1.5 h-1.5 rounded-full bg-michelin-red opacity-45"
          ></div>
        </div>
        <div class="text-[13px] font-semibold tracking-widest uppercase">
          Michelin Guide
        </div>
      </div>

      <div class="hidden md:flex gap-8 h-full">
        <button
          v-for="tab in tabs"
          :key="tab.id"
          @click="$emit('select-tab', tab.id)"
          :class="[
            'text-xs transition-colors relative h-full flex items-center px-2',
            currentTab === tab.id
              ? 'text-white font-medium'
              : 'text-white/40 hover:text-white',
          ]"
        >
          {{ tab.label }}
          <div
            v-if="currentTab === tab.id"
            class="absolute bottom-0 left-0 right-0 h-[2.5px] bg-michelin-red shadow-[0_-4px_12px_rgba(204,0,0,0.4)]"
          ></div>
        </button>
      </div>

      <div class="flex items-center gap-6">
        <button
          class="text-xs text-white/40 hover:text-white transition-colors font-medium"
        >
          Connexion
        </button>
        <button
          class="bg-michelin-red text-white text-[11px] font-bold px-6 py-2.5 rounded-full hover:bg-[#e60000] hover:scale-105 active:scale-95 transition-all shadow-lg shadow-michelin-red/20"
        >
          REJOINDRE GRATUITEMENT
        </button>
      </div>
    </nav>
  </header>
</template>

<script setup>
defineProps({
  tabs: { type: Array, required: true },
  currentTab: { type: String, required: true },
});

defineEmits(["select-tab"]);
</script>
