<script setup>
import {
    Plus,
    Mail,
} from 'lucide-vue-next'

defineEmits([
    'create-event',
])
</script>
<template>
    <div
        class="
            rounded-2xl
            border border-white/5
            bg-[#111111]
            p-5
        "
        >
    
        <h2
            class="
                mb-5
                text-base
                font-semibold
                text-white
            "
        >
            Actions
        </h2>
    
        <div class="space-y-3">
            <!-- ADD new random event -->
<button
        @click="$emit('create-event')"
        class="
            flex w-full items-center justify-between

            rounded-2xl

            border border-white/10

            bg-white/[0.03]

            px-4 py-3

            text-white

            transition-all duration-200

            hover:bg-white/[0.05]
        "
    >

        <div class="flex items-center gap-3">

            <Plus class="h-4 w-4" />

            <span class="text-sm">
                Add new random event
            </span>

        </div>

    </button>
        
            <!-- ADD INTERVIEW -->
            <button
                class="
                    flex w-full items-center justify-between
        
                    rounded-2xl
        
                    border border-white/10
        
                    bg-white/[0.03]
        
                    px-4 py-3
        
                    text-white
        
                    transition-all duration-200
        
                    hover:bg-white/[0.05]
                "
            >
        
                <div class="flex items-center gap-3">
                
                    <Plus class="h-4 w-4" />
                
                    <span class="text-sm">
                        Add Interview
                    </span>
                
                </div>
            
            </button>
        
            <!-- IMPORT -->
            <button
                class="
                    flex w-full items-center justify-between
        
                    rounded-2xl
        
                    border border-white/10
        
                    bg-white/[0.03]
        
                    px-4 py-3
        
                    text-white
        
                    transition-all duration-200
        
                    hover:bg-white/[0.05]
                "
            >
        
                <div class="flex items-center gap-3">
                
                    <Mail class="h-4 w-4" />
                
                    <span class="text-sm">
                        Import from Gmail
                    </span>
                
                </div>
            
            </button>
        
        </div>
    
    </div>
</template>