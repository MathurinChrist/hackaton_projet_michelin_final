<template>
    <div class="max-w-2xl mx-auto mb-20 animate-fade-up">
        <div class="relative group">
            <input 
                :value="modelValue"
                @input="$emit('update:modelValue', $event.target.value)"
                @keyup.enter="$emit('submit')"
                type="text" 
                placeholder="Ex: Un brunch ensoleillé avec vue sur la Tour Eiffel..."
                class="w-full bg-white border-2 border-michelin-border rounded-full py-6 px-10 text-lg font-medium focus:outline-none focus:border-michelin-red transition-all shadow-xl shadow-michelin-red/5 pr-32"
            />
            <button 
                @click="$emit('submit')"
                class="absolute right-4 top-1/2 -translate-y-1/2 bg-michelin-red text-white px-6 py-3 rounded-full text-[10px] font-black uppercase tracking-widest hover:bg-michelin-dark transition-all transform active:scale-95"
            >
                Inspirer l'IA
            </button>
        </div>
        <p class="text-center mt-4 text-[10px] text-michelin-dark/30 font-bold uppercase tracking-widest">
            Appuyez sur Entrée pour lancer la recherche
        </p>
    </div>
</template>

<script setup>
defineProps(['modelValue'])
defineEmits(['update:modelValue', 'submit'])
</script>
