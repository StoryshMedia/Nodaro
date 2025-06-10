<template>
  <article
    v-if="detailItem.slug && loading === false"
    class="pb-10"
  >
    <a
      :href="getUrl()"
      :title="detailItem.title"
    >
      <div
        data-aos="fade-zoom-in"
        class="text-left flex flex-wrap items-center lg:items-start"
      >
        <div class="w-full text-center md:text-left md:w-3/12 lg:w-2/12">
          <div class="inline-block relative">
            <div
              class="text-center md:text-left relative w-32 h-32 rounded-full border-4 border-primary overflow-hidden"
            >
              <detail-image
                v-if="detailItem.images.length > 0"
                :headline="detailItem.title"
                :height="'h-32'"
                :image="getImageSrc(detailItem)"
              />
              <fallback-image
                v-else
                :headline="detailItem.title"
                :height="'h-32'"
              />
            </div>
          </div>
        </div>
        <div class="md:w-9/12 lg:w-10/12 mt-5 lg:mt-0">
          <div class="py-5">
            <h4 class="text-left card-title text-xl font-semibold">
              {{ detailItem.title }}
            </h4>
            <div class="justify-center md:justify-start mx-auto py-5 hidden lg:flex lg:flex-wrap">
              <div class="tags-wrapper flex items-center text-sm lg:text-base">
                <p
                  v-if="detailItem.category"
                  class="flex flex-wrap"
                >
                  <span class="mr-4 ml-5 text-primary"><icon-tag /></span>
                  {{ detailItem.category.title }}
                </p>
                <section
                  v-if="typeof detailItem.tags !== 'undefined'"
                  class="flex flex-wrap"
                >
                  <p
                    v-for="(tag, tagIndex) in detailItem.tags"
                    :key="tagIndex"
                    class="flex flex-wrap"
                  >
                    <span class="mr-4 ml-5 text-primary"><icon-tag /></span>
                    {{ tag.title }}
                  </p>
                </section>
              </div>
            </div>
            <div class="lg:hidden flex flex-row justify-start py-4">
              <p
                v-if="detailItem.category"
                class="flex flex-wrap"
              >
                <span class="mr-4 ml-5 text-primary"><icon-tag /></span>
                {{ detailItem.category.title }}
              </p>
              <section
                v-if="typeof detailItem.tags !== 'undefined'"
                class="flex flex-wrap"
              >
                <p
                  v-for="(tag, tagIndex) in detailItem.tags"
                  :key="tagIndex"
                  class="flex flex-wrap"
                >
                  <span class="mr-4 ml-5 text-primary"><icon-tag /></span>
                  {{ tag.title }}
                </p>
              </section>
            </div>
            <p>
              <span
                v-html="truncate(detailItem.description, 255, '...')"
              />
            </p>
          </div>
        </div>
      </div>
    </a>
    <div class="bg-dark text-white rounded-md hidden lg:flex lg:flex-wrap">
      <div class="md:w-2/12" />
      <div
        v-if="detailItem.additional"
        class="py-3 md:w-10/12"
      >
        <div class="flex flex-wrap">
          <div class="w-1/4 pr-5 flex flex-wrap">
            <span class="pr-3">{{ detailItem.additional.messageCount }}</span> <svg
              class="w-6 h-6"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
              xmlns="http://www.w3.org/2000/svg"
            ><path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"
            /></svg>
          </div>
          <div
            class="w-1/4 pl-5 flex flex-wrap"
          >
            <span class="pr-3">{{ detailItem.additional.visits }}</span>
            <svg
              class="w-6 h-6"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
              xmlns="http://www.w3.org/2000/svg"
            ><path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"
            /><path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"
            /></svg>
          </div>
          <div
            v-if="detailItem.additional.userInformation.length > 0"
            class="w-1/4 px-5"
          >
            <div
              class="flex flex-wrap"
            >
              <div
                v-for="(user, userIndex) in detailItem.additional.userInformation"
                :key="userIndex"
                class="mx-1 border-1 border-kelp-700"
              >
                <a
                  :href="user.slug"
                  :title="user.title"
                  target="blank"
                >
                  <img
                    class="w-6 h-6 rounded-full"
                    :src="user.image.src"
                    :alt="user.title"
                  >
                </a>
              </div>
            </div>
          </div>
          <div
            v-if="detailItem.additional.lastMessage"
            class="w-1/4 pl-5 flex flex-wrap"
          >
            <span class="pr-3">{{ detailItem.additional.lastMessage }}</span>
            <svg
              class="w-6 h-6"
              fill="none"
              stroke="currentColor"
              viewBox="0 0 24 24"
              xmlns="http://www.w3.org/2000/svg"
            ><path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z"
            /></svg> 
          </div>
        </div>
      </div>
    </div>
  </article>
  <article v-else>
    <div
      class="block relative lg:mb-0 mb-5 group"
    >
      <div
        class="max-w-sm overflow-hidden px-1"
      >
        <div class="lg:h-48 rounded-lg bg-gray-400 md:h-36 w-full object-cover object-center" />
      </div>
      <div class="px-3 py-4">
        <p class="leading-relaxed mb-3 rounded-md w-full h-3 animate-pulse bg-gray-400" />
        <p class="leading-relaxed mb-3 rounded-md w-2/3 h-3 animate-pulse bg-gray-400" />
        <p class="leading-relaxed mb-3 rounded-md w-1/2 h-3 animate-pulse bg-gray-400" />
      </div>
    </div>
  </article>
