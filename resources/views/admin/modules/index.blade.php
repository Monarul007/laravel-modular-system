@extends('modular-system::admin.layout')

@section('content')
<div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
    <div class="p-6 bg-white border-b border-gray-200">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold">Modules</h2>
            <button onclick="document.getElementById('uploadModal').classList.remove('hidden')" 
                    class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-md">
                Upload Module
            </button>
        </div>

        @if(count($modules) > 0)
        <div class="space-y-4">
            @foreach($modules as $module)
            <div class="border border-gray-200 rounded-lg p-6">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <h3 class="text-lg font-semibold">{{ $module['name'] }}</h3>
                        <p class="text-gray-600 mt-1">{{ $module['description'] ?? 'No description' }}</p>
                        <div class="mt-2 flex items-center space-x-4 text-sm text-gray-500">
                            <span>Version: {{ $module['version'] ?? 'N/A' }}</span>
                            @if(isset($module['author']))
                            <span>Author: {{ $module['author'] }}</span>
                            @endif
                        </div>
                    </div>
                    
                    <div class="flex items-center space-x-2">
                        <span class="px-3 py-1 text-sm rounded-full {{ $module['enabled'] ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                            {{ $module['enabled'] ? 'Enabled' : 'Disabled' }}
                        </span>
                        
                        <form method="POST" action="{{ route('admin.modules.' . ($module['enabled'] ? 'disable' : 'enable')) }}" class="inline">
                            @csrf
                            <input type="hidden" name="name" value="{{ $module['name'] }}">
                            <button type="submit" 
                                    class="px-3 py-1 text-sm rounded {{ $module['enabled'] ? 'bg-yellow-100 text-yellow-800 hover:bg-yellow-200' : 'bg-green-100 text-green-800 hover:bg-green-200' }}">
                                {{ $module['enabled'] ? 'Disable' : 'Enable' }}
                            </button>
                        </form>
                        
                        <a href="{{ route('admin.modules.download', $module['name']) }}" 
                           class="px-3 py-1 text-sm rounded bg-blue-100 text-blue-800 hover:bg-blue-200">
                            Download
                        </a>
                        
                        <form method="POST" action="{{ route('admin.modules.uninstall') }}" 
                              class="inline" 
                              onsubmit="return confirm('Are you sure you want to uninstall {{ $module['name'] }}?')">
                            @csrf
                            <input type="hidden" name="name" value="{{ $module['name'] }}">
                            <button type="submit" 
                                    class="px-3 py-1 text-sm rounded bg-red-100 text-red-800 hover:bg-red-200">
                                Uninstall
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="text-center py-12 text-gray-500">
            <p>No modules installed yet.</p>
            <p class="mt-2">Upload a module to get started.</p>
        </div>
        @endif
    </div>
</div>

<!-- Upload Modal -->
<div id="uploadModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="flex justify-between items-center mb-4">
            <h3 class="text-lg font-semibold">Upload Module</h3>
            <button onclick="document.getElementById('uploadModal').classList.add('hidden')" 
                    class="text-gray-400 hover:text-gray-600">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        
        <form method="POST" action="{{ route('admin.modules.upload') }}" enctype="multipart/form-data">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Module ZIP File</label>
                <input type="file" name="module_zip" accept=".zip" required
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
            </div>
            
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Module Name (Optional)</label>
                <input type="text" name="module_name" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500"
                       placeholder="Leave empty to auto-detect">
            </div>
            
            <div class="flex justify-end space-x-2">
                <button type="button" 
                        onclick="document.getElementById('uploadModal').classList.add('hidden')"
                        class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300">
                    Cancel
                </button>
                <button type="submit" 
                        class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                    Upload
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
