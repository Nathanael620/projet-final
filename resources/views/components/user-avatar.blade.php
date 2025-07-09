@props([
    'user',
    'size' => 'md',
    'showStatus' => false,
    'showRole' => false,
    'clickable' => false,
    'class' => ''
])

@php
    $sizeClasses = [
        'xs' => 'w-8 h-8',
        'sm' => 'w-10 h-10',
        'md' => 'w-12 h-12',
        'lg' => 'w-16 h-16',
        'xl' => 'w-20 h-20',
        '2xl' => 'w-24 h-24',
        '3xl' => 'w-32 h-32',
    ];
    
    $sizeClass = $sizeClasses[$size] ?? $sizeClasses['md'];
    
    $statusColors = [
        'online' => 'bg-green-500',
        'offline' => 'bg-gray-400',
        'away' => 'bg-yellow-500',
        'busy' => 'bg-red-500',
    ];
@endphp

<div class="relative inline-block {{ $class }}">
    @if($clickable)
        <button type="button" 
                class="focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 rounded-full"
                onclick="openUserProfile({{ $user->id }})">
    @endif
    
    <div class="relative">
        @if($user->avatar)
            <img src="{{ Storage::url($user->avatar) }}" 
                 alt="{{ $user->name }}" 
                 class="{{ $sizeClass }} rounded-full object-cover border-2 border-gray-200 shadow-sm"
                 loading="lazy"
                 onerror="this.onerror=null; this.src='/images/default-avatar.png';">
        @else
            <div class="{{ $sizeClass }} rounded-full bg-gradient-to-br from-blue-400 to-purple-500 flex items-center justify-center text-white font-semibold border-2 border-gray-200 shadow-sm">
                {{ strtoupper(substr($user->name, 0, 2)) }}
            </div>
        @endif
        
        @if($showStatus)
            <div class="absolute -bottom-1 -right-1 w-4 h-4 {{ $statusColors['online'] }} border-2 border-white rounded-full"></div>
        @endif
        
        @if($showRole)
            <div class="absolute -top-1 -right-1">
                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium 
                    {{ $user->isTutor() ? 'bg-green-100 text-green-800' : 'bg-blue-100 text-blue-800' }}">
                    {{ $user->isTutor() ? 'Tuteur' : 'Étudiant' }}
                </span>
            </div>
        @endif
    </div>
    
    @if($clickable)
        </button>
    @endif
</div>

@if($clickable)
<script>
function openUserProfile(userId) {
    window.open(`/profile/${userId}`, '_blank');
}
</script>
@endif 