import { ref } from 'vue'
import axios from 'axios'

export function useVibes() {
    const selectedMood = ref(null)
    const loading = ref(false)
    const vibeResults = ref(null)
    const loadingText = ref('Analyse de votre humeur...')
    const customVibeText = ref('')

    const moods = [
        { id: 'date romantique', label: 'Date Romantique', emoji: '🕯️', description: 'Lumière tamisée, service discret.' },
        { id: 'jardin secret', label: 'Jardin Secret', emoji: '🌿', description: 'Terrasses cachées, nature.' },
        { id: 'entre amis', label: 'Entre Amis', emoji: '🍻', description: 'Généreux, convivial, joyeux.' },
        { id: 'célébration', label: 'Célébration', emoji: '🥂', description: 'Les plus grandes tables.' },
        { id: 'zen & détox', label: 'Zen & Détox', emoji: '🧘', description: 'Végétal, léger, minimaliste.' },
        { id: 'custom', label: 'Sur-mesure', emoji: '✨', description: 'Décris ton envie du moment.' },
    ]

    const loadingMessages = [
        'Analyse de votre humeur...',
        'Consultation de la base Michelin...',
        'Sélection des meilleures adresses...',
        'Rédaction de la recommandation...'
    ]

    const selectMood = async (mood) => {
        selectedMood.value = mood
        vibeResults.value = null

        if (mood.id === 'custom') {
            return
        }

        await fetchVibes(mood.id)
    }

    const submitCustomVibe = async () => {
        if (!customVibeText.value) return
        await fetchVibes(customVibeText.value)
    }

    const fetchVibes = async (vibeText) => {
        loading.value = true
        vibeResults.value = null

        let msgIndex = 0
        const interval = setInterval(() => {
            msgIndex = (msgIndex + 1) % loadingMessages.length
            loadingText.value = loadingMessages[msgIndex]
        }, 800)

        try {
            // Updated to relative URL for better portability if needed, 
            // but keeping absolute for now to match project state
            const response = await axios.post('http://localhost:8000/api/vibes', {
                vibe: vibeText
            })

            await new Promise(resolve => setTimeout(resolve, 2000))
            vibeResults.value = response.data
        } catch (err) {
            console.error('Vibes API Error:', err)
        } finally {
            loading.value = false
            clearInterval(interval)
        }
    }

    const resetVibes = () => {
        selectedMood.value = null
        vibeResults.value = null
        customVibeText.value = ''
    }

    return {
        moods,
        selectedMood,
        loading,
        loadingText,
        vibeResults,
        customVibeText,
        selectMood,
        submitCustomVibe,
        resetVibes
    }
}
