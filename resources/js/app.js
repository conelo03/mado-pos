import './bootstrap';
import TomSelect from "tom-select";
import "tom-select/dist/css/tom-select.css";

// function initTomSelect() {
//     document.querySelectorAll('[data-tom-select]').forEach(el => {
//         if (el.tomselect) return;

//         console.log('INIT TOM:', el);

//         new TomSelect(el, {
//             plugins: ['clear_button'],
//             onChange: function(value) {
//                 Livewire.dispatch('setCustomer', { value: value })
//             }
//         });
//     });
// }

// // 🔥 jalan terus tiap DOM berubah
// const observer = new MutationObserver(() => {
//     initTomSelect();
// });

// observer.observe(document.body, {
//     childList: true,
//     subtree: true
// });

// // initial load
// document.addEventListener('DOMContentLoaded', initTomSelect);

function initTomSelect() {
    document.querySelectorAll('[data-tom-select]').forEach(el => {
        if (el.tomselect) return;

        new TomSelect(el, {
            plugins: ['clear_button'],
            onChange: function(value) {
                Livewire.dispatch('setCustomer', value);
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