<template>
  <div
    class="min-h-screen bg-michelin-dark text-white font-sans selection:bg-michelin-red selection:text-white"
  >
    <AppHeader :tabs="tabs" :current-tab="currentTab" @select-tab="goToTab" />

    <main>
      <RouterView />
    </main>

    <AppFooter :tabs="tabs" @select-tab="goToTab" />
  </div>
</template>

<script setup>
import { ref, watch } from "vue";
import { RouterView, useRoute, useRouter } from "vue-router";
import AppFooter from "../components/AppFooter.vue";
import AppHeader from "../components/AppHeader.vue";

const currentTab = ref("explorer");

const tabs = [
  { id: "explorer", label: "EXPLORER" },
  { id: "factcheck", label: "FACT-CHECK" },
  { id: "vibes", label: "VIBES" },
  { id: "passport", label: "PASSEPORT" },
  { id: "snack", label: "SNACK VIDEO" },
];

const router = useRouter();
const route = useRoute();

const isKnownTab = (tabId) => tabs.some((t) => t.id === tabId);

const tabFromRoute = () => {
  if (route.name === "explorer" || route.path === "/") return "explorer";
  const tabParam = route.params.tab;
  return typeof tabParam === "string" ? tabParam : "explorer";
};

const goToTab = async (tabId) => {
  if (!isKnownTab(tabId)) return;

  currentTab.value = tabId;
  const targetPath = tabId === "explorer" ? "/" : `/${tabId}`;
  if (route.path !== targetPath) await router.push(targetPath);
};

watch(
  () => route.fullPath,
  () => {
    const tabId = tabFromRoute();
    if (isKnownTab(tabId) && currentTab.value !== tabId)
      currentTab.value = tabId;
  },
  { immediate: true },
);
</script>

<style>
@import url("https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap");

:root {
  --font-outfit: "Outfit", sans-serif;
}

body {
  font-family: var(--font-outfit);
}

.rcib1 {
  background: radial-gradient(circle at top right, #331a1a, #1a0f0f);
}
.rcib2 {
  background: radial-gradient(circle at top right, #1a1a33, #0f0f1a);
}
.rcib3 {
  background: radial-gradient(circle at top right, #1a3324, #0a1a12);
}
.rcib4 {
  background: radial-gradient(circle at top right, #332b1a, #1a1500);
}

/* Custom Scrollbar */
::-webkit-scrollbar {
  width: 8px;
}
::-webkit-scrollbar-track {
  background: #0a0a0a;
}
::-webkit-scrollbar-thumb {
  background: #222;
  border-radius: 10px;
}
::-webkit-scrollbar-thumb:hover {
  background: #333;
}
</style>
