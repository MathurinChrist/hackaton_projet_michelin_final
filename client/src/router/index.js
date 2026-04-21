import { createRouter, createWebHistory } from "vue-router";
import ExplorerView from "../views/ExplorerView.vue";
import PlaceholderView from "../views/PlaceholderView.vue";

const routes = [
  {
    path: "/",
    name: "explorer",
    meta: { tab: "explorer" },
    component: ExplorerView,
  },
  {
    path: "/:tab(factcheck|vibes|passport|snack)",
    name: "tab",
    component: PlaceholderView,
  },
];

const router = createRouter({
  history: createWebHistory(),
  routes,
  scrollBehavior() {
    return { top: 0 };
  },
});

export default router;
