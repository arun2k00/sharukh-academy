import Alpine from 'alpinejs';
import collapse from '@alpinejs/collapse';
import lessonPlayer from './lesson-player';
import certificateEditor from './certificate-editor';

Alpine.plugin(collapse);
Alpine.data('lessonPlayer', lessonPlayer);
Alpine.data('certificateEditor', certificateEditor);

window.Alpine = Alpine;
Alpine.start();
