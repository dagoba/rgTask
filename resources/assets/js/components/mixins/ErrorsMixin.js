export default {
    data() {
        return {
            errors: {}
        }
    },

    methods: {
        removeErrors(key) {
            Vue.delete(this.errors, key);
        }
    }
}