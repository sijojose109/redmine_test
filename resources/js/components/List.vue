<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="row">
                <div class="col-md-8 p-0" v-if="capture_project_id==true && project_id !== null">
                    <ProjectSelected v-bind:project_id="project_id"/>
                </div>
                <div class="col-md-4 p-0">
                    <router-link v-if="project_id_error===false" class="btn btn-primary btn-md float-end my-3" to="/create">
                            Create new issue
                    </router-link>
                </div>
            </div>
            <div class="col-md-12">
                <div class="card mb-3 w-100">
                    <div class="card-header text-center">
                        <h5>Search your issues here.</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-2 col-sm-6">
                                <label class="form-label">Status</label>
                                <select v-if="status_list && status_list.issue_statuses && status_list.issue_statuses.length>0" class="form-control" name="status" @change="filterChange($event)">
                                    <option value="">Select</option>
                                    <option v-for="status_item in status_list.issue_statuses" :value="status_item.id" :key="status_item.id">{{status_item.name}}</option>
                                </select>
                            </div>
                            <div class="col-md-2 col-sm-6">
                                <label class="form-label">Tracker</label>
                                <select v-if="tracker_list && tracker_list.trackers && tracker_list.trackers.length>0" class="form-control" name="tracker" @change="filterChange($event)">
                                    <option value="">Select</option>
                                    <option v-for="tracker_item in tracker_list.trackers" :value="tracker_item.id" :key="tracker_item.id">{{tracker_item.name}}</option>
                                </select>
                            </div>
                            <div class="col-md-4 col-sm-6">
                                <label class="form-label">Subject</label>
                                <input type="text" class="form-control" name="subject" @keyup="filterChange($event)" @change="filterChange($event)"/>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header text-center">
                        <h2>Issues</h2>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered text-center">
                                <thead>
                                    <tr>
                                        <th>Subject</th>
                                        <th>Tracker</th>
                                        <th>Status</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody v-if="issues && issues.data && issues.data.length > 0">
                                    <tr v-for="(issue,index) in issues.data" :key="index">
                                        <td>{{ issue.subject }}</td>
                                        <td>{{ issue.tracker.name }}</td>
                                        <td>{{ issue.status.name }}</td>
                                        <td><button :value=issue.id class="btn btn-sm btn-danger" @click="delIssueConfirm($event)">Delete</button></td>
                                    </tr>
                                </tbody>
                                <tbody v-else>
                                    <tr>
                                        <td align="center" colspan="5">No record found.</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <Bootstrap4Pagination align="center" :data="issues" @pagination-change-page="getIssues"></Bootstrap4Pagination>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <BModal :hideHeader="true" :hideFooter="true" v-model="modal">
        <p class="text-center">Do you really want to delete this?</p>
        <button class="btn btn-primary btn-md float-end" @click="delIssue($event)">Yes</button>
        <BButton class="float-end mx-2" @click="modal = false">Cancel</BButton>
    </BModal>
    <BModal class="text-center" :hideHeader="true" :hideFooter="true" v-model="response_modal">
        <IBiCheck2Circle v-if="response_modal_type === 'success'" style="font-size: 50px;" color="green" />
        <IBiXCircle v-else-if="response_modal_type === 'error'" style="font-size: 50px;" color="red" />
        <i class="bi bi-check-circle"></i>
        <p class="text-center">{{response_modal_message}}</p>
        <button v-if="project_id_error===false" class="btn btn-md btn-primary float-end mx-2" @click="response_modal = false">OK</button>
        <button v-else-if="project_id_error===true" class="btn btn-md btn-primary float-end mx-2" @click="redirectProject($event)">OK</button>
    </BModal>
</template>

