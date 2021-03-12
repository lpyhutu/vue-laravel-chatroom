import Vue from 'vue'
import App from './App.vue'
import router from "./router"
Vue.config.productionTip = false
import { Button, Input, Tooltip, Image, MessageBox, Avatar, Dialog } from 'element-ui';
Vue.use(Button);
Vue.use(Input);
Vue.use(Tooltip);
Vue.use(Image)
Vue.use(Avatar)
Vue.use(Dialog)
Vue.prototype.$msgBox = MessageBox;


router.beforeEach((to, from, next) => {
  if (to.meta.title) {
    document.title = to.meta.title;
  }
  next();
});
import store from "@/assets/vuex/store"
import { msg } from "@/assets/js/message"

Vue.prototype.$msg = msg
Vue.prototype.$api = "http://localhost:88/chatroom/public/"

new Vue({
  router,
  store,
  render: h => h(App),
}).$mount('#app')
