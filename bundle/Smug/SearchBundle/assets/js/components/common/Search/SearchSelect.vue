<template>
  <section
    class="w-full"
  >
    <div class="flex flex-wrap">
      <div
        class="relative mb-3 flex-auto pr-1"
        :class="getWidth()"
      >
        <input
          ref="showSearchModalInput"
          v-model="searchTerm"
          class="w-full input rounded-3xl shadow-xl border-gray"
          type="search"
          name="search"
          autocomplete="off"
          :placeholder="$t('SEARCH')"
          style="width: 100%"
          @focus="handleSelectFocus()"
        >
      </div>
    </div>
    <div
      id="search-select-modal"
      ref="searchSelectModal"
      tabindex="-1"
      aria-hidden="true"
      class="hidden overflow-y-auto bg-overlay-transparent overflow-x-hidden fixed top-0 right-0 left-0 z-50 w-full md:inset-0"
      :class="(inModal === false) ? 'h-modal md:h-full' : ''"
    >
      <div
        class="fixed z-10 inset-0 overflow-y-auto"
        aria-labelledby="modal-title"
        role="dialog"
        aria-modal="true"
      >
        <div
          class="flex items-center lg:items-end justify-center pt-4 px-4 pb-20 text-center sm:block sm:p-0"
          :class="(inModal === false) ? 'min-h-screen' : ''"
        >
          <div
            class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
            aria-hidden="true"
          />

          <!-- This element is to trick the browser into centering the modal contents. -->
          <span
            class="hidden sm:inline-block sm:align-middle"
            :class="(inModal === false) ? 'h-screen' : 'h-50vh'"
            aria-hidden="true"
          >&#8203;</span>

          <div
            class="inline-block align-bottom rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle md:w-8/12 sm:w-full"
          >
            <div class="bg-white h-75vh overflow-y-auto   pt-5 px-5">
              <div
                class="text-right"
              >
                <button
                  type="button"
                  class="outline-link outline-link-dark loading-spinner-outline-link mt-5 mb-3"
                  @click="hideModalView()"
                >
                  {{ $t('CLOSE') }}
                </button>
              </div>
              <div
                class="mb-3 flex-auto pr-1"
              >
                <input
                  :ref="'searchInput'"
                  v-model="searchTerm"
                  class="w-full input rounded-3xl shadow-xl border-gray"
                  type="search"
                  name="search"
                  :placeholder="$t('SEARCH')"
                  style="width: 100%"
                  @input="searchit"
                  @focus="handleFocus"
                  @focusout="handleFocusOut($event)"
                >
              </div>
              <div
                v-if="isFocused === true && results.length > 0"
                class="dropdown-menu mt-0 w-full overflow-y-auto overflow-x-hidden"
                aria-labelledby="dropdownMenuButtonX"
              >
                <div
                  v-for="(res, index) in results"
                  :key="index"
                  class="relative mx-3"
                >
                  <div
                    class="search-result block text-sm text-dark cursor-pointer"
                    role="menuitem"
                    @click="setSearchResult(res)"
                  >
                    <div class="p-2 flex items-center">
                      <div class="w-3/12 lg:w-1/12">
                        <search-image
                          :image="getImageSrc(res)"
                          :headline="getLabel(res)"
                        />
                      </div>
                      <div class="py-2 pl-4 pr-2 w-9/12 lg:w-11/12">
                        <div class="mb-2">
                          <h6 class="search-result--label text-black line-clamp-1 font-sans transition-colors">
                            {{ getLabel(res) }}
                          </h6>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div
                v-if="loading === true"
                class="pl-5"
              >
                <loading />
              </div>      
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<script>
import { defineAsyncComponent } from "vue";
import ApiService from '../../../../../../AdministrationBundle/assets/js/services/api/api.service';
import ImageService from '../../../../../../AdministrationBundle/assets/js/services/image/image.service';
const Loading = defineAsyncComponent(() =>
  import("../../../../../../FrontendBundle/assets/js/components/common/Content/Loading" /* webpackChunkName: "loading" */)
);
const SearchImage = defineAsyncComponent(() =>
  import("../../../../../../FrontendBundle/assets/js/components/common/Image/SearchImage" /* webpackChunkName: "search-image" */)
);

export default {
  name: "SearchSelect",
  components: {
    Loading,
    SearchImage
  },
  model: {
    isFocused: false,
    showSearch: false,
    loading: false,
    searchTerm: '',
    results: [],
    news: []
  },
  props: {
    url: {
      type: String,
      required: false,
      default: '/fe/api/search'
    },
    searchFields: {
      type: Object,
      required: false,
      default: ['title']
    },
    inModal: {
      type: Boolean,
      required: false,
      default: false
    },
    width: {
      type: String,
      required: false,
      default: 'w-1/2'
    }
  },
  data() {
    return {
      timeout: null,
      isFocused: false,
      loading: false,
      showSearch: false,
      searchTerm: '',
      results: []
    };
  },
  computed: {
    input: {
      get() {
        return this.searchTerm
      },
      set(val) {
        if (this.timeout) clearTimeout(this.timeout)
        this.timeout = setTimeout(() => {
          this.debouncedInput = val
        }, 300)
      }
    }
  },
  mounted() {
    window.addEventListener('keydown', (e) => {
      if (e.key == 'Escape') {
        this.hideModalView();
      }
    });
  },
  methods: {
    handleSelectFocus() {
      this.$emit('searchModal', true);
      this.$refs.searchSelectModal.classList.remove('hidden');
      this.$refs.searchInput.focus();
    },
    getLabel(item) {
      let label = '';

      if (this.searchFields.length === 0) {
        return item.title ?? '';
      }

      for (let i = 0; i <= this.searchFields.length - 1; i++) {
        label = (label === '') ? item[this.searchFields[i]] : label + ' ' + item[this.searchFields[i]];

        if (i === this.searchFields.length - 1) {
          return label;
        }
      }
    },
    handleFocus() {
      this.isFocused = true;
    },
    getImageSrc(item) {
      return ImageService.getImageFromItem(item);
    },
    hideModalView() {
      if (this.$refs.searchSelectModal !== null) {
        this.$refs.searchSelectModal.classList.add('hidden');
        this.$refs.showSearchModalInput.blur();
      }
  
      this.$emit('searchModal', false);
    },
    getWidth() {
      return this.width;
    },
    handleFocusOut(event) {
      if (event.relatedTarget === null || event.relatedTarget.tagName !== 'A') {
        this.searchTerm = '';
      }
    },
    setSearchResult(item) {
      this.$emit('searchItem', item);
      this.searchTerm = '';
      this.results = [];
      if (this.$refs.searchSelectModal !== null) {
        this.$refs.searchSelectModal.classList.add('hidden');
        this.$refs.showSearchModalInput.blur();
      }
    
      this.$emit('searchModal', false);
    },
    searchit(val, event) {
      if (this.searchTerm.length > 2) {
        this.loading = true;
        if (this.timeout) clearTimeout(this.timeout)
        this.timeout = setTimeout(() => {
          ApiService.post(this.url, {searchTerm: this.searchTerm, searchFields: this.searchFields})
            .then(result => {
              this.results = result;
              this.loading = false;
            })
            .catch(error => {
              this.loading = false;
            })
            .then(final => {
              this.loading = false;
            });
        }, 400);
      }
    }
  }
}
</script>