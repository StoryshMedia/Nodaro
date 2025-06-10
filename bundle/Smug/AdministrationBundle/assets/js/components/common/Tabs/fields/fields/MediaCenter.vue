<template>
  <div>
    <TransitionGroup
      type="transition"
      name="flip-list"
    >
      <ul class="list-none">
        <li
          v-for="(el, folderIndex) in folders"
          :key="el.title"
          class="pt-3"
        >
          <button
            type="button"
            class="flex flex-wrap"
          >
            <icon
              v-if="showArrow(el)"
              class="transform transition duration-300 mr-3 text-primary w-4 h-5 flex-none"
              :class="{ '-rotate-90': expanded !== el.title }"
              :icon-string="'IconCaretDown'"
              @click="handleAccordionClick(el, folderIndex)"
            />
            <span @click="setDetailFolder(el)">
              {{ el.title }}
            </span>
          </button>
          <vue-collapsible
            v-if="el.children && el.children.length > 0"
            class="pt-3"
            :is-open="expanded === el.title"
          >
            <MediaCenter
              :given-folders="el.children"
              :is-selection="getIsSelection()"
              class="bg-gray-100 pl-5"
              @selectFile="selectFile($event)"
            />
          </vue-collapsible>
        </li>  
      </ul>
    </TransitionGroup>
    <detail-folder
      v-if="detailFolderPath !== ''"
      :is-selection="getIsSelection()"
      :detail-folder-title="detailFolderTitle"
      :detail-folder="detailFolderPath"
      @reaction="removeDetailFolder()"
      @selectFile="selectFile($event)"
    />
  </div>
</template>

<script>
import ApiService from '@SmugAdministration/js/services/api/api.service';
import { TransitionGroup } from "vue";
import VueCollapsible from 'vue-height-collapsible/vue3';
import { defineAsyncComponent } from "vue";
const Icon = defineAsyncComponent(() =>
  import("@core/js/icons/Icon.vue" /* webpackChunkName: "icon" */)
);
const DetailFolder = defineAsyncComponent(() =>
  import("./additional/mediaCenter/DetailFolder.vue" /* webpackChunkName: "administration-media-center-detail-folder" */)
);

export default {
  name: "MediaCenter",
  components: {
    Icon,
    DetailFolder,
    VueCollapsible,
    TransitionGroup
  },
  props: {
    editAllowed:{
      type: Boolean,
      required: true
    },
    isSelection: {
      type: Boolean,
      required: false,
      default: false
    },
    baseId:{
      type: String,
      required: false,
      default: null
    },
    fieldValue:{
      type: String,
      required: false,
      default: ''
    },
    fieldConfig:{
      type: Object,
      required: false,
      default: () => ({})
    },
    fieldPlaceholder:{
      type: String,
      required: false,
      default: 'PLEASE_CHOOSE'
    },
    givenFolders:{
      type: Object,
      required: false,
      default: () => ({})
    }
  },
  data() {
    return {
      folders: [],
      detailFolderPath: '',
      detailFolderTitle: '',
      expanded: ''
    };
  },
  mounted() {
    if (Object.keys(this.givenFolders).length > 0) {
      this.folders = this.givenFolders;
    } else {
      this.getData();
    }
  },
  methods: {
    showArrow(el) {
      if (typeof el.children === 'undefined') {
        return true;
      }

      return el.children.length > 0;
    },
    getIsSelection() {
      return this.isSelection;
    },
    selectFile(file) {
      this.$emit('selectFile', file);
    },
    setDetailFolder(el) {
      this.detailFolderPath = el.path;
      this.detailFolderTitle = el.title;
    },
    removeDetailFolder() {
      this.detailFolderPath = '';
      this.detailFolderTitle = '';
    },
    handleAccordionClick(el, folderIndex) {
      if (el.title === this.expanded) {
        this.expanded = '';
      } else {
        ApiService.post('/be/api/custom/media/folder', {folder: el.path})
          .then(result =>  {
            this.folders[folderIndex].children = result;
          })
          .catch(error => {
            this.isLoading = false;
          })
          .then(function () {
          });
        this.expanded = el.title ?? '';
      }
    },
    getData() {
      this.isLoading = true;
      
      ApiService.get('/be/api/custom/media/folder')
        .then(result =>  {
          this.folders = result;
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