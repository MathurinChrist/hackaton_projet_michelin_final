<template>
  <div>
    <!-- Hero Section -->
    <section
      class="grid grid-cols-1 lg:grid-cols-[1.2fr_1fr] min-h-[600px] border-b border-michelin-border"
    >
      <div
        class="py-20 px-6 md:py-24 md:px-12 lg:px-20 flex flex-col justify-center bg-gradient-to-br from-[#0d0d0d] to-michelin-dark"
      >
        <div
          class="flex items-center gap-[7px] bg-michelin-red/[0.12] border border-michelin-red/[0.35] rounded-full px-4 py-1.5 mb-10 w-fit"
        >
          <div
            class="w-1.5 h-1.5 rounded-full bg-michelin-red animate-pulse"
          ></div>
          <span
            class="text-[10px] text-[#FF5555] tracking-widest font-bold uppercase"
            >Nouvelle expérience · IA embarquée</span
          >
        </div>

        <h1
          class="text-5xl md:text-7xl font-bold leading-[1.05] mb-8 tracking-tight"
        >
          L'autorité<br />gastronomique<br /><span
            class="text-michelin-red italic font-serif"
            >réinventée</span
          >.
        </h1>

        <p class="text-lg text-white/50 leading-relaxed mb-12 max-w-[480px]">
          Finis les pièges Instagram. Trouve l'adresse qui te ressemble —
          validée par un siècle d'expertise, guidée par ton humeur du moment.
        </p>

        <div class="flex flex-wrap gap-5 items-center mb-20">
          <button
            class="bg-michelin-red text-white text-sm font-bold tracking-wide px-10 py-5 rounded-full hover:bg-[#e60000] hover:shadow-[0_12px_24px_rgba(204,0,0,0.25)] hover:scale-[1.02] transition-all duration-300"
          >
            EXPLORER MAINTENANT
          </button>
          <button
            class="flex items-center gap-3 text-sm text-white/70 hover:text-white px-8 py-5 rounded-full border border-white/10 bg-white/[0.02] hover:bg-white/[0.05] hover:border-white/20 transition-all group overflow-hidden relative"
          >
            <div class="flex items-center justify-center relative z-10">
              <div
                class="w-[24px] h-[24px] rounded-full border border-white/20 flex items-center justify-center mr-2 group-hover:border-michelin-red group-hover:bg-michelin-red/10 transition-colors"
              >
                <Play class="w-2.5 h-2.5 fill-white" />
              </div>
              Voir le concept — 90s
            </div>
          </button>
        </div>

        <div
          class="grid grid-cols-2 sm:grid-cols-4 gap-12 pt-12 border-t border-white/[0.07]"
        >
          <div v-for="stat in stats" :key="stat.label" class="group">
            <div
              class="text-3xl font-bold group-hover:text-michelin-red transition-colors"
            >
              {{ stat.value }}
            </div>
            <div
              class="text-[10px] text-white/30 mt-1 tracking-widest uppercase font-semibold"
            >
              {{ stat.label }}
            </div>
          </div>
        </div>
      </div>

      <div
        class="relative overflow-hidden bg-[#0a0a0a] border-l border-michelin-border hidden lg:block"
      >
        <div
          class="grid grid-cols-2 grid-rows-2 h-full gap-[1px] bg-michelin-border"
        >
          <div
            v-for="(card, i) in heroCards"
            :key="i"
            class="group p-10 flex flex-col justify-end relative overflow-hidden transition-all duration-700 cursor-pointer"
          >
            <div
              :class="[
                'absolute inset-0 opacity-40 group-hover:opacity-60 transition-opacity duration-700',
                card.bgGradient,
              ]"
            ></div>
            <div
              class="absolute top-0 left-0 right-0 h-[4px] scale-x-0 group-hover:scale-x-100 transition-transform duration-500 origin-left"
              :style="{ backgroundColor: card.accent }"
            ></div>

            <div class="relative z-10">
              <div class="flex justify-between items-start mb-6">
                <div
                  class="text-[10px] text-white/40 tracking-[0.15em] uppercase font-bold"
                >
                  {{ card.category }}
                </div>
                <div
                  class="w-10 h-10 rounded-full flex items-center justify-center bg-white/[0.05] border border-white/10 group-hover:scale-110 group-hover:border-michelin-red/40 transition-all duration-500"
                >
                  <component
                    :is="card.icon"
                    class="w-4 h-4"
                    :style="{ stroke: card.accent }"
                  />
                </div>
              </div>

              <span
                :class="[
                  'inline-block text-[10px] font-bold tracking-wide px-3 py-1.5 rounded-full mb-3 uppercase',
                  card.tagClass,
                ]"
              >
                {{ card.tag }}
              </span>
              <div class="text-michelin-red text-xs mb-2 tracking-[3px]">
                ★★★★★
              </div>
              <div
                class="text-xl font-bold mb-1 group-hover:translate-x-1 transition-transform duration-300"
              >
                {{ card.name }}
              </div>
              <div class="text-sm text-white/40 font-medium">
                {{ card.meta }}
              </div>
            </div>

            <!-- Hover Effect Light -->
            <div
              class="absolute top-0 right-0 w-32 h-32 bg-white/5 rounded-full blur-3xl -mr-16 -mt-16 group-hover:bg-michelin-red/10 transition-colors duration-500"
            ></div>
          </div>
        </div>
      </div>
    </section>

    <!-- Vibe Selection Section -->
    <section class="bg-michelin-dark py-16 px-6 md:px-12 lg:px-20">
      <div class="max-w-7xl mx-auto">
        <div class="text-center mb-12">
          <h3
            class="text-[11px] font-bold text-michelin-red tracking-[0.2em] mb-3 uppercase"
          >
            L'IA ÉMOTIONNELLE
          </h3>
          <h2 class="text-3xl font-bold">Quelle est ta vibe ce soir ?</h2>
        </div>

        <div class="flex flex-wrap justify-center gap-4">
          <button
            v-for="vibe in vibes"
            :key="vibe.label"
            @click="selectedVibe = vibe.label"
            :class="[
              'flex items-center gap-3 px-8 py-4 rounded-full border transition-all duration-300 group',
              selectedVibe === vibe.label
                ? 'border-michelin-red/40 bg-michelin-red/[0.08] shadow-[0_8px_20px_rgba(204,0,0,0.1)]'
                : 'border-white/5 bg-white/[0.02] hover:bg-white/[0.05] hover:border-white/10',
            ]"
          >
            <div
              class="w-2 h-2 rounded-full ring-4 transition-all duration-300"
              :style="{
                backgroundColor: vibe.color,
                ringColor:
                  selectedVibe === vibe.label
                    ? vibe.color + '22'
                    : 'transparent',
              }"
            ></div>
            <span
              :class="[
                'text-sm font-bold uppercase tracking-wider transition-colors',
                selectedVibe === vibe.label
                  ? 'text-white'
                  : 'text-white/40 group-hover:text-white/70',
              ]"
            >
              {{ vibe.label }}
            </span>
          </button>
        </div>
      </div>
    </section>

    <!-- Results Section -->
    <section
      class="bg-[#080808] py-24 px-6 md:px-12 lg:px-20 border-t border-michelin-border"
    >
      <div class="max-w-7xl mx-auto">
        <div
          class="flex flex-col lg:flex-row justify-between items-start lg:items-end gap-8 mb-16"
        >
          <div>
            <div
              class="text-michelin-red text-xs font-bold tracking-[0.2em] mb-3 uppercase"
            >
              Le Guide local
            </div>
            <h2 class="text-4xl font-bold">
              Sélection ·
              <span class="text-white/60 font-light">{{ selectedVibe }}</span>
              · Paris
            </h2>
          </div>

          <div
            class="flex flex-wrap gap-2 px-1.5 py-1.5 bg-white/[0.03] border border-white/[0.08] rounded-2xl"
          >
            <button
              v-for="star in filterOptions"
              :key="star"
              @click="selectedStar = star"
              :class="[
                'px-6 py-2.5 rounded-xl text-[10px] font-bold uppercase tracking-widest transition-all duration-300',
                selectedStar === star
                  ? 'bg-michelin-red text-white shadow-lg shadow-michelin-red/20'
                  : 'text-white/40 hover:text-white/70 hover:bg-white/[0.05]',
              ]"
            >
              {{ star }}
            </button>
          </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
          <div
            v-for="resto in restaurants"
            :key="resto.id"
            class="group rounded-3xl overflow-hidden border border-white/[0.08] bg-[#111] transition-all duration-500 hover:border-michelin-red/30 hover:shadow-[0_20px_40px_rgba(0,0,0,0.4)]"
          >
            <div
              :class="[
                'h-[220px] flex flex-col justify-end p-6 relative overflow-hidden',
                resto.bgClass,
              ]"
            >
              <!-- Image Overlay Pattern -->
              <div
                class="absolute inset-0 opacity-20 group-hover:scale-110 transition-transform duration-700 bg-[radial-gradient(circle_at_20%_20%,rgba(255,255,255,0.1)_0,transparent_100%)]"
              ></div>

              <div
                class="absolute top-0 left-0 right-0 h-[4px] transition-all duration-500 group-hover:h-[6px]"
                :style="{ backgroundColor: resto.accentColor }"
              ></div>

              <div class="flex justify-between items-end relative z-10">
                <span
                  class="text-[10px] font-bold tracking-widest uppercase px-4 py-1.5 rounded-full bg-black/40 backdrop-blur-md border border-white/10 text-white/90"
                >
                  {{ resto.status }}
                </span>
                <div
                  class="w-10 h-10 rounded-full flex items-center justify-center bg-michelin-red shadow-lg shadow-michelin-red/30 group-hover:scale-110 transition-transform duration-500"
                >
                  <component :is="resto.iconComp" class="w-4 h-4 text-white" />
                </div>
              </div>
            </div>

            <div class="p-8">
              <div class="text-michelin-red text-[10px] tracking-[3px] mb-3">
                {{ "★".repeat(resto.stars) }}{{ "☆".repeat(5 - resto.stars) }}
              </div>
              <h4
                class="text-lg font-bold mb-2 group-hover:text-michelin-red transition-colors"
              >
                {{ resto.name }}
              </h4>
              <p class="text-sm text-white/40 leading-relaxed mb-6">
                {{ resto.location }} · {{ resto.description }}
              </p>

              <div
                class="flex items-center justify-between pt-6 border-t border-white/[0.05]"
              >
                <span
                  class="text-[10px] font-bold tracking-widest uppercase text-white/80"
                >
                  {{ resto.vibe }}
                </span>
                <span class="text-sm font-medium text-michelin-red/80">{{
                  resto.price
                }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- KPI Section -->
    <section class="max-w-7xl mx-auto px-6 md:px-12 py-32">
      <div
        class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-[1px] bg-white/5 rounded-3xl overflow-hidden border border-white/5 shadow-2xl"
      >
        <div
          v-for="(kpi, i) in kpis"
          :key="i"
          class="bg-michelin-dark p-12 group transition-all hover:bg-[#0d0d0d]"
        >
          <div class="flex items-center gap-4 mb-6">
            <div
              class="w-12 h-12 rounded-2xl flex items-center justify-center bg-michelin-red/[0.1] border border-michelin-red/[0.1] group-hover:scale-110 group-hover:border-michelin-red/30 transition-all duration-500"
            >
              <component :is="kpi.icon" class="w-5 h-5 text-michelin-red" />
            </div>
            <span
              class="text-[10px] font-bold text-white/30 tracking-[0.2em] uppercase"
              >{{ kpi.label }}</span
            >
          </div>
          <div
            class="text-5xl font-bold mb-3 tracking-tight group-hover:text-white transition-colors"
          >
            {{ kpi.value }}
          </div>
          <div class="text-sm text-white/20 font-medium">{{ kpi.sub }}</div>
        </div>
      </div>
    </section>
  </div>
</template>

<script setup>
import { computed, markRaw, onMounted, ref } from "vue";
import axios from "axios";
import { Award, Check, Flame, Globe, Leaf, Play, Star } from "lucide-vue-next";

const API_URL = "http://localhost:8000/api";

const iconMap = {
  check: markRaw(Check),
  star: markRaw(Star),
  vibe: markRaw(Flame),
  leaf: markRaw(Leaf),
  globe: markRaw(Globe),
  award: markRaw(Award),
};

const selectedVibe = ref("Jardin Secret");
const selectedStar = ref("Tous");
const restaurants = ref([]);
const statsData = ref(null);

const vibes = [
  { label: "Jardin Secret", color: "#CC0000" },
  { label: "Date romantique", color: "#534AB7" },
  { label: "Entre amis", color: "#1D9E75" },
  { label: "Célébration", color: "#BA7517" },
  { label: "Solo chill", color: "#5F5E5A" },
  { label: "Business lunch", color: "#D4537E" },
];

const filterOptions = [
  "Tous",
  "1 étoile",
  "2 étoiles",
  "3 étoiles",
  "Bib Gourmand",
];

const stats = computed(() => {
  if (!statsData.value)
    return [
      { label: "Restaurants vérifiés", value: "35k+" },
      { label: "Pays couverts", value: "140" },
      { label: "Anti-buzz", value: "100%" },
      { label: "Explorateurs actifs", value: "128k" },
    ];

  return [
    { label: "Restaurants vérifiés", value: "35k+" },
    { label: "Satisfaction", value: statsData.value.satisfaction },
    { label: "Anti-buzz", value: "100%" },
    { label: "Explorateurs actifs", value: statsData.value.activeExplorers },
  ];
});

const kpis = computed(() => {
  if (!statsData.value)
    return [
      {
        label: "Fact-Check",
        value: "2 847",
        sub: "Tendances analysées ce mois",
        icon: markRaw(Check),
      },
      {
        label: "Mood Match",
        value: "94%",
        sub: "Satisfaction expérience vibe",
        icon: markRaw(Flame),
      },
      {
        label: "Passport",
        value: "128k",
        sub: "Explorateurs inscrits",
        icon: markRaw(Globe),
      },
      {
        label: "Badges",
        value: "19k",
        sub: "Cette semaine",
        icon: markRaw(Award),
      },
    ];

  return [
    {
      label: "Fact-Check",
      value: statsData.value.factChecks,
      sub: "Tendances analysées ce mois",
      icon: markRaw(Check),
    },
    {
      label: "Mood Match",
      value: statsData.value.satisfaction,
      sub: "Satisfaction expérience vibe",
      icon: markRaw(Flame),
    },
    {
      label: "Passport",
      value: statsData.value.activeExplorers,
      sub: "Explorateurs inscrits",
      icon: markRaw(Globe),
    },
    {
      label: "Badges",
      value: statsData.value.badgesObtained,
      sub: "Cette semaine",
      icon: markRaw(Award),
    },
  ];
});

const heroCards = [
  {
    category: "Fact-check",
    tag: "Vérifié Michelin",
    name: "Guy Savoy",
    meta: "Paris 6e · Gastronomique · €€€€€",
    accent: "#CC0000",
    bgGradient: "bg-gradient-to-br from-[#1a0505] to-[#0a0000]",
    tagClass: "bg-michelin-red/20 text-[#FF7777] border border-michelin-red/30",
    icon: markRaw(Check),
  },
  {
    category: "Mood match",
    tag: "Date romantique",
    name: "Septime",
    meta: "Marais · Bistronomie · €€€",
    accent: "#534AB7",
    bgGradient: "bg-gradient-to-br from-[#0f0a2e] to-[#05051a]",
    tagClass: "bg-[#534AB7]/20 text-[#AFA9EC] border border-[#534AB7]/30",
    icon: markRaw(Flame),
  },
  {
    category: "Éco-responsable",
    tag: "Jardin secret",
    name: "Saturne",
    meta: "2e arr. · Naturel · €€€",
    accent: "#1D9E75",
    bgGradient: "bg-gradient-to-br from-[#0a1f14] to-[#050a05]",
    tagClass: "bg-[#1D9E75]/20 text-[#5DCAA5] border border-[#1D9E75]/30",
    icon: markRaw(Leaf),
  },
  {
    category: "Passeport",
    tag: "Badge débloqué",
    name: "Frenchie",
    meta: "2e arr. · Moderne · €€€€",
    accent: "#BA7517",
    bgGradient: "bg-gradient-to-br from-[#241a0a] to-[#140a05]",
    tagClass: "bg-[#BA7517]/20 text-[#EF9F27] border border-[#BA7517]/30",
    icon: markRaw(Award),
  },
];

const fetchData = async () => {
  try {
    const [restoRes, statsRes] = await Promise.all([
      axios.get(`${API_URL}/restaurants`),
      axios.get(`${API_URL}/stats`),
    ]);

    restaurants.value = restoRes.data.map((r, i) => ({
      ...r,
      iconComp: iconMap[r.icon] || markRaw(Check),
      bgClass: `rcib${(i % 4) + 1}`,
    }));

    statsData.value = statsRes.data;
  } catch (err) {
    console.error("API Error:", err);
    restaurants.value = [
      {
        id: 1,
        name: "L'Impasse Fleurie",
        location: "Marais",
        description: "Terrasse privée",
        vibe: "Jardin caché",
        price: "€€€",
        stars: 5,
        status: "Vérifié",
        icon: "check",
        iconComp: markRaw(Check),
        bgClass: "rcib1",
        accentColor: "#CC0000",
      },
      {
        id: 2,
        name: "La Terrasse Cachée",
        location: "Saint-Germain",
        description: "Patio romantique",
        vibe: "Date idéale",
        price: "€€€€",
        stars: 4,
        status: "Romantique",
        icon: "vibe",
        iconComp: markRaw(Flame),
        bgClass: "rcib2",
        accentColor: "#534AB7",
      },
      {
        id: 3,
        name: "Saturne",
        location: "Sentier",
        description: "Cave naturelle",
        vibe: "Naturel",
        price: "€€€",
        stars: 5,
        status: "Éco",
        icon: "leaf",
        iconComp: markRaw(Leaf),
        bgClass: "rcib3",
        accentColor: "#1D9E75",
      },
      {
        id: 4,
        name: "Frenchie Bar",
        location: "Covent Garden",
        description: "Bar convivial",
        vibe: "Convivial",
        price: "€€",
        stars: 3,
        status: "Top",
        icon: "star",
        iconComp: markRaw(Star),
        bgClass: "rcib4",
        accentColor: "#BA7517",
      },
    ];
  }
};

onMounted(() => {
  fetchData();
});
</script>
