export default {
    data() {
        return {
            editing: false,
            editValuePath: null,
            field: null
        }
    },

    methods: {
        edit() {
            this.field = _.get(this, this.editValuePath);
            this.editing = true;
        },

        cancel() {
            this.editing = false;
        },
    }
}