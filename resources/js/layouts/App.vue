<template>
    <header>
        <div class="p-3 border-bottom mb-3">
            <ul class="nav nav-pills">
                <li class="nav-item">
                    <router-link class="nav-link" to="/issues">
                        Issues
                    </router-link>
                </li>
                <li class="nav-item">
                    <router-link class="nav-link" to="/projects">
                        Projects
                    </router-link>
                </li>
            </ul>
        </div>
    </header>
    <main>
        <div>
            <router-view>
            </router-view>
        </div>
    </main>
</template>