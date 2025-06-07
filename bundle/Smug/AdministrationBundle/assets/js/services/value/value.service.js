class ValueService {
  getValue(item, config) {
    if (typeof item === 'object') {
      if (typeof item[config.identifier] === 'object') {
        if (!config.returnObject || config.returnObject === false) {
          return this.getValue(item, config);
        }
      }

      if (item[config.identifier]) {
        return item[config.identifier];
      } else {
        return item;    
      }
    }

    return item;
  }
  getBooleanValue(item) {
    if (item === "true" || item === "1" || item === 1) {
      return true;
    }
    if (item === "false" || item === "0" || item === 0) {
      return false;
    }

    return item;
  }
}
export default new ValueService();