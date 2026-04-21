<template>
  <div class="M">
    <h2 class="sr-only">Page Explorer desktop — Guide Michelin nouvelle génération</h2>

    <div class="nav">
      <div class="nlogo">
        <div class="ndots">
          <div class="nd" style="width:11px;height:11px;"></div>
          <div class="nd" style="width:8px;height:8px;opacity:.7;"></div>
          <div class="nd" style="width:6px;height:6px;opacity:.45;"></div>
        </div>
        <div class="nname">MICHELIN GUIDE</div>
      </div>
      <div class="nlinks">
        <div class="nl" :class="{ on: currentTab === 'explorer' }" @click="currentTab = 'explorer'">Explorer</div>
        <div class="nl" :class="{ on: currentTab === 'factcheck' }" @click="currentTab = 'factcheck'">Fact-Check</div>
        <div class="nl" :class="{ on: currentTab === 'vibes' }" @click="currentTab = 'vibes'">Vibes</div>
<!--        <div class="nl" :class="{ on: currentTab === 'passport' }" @click="currentTab = 'passport'">Passeport</div>-->
        <div class="nl" :class="{ on: currentTab === 'snack' }" @click="currentTab = 'snack'">Snack Video</div>
      </div>
      <div style="display:flex;gap:12px;align-items:center;">
        <div style="font-size:12px;color:rgba(255,255,255,.4);cursor:pointer">Connexion</div>
        <div class="ncta" style="cursor:pointer">Rejoindre gratuitement</div>
      </div>
    </div>

    <!-- MAIN CONTENT -->
    <template v-if="currentTab === 'explorer'">
      <div class="hero">
        <div class="hl">
          <div class="htag"><div class="htdot"></div><div class="httxt">Nouvelle expérience · IA embarquée</div></div>
          <div class="hh1">L'autorité<br>gastronomique<br><em>réinventée</em>.</div>
          <div class="hsub">Finis les pièges Instagram. Trouve l'adresse qui te ressemble — validée par un siècle d'expertise, guidée par ton humeur du moment.</div>
          <div class="hbtns">
            <div class="hbp" @click="scrollToSelection">Explorer maintenant</div>
            <div class="hbg"><div class="pli"><div class="plt"></div></div>Voir le concept — 90s</div>
          </div>
          <div class="hstats">
            <div><div class="hsv">35k+</div><div class="hsl">Restaurants vérifiés</div></div>
            <div><div class="hsv">140</div><div class="hsl">Pays couverts</div></div>
            <div><div class="hsv">100%</div><div class="hsl">Anti-buzz</div></div>
            <div><div class="hsv">128k</div><div class="hsl">Explorateurs actifs</div></div>
          </div>
        </div>
        <div class="hr">
          <div class="hgrid">
            <div class="ht ht1">
              <div class="hta" style="background:#CC0000;"></div>
              <div class="hts">Fact-check</div>
              <div class="htbdg" style="background:rgba(204,0,0,.18);">
                <svg width="14" height="14" viewBox="0 0 16 16" fill="none"><path d="M3 8l4 4 6-6" stroke="#FF6666" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
              </div>
              <span class="httag tagr">Vérifié Michelin</span>
              <div class="htstars">★★★★★</div>
              <div class="htname">Guy Savoy · Paris 6e</div>
              <div class="htmeta">Gastronomique · €€€€€</div>
            </div>
            <div class="ht ht2">
              <div class="hta" style="background:#534AB7;"></div>
              <div class="hts">Mood match</div>
              <div class="htbdg" style="background:rgba(83,74,183,.18);">
                <svg width="14" height="14" viewBox="0 0 16 16" fill="none"><path d="M8 2C8 2 3 6 3 9.5a5 5 0 0010 0C13 6 8 2 8 2z" stroke="#AFA9EC" stroke-width="1.3"/></svg>
              </div>
              <span class="httag tagp">Date romantique</span>
              <div class="htstars">★★★★☆</div>
              <div class="htname">Septime · Marais</div>
              <div class="htmeta">Bistronomie · €€€</div>
            </div>
            <div class="ht ht3">
              <div class="hta" style="background:#1D9E75;"></div>
              <div class="hts">Éco-responsable</div>
              <div class="htbdg" style="background:rgba(29,158,117,.18);">
                <svg width="14" height="14" viewBox="0 0 16 16" fill="none"><path d="M8 14s-6-3-6-7a6 6 0 0112 0c0 4-6 7-6 7z" stroke="#5DCAA5" stroke-width="1.3"/></svg>
              </div>
              <span class="httag tagg">Jardin secret</span>
              <div class="htstars">★★★★★</div>
              <div class="htname">Saturne · 2e arr.</div>
              <div class="htmeta">Naturel · €€€</div>
            </div>
            <div class="ht ht4">
              <div class="hta" style="background:#BA7517;"></div>
              <div class="hts">Passeport</div>
              <div class="htbdg" style="background:rgba(186,117,23,.18);">
                <svg width="14" height="14" viewBox="0 0 16 16" fill="none"><polygon points="8,2 10,6 14,6.5 11,9.5 12,14 8,11.5 4,14 5,9.5 2,6.5 6,6" stroke="#EF9F27" stroke-width="1.3" fill="none"/></svg>
              </div>
              <span class="httag taga">Badge débloqué</span>
              <div class="htstars">★★★☆☆</div>
              <div class="htname">Frenchie · 2e arr.</div>
              <div class="htmeta">Moderne · €€€€</div>
            </div>
          </div>
        </div>
      </div>

      <div class="vmood" id="selection-section">
        <div class="vmsec">Quelle est ta vibe ce soir ?</div>
        <div class="vmrow">
          <div 
            v-for="vibe in vibes" 
            :key="vibe.label"
            class="vmc" 
            :class="{ on: selectedVibe === vibe.label }"
            @click="selectedVibe = vibe.label"
          >
            <div class="vmcd" :style="{ background: vibe.color }"></div>
            <div class="vmcl">{{ vibe.label }}</div>
          </div>
        </div>
      </div>

      <div class="restogrid">
        <div class="rgtitle">
          <div class="rgt">Sélection · {{ selectedVibe }} · Paris</div>
          <div class="rgf">
            <div 
              v-for="star in [ 'Tous', '1 étoile', '2 étoiles', '3 étoiles', 'Bib Gourmand' ]"
              :key="star"
              class="rgfb"
              :class="{ on: selectedStars === star }"
              @click="selectedStars = star"
            >
              {{ star }}
            </div>
          </div>
        </div>
        <div class="rcards">
          <div v-for="resto in filteredRestaurants" :key="resto.name" class="rc">
            <div class="rci" :class="resto.bgClass">
              <div class="rcitop" :style="{ background: resto.accentColor }"></div>
              <div class="rcb">
                <span class="httag" :class="resto.tagClass" style="margin-bottom:0;">{{ resto.status }}</span>
                <div class="rcbdg" :style="{ background: resto.accentColorBg }">
                  <svg v-if="resto.icon === 'check'" width="12" height="12" viewBox="0 0 16 16" fill="none"><path d="M3 8l4 4 6-6" stroke="white" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
                  <svg v-if="resto.icon === 'star'" width="12" height="12" viewBox="0 0 16 16" fill="none"><polygon points="8,2 10,6 14,6.5 11,9.5 12,14 8,11.5 4,14 5,9.5 2,6.5 6,6" stroke="white" stroke-width="1.3" fill="none"/></svg>
                  <svg v-if="resto.icon === 'vibe'" width="12" height="12" viewBox="0 0 16 16" fill="none"><path d="M8 2C8 2 3 6 3 9.5a5 5 0 0010 0C13 6 8 2 8 2z" stroke="white" stroke-width="1.3"/></svg>
                  <svg v-if="resto.icon === 'leaf'" width="12" height="12" viewBox="0 0 16 16" fill="none"><path d="M8 14s-6-3-6-7a6 6 0 0112 0c0 4-6 7-6 7z" stroke="white" stroke-width="1.3"/></svg>
                </div>
              </div>
            </div>
            <div class="rcbody">
              <div class="rcstars">{{ '★'.repeat(resto.stars) }}{{ '☆'.repeat(5 - resto.stars) }}</div>
              <div class="rcname">{{ resto.name }}</div>
              <div class="rcmeta">{{ resto.meta }}</div>
              <div class="rctag" :class="resto.tagClass">{{ resto.vibe }} · {{ resto.price }}</div>
            </div>
          </div>
        </div>
      </div>

      <div class="kpis">
        <div class="kpi">
          <div class="kpiico">
            <div class="kpiib" style="background:rgba(204,0,0,.15);">
              <svg width="14" height="14" viewBox="0 0 16 16" fill="none"><path d="M3 8l4 4 6-6" stroke="#FF6666" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </div>
            <div class="kpilbl">Fact-Check</div>
          </div>
          <div class="kpival">2 847</div>
          <div class="kpisub">Tendances analysées ce mois</div>
        </div>
        <div class="kpi">
          <div class="kpiico">
            <div class="kpiib" style="background:rgba(83,74,183,.15);">
              <svg width="14" height="14" viewBox="0 0 16 16" fill="none"><path d="M8 2C8 2 3 6 3 9.5a5 5 0 0010 0C13 6 8 2 8 2z" stroke="#AFA9EC" stroke-width="1.3"/></svg>
            </div>
            <div class="kpilbl">Mood Match</div>
          </div>
          <div class="kpival">94%</div>
          <div class="kpisub">Satisfaction expérience vibe</div>
        </div>
        <div class="kpi">
          <div class="kpiico">
            <div class="kpiib" style="background:rgba(29,158,117,.15);">
              <svg width="14" height="14" viewBox="0 0 16 16" fill="none"><path d="M8 14s-6-3-6-7a6 6 0 0112 0c0 4-6 7-6 7z" stroke="#5DCAA5" stroke-width="1.3"/></svg>
            </div>
            <div class="kpilbl">Passeport actif</div>
          </div>
          <div class="kpival">128k</div>
          <div class="kpisub">Explorateurs inscrits</div>
        </div>
        <div class="kpi">
          <div class="kpiico">
            <div class="kpiib" style="background:rgba(186,117,23,.15);">
              <svg width="14" height="14" viewBox="0 0 16 16" fill="none"><polygon points="8,2 10,6 14,6.5 11,9.5 12,14 8,11.5 4,14 5,9.5 2,6.5 6,6" stroke="#EF9F27" stroke-width="1.3" fill="none"/></svg>
            </div>
            <div class="kpilbl">Badges obtenus</div>
          </div>
          <div class="kpival">19k</div>
          <div class="kpisub">Cette semaine</div>
        </div>
      </div>
    </template>

    <!-- OTHER TABS PLACEHOLDERS -->
    <div v-else class="tab-content">
      <div class="hero">
        <div class="hl">
          <div class="htag"><div class="htdot"></div><div class="httxt">Module {{ currentTab }}</div></div>
          <div class="hh1">Bientôt disponible en version <em>Bêta</em>.</div>
          <div class="hsub">Nous finalisons l'expérience pour les 25-30 ans.</div>
          <div class="hbp" style="width: fit-content" @click="currentTab = 'explorer'">Retour à l'Exploration</div>
        </div>
      </div>
    </div>

    <div class="footer">
      <div class="fdots">
        <div class="nd" style="width:10px;height:10px;"></div>
        <div class="nd" style="width:7px;height:7px;opacity:.5;"></div>
        <div class="nd" style="width:5px;height:5px;opacity:.3;"></div>
        <div style="font-size:11px;color:rgba(255,255,255,.2);margin-left:8px;">MICHELIN GUIDE · 2025</div>
      </div>
      <div class="ftxt">Explorer · Fact-Check · Vibes · Passeport · Snack Video</div>
      <div class="ftxt">Concept MVP — Hackathon</div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';

