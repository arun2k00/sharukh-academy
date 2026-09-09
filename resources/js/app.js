import Alpine from 'alpinejs';
import collapse from '@alpinejs/collapse';
import lessonPlayer from './lesson-player';

Alpine.plugin(collapse);
Alpine.data('lessonPlayer', lessonPlayer);

window.Alpine = Alpine;
Alpine.start();
