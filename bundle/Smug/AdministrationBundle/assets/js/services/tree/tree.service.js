class TreeService {
  buildTree(items, parentId = '') {
    const result = [];

    for (const item of items) {
      if (item.parentId === parentId) {
        const children = this.buildTree(items, item.id);
        result.push({
          ...item,
          children: children.length ? children : []
        });
      }
    }

    return result;
  }
}
export default new TreeService();