<script>
    import {Bootstrap4Pagination} from 'laravel-vue-pagination';
    import IBiCheck2Circle from '~icons/bi/check2-circle';
    import IBiXCircle from '~icons/bi/x-circle';
    import ProjectSelected from './ProjectSelected.vue';
    export default {
        name:"List",
        components:{
            Bootstrap4Pagination,
            IBiCheck2Circle,
            IBiXCircle,
            ProjectSelected
        },
        data(){
            return {
                issues:{
                    type:Object,
                    default:null
                },
                filter_data:{
                    status:"All",
                    tracker:null,
                    subject:null
                },
                post_filter_data:{
                    status:null,
                    tracker:null,
                    subject:null
                },
                modal:false,
                response_modal:false,
                response_modal_message:'',
                response_modal_type:'success',
                project_id_error:false,
                item_del:null,
                project_id:null,
                capture_project_id:false,
                tracker_list:null,
                status_list:null,
                category_list:null
            }
        },
        async mounted(){
            await this.getTrackers();
            await this.getStatuses();
            await this.getCategories();
            await this.getProjectDetails();
            await this.getIssues();
            // this.list()
        },
        methods:{
            async listBkp(page=1){
                let url = `/api/issues?project=${this.$route.params.id}&page=${page}`;
                if(this.post_filter_data.status !== null) {
                    url+=`&status=${this.post_filter_data.status}`;
                }
                if(this.post_filter_data.tracker !== null) {
                    url+=`&tracker=${this.post_filter_data.tracker}`;
                }
                if((this.post_filter_data.subject !== null) && (this.post_filter_data.subject !== '')) {
                    url+=`&subject=${this.post_filter_data.subject}`;
                }
                await axios.get(url).then(({data})=>{
                    this.issues = data
                }).catch(({ response })=>{
                    console.error(response)
                })
            },
            async list(project_id=1, page=1){
                let url = `/api/issues?project=${project_id}&page=${page}`;
                if(this.post_filter_data.status !== null) {
                    url+=`&status=${this.post_filter_data.status}`;
                }
                if(this.post_filter_data.tracker !== null) {
                    url+=`&tracker=${this.post_filter_data.tracker}`;
                }
                if((this.post_filter_data.subject !== null) && (this.post_filter_data.subject !== '')) {
                    url+=`&subject=${this.post_filter_data.subject}`;
                }
                await axios.get(url).then(({data})=>{
                    this.issues = data
                }).catch(({ response })=>{
                    console.error(response);
                })
            },
            filterChange(e){
                if(((e.target.name === 'status') && (e.target.value === 'All')) || ((e.target.name === 'tracker') && (e.target.value === 'All'))) {
                    this.post_filter_data[e.target.name] = null;
                }
                else {
                    this.post_filter_data[e.target.name] = e.target.value.trim();
                }
                this.filter_data[e.target.name]=e.target.value.trim();
                this.getIssues();
            },
            delIssueConfirm(e)
            {
                this.item_del = e.target.value;
                this.modal=true;
            },
            async delIssue(e)
            {
                await axios.post('/api/issues/delete', {id:this.item_del})
                .then((response)=> {
                    this.getIssues();
                    this.response_modal_message='Requested item has been deleted succesfully.';
                    this.response_modal_type='success';
                })
                .catch((error)=>{
                    this.save_error = true;
                    this.save_error_message = 'Error occurred while save the issue.';
                    this.response_modal_message='Error occured while delete the item.';
                    this.response_modal_type='error';
                    this.response_modal=true;
                });
                this.modal=false;
                this.response_modal=true;
            },
            getIssues(page=1){
                this.project_id_error=false;
                return Promise.resolve().then(function () {
                    return localStorage.getItem('project_id');
                }).then((project_id)=>{
                    if((project_id == 'undefined') || (project_id===null) || (project_id=='null')) {
                        this.response_modal_message='Select a project to continue';
                        this.response_modal_type='error';
                        this.project_id_error=true;
                        this.response_modal=true;
                        return;
                    }
                    this.list(project_id, page);
                });
            },
            redirectProject(e) {
                this.$router.push('/projects');
            },
            getProjectDetails() {
                return Promise.resolve().then(function () {
                    return localStorage.getItem('project_id');
                }).then((project_id)=>{
                    this.project_id = project_id;
                    this.capture_project_id = true;
                });
            },
            async getTrackers() {
                await axios.get(`/api/trackers`).then(({data})=>{
                    this.tracker_list = data;
                }).catch(({ response })=>{
                    console.error(response);
                });
            },
            async getStatuses() {
                await axios.get(`/api/statuses`).then(({data})=>{
                    this.status_list = data;
                }).catch(({ response })=>{
                    console.error(response);
                });
            },
            async getCategories() {
                return Promise.resolve().then(function () {
                    return localStorage.getItem('project_id');
                }).then(async(project_id)=>{
                    await axios.get(`/api/categories?project=${project_id}`).then(({data})=>{
                        this.category_list = data;
                    }).catch(({ response })=>{
                        console.error(response);
                    });
                });
            }
        }
    }
</script>

<style scoped>
    .pagination{
        margin-bottom: 0;
    }
</style>