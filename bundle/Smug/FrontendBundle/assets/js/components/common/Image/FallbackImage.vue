<template>
  <div
    class="w-full lazy object-cover transition-all duration-500 ease-in-out transform group-hover:scale-110"
    :class="height"
    style="background-repeat: no-repeat; background-position: center; background-size: cover;"
    :data-bg="getFallback()"
    :data-bg-hidpi="getFallback()"
  />
</template>

<script>
export default {
  name: "DetailImage",
  props: {
    headline: {
      type: String,
      required: true
    },
    height: {
      type: String,
      required: false,
      default: 'h-paginatedListItem'
    }
  },
  data() {
    return {
      fallbackImage: ''
    }
  },
  mounted() {
    this.setFallback();
  },
  methods: {
    setFallback() {
      this.fallbackImage = process.env.apiURL + '/Resources/fallback/fallbackImage-' + Math.floor(Math.random() * (26 - 1 + 1) + 1) + '.webp'

      setTimeout(() => {
        window.LL.update();
      }, "100");
    },
    getFallback() {
      setTimeout(() => {
        window.LL.update();
      }, "100");
      return this.fallbackImage;
    }
  }
}
</script>