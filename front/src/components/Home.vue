<template>
    <div class="home">
        <div class="left">
            <div class="top">
                <el-tooltip content="用户详情" placement="right">
                    <div class="avatar left-comment">
                        <img
                            @click="userInfoDetail"
                            :src="userInfo==''?'../assets/img/avatar.png': $api+userInfo.avatar"
                            alt="头像"
                        />
                    </div>
                </el-tooltip>
                <el-tooltip
                    v-for="(item,key) in iconList"
                    :key="key"
                    :content="item.title"
                    placement="right"
                >
                    <div
                        class="friends left-comment"
                        :class="key===iconIndex?'active':''"
                        @click="changeMenuIndex(key,item.type)"
                    >
                        <i class="iconfont" :class="item.icon"></i>
                    </div>
                </el-tooltip>
            </div>
            <el-tooltip content="退出" placement="right">
                <div class="bottom left-comment" @click="logout">
                    <i class="iconfont icon-poweroff"></i>
                </div>
            </el-tooltip>
        </div>
        <div class="center">
            <div class="header">
                <el-input
                    prefix-icon="el-icon-search"
                    size="mini"
                    placeholder="搜索"
                    v-model="search"
                ></el-input>
                <el-button size="mini" icon="el-icon-plus" @click="dialogVisible=true"></el-button>
            </div>
            <div class="content">
                <div v-show="this.chatType=='chat'?true:false">
                    <div class="user-group" @click="changeCurrentFriendId">
                        <div class="group-list active">
                            <div class="avatar">
                                <img :src="$api+'/img/avatar.png'" alt="图标" />
                            </div>
                            <div class="content">
                                <div class="top">
                                    <div class="app-line-clamp">如何减少掉发学习交流群？</div>
                                    <div></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="user-group" v-show="this.chatType!='chat'?true:false">
                    <div
                        class="group-list"
                        :class="currentFriendId==item.friend_info.id?'active':''"
                        v-for="(item,key) in chatList "
                        :key="key"
                        @click="changeCurrentFriendId(item.friend_info.id,item.friend_info)"
                    >
                        <div class="avatar">
                            <img :src="$api+item.friend_info.avatar" alt />
                        </div>
                        <div class="content">
                            <div class="top">
                                <div class="app-line-clamp">{{item.friend_info.email}}</div>
                                <div>{{item.time!=undefined?SwitchTimeSlot(item.time):''}}</div>
                            </div>
                            <div class="bottom app-line-clamp">{{item.comment}}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="right">
            <div
                class="header"
            >{{chatType=="chat"?`如何减少掉发学习交流群？(${groupOnline})人在线`: currentFriend.email}}</div>
            <div class="content cc" ref="content">
                <div class="list" v-for="(item,key) in commentList" :key="key">
                    <div class="avatar">
                        <img :src="$api+item.avatar" alt />
                    </div>
                    <div class="left">
                        <div class="top">
                            <div class="name">{{item.email}}</div>
                            <div class="time">{{SwitchTimeSlot(item.time)}}</div>
                        </div>
                        <div class="text">
                            <div v-html="processEmotion(item.comment)"></div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="comment">
                <div class="nav">
                    <Emotion @handleEmotion="handleEmotion"></Emotion>
                </div>
                <div class="text">
                    <el-input type="textarea" placeholder="请输入内容" v-model="comment" maxlength="300"></el-input>
                </div>
                <div class="btn">
                    <el-button size="mini" @click="sendMessage">发送</el-button>
                </div>
            </div>
        </div>
        <input
            type="file"
            ref="fileInput"
            accept="image/*"
            @change="uploadAvatar"
            style="display:none"
        />
        <el-dialog title="添加用户" :visible.sync="dialogVisible" width="50%">
            <el-input
                prefix-icon="el-icon-search"
                placeholder="搜索用户"
                @change="searchEmail"
                v-model="email"
            ></el-input>
            <div class="search-email" v-for="(item,key) in searchEmailData" :key="key">
                <span>{{item.email}}</span>
                <el-button size="mini" @click="applyFriend(item.id)">添加好友</el-button>
            </div>
        </el-dialog>
        <el-dialog title="温馨提示" :visible.sync="tipFlag" width="50%">
            <div class="dialog-tip">
                <h3>关于聊天室</h3>
                <br />
                <p>1、该聊天室只用于学习交流，聊天记录只保存在本地</p>
                <p>2、项目开源，在登陆界面可以找到github链接</p>
                <p>3、主要用到socket、redis、laravel、mysql、jwt、邮箱认证等</p>
                <p>4、技术？非常有限...（此处省略1万字），欢迎各位大佬提建议，邮箱1048672466@qq.com</p>
                <p>5、最后请教个问题：为什么nginx开启两个socket端口号，只能请求其中一个，请求第二个会出现502错误！！！</p>
                <br />
                <br />
                <br />
                <h2 style="color:red">不得发不良信息！不得发不良信息！不得发不良信息！</h2>
            </div>
        </el-dialog>
    </div>
