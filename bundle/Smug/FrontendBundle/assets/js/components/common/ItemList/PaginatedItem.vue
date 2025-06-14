<template>
  <article v-if="publication.slug && isLoading === false">
    <item
      v-if="bigTiles === false"
      :image="getImageSrc(publication)"
      :mobile-image="getImageSrc(publication)"
      :headline="publication.completeName"
      :rating="publication.rating"
      :slug="getSlugMode(model) + publication.slug"
    />
    <tile
      v-else
      :item="getPublication()"
      :get-data="false"
      :image="publication.image"
      :headline="publication.completeName"
      :slug="getSlugMode(model) + publication.slug"
    />
  </article>
  <article v-else>
    <div
      class="block relative lg:mb-0 mb-5 group"
    >
      <div
        class="max-w-sm overflow-hidden px-1"
      >
        <div class="lg:h-48 rounded-lg bg-gray-400  md:h-36 w-full object-cover object-center" />
      </div>
      <div class="px-3 py-4">
        <p class="leading-relaxed mb-3 rounded-md w-full h-3 animate-pulse bg-gray-400 " />
        <p class="leading-relaxed mb-3 rounded-md w-2/3 h-3 animate-pulse bg-gray-400 " />
        <p class="leading-relaxed mb-3 rounded-md w-1/2 h-3 animate-pulse bg-gray-400 " />
      </div>
    </div>
  </article>
</template>

<script>
import { defineAsyncComponent } from "vue";
const Item = defineAsyncComponent(() =>
  import("./Item" /* webpackChunkName: "item" */)
);
const Tile = defineAsyncComponent(() =>
  import("./Tile" /* webpackChunkName: "tile" */)
);
import axios from "axios";

export default {
  name: "PaginatedItem",
  components: {
    Item,
    Tile
  },
  props: {
    item: {
      type: Object,
      required: true
    },
    bigTiles: {
      type: Boolean,
      required: true
    },
    loading: {
      type: Boolean,
      required: false,
      default: false
    },
    model: {
      type: String,
      required: true
    },
    mode: {
      type: String,
      required: false,
      default: ''
    },
    slugMode: {
      type: String,
      required: false,
      default: ''
    },
    detailGetCall: {
      type: String,
      required: false,
      default: ''
    },
    showFilters: {
      type: Boolean,
      required: true
    }
  },
  data() {
    return {
      publication: {},
      isLoading: false,
      image: '',
      backendHost: '',
      visible: false,
      lightboxIndex: 0,
    };
  },
  watch: {
    item: function(newVal, oldVal) {
      this.getItem();
    }
  },
  mounted() {
    this.isLoading = true;
    this.getItem();
  },
  methods: {
    getItem() {
      this.publication = {};
      axios.get(
        (this.detailGetCall === '') ? process.env.apiURL + '/fe/api/' + this.getDetailMode(this.model) + '/detail/' + this.item.slug : process.env.apiURL + this.detailGetCall + this.item.slug
      )
        .then(response =>  {
          this.publication = response.data;
          this.isLoading = false;
        })
        .catch(function (error) {
        });
    },
    getPublication() {
      return this.publication;
    },
    getDetailMode(model) {
      switch (model) {
      case 'stories':
        return 'story'
      case 'publications':
        return 'publication'
      case 'publication':
        return 'publication'
      case 'authors':
        return 'author'
      case 'author':
        return 'author'
      case 'genres':
        return 'genre'
      case 'genre':
        return 'genre'
      case 'schools':
        return 'school'
      case 'school':
        return 'school'
      case 'markets':
        return 'market'
      case 'market':
        return 'market'
      case 'users':
        return 'community'
      case 'results':
        return this.mode
      default:
        return model
      }
    },
    getSlugMode(model) {
      if (this.slugMode === '') {
        return model
      }
      switch (model) {
      case 'stories':
        return '/story/'
      case 'publications':
        return '/publication/'
      case 'publication':
        return '/publication/'
      case 'authors':
        return '/'
      case 'author':
        return '/'
      case 'genres':
        return '/genre/'
      case 'genre':
        return '/genre/'
      case 'schools':
        return '/wettbewerbe/'
      case 'school':
        return '/wettbewerbe/'
      case 'markets':
        return '/tauschboerse/'
      case 'users':
        return '/community/'
      case 'results':
        return this.slugMode
      default:
        return model
      }
    },
    getImageSrc(item) {
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
        return item.images[0];
      }

      for (count; count <= imageCount - 1; count++) {
        if (item.images[count].main === true) {
          hasMain = true;
          return item.images[count];
        }

        if (count === imageCount - 1 && hasMain === false) {
          return item.images[0];
        }
      }
    },
  }
}
</script>
