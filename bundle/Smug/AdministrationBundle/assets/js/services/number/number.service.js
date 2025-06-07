class NumberService {
  getFloat(value) {
    if (typeof value !== 'string') {
      if (typeof value !== 'number') {
        return 0.00;
      }

      return value;
    }

    return parseFloat(value).toFixed(2);
  }
}
export default new NumberService();