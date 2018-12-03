<template>
    <div class="alert alert-danger" role="alert">
        {{ error.response.data.error }}
    </div>
</template>

<script>
    export default {
        name: 'errorMessage',
        props: ['error'],
    }
</script>