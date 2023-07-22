import './bootstrap';
import {createApp} from "vue";
import App from "./layouts/App.vue";
import List from "./components/List.vue";
import Create from "./components/Create.vue";
import ListProjects from "./components/ListProjects.vue";
import { createRouter, createWebHistory } from 'vue-router';
import 'bootstrap/dist/css/bootstrap.css'
import 'bootstrap-vue-next/dist/bootstrap-vue-next.css'

const routes = [
    {path:'/',component:List},
    {path:'/issues',component:List},
    {path:'/create',component:Create},
    {path:'/example-app/public',component:ListProjects},
    {path:'/projects',component:ListProjects}
]

const router = createRouter({
    history:createWebHistory(),
    routes,
    linkActiveClass: "active"
})

const app = createApp(App).use(router).mount("#app");