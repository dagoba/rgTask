<template>
    <li
        class="list-group-item"
        @mouseenter.stop="showIcons"
        @mouseleave.stop="hideIcons"
    >
        <div class="row">
            <div class="col-xs-1 col-sm-1 col-md-1 col-lg-1">
                <input type="checkbox" v-model="task.done" :disabled="editing" @change="submit(false)">
            </div>
            <div class="col-xs-7 col-sm-8  col-md-8 col-lg-8">
                <div class="form-group" :class="{'has-error': errors.description}" v-if="editing">
                    <input
                        :ref="'descriptionInput'"
                        type="text"
                        class="form-control"
                        v-model="field"
                        @keyup.enter="submit(true)"
                        @keyup.esc="cancel"
                        @focus="removeErrors('description')"
                    >
                </div>

                <span v-else @dblclick="edit">
                    {{ task.description }}
                </span>
            </div>
            <div class="col-xs-4 col-sm-3 col-md-3 col-lg-3">
                <div v-show="hover" class="pull-right">
                    <span v-if="editing" class="glyphicon glyphicon-ban-circle text-danger" aria-hidden="true" @click="cancel"></span>
                    <span v-else class="glyphicon glyphicon-pencil" aria-hidden="true" @click="edit"></span>
                    
                    <datepicker
                        calendar-class="inner-calendar"
                        input-class="invisible-input"
                        :calendar-button="true"
                        calendar-button-icon="glyphicon glyphicon-calendar"
                        
                        :clear-button="task.deadline !== null"
                        clear-button-icon="glyphicon glyphicon-calendar text-danger"

                        :value="task.deadline"

                        @selected="setDeadline"
                    ></datepicker> 
                    
                    <span v-show="showMoveUp" class="glyphicon glyphicon-triangle-top" aria-hidden="true" @click="moveUp"></span>
                    <span v-show="showMoveDown" class="glyphicon glyphicon-triangle-bottom" aria-hidden="true" @click="moveDown"></span>

                    <span class="glyphicon glyphicon-trash" aria-hidden="true" @click="remove"></span>
                </div>
            </div>
        </div>
    </li>
</template>

<script>
import Datepicker from 'vuejs-datepicker';
import IconsMixin from './mixins/IconsMixin.js';
import EditMixin from './mixins/EditMixin.js';
import ErrorsMixin from './mixins/ErrorsMixin.js';

export default {
    mixins: [
        IconsMixin,
        EditMixin,
        ErrorsMixin
    ],

    components: {
        Datepicker
    },

    props: ['task', 'showMoveUp', 'showMoveDown'],

    created() {
        this.editValuePath = 'task.description';
    },

    methods: {
        submit(changeField = false) {
            const task = changeField ? Object.assign({}, this.task, {description: this.field}) : this.task;

            axios.put(`/projects/${this.task.project_id}/tasks/${task.id}`, task).then(response => {
                this.task.description = response.data.description;
                this.cancel();
            }, error => {
                const response = error.response;

                if (response.status === 422) {
                    this.errors = response.data;
                }
            });
        },

        remove() {
            this.$modal.show('dialog', {
                text: 'You are sure you want to delete this task?',
                buttons: [
                    {
                        title: 'Yes',
                        handler: () => {
                            axios.delete(`/projects/${this.task.project_id}/tasks/${this.task.id}`).then(response => {
                                this.$modal.hide('dialog');
                                this.$emit('remove');
                            }, error => {
                                this.$modal.hide('dialog');
                            });
                        },
                    },
                    { title: 'No' }
                ]
            });
        },

        moveUp()
        {
            this.$emit('move-up', this.task.id);
        },

        moveDown()
        {
            this.$emit('move-down', this.task.id);
        },

        setDeadline(date) {
            this.task.deadline = !date ? date : new Date(date).toISOString().substr(0, 10);

            this.submit(false);
        }
    }
}
</script>

<style scoped>
    .form-group {
        margin: 0;
    }

    .form-group input {
        padding: 0;
        height: 20px;
    }

    .vdp-datepicker {
        display: inline-block;
    }
</style>
