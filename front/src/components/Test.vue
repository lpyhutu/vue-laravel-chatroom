<template>
    <div class="test">{{test}}</div>
</template>

<script>
export default {
    name: "test",
    data() {
        return {
            test: 0,
        };
    },
    methods: {},
    created() {
        this.test=this.$route.query.test
        console.log(this.$route.query.test);
    },
};
</script>

<style>
</style>