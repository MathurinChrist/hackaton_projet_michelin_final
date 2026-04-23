<template>
  <div class="px-6 md:px-24 py-4">
    <div
      class="mx-auto w-full max-w-105 select-none touch-none"
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
      <div class="aspect-10/16 w-full overflow-hidden rounded-2xl bg-black">
        <video
          ref="videoEl"
          class="h-3/4 w-full object-cover"
          :src="currentSrc"
          controls
          muted
          playsinline
          loop
          preload="metadata"
        />
        <div class="mt-4">
          <h2 class="text-xl font-semibold text-slate-50">
            {{ currentReel?.title ?? "Snack" }}
          </h2>
          <p class="mt-1 text-sm text-slate-300">
            {{ currentReel?.author ?? ""
            }}<span v-if="currentReel?.author && currentReel?.date"> · </span
            >{{ currentReel?.date ?? "" }}
          </p>
          <p class="mt-3 text-sm leading-relaxed text-slate-400">
            {{ currentReel?.description ?? "" }}
          </p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, nextTick, onMounted, ref } from "vue";
import { snackReels } from "../mocks/snackReels";

const reels = snackReels;

const index = ref(0);
const videoEl = ref(null);

const currentReel = computed(() => reels[index.value] ?? reels[0] ?? null);

const currentSrc = computed(() => {
  return currentReel.value?.src ?? "";
});

const playCurrent = async () => {
  await nextTick();
  const el = videoEl.value;
  if (!el) return;
  try {
    await el.play();
  } catch {
    // Autoplay peut être bloqué (selon navigateur). Les contrôles restent dispo.
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

let wheelCooldown = false;
const onWheel = async (e) => {
  if (wheelCooldown) return;
  if (Math.abs(e.deltaY) < 10) return;

  wheelCooldown = true;
  window.setTimeout(() => {
    wheelCooldown = false;
  }, 350);

  if (e.deltaY > 0) await next();
  else await prev();
};

onMounted(() => {
  playCurrent();
});
</script>
