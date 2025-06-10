import AuthService from '../services/auth.service';
const responsibleToken = window.localStorage.getItem('responsible-user-token');
const initialResponsibleState = responsibleToken
  ? { status: { loggedIn: true }, responsibleToken }
  : { status: { loggedIn: false }, responsibleToken: null };
export const responsibleAuth = {
  namespaced: true,
  state: initialResponsibleState,
  actions: {
    responsibleLogin({ commit }, user) {
      return AuthService.responsibleLogin(user).then(
        user => {
          commit('responsibleLoginSuccess', user);
          return Promise.resolve(user);
        },
        error => {
          commit('login');
          return Promise.reject(error);
        }
      );
    },
    responsibleLogout({ commit }) {
      AuthService.responsibleLogout();
      commit('logout');
    },
  },
  getters: {
    getToken: state => {
      return state.responsibleToken
    }
  },
  mutations: {
    loginSuccess(state, user) {
      state.status.loggedIn = true;
      state.token = user;
      window.location.reload();
    },
    responsibleLoginSuccess(state, user) {
      state.status.responsibleLoggedIn = true;
      state.responsibleToken = user;
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