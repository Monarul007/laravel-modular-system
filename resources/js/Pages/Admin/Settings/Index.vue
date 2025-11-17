<template>
  <Layout>
    <div class="bg-white shadow rounded-lg">
      <div class="px-4 py-5 sm:p-6">
        <h2 class="text-lg font-medium text-gray-900 mb-6">Settings Management</h2>

        <!-- Settings Groups Tabs -->
        <div class="border-b border-gray-200 mb-6">
          <nav class="-mb-px flex space-x-8">
            <button
              v-for="group in settingsGroups"
              :key="group"
              @click="activeGroup = group"
              :class="[
                'py-2 px-1 border-b-2 font-medium text-sm',
                activeGroup === group
                  ? 'border-indigo-500 text-indigo-600'
                  : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'
              ]"
            >
              {{ group.charAt(0).toUpperCase() + group.slice(1) }}
            </button>
          </nav>
        </div>

        <!-- Settings Form -->
        <form @submit.prevent="saveSettings" class="space-y-6">
          <div v-for="(value, key) in currentSettings" :key="key" class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
            <div class="sm:col-span-3">
              <label :for="key" class="block text-sm font-medium text-gray-700">
                {{ formatKey(key) }}
              </label>
              <div class="mt-1">
                <input
                  v-if="typeof value === 'string' || typeof value === 'number'"
                  :id="key"
                  v-model="currentSettings[key]"
                  :type="typeof value === 'number' ? 'number' : 'text'"
                  class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
                />
                <select
                  v-else-if="typeof value === 'boolean'"
                  :id="key"
                  v-model="currentSettings[key]"
                  class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
                >
                  <option :value="true">Yes</option>
                  <option :value="false">No</option>
                </select>
                <textarea
                  v-else
                  :id="key"
                  v-model="currentSettings[key]"
                  rows="3"
                  class="shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
                />
              </div>
            </div>
          </div>

          <!-- Add New Setting -->
          <div class="border-t border-gray-200 pt-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Add New Setting</h3>
            <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
              <div class="sm:col-span-2">
                <label for="newKey" class="block text-sm font-medium text-gray-700">Key</label>
                <input
                  id="newKey"
                  v-model="newSetting.key"
                  type="text"
                  class="mt-1 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
                />
              </div>
              <div class="sm:col-span-2">
                <label for="newValue" class="block text-sm font-medium text-gray-700">Value</label>
                <input
                  id="newValue"
                  v-model="newSetting.value"
                  type="text"
                  class="mt-1 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
                />
              </div>
              <div class="sm:col-span-2 flex items-end">
                <button
                  type="button"
                  @click="addSetting"
                  class="bg-green-600 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-green-700"
                >
                  Add Setting
                </button>
              </div>
            </div>
          </div>

          <div class="flex justify-end">
            <button
              type="submit"
              :disabled="processing"
              class="bg-indigo-600 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-indigo-700 disabled:opacity-50"
            >
              {{ processing ? 'Saving...' : 'Save Settings' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </Layout>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import { router } from '@inertiajs/vue3'
import Layout from '../Layout.vue'

const props = defineProps({
  settings: {
    type: Object,
    required: true
  }
})

const processing = ref(false)
const activeGroup = ref('general')
const currentSettings = ref({})
const newSetting = ref({ key: '', value: '' })

const settingsGroups = computed(() => {
  return Object.keys(props.settings)
})

const formatKey = (key) => {
  return key.split('.').pop().replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase())
}

watch(activeGroup, (newGroup) => {
  currentSettings.value = { ...props.settings[newGroup] }
}, { immediate: true })

const saveSettings = () => {
  processing.value = true
  router.post(route('admin.settings.update', activeGroup.value), currentSettings.value, {
    onFinish: () => processing.value = false
  })
}

const addSetting = () => {
  if (newSetting.value.key && newSetting.value.value) {
    currentSettings.value[newSetting.value.key] = newSetting.value.value
    newSetting.value = { key: '', value: '' }
  }
}
</script>
