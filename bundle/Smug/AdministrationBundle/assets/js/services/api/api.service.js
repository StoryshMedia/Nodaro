import axios from "axios";

class ApiService {
  get(url, id, auth = true, getParams = '') {
    return new Promise((resolve) => {
      if (auth === true) {
        let tokenString = 'jwt_token';

        if (url.substring(0,7) === '/be/api') {
          tokenString = 'be_' + tokenString;
        }

        const token = this.getToken(tokenString);

        if (url.substring(0,7) === '/be/api' && typeof token === 'undefined') {
          window.location.replace("/admin/login");
        }

        const config = {
          headers: { Authorization: `Bearer ${token}` }
        };
  
        let call = process.env.apiURL + url;
    
        if (id && id !== null) {
          call = call + id;
        }

        if (getParams !== '') {
          call += '?' + getParams;
        }
  
        axios.get(call, config)
          .then(response =>  {
            resolve((response.data.data) ? response.data.data : response.data);
          })
          .catch(error => {
          });
      } else {
        let call = process.env.apiURL + url;
    
        if (id && id !== null) {
          call = call + id;
        }

        if (getParams !== '') {
          call += '?' + getParams;
        }
  
        axios.get(call)
          .then(response =>  {
            resolve((response.data.data) ? response.data.data : response.data);
          })
          .catch(error => {
          });
      }
    });
  }
  put(url, data, auth = true) {
    return new Promise((resolve) => {
      if (auth === true) {
        let tokenString = 'jwt_token';

        if (url.substring(0,7) === '/be/api') {
          tokenString = 'be_' + tokenString;
        }

        const token = this.getToken(tokenString);

        if (url.substring(0,7) === '/be/api' && typeof token === 'undefined') {
          window.location.replace("/admin/login");
        }

        const config = {
          headers: { Authorization: `Bearer ${token}` }
        };
        
        axios.put(process.env.apiURL + url, data, config)
          .then(response =>  {
            const result = (response.data) ? response.data : response;
            resolve(result);
          })
          .catch(error => {
            this.isLoading = false;
          });
      } else {
        axios.put(process.env.apiURL + url, data)
          .then(response =>  {
            const result = (response.data) ? response.data : response;
            resolve(result);
          })
          .catch(error => {
            this.isLoading = false;
          });
      }
    });
  }
  post(url, data, auth = true, additionalConfig = {}) {
    return new Promise((resolve) => {
      if (auth === true) {
        let tokenString = 'jwt_token';

        if (url.substring(0,7) === '/be/api') {
          tokenString = 'be_' + tokenString;
        }

        const token = this.getToken(tokenString);

        if (url.substring(0,7) === '/be/api' && typeof token === 'undefined') {
          window.location.replace("/admin/login");
        }

        const config = {
          headers: { Authorization: `Bearer ${token}` }
        };
        const requestConfig = {...config, ...additionalConfig};

        axios.post(process.env.apiURL + url, data, requestConfig)
          .then(response =>  {
            const result = (typeof response.data.data !== 'undefined') ? response.data.data : response.data;
            resolve(result);
          })
          .catch(error => {
            resolve(error);
          });
      } else {
        axios.post(process.env.apiURL + url, data, additionalConfig)
          .then(response =>  {
            const result = (typeof response.data.data !== 'undefined') ? response.data.data : response.data;
            resolve(result);
          })
          .catch(error => {
            resolve(error);
          });
      }
    });
  }
  getToken(name) {
    const value = `; ${document.cookie}`;
    const parts = value.split(`; ${name}=`);
    if (parts.length === 2) return parts.pop().split(';').shift();
  }
}
export default new ApiService();