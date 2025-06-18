import axios from 'axios';
class AuthService {
  login(user) {
    const mode = user.mode ?? 'fe';
    const API_URL = process.env.apiURL + '/' + mode + '_login';
    return axios
      .post(API_URL, user)
      .then(response => {
        if (response.data.token) {
          if (mode === 'be') {
            try {
              document.cookie = "be_jwt_token=" + response.data.token + "; Secure; SameSite=Lax";
              window.window.localStorage.setItem('be-logged-in', true);
              window.location.replace(window.location.origin + "/admin");
            } catch (e) {
              console.log(e);
            }
          } else {
            const token = this.getToken('jwt_token');

            if (!token) {
              document.cookie = "jwt_token=" + response.data.token + "; Secure; SameSite=Lax";
            }

            window.window.localStorage.setItem('logged-in', true);
            if (user.redirectAfterLogin === true) {
              window.location.replace("/account");
            }
          }
        }
      }).catch(error => {
        console.log(error);
        return false;
      });
  }
  logout() {
    window.window.localStorage.removeItem('user-token');
    window.window.localStorage.removeItem('show-ads');
    window.window.localStorage.setItem('logged-in', false);
  }
  getToken(name) {
    const value = `; ${document.cookie}`;
    const parts = value.split(`; ${name}=`);
    if (parts.length === 2) return parts.pop().split(';').shift();
  }
}
export default new AuthService();