<template>
  <div class="snack-container flex flex-col items-center justify-center bg-black min-h-[calc(100vh-60px)] pb-24">
    <!-- Phone Frame / Reel Player (Full screen for mobile) -->
    <div
      class="relative w-full h-full flex-1 overflow-hidden bg-black select-none touch-none group"
      role="application"
      tabindex="0"
      aria-label="Snack videos"
      @pointerdown="onPointerDown"
      @pointerup="onPointerUp"
      @pointercancel="onPointerCancel"
      @wheel.passive="onWheel"
      @keydown.up.prevent="prev"
      @keydown.down.prevent="next"
    >
      <!-- Video Element -->
      <video
        ref="videoEl"
        class="h-full w-full object-cover transition-opacity duration-500"
        :src="currentSrc"
        :key="currentSrc"
        muted
        playsinline
        loop
        preload="metadata"
        @click="togglePlay"
      />

      <!-- Play Overlay (Centered) -->
      <div v-if="!isPlaying" class="absolute inset-0 flex items-center justify-center bg-black/20 pointer-events-none">
        <div class="w-20 h-20 rounded-full bg-white/20 backdrop-blur-md flex items-center justify-center animate-pulse">
           <Play class="w-10 h-10 text-white fill-white" />
        </div>
      </div>

      <!-- Bottom Gradient Overlay -->
      <div class="absolute inset-x-0 bottom-0 h-1/2 bg-gradient-to-t from-black/80 via-black/40 to-transparent pointer-events-none" />

      <!-- Side Actions (Right) -->
      <div class="absolute right-4 bottom-1/4 flex flex-col gap-6 z-10">
        <button class="flex flex-col items-center gap-1 group/btn" @click.stop="toggleLike">
          <div class="w-12 h-12 rounded-full bg-white/10 backdrop-blur-md flex items-center justify-center border border-white/20 group-hover/btn:bg-michelin-red group-hover/btn:border-michelin-red transition-all transform active:scale-90">
            <Heart :class="{'fill-michelin-red text-michelin-red': isLiked, 'text-white': !isLiked}" class="w-6 h-6 transition-colors" />
          </div>
          <span class="text-[10px] font-bold text-white uppercase tracking-tighter">1.2k</span>
        </button>
      </div>

      <!-- Bottom Info Overlay -->
      <div class="absolute inset-x-0 bottom-0 p-6 pt-12 text-white z-10 pointer-events-none">
        <div class="flex items-center gap-3 mb-3">
          <div class="w-10 h-10 rounded-full bg-michelin-red border-2 border-white flex items-center justify-center overflow-hidden">
             <img src="https://ui-avatars.com/api/?name=Michelin+Chef&background=BA0B2F&color=fff" alt="Chef Avatar" />
          </div>
          <div>
            <h3 class="text-sm font-bold tracking-wide uppercase text-white">{{ currentReel?.author ?? "Michelin Chef" }}</h3>
            <p class="text-[10px] text-white/60 font-medium">Analyse certifiée Guide Michelin</p>
          </div>
        </div>
        
        <h2 class="text-xl font-bold mb-2 leading-tight text-white">
          {{ currentReel?.title ?? "Snack" }}
        </h2>
        
        <p class="text-sm text-white/80 line-clamp-2 mb-6 font-medium">
          {{ currentReel?.description ?? "" }}
        </p>

        <!-- Progress Bar -->
        <div class="w-full h-1 bg-white/20 rounded-full overflow-hidden">
          <div 
            class="h-full bg-michelin-red transition-all duration-100 ease-linear"
            :style="{ width: `${progress}%` }"
          />
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, nextTick, onMounted, onUnmounted, ref } from "vue";
import { Play, Heart } from "lucide-vue-next";
import { snackReels } from "../mocks/snackReels";

const reels = snackReels;
const index = ref(0);
const videoEl = ref(null);
const isPlaying = ref(false);
const isLiked = ref(false);
const progress = ref(0);

const currentReel = computed(() => reels[index.value] ?? reels[0] ?? null);
const currentSrc = computed(() => currentReel.value?.src ?? "");

const togglePlay = () => {
  const el = videoEl.value;
  if (!el) return;
  if (el.paused) {
    el.play();
    isPlaying.value = true;
  } else {
    el.pause();
    isPlaying.value = false;
  }
};

const toggleLike = () => {
  isLiked.value = !isLiked.value;
};

const updateProgress = () => {
  if (videoEl.value) {
    progress.value = (videoEl.value.currentTime / videoEl.value.duration) * 100;
  }
};

const playCurrent = async () => {
  await nextTick();
  const el = videoEl.value;
  if (!el) return;
  progress.value = 0;
  isLiked.value = false;
  try {
    await el.play();
    isPlaying.value = true;
  } catch {
    isPlaying.value = false;
  }
};

const next = async () => {
  if (reels.length <= 1) return;
  index.value = (index.value + 1) % reels.length;
  await playCurrent();
};

const prev = async () => {
  if (reels.length <= 1) return;
  index.value = (index.value - 1 + reels.length) % reels.length;
  await playCurrent();
};

// Pointer Events for Swipe
const pointerStartY = ref(null);
const pointerStartAt = ref(0);

const onPointerDown = (e) => {
  if (typeof e.button === "number" && e.button !== 0) return;
  pointerStartY.value = e.clientY;
  pointerStartAt.value = Date.now();
};

const SWIPE_THRESHOLD_PX = 60;
const SWIPE_MAX_DURATION_MS = 700;

const onPointerUp = async (e) => {
  if (pointerStartY.value === null) return;

  const deltaY = e.clientY - pointerStartY.value;
  const duration = Date.now() - pointerStartAt.value;
  pointerStartY.value = null;

  if (duration > SWIPE_MAX_DURATION_MS) return;
  if (Math.abs(deltaY) < SWIPE_THRESHOLD_PX) return;

  if (deltaY < 0) await next();
  else await prev();
};

const onPointerCancel = () => {
  pointerStartY.value = null;
};

// Wheel events
let wheelCooldown = false;
const onWheel = async (e) => {
  if (wheelCooldown) return;
  if (Math.abs(e.deltaY) < 10) return;

  wheelCooldown = true;
  window.setTimeout(() => {
    wheelCooldown = false;
  }, 500);

  if (e.deltaY > 0) await next();
  else await prev();
};

let progressInterval;

onMounted(() => {
  playCurrent();
  progressInterval = setInterval(updateProgress, 100);
});

onUnmounted(() => {
  clearInterval(progressInterval);
});
</script>

<style scoped>
.snack-container {
  overflow: hidden;
}

[role="application"] {
  outline: none;
}

.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>
