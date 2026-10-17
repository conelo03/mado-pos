import './bootstrap';
import TomSelect from "tom-select";
import "tom-select/dist/css/tom-select.css";

// Expose TomSelect globally so Alpine x-data can access it
window.TomSelect = TomSelect;

/**
 * Auto-init Tom Select for elements with [data-tom-select].
 *
 * Attributes:
 *   data-tom-select              — marks the element for init
 *   data-livewire-prop           — Livewire property name to $wire.set() on change
 *   data-livewire-event          — legacy: Livewire event name to dispatch on change
 */
function initTomSelect() {
    document.querySelectorAll('[data-tom-select]').forEach(el => {
        if (el.tomselect) return;

        const prop  = el.dataset.livewireProp;
        const event = el.dataset.livewireEvent;

        new TomSelect(el, {
            plugins: ['clear_button'],
            // Append dropdown to body so DaisyUI modal overflow doesn't clip it
            dropdownParent: document.body,
            onChange: function(value) {
                if (prop) {
                    const wireEl = el.closest('[wire\\:id]');
                    if (wireEl) {
                        const id = wireEl.getAttribute('wire:id');
                        Livewire.find(id).set(prop, value);
                    }
                } else if (event) {
                    Livewire.dispatch(event, value);
                }
            }
        });
    });
}

const observer = new MutationObserver(() => {
    initTomSelect();
});

observer.observe(document.body, {
    childList: true,
    subtree: true
});

document.addEventListener('DOMContentLoaded', initTomSelect);