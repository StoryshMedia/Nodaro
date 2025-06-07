class LocalStorageService {
  has(key) {
    return (this.get(key) !== null);
  }
  get(key) {
    return window.localStorage.getItem(key);
  }
  set(key, value) {
    return window.localStorage.setItem(key, value);
  }
}
export default new LocalStorageService();