</template>

<script>
import axios from "axios";
import {defineAsyncComponent} from "vue";

const IconTag = defineAsyncComponent(() => import("@core/js/icons/icons/IconTag.vue" /* webpackChunkName: "icon-tag" */));
const DetailImage = defineAsyncComponent(() => import("../Image/DetailImage" /* webpackChunkName: "detail-image" */));
const FallbackImage = defineAsyncComponent(() => import("../Image/FallbackImage" /* webpackChunkName: "fallback-image" */));

export default {
  name: "StandardListItem",
  components: {
    DetailImage,
    FallbackImage,
    IconTag
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
    getData: {
      type: Boolean,
      required: false,
      default: true
    },
    absoluteLink: {
      type: Boolean,
      required: false,
      default: false
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
    additionalCall: {
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
      detailItem: {},
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
    this.getItem();
  },
  methods: {
    getItem() {
      this.detailItem = {};
      if (this.getData === true) {
        axios.get(
          (this.detailGetCall === '') ? process.env.apiURL + '/fe/api/' + this.getDetailMode(this.model) + '/detail/' + this.item.slug : process.env.apiURL + this.detailGetCall + this.item.slug
        )
          .then(response =>  {
            this.detailItem = response.data;
            this.loading = false;
          })
          .catch(function (error) {
          })
          .finally(res => {
            if (this.additionalCall !== '') {
              axios.get(
                process.env.apiURL + '/fe/api/' + this.additionalCall + this.detailItem.slug
              ).then(response =>  {
                this.detailItem.additional = response.data;
              })
                .catch(function (error) {
                });
            }
          });
      } else {
        this.detailItem = this.item;

        if (this.additionalCall !== '') {
          axios.get(
            process.env.apiURL + '/fe/api/' + this.additionalCall + this.item.slug
          ).then(response =>  {
            this.detailItem.additional = response.data;
          })
            .catch(function (error) {
            });
        }
      }
    },
    truncate: function (text, length, suffix) {
      if (text.length > length) {
        if (text.includes('<p>')) {
          return text.substring(0, length) + suffix + '</p>';
        } else {
          return text.substring(0, length) + suffix;  
        }
      } else {
        return text;
      }
    },
    getPublication() {
      return this.publication;
    },
    getUrl() {
      if (this.absoluteLink === true) {
        return process.env.frontendURL + this.slugMode + '/' + this.detailItem.slug;
      } else {
        return (this.getSlugMode(this.model) !== '') ? this.getSlugMode(this.model) + '/' + this.detailItem.slug : process.env.frontendURL + '/' + this.slugMode + '/' + this.detailItem.slug;
      }
    },
    getDetailMode(model) {
      switch (model) {
      case 'stories':
        return 'story'
      case 'publications':
        return 'publication'
      case 'publication':
        return 'publication'
      case 'threads':
        return 'community/thread'
      case 'thread':
        return 'community/thread'
      case 'authors':
        return 'author'
      case 'author':
        return 'author'
      case 'genres':
        return 'genre'
      case 'genre':
        return 'genre'
      case 'markets':
        return 'tauschboerse'
      case 'market':
        return 'tauschboerse'
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
        return 'story'
      case 'publications':
        return 'publication'
      case 'publication':
        return 'publication'
      case 'authors':
        return 'author'
      case 'author':
        return 'author'
      case 'threads':
        return ''
      case 'genres':
        return 'genre'
      case 'genre':
        return 'genre'
      case 'markets':
        return 'tauschboerse'
      case 'market':
        return 'tauschboerse'
      case 'users':
        return 'community'
      case 'results':
        return this.slugMode
      default:
        return this.slugMode
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
    getImageHeightClass() {
      return (this.showFilters === true) ? 'h-96 md:h-paginatedListItem' : 'h-96 md:h-paginatedListItem';
    },
  }
}
</script>
