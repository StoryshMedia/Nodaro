<template>
  <section
    v-if="adData.link !== '' && showAds === true"
    class="pb-3"
  >
    <div class="w-full md:max-w-3/4 mx-auto">
      <a
        rel="sponsored"
        title="Werbelink"
        :href="adData.link"
      >
        <img
          alt="Werbebanner"
          class="mx-auto"
          :src="adData.image"
          :width="adData.width"
          :height="adData.height"
          border="0"
        >
      </a>
    </div>
  </section>
</template>

<script>
export default {
  name: "FooterAd",
  data() {
    return {
      adData: {
        link: "",
        image: ""
      },
      showAds: true
    };
  },
  mounted() {
    const banners = [
      {
        link: "https://www.awin1.com/cread.php?s=3537570&v=14158&q=377601&r=1247723",
        image: "https://www.awin1.com/cshow.php?s=3537570&v=14158&q=377601&r=1247723",
        width: 728,
        height: 90
      },
      {
        link: "https://www.awin1.com/cread.php?s=3537569&v=14158&q=377601&r=1247723",
        image: "https://www.awin1.com/cshow.php?s=3537569&v=14158&q=377601&r=1247723",
        width: 728,
        height: 90
      }
    ];

    if (typeof window.localStorage.getItem('show-ads') === 'undefined') {
      this.showAds = true;
    }

    this.showAds = window.localStorage.getItem('show-ads');

    this.adData = banners[Math.floor(Math.random()*banners.length)];
  },
  methods: {
  },
};
</script>
