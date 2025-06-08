import axios from 'axios';
class AuthService {
  login(user) {
    const API_URL = process.env.apiURL + '/be_login';

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