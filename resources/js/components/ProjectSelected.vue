<template>
        <div class="col-md-12">
            <label style="font-weight:bold;">Project selected:</label>
            <button class="m-3 btn btn-secondary px-5">{{ this.project_name }}</button>
        </div>
    <BModal :hideHeader="true" :hideFooter="true" class="text-center" v-model="response_modal">
        <p class="text-center">{{response_modal_message}}</p>
        <button class="btn btn-md btn-primary float-end mx-2" @click="redirectProject($event)">OK</button>
    </BModal>
</template>
<script>
    export default {
        props:['project_id'],
        name:"ProjectSelected",
        components: {
        },
        data() {
            return {
                project_details:null,
                response_modal_message:null,
                response_modal:false,
                project_name:''
            }
        },
        async mounted() {
            let url = `/api/project/${this.project_id}`;
            await axios.get(url).then(({data})=>{
                    this.project_details = data;
                    if(data && data.name) {
                        if(data.name == '' && data.name === null) {
                            this.unset_project_id();
                        }
                        else {
                            this.project_name = data.name;
                        }
                    }
                }).catch(({ response })=>{
                    this.unset_project_id();
                })
        },
        methods:{
            redirectProject(e) {
                response_modal:false;
                this.$router.push('/projects');
            },
            unset_project_id() {
                return Promise.resolve().then(function () {
                    localStorage.removeItem("project_id");
                }).then(()=>{
                    this.response_modal_message = 'Please select a project';
                    this.response_modal=true;
                });
            }
        }
    }
</script>