import { ref, onMounted, computed, markRaw } from 'vue'
import axios from 'axios'
import { Check, Star, Flame, Leaf, Globe, Award, Search, Sparkles } from 'lucide-vue-next'

export function useApp() {
    let base_url = import.meta.env.VITE_API_URL || 'http://54.37.159.216:8000/api'
    if (base_url && !base_url.endsWith('/api')) {
        base_url = base_url + '/api';
    }
    const API_URL = base_url
    const iconMap = {
        check: markRaw(Check),
        star: markRaw(Star),
        vibe: markRaw(Flame),
        leaf: markRaw(Leaf),
        globe: markRaw(Globe),
        award: markRaw(Award)
    }

    const currentTab = ref('explorer')
    const restaurants = ref([])
    const showIADropdown = ref(false)
    const searchEntry = ref('')
    const filterStars = ref('all')

    const tabs = [
        { id: 'explorer', label: 'EXPLORER' },
        { id: 'ia', label: 'IA MICHELIN', children: true },
        { id: 'snack', label: 'SNACK VIDEO' }
    ]

    const iaChildren = [
        { id: 'factcheck', label: 'Fact-Check', description: 'Vérifier un buzz culinaire', icon: markRaw(Search) },
        { id: 'vibes', label: 'Vibes & Mood', description: 'Trouver par ambiance', icon: markRaw(Sparkles) },
    ]

    const fetchData = async () => {
        try {
            const restoRes = await axios.get(`${API_URL}/restaurants`)
            restaurants.value = restoRes.data
        } catch (err) {
            console.error("API Error, fallback to mock", err)
            restaurants.value = [
                { id: 1, name: "Le Jules Verne", location: 'Paris 7e', description: 'Exceptionnel', vibe: 'Vue', price: '€€€€', stars: 5, status: 'Top', icon: 'award', source: 'michelin', accentColor: '#BA0B2F' },
                { id: 2, name: "Septime", location: 'Paris 11e', description: 'Moderne', vibe: 'Bio', price: '€€€', stars: 3, status: 'Validé', icon: 'vibe', source: 'community', accentColor: '#1D9E75' }
            ]
        }
    }

    const filteredRestaurants = computed(() => {
        return restaurants.value.filter(r => {
            const matchesSearch = r.name.toLowerCase().includes(searchEntry.value.toLowerCase()) ||
                r.location.toLowerCase().includes(searchEntry.value.toLowerCase())
            const matchesStars = filterStars.value === 'all' || r.stars >= parseInt(filterStars.value)
            return matchesSearch && matchesStars
        })
    })

    onMounted(() => fetchData())

    return {
        currentTab,
        restaurants,
        showIADropdown,
        searchEntry,
        filterStars,
        tabs,
        iaChildren,
        iconMap,
        filteredRestaurants
    }
}
