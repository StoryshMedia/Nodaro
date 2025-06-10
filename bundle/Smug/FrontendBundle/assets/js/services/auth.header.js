export default function authHeader() {
  let token = window.localStorage.getItem('user-token');
  if (token && token !== '') {
    return { Authorization: 'Bearer ' + token };
  } else {
    return {};
  }
}