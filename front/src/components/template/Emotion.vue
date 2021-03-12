<template>
    <div class="comment">
        <div @click.stop="handleShowClick" class="emotion-box">
            <slot>
                <i class="iconfont icon-smile"></i>
            </slot>
            <div v-show="$store.state.emotionShowFlag" class="box">
                <span
                    class="emo"
                    v-for="(line, i) in emotionArr"
                    :key="'emojo'+i"
                    @click.stop="emoClick(line.name)"
                    v-html="line.url"
                ></span>
            </div>
        </div>
    </div>
</template>

<script>
import emotions from "@/assets/js/emotionList.js";
export default {
    name: "comment",
    data() {
        return {
            show: false,
            load: false,
            emotionArr: null,
            emotionShowFlag: false,
        };
    },
    methods: {
        handleShowClick() {
            this.$store.commit(
                "setEmotionShowFlag",
                !this.$store.state.emotionShowFlag
            );
            if (this.$store.state.emotionShowFlag) {
                this.loadEmotion();
            }
        },
        emoClick(arg) {
            this.$store.commit("setEmotionShowFlag", false);
            this.$emit("handleEmotion", arg);
        },
        loadEmotion() {
            const list = emotions.emotionList;
            let emotionArr = [];
            list.map((item, index) => {
                emotionArr.push({
                    name: `#${item};`,
                    url: `<img title="${item}" src="https://res.wx.qq.com/mpres/htmledition/images/icon/emotion/${index}.gif">`,
                });
            });
            this.emotionArr = emotionArr;
        },
    },
};
</script>

<style lang="scss" scoped>
.emotion-box {
    display: inline-block;
    position: relative;
    cursor: pointer;
    > .box {
        position: absolute;
        z-index: 10;
        background-color: #fff;
        width: 440px;
        max-height: 130px;
        overflow: scroll;
        padding: 8px;
        bottom: 30px;
        .emo {
            cursor: pointer;
        }
    }
}
</style>