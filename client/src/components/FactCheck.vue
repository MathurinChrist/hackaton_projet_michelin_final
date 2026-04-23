<template>
  <div class="bg-[#f5f5f5] min-h-screen font-sans text-michelin-dark pb-20">
    <FactCheckHero 
        v-model="viralUrl" 
        :loading="loading" 
        @analyze="analyzeUrl" 
    />

    <AnalysisResult 
        v-if="loading || analysis" 
        :loading="loading" 
        :analysis="analysis" 
        :currentStepText="currentStepText" 
        :linkCopied="linkCopied" 
        @share="share" 
        @copy="copyLink" 
    />

    <RecentHistory 
        :recentAnalyses="recentAnalyses" 
        @select="recallAnalysis" 
    />
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useFactCheck } from '../composables/useFactCheck'
import FactCheckHero from './factcheck/FactCheckHero.vue'
import AnalysisResult from './factcheck/AnalysisResult.vue'
import RecentHistory from './factcheck/RecentHistory.vue'

const {
    viralUrl,
    loading,
    analysis,
    recentAnalyses,
    linkCopied,
    currentStepText,
    analyzeUrl,
    recallAnalysis
} = useFactCheck()

const copyLink = () => {
    navigator.clipboard.writeText(window.location.href)
    linkCopied.value = true
    setTimeout(() => linkCopied.value = false, 2000)
}

const share = (platform) => {
    const text = `Avis Michelin pour ${analysis.value.name} : ${analysis.value.score}/10`
    window.open(`https://wa.me/?text=${encodeURIComponent(text)}`, '_blank')
}
</script>
