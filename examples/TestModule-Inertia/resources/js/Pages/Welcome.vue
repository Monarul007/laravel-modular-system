<template>
    <div class="container mx-auto p-8">
        <h1 class="text-3xl font-bold mb-4">{{ message }}</h1>
        
        <div class="bg-white shadow rounded-lg p-6">
            <h2 class="text-xl font-semibold mb-3">Items:</h2>
            <ul class="list-disc list-inside">
                <li v-for="item in items" :key="item" class="mb-2">
                    {{ item }}
                </li>
            </ul>
        </div>

        <div class="mt-6">
            <a href="/testmodule/blade" class="text-blue-600 hover:underline">
                View Blade Example
            </a>
        </div>
    </div>
</template>

<script setup>
defineProps({
    message: String,
    items: Array
});
</script>
