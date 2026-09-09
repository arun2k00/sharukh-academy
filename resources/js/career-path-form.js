export default function careerPathForm(initialRows) {
    return {
        rows: (initialRows.length ? initialRows : [{ course_id: '', stage_label: '', sort_order: 0 }])
            .map((row, index) => ({ ...row, key: index })),
        nextKey: initialRows.length || 1,

        addRow() {
            this.rows.push({ course_id: '', stage_label: '', sort_order: this.rows.length, key: this.nextKey++ });
        },

        removeRow(index) {
            this.rows.splice(index, 1);
        },
    };
}
