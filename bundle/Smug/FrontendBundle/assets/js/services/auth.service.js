import axios from 'axios';
const API_URL = 'https://storysh.de/fe_login';
const RESPONSIBLE_API_URL = 'https://storysh.de/responsible_login';
class AuthService {
  login(user) {
    return axios
      .post(API_URL, user)
      .then(response => {
        if (response.data.token) {
          const token = this.getToken('jwt_token');

          if (!token) {
            document.cookie = "jwt_token=" + response.data.token + "; Secure; SameSite=Lax";
          }

          window.window.localStorage.setItem('logged-in', true);
          if (user.redirectAfterLogin === true) {
            window.location.replace("/account");
          }
        }
        return response.data.token;
      }).catch(error => {
        return false;
      });
  }
  responsibleLogin(user) {
    return axios
      .post(RESPONSIBLE_API_URL, user)
      .then(response => {
        if (response.data.token) {
          window.window.localStorage.setItem('responsible-user-token', response.data.token);
          window.window.localStorage.setItem('responsible-logged-in', true);
          window.location.reload();
        }
      });
  }
  logout() {
    window.window.localStorage.removeItem('user-token');
    window.window.localStorage.removeItem('show-ads');
    window.window.localStorage.setItem('logged-in', false);
  }
  responsibleLogout() {
    window.window.localStorage.removeItem('responsible-user-token');
    window.window.localStorage.removeItem('show-ads');
    window.window.localStorage.setItem('responsible-logged-in', false);
  }
  getToken(name) {
    const value = `; ${document.cookie}`;
    const parts = value.split(`; ${name}=`);
    if (parts.length === 2) return parts.pop().split(';').shift();
  }
}
export default new AuthService();