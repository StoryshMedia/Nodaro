import AuthService from '../services/auth.service';
const token = window.localStorage.getItem('user-token');
const initialState = token
  ? { status: { loggedIn: true }, token }
  : { status: { loggedIn: false }, token: null };
export const auth = {
  namespaced: true,
  state: initialState,
  actions: {
    login({ commit }, user) {
      return AuthService.login(user).then(
        user => {
          if (user === false) {
            commit('loginFailure');
            return Promise.reject(user);
          }
          commit('loginSuccess', user);
          window.location.replace("/admin");
          return Promise.resolve(user);
        },
        error => {
          commit('login');
          return Promise.reject(error);
        }
      );
    },
    logout({ commit }) {
      AuthService.logout();
      commit('logout');
    },
  },
  getters: {
    getToken: state => {
      return state.token
    }
  },
  mutations: {
    loginSuccess(state, user) {
      state.status.loggedIn = true;
      state.token = user;
      window.location.reload();
    },
    loginFailure(state) {
      state.status.loggedIn = false;
      state.token = null;
    },
    logout(state) {
      state.status.loggedIn = false;
      state.token = null;
    }
  }
};