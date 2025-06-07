class ItemService {
  insertIntoObject(obj, newValue, afterIndex) {
    return new Promise((resolve) => {
      const newObj = {};
      let count = 0;

      for (const key of Object.keys(obj)) {
        if (count === afterIndex) {
          newObj[count] = obj[key];
          count++;
          newObj[count] = newValue;
          count++;
        } else {
          newObj[count] = obj[key];
        }

        count++;
      }
      resolve(newObj);
    });
  }
  getItemPositionOfItem(items, item, field = 'id') {
    return new Promise((resolve) => {
      for (let i = 0; i <= Object.keys(items).length - 1; i++) {
        if (items[Object.keys(items)[i]][field] === item[field]) {
          resolve(i);
        }
      }
    });
  }
  getItemFromNestedItems(items, identifier, field = 'id') {
    return new Promise((resolve) => {
      for (let item of Object.values(items)) {
        if (item[field] === identifier) resolve(item);
    
        if (item.children) {
          this.getItemFromNestedItems(item.children, identifier, field).then(desiredItem => {
            if (desiredItem) resolve(desiredItem);
          });
        }
      }
    });
  }
  getItemPositionInNestedArray(items, newItem, field = 'id', parentKey = null) {
    return new Promise((resolve) => {
      console.log(items);
      console.log(newItem);
      for (let i = 0; i <= Object.keys(items).length - 1; i++) {
        if (items[Object.keys(items)[i]][field] === newItem[field]) {
          resolve({
            index: Object.keys(items)[i],
            parentKey: parentKey 
          });
        }

        if (items[Object.keys(items)[i]].children) {
          this.getItemPositionInNestedArray(items[Object.keys(items)[i]].children, newItem, field, Object.keys(items)[i]).then(desiredItem => {
            if (desiredItem) {
              resolve(desiredItem);
            }
          });
        }
      }
    });
  }
}
export default new ItemService();