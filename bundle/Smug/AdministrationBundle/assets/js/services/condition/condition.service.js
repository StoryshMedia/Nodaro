class ConditionService {
  check(config, data) {
    if (config.condition.type === 'multiple') {
      let result = true;

      if (!config.condition.allowedBy) {
        config.condition.allowedBy = 'and';
      }

      if (config.condition.allowedBy.toLowerCase()  === 'and') {
        for (let count = 0; count <= config.condition.checks.length - 1; count++) {
          if (this.checkByType(config.condition.checks[count], data) === false) {

            result = false;
            break;
          }
        }
      }

      if (config.condition.allowedBy.toLowerCase()  === 'or') {
        for (let count = 0; count <= config.condition.checks.length - 1; count++) {
          if (this.checkByType(config.condition.checks[count], data) === true) {
            break;
          }
        }
      }

      return result;
    } else {
      return this.checkByType(config, data);
    }
  }
  checkByType(config, data) {
    switch (config.condition.type) {
    case 'isTrue':
      return data[config.condition.checkProperty] === true;
    case 'isFalse':
      return data[config.condition.checkProperty] === false;
    case 'isEqual':
      return data[config.condition.checkProperty] === compareValue;
    default:
      return true
    }
  }
}
export default new ConditionService();