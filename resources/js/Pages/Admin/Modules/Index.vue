<template>
  <Layout>
    <div class="bg-white shadow rounded-lg">
      <div class="px-4 py-5 sm:p-6">
        <!-- Flash Messages -->
        <div v-if="$page.props.flash?.success" class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
          {{ $page.props.flash.success }}
        </div>
        <div v-if="$page.props.flash?.error" class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
          {{ $page.props.flash.error }}
        </div>
        <div v-if="$page.props.errors?.upload" class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
          {{ $page.props.errors.upload }}
        </div>

        <div class="flex justify-between items-center mb-6">
          <h2 class="text-lg font-medium text-gray-900">Module Management</h2>
          <div class="flex space-x-3">
            <button
              @click="showUploadForm = !showUploadForm"
              class="bg-green-600 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-green-700"
            >
              Upload Module
            </button>
            <button
              @click="refreshModules"
              class="bg-indigo-600 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-indigo-700"
            >
              Refresh
            </button>
          </div>
        </div>

        <!-- Upload Form -->
        <div v-if="showUploadForm" class="bg-gray-50 p-6 rounded-lg mb-6">
          <h3 class="text-lg font-medium text-gray-900 mb-4">Upload Module ZIP</h3>
          <form @submit.prevent="uploadModule" enctype="multipart/form-data">
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
              <div>
                <label for="module_zip" class="block text-sm font-medium text-gray-700">
                  Module ZIP File
                </label>
                <input
                  id="module_zip"
                  ref="fileInput"
                  type="file"
                  accept=".zip"
                  required
                  class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100"
                />
              </div>
              <div>
                <label for="module_name" class="block text-sm font-medium text-gray-700">
                  Module Name (Optional)
                </label>
                <input
                  id="module_name"
                  v-model="uploadForm.module_name"
                  type="text"
                  placeholder="Leave empty to use name from ZIP"
                  class="mt-1 shadow-sm focus:ring-indigo-500 focus:border-indigo-500 block w-full sm:text-sm border-gray-300 rounded-md"
                />
              </div>
            </div>
            <div class="mt-4 flex justify-end space-x-3">
              <button
                type="button"
                @click="showUploadForm = false"
                class="bg-gray-300 text-gray-700 px-4 py-2 rounded-md text-sm font-medium hover:bg-gray-400"
              >
                Cancel
              </button>
              <button
                type="submit"
                :disabled="uploading"
                class="bg-green-600 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-green-700 disabled:opacity-50"
              >
                {{ uploading ? 'Uploading...' : 'Upload Module' }}
              </button>
            </div>
          </form>
        </div>

        <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 md:rounded-lg">
          <table class="min-w-full divide-y divide-gray-300">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Module
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Version
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Status
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Dependencies
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Actions
                </th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr v-for="(module, name) in modules" :key="name">
                <td class="px-6 py-4 whitespace-nowrap">
                  <div>
                    <div class="text-sm font-medium text-gray-900">{{ module.name }}</div>
                    <div class="text-sm text-gray-500">{{ module.description }}</div>
                  </div>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                  {{ module.version }}
                </td>
                <td class="px-6 py-4 whitespace-nowrap">
                  <span
                    :class="[
                      'inline-flex px-2 py-1 text-xs font-semibold rounded-full',
                      module.enabled
                        ? 'bg-green-100 text-green-800'
                        : 'bg-red-100 text-red-800'
                    ]"
                  >
                    {{ module.enabled ? 'Enabled' : 'Disabled' }}
                  </span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                  <span v-if="module.dependencies && module.dependencies.length > 0">
                    {{ module.dependencies.join(', ') }}
                  </span>
                  <span v-else class="text-gray-400">None</span>
                </td>
                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                  <div class="flex space-x-2">
                    <button
                      v-if="module.enabled"
                      @click="disableModule(name)"
                      :disabled="processing"
                      class="text-red-600 hover:text-red-900 disabled:opacity-50"
                    >
                      Disable
                    </button>
                    <button
                      v-else
                      @click="enableModule(name)"
                      :disabled="processing"
                      class="text-indigo-600 hover:text-indigo-900 disabled:opacity-50"
                    >
                      Enable
                    </button>
                    <button
                      @click="downloadModule(name)"
                      :disabled="processing"
                      class="text-blue-600 hover:text-blue-900 disabled:opacity-50"
                    >
                      Download
                    </button>
                    <button
                      @click="uninstallModule(name)"
                      :disabled="processing"
                      class="text-red-600 hover:text-red-900 disabled:opacity-50"
                    >
                      Uninstall
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </Layout>
</template>

<script setup>
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'
import Layout from '../Layout.vue'

const props = defineProps({
  modules: {
    type: Object,
    required: true
  }
})

const processing = ref(false)
const uploading = ref(false)
const showUploadForm = ref(false)
const fileInput = ref(null)
const uploadForm = ref({
  module_name: ''
})

const enableModule = (name) => {
  processing.value = true
  router.post(route('admin.modules.enable'), { name }, {
    onFinish: () => processing.value = false
  })
}

const disableModule = (name) => {
  processing.value = true
  router.post(route('admin.modules.disable'), { name }, {
    onFinish: () => processing.value = false
  })
}

const uploadModule = () => {
  const fileElement = fileInput.value
  if (!fileElement.files[0]) {
    alert('Please select a ZIP file')
    return
  }

  uploading.value = true
  
  router.post(route('admin.modules.upload'), {
    module_zip: fileElement.files[0],
    module_name: uploadForm.value.module_name || null
  }, {
    forceFormData: true,
    onFinish: () => {
      uploading.value = false
    },
    onSuccess: () => {
      showUploadForm.value = false
      uploadForm.value.module_name = ''
      if (fileElement) {
        fileElement.value = ''
      }
    },
    onError: (errors) => {
      console.error('Upload errors:', errors)
    }
  })
}

const uninstallModule = (name) => {
  if (confirm(`Are you sure you want to uninstall module '${name}'? This action cannot be undone.`)) {
    processing.value = true
    router.post(route('admin.modules.uninstall'), { name }, {
      onFinish: () => processing.value = false
    })
  }
}

const downloadModule = (name) => {
  window.location.href = route('admin.modules.download', name)
}

const refreshModules = () => {
  router.reload()
}
</script>
