<template>
    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="row">
                    <div class="col-md-6" v-if="capture_project_id==true && project_id !== null">
                        <ProjectSelected v-bind:project_id="project_id"/>
                    </div>
                    <div v-if="projects && projects.data && projects.data.length > 0" class="offset-md-2 col-md-4">
                        <input type="text" class="form-control my-3" placeholder="Search project by name" @change="searchProject($event)" @keyup="searchProject($event)"/>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header text-center">
                        <h2>Projects</h2>
                    </div>
                    <div class="card-body">
                        <div class="row" v-if="projects && projects.data && projects.data.length > 0">
                            <div class="col-md-6 p-2" v-for="(project,index) in projects.data" :key="index">
                                <button class="border p-3" style="width: 100%;" :value="project.id" @click="setProjectId($event)">{{ project.name }}</button>
                            </div>
                            <Bootstrap4Pagination class="my-3" align="center" :data="projects" @pagination-change-page="list"></Bootstrap4Pagination>
                        </div>
                        <div class="row" v-if="projects && projects.data && projects.data.length === 0"><p class="text-center">No projects are available.</p></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
    import {Bootstrap4Pagination} from 'laravel-vue-pagination';
    import ProjectSelected from './ProjectSelected.vue';
    export default {
        name:"ListProjects",
        components:{
            Bootstrap4Pagination,
            ProjectSelected
        },
        data(){
            return {
                    projects:[],
                    asyncLocalStorage: {
                        setItem(key, value) {
                            return Promise.resolve().then(function () {
                                localStorage.setItem(key, value);
                            });
                        },
                        getItem(key) {
                            return Promise.resolve().then(function () {
                                return localStorage.getItem(key);
                            });
                        }
                    },
                    project_id:null,
                    capture_project_id:false,
                    searchKeayword:null
            }
        },
        async mounted(){
            await this.getProjectDetails();
            this.list();
        },
        methods:{
            async list(page=1){
                let url = `/api/projects?page=${page}`;
                if(this.searchKeayword !== null) {
                    url+='&name='+this.searchKeayword;
                }
                await axios.get(url).then(({data})=>{
                    this.projects = data;
                }).catch(({ response })=>{
                    console.error(response)
                })
            },
            setProjectId(e){
                this.asyncLocalStorage.setItem('project_id', e.target.value).then(()=> {
                    return this.asyncLocalStorage.getItem('project_id');
                }).then((value)=> {
                    // alert(value);
                    this.$router.push('/issues');
                });
            },
            getProjectDetails() {
                return Promise.resolve().then(function () {
                    return localStorage.getItem('project_id');
                }).then((project_id)=>{
                    this.project_id = project_id;
                    this.capture_project_id = true;
                });
            },
            searchProject(e) {
                if((e.target.value === null) || (e.target.value.trim() == '')) {
                    this.searchKeayword = null;
                } else {
                    this.searchKeayword = e.target.value.trim();
                    this.list();
                }
            }
        }
    }
</script>