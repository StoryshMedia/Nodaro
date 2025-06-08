<template>
  <section class="min-h-80vh">
    <div
      v-if="isLoading === true"
      class="h-full min-h-80vh flex"
    >
      <loading /> 
    </div>
    <div
      v-if="isLoading === false && notAllowed === false"
    >
      <div class="flex items-end mb-8">
        <button
          v-if="config.url && config.url.administration.back"
          type="button"
          class="btn btn-dark mr-3"
          @click="back()"
        >
          {{ $t('BACK') }}
        </button>
        <button
          v-if="editAllowed === true && config.url && config.url.api.save"
          type="button"
          class="btn btn-success mr-3"
          @click="save()"
        >
          {{ $t('SAVE') }}
        </button>
        <button
          v-if="isEdit === true && editAllowed === true && config.url && config.url.api.delete" 
          type="button"
          class="btn btn-danger"
          @click="deleteButtonClicked()"
        >
          {{ $t('DELETE') }}
        </button>
      </div>

      <TabGroup
        as="div"
        class="mb-5 flex flex-col sm:flex-row"
      >
        <div class="mr-10 ml-5 mb-5 sm:mb-0">
          <TabList class="w-48 m-auto text-center font-semibold">
            <Tab
              v-for="(tab, tabindex) in tabs"
              :key="tabindex"
              v-slot="{ selected }"
              as="template"
            >
              <a
                href="javascript:;"
                class="p-3.5 py-4 flex -mb-1 block border-r border-white-light relative before:transition-all before:duration-700 hover:text-primary hover:border-primary before:absolute before:w-1 before:bottom-0 before:top-0 before:-right-1 before:m-auto before:h-0 before:bg-secondary hover:before:h-[80%] !outline-none transition duration-300"
                :class="{ 'text-primary border-primary before:!h-[80%]': selected }"
              >
                <icon
                  :icon-string="(tab.icon) ? (tab.icon) : 'IconHome'"
                  :class="'w-5 h-5 flex-none mr-2'"
                /> {{ $t(tab.headline) }}
              </a>
            </Tab>
          </TabList>
        </div>
        <TabPanels class="flex-1 text-sm">
          <TabPanel
            v-for="(tabConfig, tabindex) in tabs"
            :key="tabindex"
          >
            <tab-content
              v-if="detailData"
              :tab-config="tabConfig"
              :item="detailData"
              :edit-allowed="editAllowed"
              :disallowed-fields="disallowedFields"
              :hidden-fields="hiddenFields"
              @update-value="setValueChange($event)"
            />
          </TabPanel>
        </TabPanels>
      </TabGroup>
      <confirm
        v-if="showConfirmation === true"
        :text="'DELETE_CONFIRMATION_TEXT'"
        :confirm-button-text="'YES'"
        :discard-button-text="'NO'"
        :modal-headline="'DELETE_CONFIRMATION_HEADLINE'"
        @confirm-reaction="handleConfirmValue($event)"
      />
    </div>
    <div v-if="isLoading === false && notAllowed === true">
      <not-allowed />
    </div>
  </section>
</template>

<script>
import { defineAsyncComponent } from "vue";
import ApiService from '@SmugAdministrationServices/api/api.service';
import { TabGroup, TabList, Tab, TabPanels, TabPanel } from '@headlessui/vue';
const Icon = defineAsyncComponent(() =>
  import("@core/js/icons/Icon.vue" /* webpackChunkName: "icon" */)
);
const TabContent = defineAsyncComponent(() =>
  import("./TabContent.vue" /* webpackChunkName: "administration-tab-content" */)
);
const Confirm = defineAsyncComponent(() =>
  import("../Modal/Confirm.vue" /* webpackChunkName: "administration-modal-confirm" */)
);
const NotAllowed = defineAsyncComponent(() =>
  import("../Main/NotAllowed.vue" /* webpackChunkName: "not-allowed" */)
);
const Loading = defineAsyncComponent(() =>
  import("../Main/Loading.vue" /* webpackChunkName: "loading" */)
);

export default {
  name: "Tabs",
  components: {
    NotAllowed,    
    Loading,    
    Icon,    
    Confirm,    
    TabList,    
    Tab,    
    TabPanels,    
    TabPanel,    
    TabContent,    
    TabGroup
  },
  inject: ['dataset'],
  data() {
    return{
      tabs: [],
      isEdit: false,
      showConfirmation: false,
      notAllowed: false,
      editAllowed: false,
      isLoading: false,
      disallowedFields: [],
      hiddenFields: [],
      detailData: {},
      security: {},
      config: {}
    }
  },
  async created() {
    await this.setProps();
    await this.getData();
  },
  methods: {
    setProps() {
      (this.dataset.tabs) ? this.tabs = JSON.parse(this.dataset.tabs) : null;
      (this.dataset.config) ? this.config = JSON.parse(this.dataset.config) : null;
      (this.dataset.security) ? this.security = JSON.parse(this.dataset.security) : null;
      if (this.dataset.edit) {
        this.isEdit = (this.dataset.edit == 1);
      }
    },
    setValueChange(data) {
      this.detailData = data;
    },
    back() {
      window.location.replace(this.config.url.administration.back)
    },
    deleteButtonClicked() {
      this.showConfirmation = true;
    },
    handleConfirmValue(value) {
      if (value === true) {
        this.delete();
      } else {
        this.showConfirmation = false;
      }
    },
    delete() {
      if (this.editAllowed === true) {
        this.isLoading = true;
        
        ApiService.put(this.config.url.api.delete, this.detailData)
          .then(result =>  {
            this.showConfirmation = false;
            this.back();
          })
          .catch(error => {
            this.isLoading = false;
          })
          .then(function () {
          });
      }
    },
    save() {
      if (this.editAllowed === true) {
        this.isLoading = true;
        
        if (this.isEdit === true) {
          ApiService.put(this.config.url.api.save, this.detailData)
            .then(response =>  {
              window.location.reload();
            })
            .catch(error => {
              this.isLoading = false;
            })
            .then(function () {
            });
        } else {
          ApiService.post(this.config.url.api.save, this.detailData)
            .then(result =>  {
              if (result.id) {
                window.location.replace(this.config.url.administration.detail + result.data.id);
              }
              this.isLoading = false;
            })
            .catch(error => {
              this.isLoading = false;
            })
            .then(function () {
            });
        }
      }
    },
    getData() {
      this.isLoading = true;
      
      ApiService.post(
        '/be/api/allowed',
        this.config
      )
        .then(result =>  {
          if (result.read === false) {
            this.isLoading = false;
            this.notAllowed = true;
          } else {
            this.editAllowed = result.write;
            this.disallowedFields = result.disallowedFields;
            this.hiddenFields = result.hiddenFields;

            if (this.config.url.api.get) {
              let url = this.config.url.api.get;

              if (this.config.id) {
                url += this.config.id;
              }

              ApiService.get(url)
                .then(res =>  {
                  this.detailData = res;
                  this.isLoading = false;
                })
                .catch(error => {
                  this.isLoading = false;
                })
                .then(function () {
                });
            } else {
              this.isLoading = false;
            }
          }
        })
        .catch(error => {
          this.isLoading = false;
        })
        .then(function () {
        });
    }
  }
}
</script>