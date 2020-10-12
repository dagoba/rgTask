export default {
    data() {
        return {
            hover: false,
        };
    },

    methods: {
        showIcons() {
            this.hover = true;
        },

        hideIcons() {
            this.hover = false;
        }
    }
}