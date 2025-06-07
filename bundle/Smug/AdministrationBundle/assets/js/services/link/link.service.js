class LinkService {
  getDetailLink(url, id) {
    return process.env.apiURL + url + id;
  }
}
export default new LinkService();