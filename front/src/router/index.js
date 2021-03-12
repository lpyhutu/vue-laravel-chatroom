import Vue from "vue";
import Router from "vue-router";

Vue.use(Router)

const routerPush = Router.prototype.push
Router.prototype.push = function push(location) {
    return routerPush.call(this, location).catch(error => error)
}

const router = new Router({
    mode: "history",
    base: process.env.BASE_URL,
    routes: [
        {
            path: '/',
            name: 'login',
            component: () => import("../components/Login.vue"),
            meta: {
                title: "糊涂简陋聊天室_Welcome"
            },
        }, {
            path: '/register',
            name: 'register',
            component: () => import("../components/Register.vue"),
            meta: {
                title: "注册"
            },
        },
        {
            path: '/home',
            name: 'home',
            component: () => import("../components/Home.vue"),
            meta: {
                title: "糊涂简陋聊天室"
            },
        }, {
            path: '/test',
            name: 'test',
            component: () => import("../components/Test.vue"),
            meta: {
                title: "测试"
            },
        },
    ]
})
router.beforeEach((to, from, next) => {
    if (to.path === '/login' || to.path === "/" || to.path === "/register") {
        next();
    } else {
        let token = localStorage.getItem('Authorization');
        if (token === null || token === '') {
            next('/');
        } else {
            next();
        }
    }
});

export default router;