<template>
  <section
    class="relative overflow-hidden grid"
    :class="getGridClass()"
  >
    <div
      v-for="(column, columnIndex) of element.children"
      :key="columnIndex"
      class="relative min-h-24"
    >
      <div
        v-for="(childElement, childElementIndex, count) of column"
        :key="childElementIndex"
        class="relative min-h-24"
      >
        <div
          v-if="count === 0"
          class="text-center cursor-pointer text-primary rounded-lg py-2 my-2"
          :title="$t('ADD_CONTENT_ITEM_TOOLTIP')"
          @click="showItemSelectionModal(columnIndex)"
        >
          <icon
            :icon-string="'IconPlus'"
            :class="'w-4 h-4 flex-none mx-auto border border-primary rounded-full'"
          />
        </div>
        <div
          class="absolute top-2 left-2 z-40 cursor-pointer rounded-md"
          @click="handleItemConfig(childElement)"
        >
          <div 
            :title="$t('ITEM_SETTINGS')"
            class="hidden group-hover:block px-4 py-2 bg-primary text-white text-center rounded-md"
          >
            <icon
              :icon-string="'IconSettings'"
            />
          </div>
        </div>
        <content-item
          v-if="loadingItemId !== childElement.id"
          :key="childElement.id"
          :field-value="childElement"
          :base-id="baseId"
          :config="fieldConfig"
          @updateValue="onUpdateItemChange($event, childElement)"
          @selectItemValue="handleEditValue($event, childElement)"
        />
        <loading v-else />
        <div
          class="text-center cursor-pointer text-primary rounded-lg py-2 my-2"
          :title="$t('ADD_CONTENT_ITEM_TOOLTIP')"
          @click="showItemSelectionModalFromElement(columnIndex, childElement)"
        >
          <icon
            :icon-string="'IconPlus'"
            :class="'w-4 h-4 flex-none mx-auto border border-primary rounded-full'"
          />
        </div>
      </div>
      <div
        v-if="column.length === 0"
        class="overflow-hidden h-auto"
      >
        <div
          class="text-center cursor-pointer text-primary rounded-lg py-2 my-2"
          :title="$t('ADD_CONTENT_ITEM_TOOLTIP')"
          @click="showItemSelectionModal(columnIndex)"
        >
          <icon
            :icon-string="'IconPlus'"
            :class="'w-4 h-4 flex-none mx-auto border border-primary rounded-full'"
          />
        </div>
      </div>
    </div>
  </section>
</template>
<script>
import { defineComponent } from 'vue';
import { defineAsyncComponent } from "vue";
import ItemService from 'SmugAdministration/js/services/item/item.service';
const ContentItem = defineAsyncComponent(() =>
  import("./ContentItem.vue" /* webpackChunkName: "administration-content-editor-item" */)
);
const Icon = defineAsyncComponent(() =>
  import("@core/js/icons/Icon.vue" /* webpackChunkName: "icon" */)
);
const Loading = defineAsyncComponent(() =>
  import("../../../../../Main/Loading.vue" /* webpackChunkName: "loading" */)
);
  
export default defineComponent({
  name: 'MultiContentItem',
  components: {
    ContentItem,
    Loading,
    Icon
  },
  props: {
    baseId:{
      type: String,
      required: false,
      default: null
    },
    loadingItemId:{
      type: String,
      required: false,
      default: ''
    },
    fieldConfig:{
      type: Object,
      required: false,
      default: () => ({})
    },
    element:{
      type: Object,
      required: false,
      default: () => ({})
    }
  },
  methods: {
    showItemSelectionModal(columnIndex) {
      this.$emit('insertChildItem', {
        parent: this.element,
        column: columnIndex,
        index: 0
      });
    },
    showItemSelectionModalFromElement(columnIndex, element) {
      ItemService.getItemPositionOfItem(this.element.children[columnIndex], element).then(position => {
        const realPosition = position++;
        this.$emit('insertChildItem', {
          parent: this.element,
          column: columnIndex,
          index: position
        });
      });
    },
    onUpdateItemChange(event, element) {
      this.$emit('updateMultiValue', {
        event: event,
        element: element
      });
    },
    handleItemConfig(element) {
      this.$emit('handleMultiItemConfig', element);
    },
    handleEditValue(event, element) {
      this.$emit('selectMultiItemValue', {
        event: event,
        element: element
      });
    },
    getGridClass() {
      if (!this.element.children || this.element.children.lenght === 0) {
        return 'grid-cols-12';
      }

      return 'grid-cols-' + this.element.children.length + ' gap-3 p-3';
    }
  }
})
</script>