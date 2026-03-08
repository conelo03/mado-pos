@props(['title' => 'Confirm', 'message' => 'Are you sure?', 'confirmText' => 'Refund', 'cancelText' => 'Cancel', 'onConfirm' => ''])

<div 
    x-data="{ open: false }"
    x-show="open"
    @confirm-refund-dialog.window="open = true; $nextTick(() => $refs.confirmBtn.focus())"
    class="fixed inset-0 z-50 flex items-center justify-center"
    style="display: none;"
>
    <!-- Backdrop -->
    <div class="absolute inset-0 bg-black/50" @click="open = false"></div>
    
    <!-- Dialog -->
    <div class="relative bg-base-100 rounded-lg shadow-lg p-6 max-w-sm mx-4">
        <h3 class="font-bold text-lg mb-2">{{ $title }}</h3>
        <p class="text-sm opacity-75 mb-6">{{ $message }}</p>
        
        <div class="flex gap-3 justify-end">
            <button 
                @click="open = false"
                class="btn btn-sm btn-ghost"
            >
                {{ $cancelText }}
            </button>
            <button 
                x-ref="confirmBtn"
                @click="open = false; {{ $onConfirm }}"
                class="btn btn-sm btn-error"
            >
                {{ $confirmText }}
            </button>
        </div>
    </div>
</div>
