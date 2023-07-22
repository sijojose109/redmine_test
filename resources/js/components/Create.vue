<template>
    <div class="container w750 my-5">
        <div v-if="capture_project_id==true && project_id !== null">
            <ProjectSelected v-bind:project_id="project_id"/>
        </div>
        <div class="card">
            <div class="card-header text-center">
                <h2>Create Issue</h2>
            </div>
            <div class="card-body">
                <form>
                    <div v-if="save_error" class="mb-3 col-md-12">
                        <h5 class="text-danger text-center">{{ this.save_error_message }}</h5>
                    </div>
                    <div class="mb-3 col-md-4">
                        <label for="exampleInputEmail1" class="form-label">Tracker</label><label class="text-danger">*</label>
                        <select v-if="tracker_list && tracker_list.trackers && tracker_list.trackers.length>0" class="form-control" @change="onChangeInput($event)" name="tracker">
                            <option value="">Select</option>
                            <option v-for="tracker_item in tracker_list.trackers" :value="tracker_item.id" :key="tracker_item.id">{{tracker_item.name}}</option>
                        </select>
                        <select v-else class="form-control" @change="onChangeInput($event)" name="tracker">
                            <option value="">Select</option>
                        </select>
                        <p class="text-danger" v-if="validation_error.tracker">Enter tracker.</p>
                    </div>
                    <div class="mb-3 col-md-12">
                        <label for="exampleInputEmail1" class="form-label">Subject</label><label class="text-danger">*</label>
                        <input class="form-control" type="text" @change="onChangeInput($event)" name="subject"/>
                        <p class="text-danger" v-if="validation_error.subject">Enter subject.</p>
                    </div>
                    <div class="mb-3 col-md-12">
                        <label for="exampleInputEmail1" class="form-label">Description</label>
                        <textarea class="form-control" @change="onChangeInput($event)" name="description"></textarea>
                    </div>
                    <div class="mb-3 col-md-4">
                        <label for="exampleInputEmail1" class="form-label">Status</label><label class="text-danger">*</label>
                        <select v-model="issue.status" v-if="status_list && status_list.issue_statuses && status_list.issue_statuses.length>0" class="form-control" @change="onChangeInput($event)" name="status">
                                <option :value="null">Select</option>
                                <option v-for="status_item in status_list.issue_statuses" :value="status_item.id" :key="status_item.id">{{status_item.name}}</option>
                        </select>
                        <select v-else class="form-control" @change="onChangeInput($event)" name="status">
                            <option value="">Select</option>
                        </select>
                        <p class="text-danger" v-if="validation_error.status">Enter status.</p>
                    </div>
                    <div class="mb-3 col-md-4">
                        <label for="exampleInputEmail1" class="form-label">Category</label>
                        <select v-if="category_list && category_list.issue_categories && category_list.issue_categories.length >0" class="form-control" @change="onChangeInput($event)" name="category">
                            <option value="">Select</option>
                            <option v-for="category in category_list.issue_categories" :value="category.id" :key="category.id">{{category.name}}</option>
                        </select>
                        <select v-else class="form-control" @change="onChangeInput($event)" name="category">
                            <option value="">Select</option>
                        </select>
                    </div>
                    <div class="mb-3 col-md-4">
                        <label for="exampleInputEmail1" class="form-label">Priority</label><label class="text-danger">*</label>
                        <select v-if="priority_list && priority_list.issue_priorities && priority_list.issue_priorities.length >0" class="form-control" @change="onChangeInput($event)" name="priority">
                            <option value="">Select</option>
                            <option v-for="issue_priority in priority_list.issue_priorities" :value="issue_priority.id" :key="issue_priority.id">{{issue_priority.name}}</option>
                        </select>
                        <select v-else class="form-control" @change="onChangeInput($event)" name="category">
                            <option value="">Select</option>
                        </select>
                        <p class="text-danger" v-if="validation_error.priority">Enter tracker.</p>
                    </div>
                    <div class="mb-3 col-md-12">
                        <button :disabled="btn_status" @click="saveIssue($event)" class="btn btn-primary float-end">Create</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <BModal class="text-center" :hideHeader="true" :hideFooter="true" v-model="response_modal">
        <IBiCheck2Circle v-if="response_modal_type === 'success'" style="font-size: 50px;" color="green" />
        <IBiXCircle v-else-if="response_modal_type === 'error'" style="font-size: 50px;" color="red" />
        <i class="bi bi-check-circle"></i>
        <p class="text-center">{{response_modal_message}}</p>
        <button class="btn btn-md btn-primary float-end mx-2" @click="responseModalOk($event)">OK</button>
    </BModal>
