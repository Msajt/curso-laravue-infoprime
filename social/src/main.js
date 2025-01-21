// The Vue build version to load with the `import` command
// (runtime-only or standalone) has been set in webpack.base.conf with an alias.
import Vue from 'vue'
import App from './App'
import router from './router'
import axios from 'axios'
import Vuex from 'vuex'
import slug from 'slug'

Vue.use(Vuex)

Vue.config.productionTip = false
Vue.prototype.$http = axios
Vue.prototype.$urlAPI = 'http://127.0.0.1:8000/api/'
Vue.prototype.$slug = slug

let store = {
  state: {
    // Variaveis
    user: sessionStorage.getItem('user') ? JSON.parse(sessionStorage.getItem('user')) : null,
    timelineContents: []
  },
  getters:{
    // Metodos
    getUser: state => {
      return state.user;
    },
    getToken: state => {
      return state.user.token;
    },
    getTimelineContents: state => {
      return state.timelineContents;
    }
  },
  mutations:{
    // Alterar os valores
    setUser(state, n){
      state.user = n;
    },
    setTimelineContents(state, n){
      state.timelineContents = n;
    },
    setPaginationTimelineContents(state, list){
      for(let item of list) state.timelineContents.push(item);
    }
  }
}

Vue.directive('scroll', {
  inserted: function(el, binding){
    let f = function(evt){
      if(binding.value(evt, el)){
        window.removeEventListener('scroll', f);
      }
    }
    window.addEventListener('scroll', f);
  }
})

/* eslint-disable no-new */
new Vue({
  el: '#app',
  store: new Vuex.Store(store),
  router,
  components: { App },
  template: '<App/>'
})
