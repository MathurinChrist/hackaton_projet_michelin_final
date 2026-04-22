import { ref, onMounted } from 'vue'
import axios from 'axios'

export function useFactCheck() {
    const viralUrl = ref('')
    const loading = ref(false)
    const analysis = ref(null)
    const recentAnalyses = ref([])
    const linkCopied = ref(false)
    const currentStepText = ref('Standby...')

    const steps = ["Radar Michelin activé...", "Scan des signaux sociaux...", "Croisement base Michelin...", "Attribution du score..."]

    let stepInterval = null
    const startStepCycling = () => {
        let index = 0
        stepInterval = setInterval(() => {
            index = (index + 1) % steps.length
            currentStepText.value = steps[index]
        }, 800)
    }
    const stopStepCycling = () => { if (stepInterval) clearInterval(stepInterval) }

    const analyzeUrl = async () => {
        if (!viralUrl.value) return
        loading.value = true
        analysis.value = null
        startStepCycling()

        const startTime = Date.now()

        try {
            const API_URL = import.meta.env.VITE_API_URL || 'http://localhost:8000/api'
            const response = await axios.post(`${API_URL}/fact-check`, { url: viralUrl.value })
            const duration = Date.now() - startTime
            const remainingDelay = Math.max(0, 3000 - duration)

            setTimeout(() => {
                analysis.value = response.data.analysis
                recentAnalyses.value = response.data.recent
                loading.value = false
                stopStepCycling()
            }, remainingDelay)

        } catch (error) {
            loading.value = false
            stopStepCycling()
            console.error('Fact-check API Error:', error)
        }
    }

    const fetchRecent = async () => {
        try {
            const API_URL = import.meta.env.VITE_API_URL || 'http://localhost:8000/api'
            const response = await axios.get(`${API_URL}/fact-check`)
            recentAnalyses.value = response.data
        } catch (err) { }
    }

    const recallAnalysis = (item) => {
        viralUrl.value = item.url
        loading.value = true
        analysis.value = null
        startStepCycling()
        setTimeout(() => {
            loading.value = false
            stopStepCycling()
            const score = parseFloat(item.score)
            let advisory = "PRUDENCE. Buzz avant plat."
            let advisoryColor = "#BA0B2F"
            if (score >= 8.5) { advisory = "FONCEZ-Y ! Excellence."; advisoryColor = "#1D9E75"; }
            else if (score >= 7.0) { advisory = "À TESTER. Intéressant."; advisoryColor = "#BA7517"; }
            analysis.value = { ...item, advisory, advisoryColor }
            window.scrollTo({ top: 0, behavior: 'smooth' })
        }, 600)
    }

    onMounted(() => fetchRecent())

    return {
        viralUrl,
        loading,
        analysis,
        recentAnalyses,
        linkCopied,
        currentStepText,
        analyzeUrl,
        recallAnalysis
    }
}
