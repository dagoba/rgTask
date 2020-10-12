<template>
    <div class="panel panel-primary">
        <div    
            class="panel-heading"
            @mouseenter.stop="showIcons"
            @mouseleave.stop="hideIcons"
        >
            <div class="row">
                <div class="col-xs-1 col-sm-1 col-md-1 col-lg-1">
                    <span class="glyphicon glyphicon-calendar" aria-hidden="true"></span>
                </div>
                <div class="col-xs-8 col-sm-9 col-md-9 col-lg-9">
                    <div class="form-group" :class="{'has-error': errors.title}" v-show="editing">
                        <input
                            ref="titleInput"
                            type="text"
                            class="form-control"
                            v-model="field"
                            @keyup.enter="submit"
                            @keyup.esc="cancel"
                            @focus="removeErrors('title')"
                        >
                    </div>

                    <span v-show="!editing" @dblclick="edit" class="project-title">
                        {{ project.title }}
                    </span>
                </div>
                <div class="col-xs-3 col-sm-2 col-md-2 col-lg-2">
                    <div v-if="hover" class="pull-right">
                        <span v-if="editing" class="glyphicon glyphicon-ban-circle text-danger" aria-hidden="true" @click="cancel"></span>
                        <span v-else class="glyphicon glyphicon-pencil" aria-hidden="true" @click="edit"></span>

                        <span class="glyphicon glyphicon-trash" aria-hidden="true" @click="remove"></span>
                    </div>
                </div>
            </div>
        </div>


        <div class="panel-body">
            <task-form :project-id="project.id" @add="addTask"></task-form>

            <ul class="list-group">
                <task
                    v-for="(task, index) in project.tasks"
                    :key="task.id"
                    :task.sync="task"

                    :show-move-up="index !== 0"
                    :show-move-down="index < (project.tasks.length - 1)"

                    @remove="removeTask(index)"

                    @move-up="moveTaskUp"
                    @move-down="moveTaskDown"
                ></task>    
            </ul>
        </div>
    </div>
</template>

<script>
import TaskForm from './TaskForm.vue';
import Task from './Task.vue';
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
        TaskForm,
        Task
    },

    props: ['project'],

    created() {
        this.editValuePath = 'project.title';
        this.sortTasks();
    },

    methods: {
        submit() {
            axios.put(
                `/projects/${this.project.id}`,
                {title: this.field}
            ).then(response => {
                this.project.title = response.data.title;
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
                text: 'You are sure you want to delete this project?',
                buttons: [
                    {
                        title: 'Yes',
                        handler: () => {
                            axios.delete(`/projects/${this.project.id}`).then(response => {
                                this.$emit('remove');
                                this.$modal.hide('dialog');
                            }, error => {
                                this.$modal.hide('dialog');
                            });
                        },
                    },
                    { title: 'No' }
                ]
            });
        },

        addTask(task) {
            if (this.project.tasks === undefined || this.project.tasks === null) {
                Vue.set(this.project, 'tasks', []);
            }

            this.project.tasks.push(task);
            
            if (this.project.tasks_order === undefined || this.project.tasks_order === null) {
                Vue.set(this.project, 'tasks_order', []);
            }

            this.project.tasks_order.push(task.id);
        },

        removeTask(index) {
            Vue.delete(this.project.tasks, index);
            Vue.delete(this.project.tasks_order, index);
            this.project.tasks_order = this.project.tasks_order.filter(val => val);
        },

        sortTasks() {
            const order = this.project.tasks_order || [];

            this.project.tasks && this.project.tasks.sort((a, b) => {
                return order.indexOf(a.id) - order.indexOf(b.id);
            });
        },

        moveTaskUp(taskId){
            const tasksOrder = this.project.tasks_order || [];
            let index = tasksOrder.indexOf(taskId);

            if (index <= 0) {
                return;
            }

            index -= 1;
            this.project.tasks_order = tasksOrder.reduce((carry, id, i) => {
                if (i === index) {
                    carry.push(taskId);
                }
                
                if (id !== taskId) {
                    carry.push(id)
                }

                return carry;
            }, []);

            this.updateTasksOrder();
        },

        moveTaskDown(taskId) {
            const tasksOrder = this.project.tasks_order || [];
            let index = tasksOrder.indexOf(taskId);

            if (index < 0 || index === tasksOrder.length - 1) {
                return;
            }

            index += 1;
            this.project.tasks_order = tasksOrder.reduce((carry, id, i) => {
                if (id !== taskId) {
                    carry.push(id)
                }
                
                if (i === index) {
                    carry.push(taskId);
                }

                return carry;
            }, []);

            this.updateTasksOrder();
        },

        updateTasksOrder() {
            axios.put(
                `/projects/${this.project.id}`,
                this.project
            ).then(response => {
                this.sortTasks()
            }, error => {

            });
        }
    }
}
</script>

<style scoped>
    .panel-heading {
        height: 56px;
    }

    .panel-heading .glyphicon {
        font-size: 2em;
        margin-top: 3px;
    }

    .form-group {
        margin-bottom: 10px;
    }

    .project-title {
        font-size: 22px;
    }

    .list-group {
        margin: 0;
    }
</style>