const currentTab = ref('explorer');
const selectedVibe = ref('Jardin Secret');
const selectedStars = ref('Tous');

const vibes = [
  { label: 'Jardin Secret', color: '#CC0000' },
  { label: 'Date romantique', color: '#534AB7' },
  { label: 'Entre amis', color: '#1D9E75' },
  { label: 'Célébration', color: '#BA7517' },
  { label: 'Solo chill', color: '#5F5E5A' },
  { label: 'Business lunch', color: '#D4537E' },
  { label: 'Découverte locale', color: '#378ADD' },
  { label: 'Gastronomie verte', color: '#639922' },
];

const restaurants = [
  {
    name: "L'Impasse Fleurie",
    meta: "Marais · Terrasse privée",
    vibe: "Jardin caché",
    price: "€€€",
    stars: 5,
    status: "Vérifié",
    icon: "check",
    bgClass: "rcib1",
    accentColor: "#CC0000",
    accentColorBg: "rgba(204,0,0,0.2)",
    tagClass: "tagr",
  },
  {
    name: "La Terrasse Cachée",
    meta: "Saint-Germain · Patio",
    vibe: "Date idéale",
    price: "€€€€",
    stars: 4,
    status: "Romantique",
    icon: "vibe",
    bgClass: "rcib2",
    accentColor: "#534AB7",
    accentColorBg: "rgba(83,74,183,0.2)",
    tagClass: "tagp",
  },
  {
    name: "Saturne",
    meta: "Sentier · Cave naturelle",
    vibe: "Naturel",
    price: "€€€",
    stars: 5,
    status: "Éco",
    icon: "leaf",
    bgClass: "rcib3",
    accentColor: "#1D9E75",
    accentColorBg: "rgba(29,158,117,0.2)",
    tagClass: "tagg",
  },
  {
    name: "Frenchie Bar",
    meta: "Covent Garden · Bar",
    vibe: "Convivial",
    price: "€€",
    stars: 3,
    status: "Top",
    icon: "star",
    bgClass: "rcib4",
    accentColor: "#BA7517",
    accentColorBg: "rgba(186,117,23,0.2)",
    tagClass: "taga",
  }
];

