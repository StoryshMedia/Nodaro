<template>
  <section v-if="adData.link !== '' && showAds === true">
    <a
      rel="sponsored"
      :href="adData.link"
    >
      <img
        alt="Werbebanner"
        :src="adData.image"
        :width="adData.width"
        :height="adData.height"
        border="0"
      >
    </a>
  </section>
</template>

<script>
import axios from "axios";

export default {
  name: "AsideAd",
  data() {
    return {
      adData: {
        link: "",
        image: ""
      },
      loginChanged: false,
      showAds: true
    };
  },
  mounted() {
    const banners = [
      {
        link: "https://www.awin1.com/cread.php?s=3537568&v=14158&q=377601&r=1247723",
        image: "https://www.awin1.com/cshow.php?s=3537568&v=14158&q=377601&r=1247723",
        width: 300,
        height: 250
      },
      {
        link: "https://www.awin1.com/cread.php?s=3537567&v=14158&q=377601&r=1247723",
        image: "https://www.awin1.com/cshow.php?s=3537567&v=14158&q=377601&r=1247723",
        width: 300,
        height: 250
      }
    ];

    this.adData = banners[Math.floor(Math.random()*banners.length)];

    if (typeof window.localStorage.getItem('show-ads') === 'undefined') {
      this.showAds = true;
    }
  }
};
</script>
