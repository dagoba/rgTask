<template>
    <div class="input-group" :class="{'has-error': errors.description}">
        <span class="input-group-addon">
            <span class="glyphicon glyphicon-plus text-success" aria-hidden="true"></span>
        </span>
        
        <input
            type="text"
            class="form-control"
            placeholder="Start typing here to create task..."
            v-model="description"
            @keyup.enter="add"
            @keyup.esc="cancel"
            @focus="removeErrors('description')"
        >
        
        <span class="input-group-btn">
            <button class="btn btn-success" type="button" @click="add">Add Task</button>
        </span>
    </div>
</template>

<script>
import ErrorsMixin from './mixins/ErrorsMixin.js';

export default {
    mixins: [
        ErrorsMixin
    ],

    props: ['projectId'],

    data() {
        return {
            description: '',
            blueprint: {
                done: false
            }
        }
    },

    methods: {
        add() {
            const task = Object.assign({}, this.blueprint, {description: this.description});

            axios.post(`/projects/${this.projectId}/tasks`, task).then(response => {
                this.$emit('add', response.data);
                this.description = '';
            }, error => {
                const response = error.response;

                if (response.status === 422) {
                    this.errors = response.data;
                }
            });
        },

        cancel() {
            this.description = '';
        }
    }
}
</script>

<style scoped>
    div {
        margin-bottom: 15px;
    }
</style>
