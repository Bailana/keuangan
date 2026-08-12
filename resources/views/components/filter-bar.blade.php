@props(['filters' => []])

@php
    $current = request()->only(array_keys($filters));
    $hasFilters = request()->hasAny($filters);
@endphp

<div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-4 mb-6">
    <form method="GET" class="space-y-3">
        <div class="flex flex-wrap gap-3">
            @foreach($filters as $key => $filter)
                <div class="flex-1 min-w-[160px]">
                    @if($filter['type'] === 'search')
                        <input type="text" name="{{ $key }}" value="{{ old($key, $current[$key] ?? '') }}"
                            placeholder="{{ $filter['placeholder'] ?? 'Cari...' }}"
                            class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-gray-50">
                    @elseif($filter['type'] === 'select')
                        <select name="{{ $key }}"
                            class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-gray-50">
                            <option value="">{{ $filter['placeholder'] ?? 'Semua' }}</option>
                            @foreach($filter['options'] as $optValue => $optLabel)
                                <option value="{{ $optValue }}" {{ ($current[$key] ?? '') == $optValue ? 'selected' : '' }}>
                                    {{ $optLabel }}
                                </option>
                            @endforeach
                        </select>
                    @elseif($filter['type'] === 'date')
                        <input type="date" name="{{ $key }}" value="{{ old($key, $current[$key] ?? '') }}"
                            class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-gray-50">
                    @endif
                </div>
            @endforeach

            <div class="flex items-end gap-2 flex-shrink-0">
                <button type="submit"
                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.335a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/></svg>
                    Filter
                </button>
                @if($hasFilters)
                    <a href="{{ request()->url() }}?{{ http_build_query(array_diff_key(request()->all(), $current)) }}"
                        class="px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors">
                        Reset
                    </a>
                @endif
            </div>
        </div>
    </form>
</div>
