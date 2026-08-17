import Alpine from 'alpinejs';
import { initReflectionSpeech } from './features/text-to-speech';

window.Alpine = Alpine;

Alpine.start();

document.addEventListener('DOMContentLoaded', initReflectionSpeech);
