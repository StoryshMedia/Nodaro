<template>
  <div
    class="w-full object-cover max-w-28"
  >
    <img
      :alt="headline"
      :title="headline"
      class="w-full max-w-28 mx-auto my-auto"
      loading="lazy"
      :src="image"
      onerror="this.onerror=null; this.src='https://api.storysh.de/_uploads/images/media/fallback/thumbnails/fallback_02_list_desktop.webp'"
    >
  </div>
</template>

<script>

export default {
  name: "SearchImage",
  props: {
    headline: {
      type: String,
      required: true
    },
    image: {
      type: String,
      required: true
    }
  },
  methods: {
    getBackgroundImageStyles() {
      return {
        'background-image': `url("${this.image}")`,
        'background-repeat': 'no-repeat',
        'background-position': 'center',
        'background-size': 'cover'
      }
    },
  }
}
</script>
