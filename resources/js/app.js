import Alpine from 'alpinejs';
import collapse from '@alpinejs/collapse';
import lessonPlayer from './lesson-player';
import certificateEditor from './certificate-editor';
import careerPathForm from './career-path-form';

Alpine.plugin(collapse);
Alpine.data('lessonPlayer', lessonPlayer);
Alpine.data('certificateEditor', certificateEditor);
Alpine.data('careerPathForm', careerPathForm);

window.Alpine = Alpine;
Alpine.start();
