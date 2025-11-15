import Alpine from 'alpinejs'
import collapse from '@alpinejs/collapse'
import focus from '@alpinejs/focus'
import 'flowbite'

// Register the plugins
Alpine.plugin(collapse)
Alpine.plugin(focus)

window.Alpine = Alpine

import './components/language-switcher'
import './components/header-sticky'
Alpine.start()
