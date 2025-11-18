@extends('modular-system::admin.layout')

@section('content')
<div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
    <div class="p-6 bg-white border-b border-gray-200">
        <h2 class="text-2xl font-bold mb-6">Settings</h2>
        
        <div class="space-y-6">
            @foreach($settings as $group => $groupSettings)
            <div class="border border-gray-200 rounded-lg p-6">
                <h3 class="text-lg font-semibold mb-4 capitalize">{{ $group }} Settings</h3>
                
                <form method="POST" action="{{ route('admin.settings.update', $group) }}">
                    @csrf
                    
                    @if(count($groupSettings) > 0)
                    <div class="space-y-4">
                        @foreach($groupSettings as $key => $value)
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                {{ ucwords(str_replace('_', ' ', str_replace($group.'.', '', $key))) }}
                            </label>
                            
                            @if(is_bool($value))
                            <select name="{{ $key }}" 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="1" {{ $value ? 'selected' : '' }}>Enabled</option>
                                <option value="0" {{ !$value ? 'selected' : '' }}>Disabled</option>
                            </select>
                            @elseif(is_numeric($value))
                            <input type="number" 
                                   name="{{ $key }}" 
                                   value="{{ $value }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                            @else
                            <input type="text" 
                                   name="{{ $key }}" 
                                   value="{{ $value }}"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                            @endif
                        </div>
                        @endforeach
                    </div>
                    
                    <div class="mt-6">
                        <button type="submit" 
                                class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                            Save {{ ucfirst($group) }} Settings
                        </button>
                    </div>
                    @else
                    <p class="text-gray-500">No settings available for this group.</p>
                    @endif
                </form>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endsection
