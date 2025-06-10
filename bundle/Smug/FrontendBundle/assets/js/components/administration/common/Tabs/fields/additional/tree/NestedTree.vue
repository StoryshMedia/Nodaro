<template>
  <div>
    <draggable
      class="dragArea font-semibold group list-group"
      tag="ul"
      :list="sites"
      :group="{ name: 'g1' }"
      v-bind="dragOptions"
      @start="isDragging = true"
      @end="isDragging = false; saveOrder()"
    >
      <TransitionGroup
        type="transition"
        name="flip-list"
      >
        <li
          v-for="el in sites"
          :key="el.title"
          class="pt-3"
        >
          <button
            type="button"
            class="flex flex-wrap"
          >
            <icon
              v-if="el.children && el.children.length > 0"
              class="transform transition duration-300 mr-3 text-primary w-4 h-5 flex-none"
              :class="{ '-rotate-90': !expanded }"
              :icon-string="'IconCaretDown'"
              @click="handleAccordionClick()"
            /> 
            <icon
              v-if="el.children && el.children.length > 0"
              class="text-primary"
              :icon-string="'IconFolder'"
            /> 
            <icon
              v-if="el.children && el.children.length === 0"
              class="text-primary"
              :icon-string="'IconPaper'"
            /> 
            <icon
              class="ml-1"
              :class="getVisibilityClass(el)"
              :icon-string="'IconEye'"
              :title="getVisibilityString(el)"
            />
            <span
              class="ml-3 tree--site-title flex flex-wrap"
              @click="handleEditClick(el)"
            >{{ el.title }} <icon
              class="transform transition duration-300 text-primary"
              :icon-string="'IconPencil'"
            /></span>
          </button>
          <vue-collapsible
            class="pt-3"
            :is-open="expanded === true && el.children.length > 0"
          >
            <NestedTree
              :sites="el.children"
              :field-config="fieldConfig"
              class="bg-gray-100 pl-5"
              @orderChanged="saveOrder()"
            />
          </vue-collapsible>
        </li>
      </TransitionGroup>
    </draggable>
    <site-editor
      v-if="showEditData === true"
      :item-id="editSite.id"
      :config="config"
      :headline="'EDIT_SITE'"
      @editReaction="handleEditClick($event)"
    />
  </div>
</template>
<script>
import { defineAsyncComponent } from "vue";
import { TransitionGroup } from "vue";
import { VueDraggableNext } from 'vue-draggable-next';
import VueCollapsible from 'vue-height-collapsible/vue3';
const Icon = defineAsyncComponent(() =>
  import("@core/js/icons/Icon.vue" /* webpackChunkName: "icon" */)
);
const SiteEditor = defineAsyncComponent(() =>
  import("./SiteEditor.vue" /* webpackChunkName: "administration-site-editor-modal" */)
);

export default {
  name: 'NestedTree',
  components: {
    VueCollapsible,
    TransitionGroup,
    Icon,
    SiteEditor,
    draggable: VueDraggableNext,
  },
  props: {
    sites: {
      required: true,
      type: Array,
    },
    baseId:{
      type: String,
      required: false,
      default: null
    },
    fieldConfig:{
      type: Object,
      required: false,
      default: () => ({})
    },
  },
  data() {
    return {
      config: {},
      editSite: {},
      expanded: false,
      showEditData: false
    };
  },
  computed: {
    dragOptions() {
      return {
        animation: 1,
        group: 'description',
        disabled: false,
        ghostClass: 'ghost',
      }
    },
  },
  mounted() {
    this.config = this.fieldConfig;
    this.config.siteGetCall = '/be/api/smug/frontend/site/';
    this.config['modules'] = {
      getCall: '/be/api/smug/frontend/module'
    };
    this.config['items'] = {
      addItemCall: '/be/api/smug/frontend/contentItem/add',
      deleteItemCall: '/be/api/smug/frontend/contentItem/delete',
      saveModuleItemCall: '/be/api/smug/frontend/contentItemModuleField/save',
      saveItemCall: '/be/api/smug/frontend/contentItem/save',
      tabAddCall: '/be/api/custom/module/tab',
      pluginConfigRefreshCall: '/be/api/custom/module/plugin/config/refresh',
      renderTemplateCall: '/be/api/custom/module/plugin/template',
      refreshCall: '/be/api/custom/module/rerender'
    };
    this.config['uploadCall'] = '/be/api/media/image/upload';
    this.config['assignAlbum'] = 'frontend';
  },
  methods: {
    handleEditClick(event) {
      if (event === false) {
        this.editSite = {};
        this.showEditData = !this.showEditData;
      } else {
        this.editSite = event;
        this.showEditData = true;
      }
    },
    getVisibilityClass(element) {
      if (element.hidden === true) {
        return 'text-danger';
      }

      if (element.hiddenInMenu === true) {
        return 'text-warning';
      }

      return 'text-dark';
    },
    getVisibilityString(element) {
      if (element.hidden === true) {
        return 'HIDDEN';
      }

      if (element.hiddenInMenu === true) {
        return 'HIDDEN_IN_MENU';
      }

      return 'VISIBLE';
    },
    handleAccordionClick() {
      this.expanded = !this.expanded;
    },
    saveOrder() {
      this.$emit('orderChanged', true);
    }
  }
}
</script>

<style scoped>
.button {
  margin-top: 35px;
}
.flip-list-move {
  transition: transform 0.5s;
}
.no-move {
  transition: transform 0s;
}
.ghost {
  opacity: 0.5;
  background: #c8ebfb;
}
.list-group {
  min-height: 20px;
}
.list-group-item {
  cursor: move;
}
.list-group-item i {
  cursor: pointer;
}

.btn {
  @apply font-bold py-2 px-4 rounded;
}
.btn-blue {
  @apply bg-blue-500 text-white;
}
.btn-blue:hover {
  @apply bg-blue-700;
}
</style>