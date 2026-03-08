@props(['message' => ''])

<div 
    x-data="{ show: false, message: '' }"
    @notify.window="show = true; message = $event.detail.message; setTimeout(() => show = false, 3000)"
    x-show="show"
    x-transition
    class="fixed top-4 right-4 z-50"
>
    <div class="alert alert-success shadow-lg">
        <div class="flex items-center gap-3">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 shrink-0 stroke-current" fill="none" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span x-text="message"></span>
        </div>
    </div>
</div>

<script>
    document.addEventListener('livewire:navigated', () => {
        Livewire.on('notify', (data) => {
            const event = new CustomEvent('notify', {
                detail: { message: data.message }
            });
            window.dispatchEvent(event);
        });
    });
</script>
