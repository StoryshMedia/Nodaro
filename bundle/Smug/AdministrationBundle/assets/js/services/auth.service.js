import axios from 'axios';
const API_URL = 'https://storysh.de/be_login';
// const API_URL = 'http://symfony.localhost/be_login';
class AuthService {
  login(user) {
    return axios
      .post(API_URL, user)
      .then(response => {
        if (response.data.token) {
          document.cookie = "be_jwt_token=" + response.data.token + "; Secure; SameSite=Lax";
          window.window.localStorage.setItem('be-logged-in', true);
          window.location.replace("/admin");
        }
        return response.data.token;
      }).catch(error => {
        return false;
      });
  }
  logout() {
    window.window.localStorage.removeItem('be-token');
    window.window.localStorage.setItem('be-logged-in', false);
    window.location.replace("/admin/login");
  }
}
export default new AuthService();