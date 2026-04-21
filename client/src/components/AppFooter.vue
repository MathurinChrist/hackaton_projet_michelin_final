<template>
  <footer
    class="bg-[#050505] py-20 px-6 md:px-12 lg:px-20 border-t border-white/5"
  >
    <div
      class="max-w-7xl mx-auto flex flex-col md:flex-row justify-between items-center gap-12"
    >
      <div class="flex flex-col items-center md:items-start gap-4">
        <div class="flex items-center gap-1.5">
          <div class="w-2.5 h-2.5 rounded-full bg-michelin-red"></div>
          <div
            class="w-1.5 h-1.5 rounded-full bg-michelin-red opacity-50"
          ></div>
          <div class="w-1 h-1 rounded-full bg-michelin-red opacity-30"></div>
          <span
            class="text-[11px] text-white font-bold ml-3 tracking-[0.2em] uppercase"
            >Michelin Guide</span
          >
        </div>
        <div
          class="text-[11px] text-white/20 uppercase font-bold tracking-widest"
        >
          L'excellence pour la nouvelle génération
        </div>
      </div>

      <div
        class="text-[11px] text-white/30 flex flex-wrap justify-center gap-x-12 gap-y-4 uppercase font-bold tracking-widest px-8 md:px-0"
      >
        <a
          v-for="tab in tabs"
          :key="tab.id"
          href="#"
          @click.prevent="$emit('select-tab', tab.id)"
          class="hover:text-michelin-red transition-colors"
          >{{ tab.label }}</a
        >
      </div>

      <div class="flex flex-col items-center md:items-end gap-3">
        <div
          class="text-[10px] text-white/20 font-bold tracking-widest uppercase"
        >
          © 2025 MICHELIN GUIDE · CONCEPT MVP
        </div>
        <div class="flex gap-4">
          <div
            class="w-8 h-8 rounded-full border border-white/10 flex items-center justify-center hover:border-michelin-red transition-colors cursor-pointer"
          >
            <Instagram class="w-3.5 h-3.5 text-white/40" />
          </div>
          <div
            class="w-8 h-8 rounded-full border border-white/10 flex items-center justify-center hover:border-michelin-red transition-colors cursor-pointer"
          >
            <Youtube class="w-3.5 h-3.5 text-white/40" />
          </div>
        </div>
      </div>
    </div>
  </footer>
</template>

<script setup>
import { Instagram, Youtube } from "lucide-vue-next";

defineProps({
  tabs: { type: Array, required: true },
});

defineEmits(["select-tab"]);
</script>
