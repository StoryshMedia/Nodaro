<template>
  <section
    :class="getContainerClasses()"
  >
    <div class="container mx-auto">
      <div
        class="flex flex-wrap items-center"
        :class="getMargins()"
      >
        <div class="w-full lg:px-4 mr-auto ml-auto">
          <div
            class="container mx-auto lg:px-4 flex flex-wrap items-center"
            :class="getPaddings()"
          >
            <div
              v-for="badge in badges"
              :key="badge.id"
              class="bg-white rounded-full w-24 h-24 mx-5 my-5"
            >
              <img
                :ref="'btnRef' + badge.id"
                class="filter"
                :alt="badge.name"
                :class="activeClasses(badge)"
                src="/img/badge.png"
                loading="lazy"
                @mouseenter="toggleTooltip(badge)"
                @mouseleave="toggleTooltip(badge)"
              >
              <div
                :ref="'tooltipRef' + badge.id"
                style="z-index: 100"
                :class="{'hidden': badge.id !== hoverBadge.id, 'block': badge.id === hoverBadge.id}"
                class="absolute bg-white border-0 ml-3 block z-100 font-normal leading-normal text-sm max-w-xs text-left no-underline break-words rounded-lg"
              >
                <div>
                  <div class="bg-white text-dark font-semibold p-3 mb-0 border-b border-solid border-dark rounded-t-lg">
                    {{ $t(badge.name) }}
                  </div>
                  <div class="text-dark p-3">
                    {{ $t(badge.name + '_DESCRIPTION') }}
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script>
import axios from "axios";

export default {
  name: "Badge",
  components: {
  },
  model: {
    badges: []
  },
  props: {
  },
  data() {
    return {
      badges: [],
      hoverBadge: false
    };
  },
  mounted() {
    this.retrieveItems();
  },
  methods: {
    toggleTooltip(badge){
      if (this.hoverBadge.id === badge.id) {
        this.hoverBadge = {};
      } else {
        this.hoverBadge = badge;
      }
    },
    retrieveItems() {
      const config = {
        headers: { Authorization: `Bearer ${this.$store.state.auth.token}` }
      };

      axios.get(process.env.apiURL + '/fe/api/account/badge', config)
        .then((response) => {
          this.badges = response.data;
        })
        .catch((e) => {
          console.clear();
        });
    },
    getContainerClasses(){
      return '';
    },
    getMargins(){
      return '';
    },
    getPaddings(){
      return '';
    },
    activeClasses(badge){
      return (badge.active === false) ? 'grayscale' : 'grayscale-0';
    }
  }
}
</script>
