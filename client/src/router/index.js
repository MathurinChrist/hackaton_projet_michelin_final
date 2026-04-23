import { createRouter, createWebHistory } from "vue-router";
import ExplorerView from "../views/ExplorerView.vue";
import PlaceholderView from "../views/PlaceholderView.vue";
import SnackView from "../views/SnackView.vue";

const routes = [
  {
    path: "/",
    name: "explorer",
    meta: { tab: "explorer" },
    component: ExplorerView,
  },
  {
    path: "/:tab(snack)",
    name: "snack",
    meta: { tab: "snack" },
    component: SnackView,
  },
  {
    path: "/:tab(factcheck|vibes|passport)",
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
