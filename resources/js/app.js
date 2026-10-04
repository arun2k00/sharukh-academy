import Alpine from 'alpinejs';
import collapse from '@alpinejs/collapse';
import lessonPlayer from './lesson-player';
import certificateEditor from './certificate-editor';
import careerPathForm from './career-path-form';
import initButtonSound from './button-sound';
import scrambleText from './scramble-text';
import countdownTimer from './countdown-timer';

Alpine.plugin(collapse);
Alpine.data('lessonPlayer', lessonPlayer);
Alpine.data('certificateEditor', certificateEditor);
Alpine.data('careerPathForm', careerPathForm);
Alpine.data('countdownTimer', countdownTimer);

window.Alpine = Alpine;
window.scrambleText = scrambleText;
Alpine.start();

initButtonSound();