const filteredRestaurants = computed(() => {
  // Simple filter for the demo
  return restaurants;
});

const scrollToSelection = () => {
  document.getElementById('selection-section')?.scrollIntoView({ behavior: 'smooth' });
}
</script>

<style scoped>
.M {
  font-family: Inter, system-ui, -apple-system, sans-serif;
  background: #0a0a0a;
  color: white;
  min-height: 100vh;
}
.sr-only { position: absolute; width: 1px; height: 1px; padding: 0; margin: -1px; overflow: hidden; clip: rect(0, 0, 0, 0); border: 0; }

.nav { display: flex; align-items: center; justify-content: space-between; padding: 0 48px; height: 70px; border-bottom: 0.5px solid #1a1a1a; background: #0a0a0a; position: sticky; top: 0; z-index: 100; }
.nlogo { display: flex; align-items: center; gap: 10px; }
.ndots { display: flex; align-items: flex-end; gap: 3px; }
.nd { border-radius: 50%; background: #CC0000; }
.nname { font-size: 13px; font-weight: 600; color: white; letter-spacing: .12em; }
.nlinks { display: flex; gap: 32px; }
.nl { font-size: 12px; color: rgba(255, 255, 255, .4); cursor: pointer; transition: color 0.3s; }
.nl:hover { color: white; }
.nl.on { color: white; position: relative; }
.nl.on::after { content: ''; position: absolute; bottom: -26px; left: 0; right: 0; height: 2px; background: #CC0000; }
.ncta { background: #CC0000; color: white; font-size: 11px; font-weight: 600; padding: 10px 22px; border-radius: 20px; transition: transform 0.2s; }
.ncta:hover { transform: scale(1.05); }

.hero { display: grid; grid-template-columns: 1.2fr 1fr; min-height: 500px; }
.hl { padding: 80px 48px; display: flex; flex-direction: column; justify-content: center; }
.htag { display: inline-flex; align-items: center; gap: 7px; background: rgba(204, 0, 0, .12); border: 0.5px solid rgba(204, 0, 0, .35); border-radius: 20px; padding: 6px 16px; margin-bottom: 32px; width: fit-content; }
.htdot { width: 6px; height: 6px; border-radius: 50%; background: #CC0000; }
.httxt { font-size: 11px; color: #FF5555; letter-spacing: .08em; font-weight: 600; }
.hh1 { font-size: 56px; font-weight: 600; color: white; line-height: 1.1; margin-bottom: 20px; letter-spacing: -0.02em; }
.hh1 em { color: #CC0000; font-style: normal; }
.hsub { font-size: 16px; color: rgba(255, 255, 255, .45); line-height: 1.6; margin-bottom: 40px; max-width: 440px; }
.hbtns { display: flex; gap: 16px; align-items: center; }
.hbp { background: #CC0000; color: white; font-size: 14px; font-weight: 600; padding: 16px 36px; border-radius: 30px; cursor: pointer; transition: all 0.3s; }
.hbp:hover { background: #e60000; box-shadow: 0 10px 20px rgba(204, 0, 0, 0.2); }
.hbg { background: transparent; color: rgba(255, 255, 255, .6); font-size: 14px; padding: 16px 24px; border-radius: 30px; border: 0.5px solid rgba(255, 255, 255, .15); cursor: pointer; display: flex; align-items: center; gap: 10px; transition: all 0.3s; }
.hbg:hover { border-color: white; color: white; }
.pli { width: 22px; height: 22px; border-radius: 50%; border: 1px solid rgba(255, 255, 255, .3); display: flex; align-items: center; justify-content: center; }
.plt { width: 0; height: 0; border-top: 4px solid transparent; border-bottom: 4px solid transparent; border-left: 7px solid rgba(255, 255, 255, .6); margin-left: 2px; }

.hstats { display: flex; gap: 48px; margin-top: 60px; padding-top: 40px; border-top: 0.5px solid rgba(255, 255, 255, .07); }
.hsv { font-size: 28px; font-weight: 600; color: white; }
.hsl { font-size: 12px; color: rgba(255, 255, 255, .3); margin-top: 4px; letter-spacing: .04em; }

.hr { position: relative; overflow: hidden; background: #0f0f0f; border-left: 0.5px solid #1a1a1a; }
.hgrid { display: grid; grid-template-columns: 1fr 1fr; grid-template-rows: 1fr 1fr; gap: 1px; height: 100%; background: #1a1a1a; }
.ht { display: flex; flex-direction: column; justify-content: flex-end; padding: 32px; position: relative; overflow: hidden; min-height: 250px; transition: all 0.5s; cursor: pointer; }
.ht:hover { filter: brightness(1.2); }
.ht1 { background: #1a0e0e; }
.ht2 { background: #0f0f1e; }
.ht3 { background: #0e1a10; }
.ht4 { background: #1a1500; }
.hta { position: absolute; top: 0; left: 0; right: 0; height: 3px; }
.hts { position: absolute; top: 24px; left: 32px; font-size: 10px; color: rgba(255, 255, 255, .3); letter-spacing: .08em; text-transform: uppercase; }
.htbdg { position: absolute; top: 20px; right: 24px; width: 36px; height: 36px; border-radius: 50%; display: flex; align-items: center; justify-content: center; }
.httag { display: inline-block; font-size: 10px; font-weight: 600; padding: 4px 10px; border-radius: 12px; margin-bottom: 8px; width: fit-content; }
.htname { font-size: 15px; font-weight: 600; color: white; margin-bottom: 4px; }
.htmeta { font-size: 12px; color: rgba(255, 255, 255, .4); }
.htstars { color: #CC0000; font-size: 12px; margin-bottom: 6px; letter-spacing: 2px; }

.tagr { background: rgba(204, 0, 0, .15); color: #FF7777; border: 0.5px solid rgba(204, 0, 0, 0.2); }
.tagp { background: rgba(83, 74, 183, .15); color: #AFA9EC; border: 0.5px solid rgba(83, 74, 183, 0.2); }
.tagg { background: rgba(29, 158, 117, .15); color: #5DCAA5; border: 0.5px solid rgba(29, 158, 117, 0.2); }
.taga { background: rgba(186, 117, 23, .15); color: #EF9F27; border: 0.5px solid rgba(186, 117, 23, 0.2); }

.vmood { background: #0a0a0a; padding: 48px; border-top: 0.5px solid #141414; }
.vmsec { font-size: 11px; font-weight: 600; color: rgba(255, 255, 255, .3); letter-spacing: .12em; margin-bottom: 24px; text-transform: uppercase; }
.vmrow { display: flex; gap: 12px; flex-wrap: wrap; }
.vmc { display: flex; align-items: center; gap: 10px; padding: 12px 24px; border-radius: 30px; border: 0.5px solid rgba(255, 255, 255, .1); background: rgba(255, 255, 255, .03); cursor: pointer; transition: all 0.3s; }
.vmc:hover { background: rgba(255, 255, 255, 0.06); border-color: rgba(255, 255, 255, 0.2); }
.vmc.on { border-color: rgba(204, 0, 0, .6); background: rgba(204, 0, 0, .08); }
.vmcd { width: 8px; height: 8px; border-radius: 50%; }
.vmcl { font-size: 13px; color: rgba(255, 255, 255, .6); font-weight: 500; }
.vmc.on .vmcl { color: #FF7777; }

.restogrid { background: #080808; padding: 60px 48px; border-top: 0.5px solid #141414; }
.rgtitle { display: flex; justify-content: space-between; align-items: center; margin-bottom: 40px; }
.rgt { font-size: 22px; font-weight: 600; color: white; }
.rgf { display: flex; gap: 10px; }
.rgfb { padding: 10px 20px; border-radius: 18px; border: 0.5px solid rgba(255, 255, 255, .1); background: rgba(255, 255, 255, .04); font-size: 12px; color: rgba(255, 255, 255, .5); cursor: pointer; transition: all 0.2s; }
.rgfb:hover { border-color: rgba(255, 255, 255, 0.2); }
.rgfb.on { border-color: rgba(204, 0, 0, .5); background: rgba(204, 0, 0, .1); color: #FF7777; }

.rcards { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 24px; }
.rc { border-radius: 20px; overflow: hidden; border: 0.5px solid rgba(255, 255, 255, .08); background: #111; transition: transform 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275); cursor: pointer; }
.rc:hover { transform: translateY(-8px); border-color: rgba(255, 255, 255, 0.15); }
.rci { height: 180px; display: flex; flex-direction: column; justify-content: flex-end; padding: 20px; position: relative; }
.rcib1 { background: radial-gradient(circle at top right, #331a1a, #1a0f0f); }
.rcib2 { background: radial-gradient(circle at top right, #1a1a33, #0f0f1a); }
.rcib3 { background: radial-gradient(circle at top right, #1a3324, #0a1a12); }
.rcib4 { background: radial-gradient(circle at top right, #332b1a, #1a1500); }
.rcitop { position: absolute; top: 0; left: 0; right: 0; height: 3px; }
.rcb { display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 6px; }
.rcbdg { width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; }
.rcbody { padding: 24px; }
.rcname { font-size: 16px; font-weight: 600; color: white; margin-bottom: 6px; }
.rcmeta { font-size: 13px; color: rgba(255, 255, 255, .4); margin-bottom: 12px; }
.rcstars { color: #CC0000; font-size: 12px; margin-bottom: 8px; letter-spacing: 2px; }
.rctag { display: inline-block; font-size: 10px; font-weight: 600; padding: 4px 12px; border-radius: 12px; margin-top: 8px; }

.kpis { background: #0a0a0a; border-top: 0.5px solid #141414; display: grid; grid-template-columns: repeat(4, 1fr); }
.kpi { padding: 48px; border-right: 0.5px solid #141414; }
.kpi:last-child { border-right: none; }
.kpiico { display: flex; align-items: center; gap: 12px; margin-bottom: 16px; }
.kpiib { width: 32px; height: 32px; border-radius: 10px; display: flex; align-items: center; justify-content: center; }
.kpilbl { font-size: 12px; font-weight: 600; color: rgba(255, 255, 255, .4); letter-spacing: .08em; text-transform: uppercase; }
.kpival { font-size: 36px; font-weight: 600; color: white; margin-bottom: 6px; }
.kpisub { font-size: 12px; color: rgba(255, 255, 255, .3); }

.footer { background: #080808; border-top: 0.5px solid #141414; padding: 32px 48px; display: flex; justify-content: space-between; align-items: center; }
.ftxt { font-size: 12px; color: rgba(255, 255, 255, .3); }
.fdots { display: flex; align-items: flex-end; gap: 4px; }

.tab-content {
  min-height: 600px;
  display: flex;
  align-items: center;
  justify-content: center;
}
</style>
