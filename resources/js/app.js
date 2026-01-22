// Alpine.js is already bundled with Livewire 3.x
// No need to import it separately

import collapse from '@alpinejs/collapse'
import dynamicHeader from './components/dynamicHeader'

// Register Alpine plugins and components before Alpine starts
document.addEventListener('alpine:init', () => {
  Alpine.plugin(collapse)
  Alpine.data('dynamicHeader', dynamicHeader)
})