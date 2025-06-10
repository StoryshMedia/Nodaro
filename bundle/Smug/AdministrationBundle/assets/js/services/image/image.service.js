class ImageService {
  getImageFromItem(item) {
    if (!item.images) {
      if (!item.profileImage) {
        return this.getFallbackImage();
      }

      return this.getImagePath(item.profileImage);
    } else {
      if (item.images.length === 0) {
        return this.getFallbackImage();
      }

      if (item.images[0].media) {
        return this.getImagePathFromMedia(item.images[0].media);
      } else {
        return this.getImagePathFromMedia(item.images[0]);
      }
    }
  }
  getImagePathFromMedia(media, type = "list") {
    if (!media.thumbnails || media.thumbnails.length === 0) {
      return this.getImagePath(media);
    }

    if (media.thumbnails[Object.keys(media.thumbnails)[0]].path) {
      return this.getImagePath(media.thumbnails[Object.keys(media.thumbnails)[0]]);
    } else {
      return this.getImagePath(media.thumbnails[Object.keys(media.thumbnails)[0]][type]);
    }
  }
  getImagePathFromImage(image) {
    return this.getImagePath(image.media.thumbnails[Object.keys(image.media.thumbnails)[0]]);
  }
  getImagePath(item) {
    if (!item) {
      return this.getFallbackImage();
    }

    let path = item.path + "/";
    path = path.replace('//', '/');

    if (!item.viewport) {
      return process.env.frontendURL + "/" +  path + item.file + "." + item.extension;  
    }

    return process.env.frontendURL + "/" + path + item.file + "_" + item.viewport + "." + item.extension;
  }
  getFilePath(item) {
    let path = item.path + "/";
    path = path.replace('//', '/');

    return process.env.frontendURL + "/" + path + item.file + "." + item.extension;
  }
  getFileName(item) {
    return item.file;
  }
  getLockScreenBackground() {
    return process.env.frontendURL + '/administration/img/lock/lock-screen-' + Math.floor(Math.random() * (6 - 1 + 1) + 1) + '.webp';
  }
  getFallbackImage() {
    return '/site/img/author/list/preview/authorListPreview-' + Math.floor(Math.random() * (26 - 1 + 1) + 1) + '.webp';
  }
  isImage(file) {
    return ['webp', 'jpg', 'png', 'svg'].includes(file.media.extension);
  }
}
export default new ImageService();