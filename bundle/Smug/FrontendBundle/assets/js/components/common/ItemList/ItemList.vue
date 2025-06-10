<template>
  <section name="itemList">
    <div
      v-if="loaded === true"
      class="container mx-auto px-4"
    >
      <div class="w-full px-4 pt-5 lg:pt-0 pb-5">
        <h3
          class="text-left uppercase text-2xl px-2 font-semibold"
          :class="getHeadlineClass()"
        >
          {{ $t(headline) }}
        </h3>
      </div>
      <div 
        v-if="listItems.length > 0"
      >
        <div v-if="showList === false">
          <div
            class="flex flex-wrap"
          >
            <div class="w-full px-4">
              <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-3">
                <div
                  v-for="item in listItems"
                  :key="item.label"
                  class="bg-white group cursor-pointer"
                >
                  <app-item
                    :slug="item.path"
                    :image="getImageSrc(item)"
                    :mobile-image="getImageSrc(item)"
                    :headline="getHeadline(item)"
                  />
                </div>
              </div>
            </div>
          </div>
        </div>
        <div v-else>
          <div
            v-for="(item, index) in listItems"
            :key="index"
            class="relative"
          >
            <app-standard-list-item
              :show-filters="false"
              :get-data="getData"
              :model="model"
              :absolute-link="absoluteLink"
              :slug-mode="slugMode"
              :item="item"
            />
          </div>
        </div>
      </div>
      <div
        v-else
        class="rounded px-4 py-3 mx-3 mt-4"
        role="alert"
      >
        <div class="flex">
          <div class="py-1">
            <svg
              class="fill-current h-6 w-6 text-dark mr-4"
              xmlns="http://www.w3.org/2000/svg"
              viewBox="0 0 20 20"
            ><path d="M2.93 17.07A10 10 0 1 1 17.07 2.93 10 10 0 0 1 2.93 17.07zm12.73-1.41A8 8 0 1 0 4.34 4.34a8 8 0 0 0 11.32 11.32zM9 11V9h2v6H9v-4zm0-6h2v2H9V5z" /></svg>
          </div>
          <div>
            <p class="uppercase text-xl text-primary">
              {{ $t('NO_DATA') }}
            </p>
            <span class="text-sm text-dark">
              {{ $t('NO_DATA_TEXT') }}
            </span>
          </div>
        </div>
      </div>
    </div>
    <div
      v-else
      class="container mx-auto px-4 lg:pt-24"
    >
      <app-loading />
    </div>
  </section>
</template>

<script>
import axios from "axios";
import { defineAsyncComponent } from "vue";
const AppItem = defineAsyncComponent(() =>
  import("./Item" /* webpackChunkName: "item" */)
);
const AppStandardListItem = defineAsyncComponent(() =>
  import("./StandardListItem" /* webpackChunkName: "standard-list-item" */)
);
const AppLoading = defineAsyncComponent(() =>
  import("../Content/Loading" /* webpackChunkName: "loading" */)
);

export default {
  name: "ItemList",
  components: {
    AppLoading,
    AppStandardListItem,
    AppItem
  },
  inject: ['dataset'],
  model: {
    listItems: []
  },
  data() {
    return {
      items: null,
      slugMode: '',
      headlineClass: '',
      model: '',
      call: '',
      headline: '',
      identifier: '',
      absoluteLink: false,
      showList: false,
      getData: false,
      listItems: [],
      loaded: false
    };
  },
  async created() {
    await this.setProps();
  },
  mounted() {
    if (this.items !== null && this.call === '') {
      this.listItems = this.items;
      this.loaded = true;
    } else {
      axios.get(process.env.apiURL + '/fe/api/' + this.call + '/' + this.identifier)
        .then(response =>  {
          this.listItems = response.data;
          this.loaded = true;
        })
        .catch(function (error) {
        })
        .then(function () {
        });
    }
  },
  methods: {
    setProps() {
      (this.dataset.items) ? this.items = JSON.parse(this.dataset.items) : null;
      (this.dataset.slugMode) ? this.slugMode = this.dataset.slugMode : '';
      (this.dataset.headlineClass) ? this.headlineClass = this.dataset.headlineClass : '';
      (this.dataset.model) ? this.model = this.dataset.model : '';
      (this.dataset.identifier) ? this.identifier = this.dataset.identifier : '';
      (this.dataset.call) ? this.call = this.dataset.call : '';
      (this.dataset.headline) ? this.headline = this.dataset.headline : '';
      (this.dataset.absoluteLink) ? this.absoluteLink = this.dataset.absoluteLink : false;
      (this.dataset.getData) ? this.getData = this.dataset.getData : false;
      (this.dataset.showList) ? this.showList = this.dataset.showList : false;
    },
    getHeadlineClass() {
      return this.headlineClass;
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
    getHeadline(item) {
      if (undefined !== item.label) {
        return this.truncate(item.label, 35, '...');
      } else {
        if(undefined !== item.subTitle && item.subTitle !== '') {
          return this.truncate(item.title + ' - ' + item.subTitle, 90, '...');
        } else {
          return this.truncate(item.title, 90, '...');
        }
      }
    },
    truncate: function (text, length, suffix) {
      if (typeof text === 'undefined') {
        return '';
      }
      if (text.length > length) {
        return text.substring(0, length) + suffix;
      } else {
        return text;
      }
    }
  }
}
</script>
