<template>
    <div class="login">
        <h1>Welcome</h1>
        <el-input class="ipt" v-model="email" placeholder="邮箱账号"></el-input>
        <el-input class="ipt" v-model="password" placeholder="密码" :show-password="true"></el-input>
        <div class="verification">
            <el-input class="ipt" v-model="code" placeholder="验证码"></el-input>
            <img @click="captcha" :src="captchaUrl" alt="验证码" />
        </div>
        <el-button @click="login">login</el-button>
        <div class="tip">
            <span>
                <a href="https://github.com/lpyhutu/chatroom" target="_blank">github</a>
            </span>
            <span>
                <router-link :to="{path:'/register'}">register</router-link>
            </span>
        </div>
    </div>
</template>

<script>
import { captcha, login } from "@/assets/api/api";
export default {
    name: "login",
    data() {
        return {
            email: "",
            password: "",
            code: "",
            captchaUrl: "",
            captchaKey: "",
        };
    },
    methods: {
        /**
         * 登陆
         */
        login() {
            if (this.email == "") {
                this.$msg({
                    code: 201,
                    msg: "请输入邮箱账号！",
                });
                return;
            }
            if (this.password == "") {
                this.$msg({
                    code: 201,
                    msg: "请输入密码！",
                });
                return;
            }
            if (this.code == "") {
                this.$msg({
                    code: 201,
                    msg: "请输入验证码！",
                });
                return;
            }
            login({
                email: this.email,
                password: this.password,
                captcha: this.code,
                captchaKey: this.captchaKey,
            }).then((res) => {
                const { token_type, access_token } = res.data.data;
                if (this.$msg(res.data)) {
                    this.$store.commit(
                        "setAuthorization",
                        token_type + access_token
                    );
                    localStorage.setItem("tokenExpireTime", Date.now());
                    this.$router.push({
                        path: "/home",
                    });
                } else {
                    this.captcha();
                }
            });
        },
        /**
         * 获取验证码
         */
        captcha() {
            captcha().then((res) => {
                const { img, key } = res.data[0];
                this.captchaUrl = img;
                this.captchaKey = key;
            });
        },
    },
    created() {
        const _this = this;
        this.captcha();
        document.onkeydown = function (e) {
            if (e.keyCode == 13) {
                _this.login();
            }
        };
    },
};
</script>
<style lang="scss">
@import "@/assets/scss/login_common";
</style>
<style lang="scss" scoped>
@import "@/assets/scss/login";
</style>