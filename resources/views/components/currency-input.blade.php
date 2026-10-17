@props([
    'wireModel',
    'placeholder' => '0',
    'label'       => null,
    'size'        => '',
    'live'        => false,
    'liveMethod'  => null,
    'debounce'    => 0,
])

@php
    $sizeClass      = $size === 'sm' ? 'input-sm' : '';
    $resolvedMethod = $liveMethod ?? 'updated' . str($wireModel)->camel()->ucfirst();
    $debounceMs     = (int) $debounce;
    $isLive         = $live || $liveMethod;
@endphp

<div
    x-data="{
        display: '',
        prop: '{{ $wireModel }}',
        isLive: {{ $isLive ? 'true' : 'false' }},
        debounceMs: {{ $debounceMs }},
        _timer: null,
        format(val) {
            if (val === '' || val === null || val === undefined) return '';
            const n = Math.round(parseFloat(val));
            return isNaN(n) ? '' : n.toLocaleString('id-ID');
        },
        onInput(e) {
            const raw = e.target.value.replace(/\D/g, '');
            this.display = raw ? Number(raw).toLocaleString('id-ID') : '';
            const numeric = raw ? Number(raw) : '';
            if (this.isLive) {
                clearTimeout(this._timer);
                this._timer = setTimeout(() => {
                    $wire.set(this.prop, numeric, true);
                }, this.debounceMs);
            } else {
                $wire.set(this.prop, numeric);
            }
        },
        init() {
            this.display = this.format($wire.get(this.prop));
            this.$watch(() => $wire.get(this.prop), val => {
                if (document.activeElement !== this.$refs.input) {
                    this.display = this.format(val);
                }
            });
        }
    }"
    x-init="init()"
>
    @if($label)
        <label class="label">
            <span class="label-text">{{ $label }}</span>
        </label>
    @endif

    <div class="input input-bordered w-full flex items-center gap-1 {{ $sizeClass }}">
        <span class="text-base-content/50 text-sm shrink-0">Rp</span>
        <input
            x-ref="input"
            type="text"
            inputmode="numeric"
            x-model="display"
            @input="onInput($event)"
            placeholder="{{ $placeholder }}"
            class="flex-1 bg-transparent outline-none min-w-0 {{ $sizeClass }}"
            {{ $attributes->except(['wire:model', 'class']) }}
        >
    </div>
</div>
