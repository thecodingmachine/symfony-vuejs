<template>
    <div class="container">
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <router-link class="navbar-brand" to="/home">App</router-link>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <router-link class="nav-item" tag="li" to="/home" active-class="active">
                        <a class="nav-link">Home</a>
                    </router-link>
                    <router-link class="nav-item" tag="li" to="/posts" active-class="active">
                        <a class="nav-link">Posts</a>
                    </router-link>
                    <li class="nav-item" v-if="isAuthenticated">
                        <a class="nav-link" href="/api/security/logout">Logout</a>
                    </li>
                </ul>
            </div>
        </nav>

        <router-view></router-view>
    </div>
</template>

<script>
    import axios from 'axios';
    import router from './router';

    export default {
        name: 'app',
        beforeMount () {
            let vueRouting = this.$parent.$el.attributes['data-vue-routing'].value,
                queryParameters = JSON.parse(this.$parent.$el.attributes['data-query-parameters'].value);

            router.push({path: vueRouting, query: queryParameters});
        },
        created () {
            axios.interceptors.response.use(undefined, (err) => {
                return new Promise(() => {
                    if (err.response.status === 403) {
                        this.$router.push({path: '/login'})
                    }
                    throw err;
                });
            });
        },
        computed: {
            isAuthenticated () {
                return this.$store.getters['security/isAuthenticated']
            },
        },
    }
</script>