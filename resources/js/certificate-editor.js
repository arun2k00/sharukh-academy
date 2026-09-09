/**
 * The certificate template visual editor: draggable field markers over the
 * uploaded artwork, kept in sync with the numeric inputs that are the real
 * source of truth submitted with the form. Positions are percentages of the
 * image, so they survive the artwork being replaced at a different
 * resolution.
 */
export default function certificateEditor({ fields, showQrCode, signature, previewBaseUrl }) {
    return {
        fields,
        showQrCode,
        signature,
        labels: {
            student_name: 'Name',
            course_name: 'Course',
            completion_date: 'Date',
            certificate_id: 'Cert. ID',
            instructor_name: 'Instructor',
            academy_name: 'Academy',
        },
        dragging: null,
        dragTarget: null,
        previewUrl: `${previewBaseUrl}?t=${Date.now()}`,

        visibleKeys() {
            return Object.keys(this.fields).filter((key) => this.fields[key].enabled);
        },

        startDrag(key) {
            this.dragging = key;
            this.dragTarget = 'field';
        },

        startDragSignature() {
            this.dragging = true;
            this.dragTarget = 'signature';
        },

        onMove(event) {
            if (!this.dragging || !this.$refs.canvas) {
                return;
            }

            const rect = this.$refs.canvas.getBoundingClientRect();
            const point = event.touches ? event.touches[0] : event;

            const clamp = (value) => Math.max(0, Math.min(100, Math.round(value * 100) / 100));
            const x = clamp(((point.clientX - rect.left) / rect.width) * 100);
            const y = clamp(((point.clientY - rect.top) / rect.height) * 100);

            if (this.dragTarget === 'signature') {
                this.signature.x = x;
                this.signature.y = y;
            } else {
                this.fields[this.dragging].x = x;
                this.fields[this.dragging].y = y;
            }
        },

        stopDrag() {
            this.dragging = null;
            this.dragTarget = null;
        },

        refreshPreview() {
            this.previewUrl = `${previewBaseUrl}?t=${Date.now()}`;
        },
    };
}
