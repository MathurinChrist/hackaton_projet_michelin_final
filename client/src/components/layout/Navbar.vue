<template>
    <nav class="bg-[#BA0B2F] sticky top-0 z-[100]">
      <div class="flex items-center justify-between px-8 md:px-16 h-[52px]">
        
        <!-- Logo — Michelin Guide exact style -->
        <div class="flex items-center gap-2 cursor-pointer flex-shrink-0" @click="$emit('update:tab', 'explorer')">
          <div style="font-family:'Noto Sans',sans-serif; font-size:15px; letter-spacing:0.04em; color:white;">
            <span style="font-weight:700;">MICHELIN</span> <span style="font-weight:300; opacity:0.9;">GUIDE</span>
          </div>
        </div>

        <!-- Centre -->
        <div class="hidden md:flex items-center gap-8 h-full">
          <button 
            v-for="tab in tabs" 
            :key="tab.id"
            @click="handleTabClick(tab)"
            :class="[
              'text-[13px] transition-all relative h-full flex items-center whitespace-nowrap',
              isTabActive(tab.id) ? 'text-white font-semibold' : 'text-white/70 hover:text-white font-normal'
            ]"
          >
            {{ tab.label }}
            <ChevronDown v-if="tab.children" class="w-3.5 h-3.5 ml-1 opacity-60" />
            <div v-if="isTabActive(tab.id)" class="absolute bottom-0 left-0 right-0 h-[2px] bg-white"></div>
          </button>
        </div>

        <!-- Dropdown IA Michelin -->
        <div v-if="showIADropdown" class="absolute top-[52px] left-1/2 -translate-x-1/2 bg-white rounded-xl shadow-2xl border border-michelin-border p-2 min-w-[260px] z-50" @mouseleave="$emit('update:dropdown', false)">
          <button 
            v-for="child in iaChildren" 
            :key="child.id"
            @click="$emit('update:tab', child.id); $emit('update:dropdown', false)"
            :class="[
              'w-full flex items-center gap-4 px-5 py-3.5 rounded-lg text-left transition-all',
              currentTab === child.id ? 'bg-michelin-red/5 text-michelin-red' : 'hover:bg-michelin-gray text-michelin-dark'
            ]"
          >
            <component :is="child.icon" class="w-4 h-4" />
            <div>
              <div class="text-[12px] font-bold">{{ child.label }}</div>
              <div class="text-[10px] text-michelin-dark/40">{{ child.description }}</div>
            </div>
          </button>
        </div>

        <!-- Droite : Profil -->
        <div class="flex items-center">
          <div class="w-8 h-8 rounded-full border-2 border-white/40 flex items-center justify-center cursor-pointer hover:border-white transition-colors">
            <User class="w-4 h-4 text-white" />
          </div>
        </div>
      </div>
    </nav>
</template>

<script setup>
import { ChevronDown, User } from 'lucide-vue-next'

const props = defineProps({
    currentTab: String,
    tabs: Array,
    iaChildren: Array,
    showIADropdown: Boolean
})

const emit = defineEmits(['update:tab', 'update:dropdown'])

const handleTabClick = (tab) => {
    if (tab.children) {
        emit('update:dropdown', !props.showIADropdown)
    } else {
        emit('update:tab', tab.id)
        emit('update:dropdown', false)
    }
}

const isTabActive = (tabId) => {
    if (tabId === 'ia') return props.currentTab === 'factcheck' || props.currentTab === 'vibes'
    return props.currentTab === tabId
}
</script>
