@extends('modular-system::admin.layout')

@section('content')
<div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
    <div class="p-6 bg-white border-b border-gray-200">
        <h2 class="text-2xl font-bold mb-6">Dashboard</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-blue-50 p-6 rounded-lg">
                <h3 class="text-lg font-semibold text-blue-900">Total Modules</h3>
                <p class="text-3xl font-bold text-blue-600">{{ $stats['total_modules'] }}</p>
            </div>
            
            <div class="bg-green-50 p-6 rounded-lg">
                <h3 class="text-lg font-semibold text-green-900">Enabled Modules</h3>
                <p class="text-3xl font-bold text-green-600">{{ $stats['enabled_modules'] }}</p>
            </div>
            
            <div class="bg-gray-50 p-6 rounded-lg">
                <h3 class="text-lg font-semibold text-gray-900">Disabled Modules</h3>
                <p class="text-3xl font-bold text-gray-600">{{ $stats['disabled_modules'] }}</p>
            </div>
        </div>

        @if(count($recent_modules) > 0)
        <div>
            <h3 class="text-xl font-bold mb-4">Recent Modules</h3>
            <div class="space-y-3">
                @foreach($recent_modules as $module)
                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                    <div>
                        <h4 class="font-semibold">{{ $module['name'] }}</h4>
                        <p class="text-sm text-gray-600">{{ $module['description'] ?? 'No description' }}</p>
                    </div>
                    <span class="px-3 py-1 text-sm rounded-full {{ $module['enabled'] ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                        {{ $module['enabled'] ? 'Enabled' : 'Disabled' }}
                    </span>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