</template>

<script>
import Emotion from "./template/Emotion";
import {
    logout,
    getUserInfo,
    uploadAvatar,
    getFriends,
    searchEmail,
    applyFriend,
    getApplyFriend,
    agreeApplyFriend,
} from "@/assets/api/api";
import { SwitchTimeSlot } from "@/assets/js/switch";
import { processEmotion } from "@/assets/js/changeEmotion.js";

export default {
    name: "home",
    data() {
        return {
            search: "",
            iconIndex: 0,
            iconList: [
                { icon: "icon-team", title: "好友", type: "friends" },
                { icon: "icon-message", title: "群聊", type: "chat" },
                { icon: " icon-adduser", title: "申请", type: "apply" },
                { icon: "icon-question-circle", title: "关于", type: "info" },
            ],
            comment: "",
            userInfo: "",
            chatList: [],
            chatType: "friends",
            currentFriendId: -1,
            currentFriend: "",
            commentList: [],
            dialogVisible: false,
            email: "",
            searchEmailData: [],
            groupOnline: 0,
            tipFlag: false,
        };
    },
    methods: {
        /**
         * nav切换
         */
        changeMenuIndex(key, type) {
            this.iconIndex = key;
            this.chatType = type;
            switch (type) {
                case "chat":
                    this.chatList = [];
                    this.changeCurrentFriendId("chat");
                    this.currentFriend = "";
                    return;
                case "friends":
                    this.getFriends(type);
                    this.commentList = [];
                    return;
                case "apply":
                    this.currentFriend = "";
                    this.commentList = [];
                    this.getApplyFriend();
                    return;
                case "info":
                    this.chatList = [];
                    this.currentFriend = "";
                    this.commentList = [];
                    this.tipFlag = true;
                    return;
            }
        },
        /**
         * 选择好友
         */
        changeCurrentFriendId(key, currentFriend) {
            switch (this.chatType) {
                case "chat":
                    this.commentList = JSON.parse(
                        localStorage.getItem(`commentData:chat_group1`)
                    );
                    this.websocketsend(
                        JSON.stringify({
                            type: "count_group_online",
                            group: "chat_group1",
                        })
                    );
                    return;
                case "friends":
                    this.currentFriendId = key;
                    this.currentFriend = currentFriend;
                    this.commentList = JSON.parse(
                        localStorage.getItem(
                            `commentData${this.currentFriendId}`
                        )
                    );
                    return;
                case "apply":
                    this.agreeApplyFriend(currentFriend);
                    return;
                case "info":
                    return;
            }

            this.comment = "";
        },
        /**
         * 获取好友
         */
        getFriends() {
            getFriends({ user_id: this.userInfo.id }).then((res) => {
                if (this.$msg(res.data)) {
                    let { data } = res.data;
                    for (let i = 0; i < data.length; i++) {
                        let orderData = JSON.parse(
                            localStorage.getItem(
                                `commentData${data[i].friend_id}`
                            )
                        );
                        if (orderData != null) {
                            data[i].comment =
                                orderData[orderData.length - 1].comment;
                            data[i].time = orderData[orderData.length - 1].time;
                        }
                    }
                    this.chatList = data;
                }
            });
        },
        /**
         * 搜索账号
         */
        searchEmail() {
            searchEmail({ email: this.email }).then((res) => {
                if (this.$msg(res.data)) {
                    this.searchEmailData = res.data.data;
                }
            });
        },
        /**
         * 申请好友
         */
        applyFriend(user_id) {
            if (this.email == this.userInfo.email) {
                this.$msg({ code: 201, msg: "不可添加自己为好友！" });
                return;
            }
            this.$msgBox
                .confirm("添加当前用户, 是否继续?", "温馨提示", {
                    confirmButtonText: "确定",
                    cancelButtonText: "取消",
                    type: "warning",
                })
                .then(() => {
                    applyFriend({
                        email: this.email,
                        friend_id: user_id,
                        user_id: this.userInfo.id,
                    }).then((res) => {
                        this.$msg(res.data);
                    });
                })
                .catch(() => {});
        },
        /**
         * 申请列表
         */
        getApplyFriend() {
            getApplyFriend({ friend_id: this.userInfo.id }).then((res) => {
                if (this.$msg(res.data)) {
                    this.chatList = res.data.data;
                    return;
                }
                this.chatList = [];
            });
        },
        /**
         * 申请通过
         */
        agreeApplyFriend(firend) {
            this.$msgBox
                .confirm(
                    "此操作将通过该用户的好友申请, 是否继续?",
                    "温馨提示",
                    {
                        confirmButtonText: "确定",
                        cancelButtonText: "取消",
                        type: "warning",
                    }
                )
                .then(() => {
                    agreeApplyFriend({
                        user_id: firend.id,
                        friend_id: this.userInfo.id,
                    }).then((res) => {
                        if (this.$msg(res.data)) {
                            this.getApplyFriend();
                        }
                    });
                })
                .catch(() => {});
        },
        /**
         * 转换时间
         */
        SwitchTimeSlot(time) {
            return SwitchTimeSlot(time);
        },
        /**
         * 获取用户信息
         */
        getUserInfo() {
            getUserInfo().then((res) => {
                if (this.$msg(res.data)) {
                    this.userInfo = res.data.data;
                    this.initWebSocket();
                    this.getFriends("friends");
                }
            });
        },
        /**
         * 用户详情
         */
        userInfoDetail() {
            const h = this.$createElement;
            this.$msgBox({
                title: "用户详情",
                message: h(
                    "div",
                    {
                        style: {
                            color: "#999",
                            display: "flex",
                            "align-items": "center",
                        },
                    },
                    [
                        h("div", { on: { click: this.handleUploadPicture } }, [
                            h("el-avatar", {
                                style: {
                                    cursor: "pointer",
                                },
                                attrs: {
                                    src:
                                        this.userInfo == ""
                                            ? require("../assets/img/avatar.png")
                                            : this.$api + this.userInfo.avatar,
                                    alt: "头像",
                                    shape: "square",
                                },
                            }),
                        ]),

                        h(
                            "div",
                            {
                                style: {
                                    display: "flex",
                                    padding: "0 10px",
                                    "flex-flow": "column",
                                },
                            },
                            [h("span", {}, this.userInfo.email)]
                        ),
                    ]
                ),
                confirmButtonText: "确定",
                beforeClose: (action, instance, done) => {
                    done();
                },
            })
                .then((action) => {
                    console.log(action);
                })
                .catch(() => {});
        },
        /**
         * 退出当前用户
         */
        logout() {
            this.$msgBox
                .confirm("此操作将退出当前用户, 是否继续?", "温馨提示", {
                    confirmButtonText: "确定",
                    cancelButtonText: "取消",
                    type: "warning",
                })
                .then(() => {
                    logout().then((res) => {
                        if (this.$msg(res.data)) {
                            this.$router.push({
                                path: "/",
                            });
                            localStorage.removeItem("Authorization");
                        }
                    });
                })
                .catch(() => {});
        },
        /**
         * 打开上传
         */
        handleUploadPicture() {
            this.$refs.fileInput.click();
        },
        /**
         * 上传头像
         */
        uploadAvatar(e) {
            if (e.target.files.length === 0) {
                return;
            }
            const files = e.target.files;
            const _this = this;
            let fd = new FormData();
            fd.append("file", files[0]);
            fd.append("uid", this.userInfo.id);
            uploadAvatar(fd).then((res) => {
                if (this.$msg(res.data)) {
                    _this.getUserInfo();
                }
            });
        },

        /**
         * 表情
         */
        handleEmotion(emotion) {
            if (this.chatType == "chat") {
                this.comment = this.comment + emotion;
                return;
            }
            if (this.currentFriendId < 0) {
                this.$msg({ code: 201, msg: "请选择好友！" });
                return;
            }
            this.comment = this.comment + emotion;
        },
        /**
         * 表情转换
         */
        processEmotion(val) {
            return processEmotion(val);
        },
        /**
         * 发送信息
         */
        sendMessage() {
            if (this.chatType == "chat") {
                const { avatar, email } = this.userInfo;
                this.websocketsend(
                    JSON.stringify({
                        type: "send_group_message",
                        group: "chat_group1",
                        email: email,
                        avatar: avatar,
                        comment: this.comment,
                        time: Date.now(),
                    })
                );
                return;
            }
            if (this.currentFriend == "") {
                this.$msg({ code: 201, msg: "请选择好友！" });
                return;
            }
            let com = this.comment.replace(/\s/g, "");
            if (com.length === 0) {
                this.$msg({ code: 201, msg: "请输入内容！" });
                return;
            }
            const { avatar, email, id } = this.userInfo;
            this.websocketsend(
                JSON.stringify({
                    type: "send_message",
                    friend_id: this.currentFriendId,
                    id: id,
                    email: email,
                    avatar: avatar,
                    comment: this.comment,
                    time: Date.now(),
                })
            );
            let data = {
                id: id,
                email: email,
                avatar: avatar,
                comment: this.comment,
                time: Date.now(),
            };
            this.setCommentData(data, this.currentFriendId);
        },
        setCommentData(data, id) {
            let commentData = JSON.parse(
                localStorage.getItem(`commentData${id}`)
            );
            commentData = commentData == null ? [] : commentData;
            commentData.push(data);
            localStorage.setItem(
                `commentData${id}`,
                JSON.stringify(commentData)
            );
            this.comment = "";
            if (this.currentFriend != "" || this.chatType == "chat") {
                this.commentList = commentData;
            }
            setTimeout(() => {
                let ele = document.getElementsByClassName("cc")[0];
                ele.scrollTop = ele.scrollHeight;
            }, 5);
        },
        /**
         * socket
         */
        initWebSocket() {
            // wsschat
            this.websock = new WebSocket("wss链接");
            this.websock.onmessage = this.websocketonmessage;
            this.websock.onopen = this.websocketonopen;
            this.websock.onerror = this.websocketonerror;
            this.websock.onclose = this.websocketclose;
        },
        websocketonopen() {
            this.websocketsend(
                JSON.stringify({
                    type: "login",
                    user_id: this.userInfo.id,
                    group: "chat_group1",
                })
            );
        },
        websocketonerror() {
            this.initWebSocket();
        },
        websocketonmessage(e) {
            let res = JSON.parse(e.data);
            switch (res.type) {
                case "send_message": {
                    this.setCommentData(res, res.id);
                    return;
                }
                case "notOnline": {
                    this.$msg({ code: 201, msg: res.msg });
                    return;
                }
                case "send_group_message": {
                    this.setCommentData(res, `:${res.group}`);
                    return;
                }
                case "count_group_online": {
                    this.groupOnline = res.data.online;
                    return;
                }
            }
        },
        websocketsend(Data) {
            this.websock.send(Data);
        },
    },
    created() {
        this.getUserInfo();
        const _this = this;
        document.onkeydown = function (e) {
            if (e.keyCode == 13) {
                _this.sendMessage();
            }
        };
    },

    components: {
        Emotion,
    },
};
</script>
<style lang="scss">
@import "@/assets/scss/home_common";
</style>
<style lang="scss" scoped>
@import "@/assets/scss/home";
@import "@/assets/scss/user_group";
</style>