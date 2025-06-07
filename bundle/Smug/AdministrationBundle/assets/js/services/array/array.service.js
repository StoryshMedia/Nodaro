class ArrayService {
  getElementFromObjectArray(array, value, identifier) {
    const item = array.find(el => {
      return el[identifier] == value;
    });

    return (typeof item === 'undefined') ? null : item;
  }
  getObjectIndexInArray(array, value, identifier) {
    return array.findIndex(x => x[identifier] === value)
  }
  getIndexInArray(array, value) {
    return array.findIndex(x => x === value)
  }
  isArray(array) {
    return Array.isArray(array);
  }
  getLastArrayElement(array) {
    if (array.length === 0) {
      return null;
    }

    return array[array.length - 1];
  }
  getLastObjectElement(objects) {
    if (Object.keys(objects).length === 0) {
      return null;
    }

    return objects[Object.keys(objects).pop()];
  }
}
export default new ArrayService();