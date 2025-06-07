export default function authHeader() {
  let token = window.localStorage.getItem('be-token');
  if (token && token !== '') {
    return { Authorization: 'Bearer ' + token };
  } else {
    return {};
  }
}