</template>
<script>
    import IBiCheck2Circle from '~icons/bi/check2-circle';
    import IBiXCircle from '~icons/bi/x-circle';
    import ProjectSelected from './ProjectSelected.vue';
    export default {
        name:"Create",
        components:{
            IBiCheck2Circle,
            IBiXCircle,
            ProjectSelected
        },
        data(){
            return {
                issue:{
                    project_id:null,
                    tracker:null,
                    subject:null,
                    description:null,
                    status:null,
                    categories:null,
                    priority:null
                },
                validation_error:{
                    tracker:false,
                    subject:false,
                    status:false,
                    priority:false
                },
                categories_all:[],
                save_error:false,
                save_error_message:'',
                response_modal:false,
                response_modal_message:'',
                response_modal_type:'success',
                tracker_list:null,
                status_list:null,
                category_list:null,
                btn_status:true,
                project_id:null,
                capture_project_id:false,
                priority_list:null
            }
        },
        async mounted(){
            await this.getProjectDetails();
            await this.getPriority();
            await this.getStatuses();
            await this.getTrackers();
            await this.getCategories();
            this.btn_status=false;
        },
        methods:{
            onChangeInput(e){
                this.validation_error = {
                    tracker:false,
                    subject:false,
                    status:false,
                    priority:false
                };
                this.save_error = false;
                this.save_error_message = '';

                if((e.target.name === 'tracker') && (e.target.value !== null) && (e.target.value != '')) {
                    if(this.tracker_list.trackers) {
                        let default_status = this.arraryFilter(this.tracker_list.trackers, 'id', e.target.value);
                        if(default_status && default_status.default_status && default_status.default_status.id)
                        this.issue.status = default_status.default_status.id;
                    }
                }

                this.issue[e.target.name] = e.target.value.trim();
            },
            async saveIssue(e){
                e.preventDefault();
                this.validation_error = {
                    tracker:false,
                    subject:false,
                    status:false,
                    priority:false
                };
                let error = false;
                if((this.issue.tracker === null) || (this.issue.tracker.trim() === '')) {
                    error = true;
                    this.validation_error.tracker=true;
                }
                if((this.issue.subject === null) || (this.issue.subject.trim() === '')) {
                    error = true;
                    this.validation_error.subject=true;
                }
                if((this.issue.status === null) || (this.issue.status === '')) {
                    this.validation_error.status=true;
                    error = true;
                }
                if((this.issue.priority === null) || (this.issue.priority == 'null') || (this.issue.priority.trim() === '')) {
                    this.validation_error.priority=true;
                    error = true;
                }
                if(error === true) {
                    return false;
                }
                return Promise.resolve().then(function () {
                    return localStorage.getItem('project_id');
                }).then(async(project_id)=>{
                    if((project_id == 'undefined') || (project_id===null)) {
                        this.response_modal_message='Select a project to continue';
                        this.response_modal_type='error';
                        this.response_modal=true;  
                    }
                    this.issue.project_id=project_id;
                    this.btn_status = true;
                    await axios.post('/api/issues/save', this.issue)
                    .then((response)=> {
                        this.response_modal_message = 'The Issue has been created successfully.';
                        this.response_modal_type = 'success';
                        this.response_modal=true;
                    })
                    .catch((error)=>{
                        this.save_error = true;
                        this.save_error_message = 'Error occurred while save the issue.';
                    });
                    this.btn_status=false;
                });
            },
            responseModalOk(e) {
                this.response_modal = false;
                this.$router.push('/issues');
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
            },
            getProjectDetails() {
                return Promise.resolve().then(function () {
                    return localStorage.getItem('project_id');
                }).then((project_id)=>{
                    this.project_id = project_id;
                    this.capture_project_id = true;
                });
            },
            async getPriority() {
                await axios.get(`/api/issue-priorities`).then(({data})=>{
                        this.priority_list = data;
                }).catch(({ response })=>{
                        console.error(response);
                });
            },
            arraryFilter(array_to_search, search_key, search_val) {
                let result = null; 
                for (var i = 0; i < array_to_search.length; i++){
                    if (array_to_search[i][search_key] == search_val){
                        result = array_to_search[i];
                        break;
                    }
                }
                return result;
            }

        }
    }
</script>