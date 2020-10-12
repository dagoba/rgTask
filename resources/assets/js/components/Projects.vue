<template>
    <div>
        <project
            v-for="(project, index) in projects"
            :key="project.id"
            :project.sync="project"
            @remove="remove(index)"
        >
        </project>

        <div class="text-center">
            <button type="button" class="btn btn-primary" @click="add">
                <span class="glyphicon glyphicon-plus" aria-hidden="true"></span> Add TODO List
            </button>
        </div>
    </div>
</template>

<script>
import Project from './Project.vue';

export default {
    components: {
        Project
    },

    props: ['dataProjects', 'currentPage'],

    data() {
        return {
            projects: [],
            boilerplate: {
                tasks: []
            }
        };
    },

    created() {
        if (this.dataProjects.length === 0 && this.currentPage > 1) {
            window.location.replace('/');
        }

        this.projects = this.dataProjects;
    },

    methods: {
        add() {
            const project = Object.assign({}, this.boilerplate, {title: "New TODO List"});
            
            axios.post('/projects', project).then(response => {
                window.location.replace('/');
            }, error => {
                console.log(`Error: ${error}`);
            });
        },

        remove(index) {
            window.location.reload();
        }
    }
}
</script>

