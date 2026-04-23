<template>
    <section class="bg-white py-10 px-6 md:px-12 lg:px-20 border-b border-michelin-border sticky top-[75px] z-40">
        <div class="max-w-7xl mx-auto flex flex-wrap gap-6 items-center justify-between">
            <div class="flex-1 min-w-[300px] relative">
                <input 
                    :value="searchEntry"
                    @input="$emit('update:searchEntry', $event.target.value)"
                    type="text" 
                    placeholder="Rechercher par nom ou ville..." 
                    class="w-full bg-michelin-gray border border-michelin-border rounded-full px-8 py-4 text-sm font-medium focus:ring-2 ring-michelin-red/20 outline-none transition-all"
                />
            </div>
            
            <div class="flex items-center gap-4">
                <span class="text-[10px] font-black text-michelin-dark/30 uppercase tracking-widest">Niveau Michelin</span>
                <select 
                    :value="filterStars"
                    @change="$emit('update:filterStars', $event.target.value)"
                    class="bg-white border border-michelin-border rounded-full px-6 py-4 text-xs font-bold uppercase tracking-widest outline-none cursor-pointer"
                >
                    <option value="all">Tous les grades</option>
                    <option value="3">3 Étoiles & +</option>
                    <option value="5">Excellence (5★)</option>
                </select>
            </div>
        </div>
    </section>
</template>

<script setup>
defineProps({
    searchEntry: String,
    filterStars: String
})

defineEmits(['update:searchEntry', 'update:filterStars'])
</script>
