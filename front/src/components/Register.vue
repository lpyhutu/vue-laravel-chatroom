<template>
    <div class="login">
        <h1>用户注册</h1>
        <el-input class="ipt" v-model="email" placeholder="邮箱（例：123@qq.com）"></el-input>
        <div class="verification">
            <el-input class="ipt" v-model="code" placeholder="邮箱验证码"></el-input>
            <el-button
                @click="sendEmailCode"
                :disabled="sendEmailBtnFlag"
            >{{sendEmailBtnFlag?$store.state.EmailTime+'S后重试':'验证邮箱'}}</el-button>
        </div>
        <el-input
            class="ipt"
            v-model="password"
            placeholder="密码（例：8~16位）"
            maxlength="16"
            @input="handlePassword"
            :show-password="true"
        ></el-input>
        <el-input
            class="ipt"
            v-model="rePassword"
            placeholder="确认密码"
            maxlength="16"
            :show-password="true"
        ></el-input>
        <el-button @click="register">register</el-button>
        <div class="tip">
            <span>
                <router-link :to="{path:'/'}">login</router-link>
            </span>
            <span @click="forgotPwd">找回密码</span>
        </div>
    </div>
</template>

<script>
import { register, sendEmailCode } from "@/assets/api/api";
import { RegEmail } from "@/assets/js/reg";
export default {
    name: "register",
    data() {
        return {
            email: "",
            password: "",
            rePassword: "",
            code: "",
            sendEmailBtnFlag: false,
        };
    },
    methods: {
        forgotPwd() {
            this.$msgBox
                .confirm("找回密码请用联系邮箱1048672466@qq.com!", "温馨提示", {
                    confirmButtonText: "确定",
                    cancelButtonText: "取消",
                    type: "warning",
                })
                .then(() => {})
                .catch(() => {});
        },
        /**
         * 去掉密码空格
         */
        handlePassword(e) {
            this.password = e.replace(/\s/g, "");
        },
        /**
         * 发送邮箱验证
         */
        sendEmailCode() {
            if (!RegEmail(this.email)) {
                this.$msg({ code: 201, msg: "邮箱输入不规范！" });
                return;
            }
            this.$store.commit("setEmailTime", 60);
            sendEmailCode({ email: this.email }).then((res) => {
                if (this.$msg(res.data)) {
                    this.sendEmailBtnFlag = true;
                    this.changeEmailTime();
                }
            });
        },
        /**
         * 倒计时
         */
        changeEmailTime() {
            const _this = this;
            if (this.$store.state.EmailTime > 0) {
                this.$store.commit(
                    "setEmailTime",
                    this.$store.state.EmailTime - 1
                );
                setTimeout(() => {
                    _this.changeEmailTime();
                }, 1000);
            } else {
                this.sendEmailBtnFlag = false;
            }
        },
        /**
         * 注册账号
         */
        register() {
            if (this.email == "" || this.password == "" || this.code == "") {
                this.$msg({
                    code: 201,
                    msg: "请用输入邮箱、密码、验证码等信息！",
                });
                return;
            }
            if (this.password != this.rePassword) {
                this.$msg({ code: 201, msg: "密码不一致！" });
                return;
            }
            if (this.password.length < 8) {
                this.$msg({ code: 201, msg: "密码长度过短！" });
                return;
            }
            if (!RegEmail(this.email)) {
                this.$msg({ code: 201, msg: "邮箱输入不规范！" });
                return;
            }
            if (isNaN(this.code)) {
                this.$msg({ code: 201, msg: "请正确输入验证码！" });
                return;
            }
            register({
                email: this.email,
                password: this.password,
                code: this.code,
            }).then((res) => {
                if (this.$msg(res.data)) {
                    this.$router.push({
                        path: "/",
                    });
                }
            });
        },

        handleCode(code) {
            this.VerificationCode = code;
        },
    },
    created() {},
};
</script>
<style lang="scss">
@import "@/assets/scss/login_common";
</style>
<style lang="scss" scoped>
@import "@/assets/scss/login";
</style>