<template>
    <div id="app" @click="handleEmotionShowFlag">
        <router-view></router-view>
    </div>
</template>

<script>
export default {
    name: "App",
    methods: {
        handleEmotionShowFlag() {
            this.$store.commit("setEmotionShowFlag", false);
        },
    },
};
</script>
<style lang="scss">
@import "@/assets/scss/iconfont.scss";
* {
    padding: 0px;
    margin: 0px;
}
::selection {
    background: rgba(3, 3, 3, 0.233);
}
::-webkit-scrollbar-track {
    width: 5px;
    background-color: #999;
}

::-webkit-scrollbar-thumb {
    background-color: #c0c0c0;
    background-clip: padding-box;
    height: 30px;
    border-radius: 10px;
}

::-webkit-scrollbar {
    width: 5px;
    height: 5px;
}
</style>
<style lang="scss" scoped>
#app {
    position: fixed;
    display: flex;
    align-items: center;
    justify-content: center;
    height: 100%;
    width: 100%;
    font-size: $font-size-two;
    color: $font-color-one;
    background: -webkit-linear-gradient(top left, #5c5c5c 0%, #2c2c2c 90%);
    background: linear-gradient(to bottom right, #5c5c5c 0%, #2c2c2c 90%);
}
</style>
