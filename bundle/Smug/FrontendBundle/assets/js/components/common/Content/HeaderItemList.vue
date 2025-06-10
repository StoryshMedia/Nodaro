<template>
  <div
    class="flex flex-wrap"
  >
    <a 
      v-for="(item, itemIndex) in items"
      :key="itemIndex"
      :href="'/' + mode + '/' + item.slug"
      class="group inline-flex items-center ml-3"
    >
      <div
        class="h-8 w-8 background rounded-full mr-3"
        :style="{ 'background-image': 'url(' + getImageSrc(item, 'mobile') + ')'}"
      />
      <span class="text-black text-xs transition-colors">
        <span
          class=" italic"
          style="font-size: 10px; margin-right: 2px;"
        >{{ $t("BY") }}</span>
        <span class="group-hover:text-primary transition-all font-sans font-bold">{{ item.completeName }}</span>
      </span>
    </a>
  </div>
</template>

<script>

export default {
  name: "HeaderItemList",
  inject: ['dataset'],
  data() {
    return {
      items: [],
      mode: ''
    }
  },
  async created() {
    await this.setProps();
  },
  methods: {
    setProps() {
      this.items = JSON.parse(this.dataset.items);
      this.mode = this.dataset.mode;
    },
    getImageSrc(item, viewport) {
      if (typeof item.images === "undefined") {
        return item.image;
      }

      if (item.images.length === 0) {
        return item.image;
      }

      let imageCount = item.images.length,
        count = 0,
        hasMain = false;

      if (imageCount === 1) {
        if (item.images[0].media.thumbnails.length === 0) {
          return item.images[0].img;
        }
        return item.images[0].media.thumbnails[viewport]['list'].img;
      }

      for (count; count <= imageCount - 1; count++) {
        if (item.images[count].main === true) {
          hasMain = true;
          return item.images[count].media.thumbnails[viewport]['list'].img;
        }

        if (count === imageCount - 1 && hasMain === false) {
          return item.images[0].media.thumbnails[viewport]['list'].img;
        }
      }
    }
  }
};
</script>
