<template>
  <div class="border border-gray rounded">
    <button
      type="button"
      class="py-1 w-full flex items-center px-2"
      :class="{ 'text-primary': expanded === true, 'text-dark': expanded === false }"
      @click="handleAccordionClick()"
    >
      {{ $t('ITEM') }}
      <span
        class="ml-3"
        :class="{ 'text-danger': item.hidden === true, 'text-success': item.hidden === false }"
      >
        <icon
          class="transform transition duration-300"
          :icon-string="'IconEye'"
          :class="'w-4 h-5 flex-none'"
        />
      </span>
      <div
        class="ml-auto"
      >
        <icon
          class="transform transition duration-300"
          :icon-string="'IconCaretDown'"
          :class="'w-4 h-5 flex-none'"
        /> 
      </div>
    </button>
    <vue-collapsible :is-open="expanded === true">
      <div class="pt-1 pb-5 px-2 border-t border-gray">
        <p
          class="mb-3 pl-2 text-xs mt-5"
        >
          {{ $t('HIDDEN') }}
        </p>

        <field
          :edit-allowed="editAllowed"
          :field-string="'Checkbox'"
          :base-id="baseId"
          :item-value="item.hiden"
          :field-config="{}"
          :field-placeholder="'HIDDEN'"
          @updateValue="setContent($event, 'hidden')"
        />

        <item-select
          :edit-allowed="true"
          :field-value="item.itemData"
          :field-config="{disabled: false}"
          @updateValue="setContent($event, 'itemData')"
        />

        <p
          class="mb-3 pl-2 text-xs mt-5"
        >
          {{ $t('DETAIL_LINK') }}
        </p>

        <field
          :edit-allowed="editAllowed"
          :field-string="'LinkField'"
          :base-id="baseId"
          :item-value="item.detailLink"
          :field-config="{fromDomain: true}"
          :field-placeholder="'DETAIL_LINK'"
          @updateValue="setContent($event, 'detailLink')"
        />
        <div class="flex justify-end items-center mt-8 mb-4 py-2 pr-3">
          <div
            class="text-center cursor-pointer text-white rounded-lg bg-danger py-2 px-3"
            @click="deleteItem()"
          >
            {{ $t('DELETE') }}
          </div>
        </div>
      </div>
    </vue-collapsible>
  </div>
</template>
  
<script>
import { defineAsyncComponent } from "vue";

import VueCollapsible from 'vue-height-collapsible/vue3';
import ApiService from '../../../../../../../../../AdministrationBundle/assets/js/services/api/api.service';
const ItemSelect = defineAsyncComponent(() =>
  import("../../../../../../../../../FrontendBundle/assets/js/components/administration/common/Tabs/fields/ItemSelect.vue" /* webpackChunkName: "content-item-select" */)
);
const Field = defineAsyncComponent(() =>
  import("../../../../../../../../../AdministrationBundle/assets/js/components/common/Tabs/fields/Field.vue" /* webpackChunkName: "field" */)
);
const Icon = defineAsyncComponent(() =>
  import("../../../../../../../../../FrontendBundle/assets/js/icons/Icon.vue" /* webpackChunkName: "icon" */)
);
  
export default {
  name: "ListItems",
  components: {
    Field,
    Icon,
    ItemSelect,
    VueCollapsible
  },
  props: {
    editAllowed:{
      type: Boolean,
      required: true
    },
    baseId:{
      type: String,
      required: true
    },
    item:{
      type: Object,
      required: true
    }
  },
  data() {
    return {
      expanded: false,
      listItem: []
    }
  },
  mounted() {
    this.listItem = this.item;
  },
  methods: {
    handleAccordionClick() {
      this.expanded = !this.expanded;
    },
    setContent(content, key) {
      this.listItem[key] = content;
    },
    deleteItem() {
      ApiService.put('/be/api/smug/search/listItem/delete', this.listItem).then(result => {
        this.$emit('deleted', true);
      });
    }
  }
}
</script>