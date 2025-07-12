<div class="message-item mb-3 {{ $message->sender_id === auth()->id() ? 'text-end' : 'text-start' }}" data-message-id="{{ $message->id }}">
    <div class="d-inline-block {{ $message->sender_id === auth()->id() ? 'bg-primary text-white' : 'bg-light' }} rounded p-3" style="max-width: 70%;">
        
        <!-- Contenu du message -->
        <div class="message-content">
            @if($message->type === 'text')
                {!! nl2br(e($message->content)) !!}
            @elseif($message->type === 'image')
                <div class="message-image mb-2">
                    <img src="{{ Storage::url($message->file_path) }}" 
                         alt="Image" 
                         class="img-fluid rounded" 
                         style="max-width: 200px; max-height: 200px;"
                         onclick="openImageModal('{{ Storage::url($message->file_path) }}')">
                </div>
                @if($message->content)
                    <div class="mt-2">
                        {!! nl2br(e($message->content)) !!}
                    </div>
                @endif
            @elseif($message->type === 'file')
                <div class="message-file mb-2">
                    <div class="d-flex align-items-center p-2 bg-white rounded">
                        <i class="fas fa-file fa-2x text-primary me-3"></i>
                        <div class="flex-grow-1">
                            <div class="fw-bold">{{ basename($message->file_path) }}</div>
                            <small class="text-muted">{{ Storage::size($message->file_path) }} octets</small>
                        </div>
                        <a href="{{ route('messages.download', $message) }}" 
                           class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-download"></i>
                        </a>
                    </div>
                </div>
                @if($message->content)
                    <div class="mt-2">
                        {!! nl2br(e($message->content)) !!}
                    </div>
                @endif
            @endif
        </div>
        
        <!-- Métadonnées du message -->
        <div class="message-meta mt-2">
            <small class="{{ $message->sender_id === auth()->id() ? 'text-white-50' : 'text-muted' }}">
                {{ $message->created_at->format('d/m/Y H:i') }}
                @if($message->is_edited)
                    <span class="ms-1" title="Modifié le {{ $message->edited_at->format('d/m/Y H:i') }}">
                        <i class="fas fa-edit"></i>
                    </span>
                @endif
                @if($message->sender_id === auth()->id())
                    <i class="fas fa-check-double ms-1 {{ $message->is_read ? 'text-info' : '' }}"></i>
                @endif
            </small>
            
            <!-- Actions du message -->
            @if($message->sender_id === auth()->id())
                <div class="message-actions mt-1">
                    <button class="btn btn-sm btn-link text-white-50 p-0 me-2" 
                            onclick="deleteMessage({{ $message->id }})"
                            title="Supprimer">
                        <i class="fas fa-trash"></i>
                    </button>
                    @if($message->created_at->diffInMinutes(now()) <= 5)
                        <button class="btn btn-sm btn-link text-white-50 p-0" 
                                onclick="editMessage({{ $message->id }})"
                                title="Modifier (disponible pendant 5 minutes)">
                            <i class="fas fa-edit"></i>
                        </button>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div> 