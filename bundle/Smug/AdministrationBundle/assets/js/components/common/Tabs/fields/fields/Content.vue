<template>
  <section 
    :style="getContentHeight()"
    class="bg-background"
  >
    <div class="site--content-editor h-full">
      <div 
        v-if="showSuccess === true"
        class="fixed top-2 right-2 z-50"
      >
        <alert
          v-if="showSuccess === true"
          :type="'green'"
          :header="'SUCCESS'"
          :label="'SUCCESS_DATA_SAVED'"
          :edit-allowed="true"
          @remove="removeSuccess()"
        />
      </div>
      <div
        v-if="showSettingsMenu === false && disableSettings === false"
        class="absolute top-16 left-2 bg-primary text-white text-center px-4 py-2 z-90 cursor-pointer rounded-md"
        @click="handleShowSettingsMenu()"
      >
        {{ $t('SITE_SETTINGS') }}
      </div>
      <aside
        class="element--menu"
        :style="getSettingsStyles()"
      >
        <div class="px-3">
          <div
            class="text-right pr-2 pt-2"
          >
            <button
              class="text-dark hover:text-primary hover:border-b-2 hover:border-primary mx-auto"
              style="transition: all 0.15s ease 0s;"
              @click="handleShowSettingsMenu()"
            >
              {{ $t('CLOSE') }}
            </button>
          </div>
          <h2 class="flex flex-row flex-nowrap items-center mb-5 mt-8">
            <span class="flex-grow block border-t border-dark" />
            <span class="flex-none block mx-2 px-4 py-2.5 rounded leading-none bg-dark text-white">
              {{ $t('SITE_SETTINGS') }}
            </span>
            <span class="flex-grow block border-t border-dark" />
          </h2>
          <ul
            class="py-1 pl-3 w-full m-0"
          >
            <li
              class="w-full rounded hover:bg-background text-sm pl-3"
            >
              <button
                type="button"
                class="px-1.5 py-2 w-full text-left font-medium cursor pointer"
                @click="setBaseSettings()"
              >
                {{ $t('BASE_SETTINGS') }}
              </button>
            </li>
            <li
              class="w-full rounded hover:bg-background text-sm pl-3"
            >
              <button
                type="button"
                class="px-1.5 py-2 w-full text-left font-medium cursor pointer"
                @click="setSeoSettings()"
              >
                {{ $t('SEO_SETTINGS') }}
              </button>
            </li>
          </ul>
        </div>
      </aside>
      <div
        id="site--content-editor"
        class="editor--content h-full"
      >
        <div
          v-if="showSeoSettings === true"
          class="absolute bg-white z-90 w-site--content-editor h-site--content-editor overflow-hidden transition-all duration-300"
        >
          <perfect-scrollbar
            :options="{
              swipeEasing: true,
              wheelPropagation: false,
            }"
            class="h-full w-full"
          >
            <div class="bg-white shadow-inner border-l-2 border-gray h-full w-full p-5">
              <h2 class="flex flex-row flex-nowrap items-center mb-5 mt-8">
                <span class="flex-grow block border-t border-dark" />
                <span class="flex-none block mx-2 px-4 py-2.5 rounded leading-none bg-dark text-white">
                  {{ $t('SEO_SETTINGS') }}
                </span>
                <span class="flex-grow block border-t border-dark" />
              </h2>
              
              <div class="grid grid-cols-1 md:grid-cols-3 gap-5 my-5">
                <div>
                  <h5
                    class="font-semibold text-lg pb-2"
                  >
                    {{ $t('SEO_TITLE') }}
                  </h5>
                  <field
                    :field-string="'Text'"
                    :item-value="parentData.seoTitle"
                    :field-placeholder="'SEO_TITLE'"
                    :edit-allowed="editAllowed"
                    :base-id="baseId"
                    @update-value="setBaseSettingValue($event, 'seoTitle')"
                  />
                </div>
                <div>
                  <h5
                    class="font-semibold text-lg pb-2"
                  >
                    {{ $t('SEO_KEYWORDS') }}
                  </h5>
                  <field
                    :field-string="'Text'"
                    :item-value="parentData.seoKeywords"
                    :field-placeholder="'SEO_KEYWORDS'"
                    :edit-allowed="editAllowed"
                    :base-id="baseId"
                    @update-value="setBaseSettingValue($event, 'seoKeywords')"
                  />
                </div>
                <div>
                  <h5
                    class="font-semibold text-lg pb-2"
                  >
                    {{ $t('SEO_DESCRIPTION') }}
                  </h5>
                  <field
                    :field-string="'Textarea'"
                    :item-value="parentData.seoDescription"
                    :field-placeholder="'SEO_DESCRIPTION'"
                    :edit-allowed="editAllowed"
                    :base-id="baseId"
                    @update-value="setBaseSettingValue($event, 'seoDescription')"
                  />
                </div>
              </div>

              <div class="grid grid-cols-1 md:grid-cols-4 gap-5 my-5">
                <div>
                  <h5
                    class="font-semibold text-lg pb-2"
                  >
                    {{ $t('NO_INDEX') }}
                  </h5>
                  <field
                    :field-string="'Checkbox'"
                    :item-value="parentData.noIndex"
                    :field-placeholder="'NO_INDEX'"
                    :edit-allowed="editAllowed"
                    :base-id="baseId"
                    @update-value="setBaseSettingValue($event, 'noIndex')"
                  />
                </div>
                <div>
                  <h5
                    class="font-semibold text-lg pb-2"
                  >
                    {{ $t('NO_FOLLOW') }}
                  </h5>
                  <field
                    :field-string="'Checkbox'"
                    :item-value="parentData.noFollow"
                    :field-placeholder="'NO_FOLLOW'"
                    :edit-allowed="editAllowed"
                    :base-id="baseId"
                    @update-value="setBaseSettingValue($event, 'noFollow')"
                  />
                </div>
              </div>

              <div class="grid grid-cols-1 gap-5 my-5">
                <div>
                  <h5
                    class="font-semibold text-lg pb-2"
                  >
                    {{ $t('CANONICAL_LINK') }}
                  </h5>
                  <field
                    :field-string="'Text'"
                    :item-value="parentData.canonicalLink"
                    :field-placeholder="'CANONICAL_LINK'"
                    :edit-allowed="editAllowed"
                    :base-id="baseId"
                    @update-value="setBaseSettingValue($event, 'canonicalLink')"
                  />
                  <p
                    class="my-3 pl-2 text-xs"
                  >
                    {{ $t('CANONICAL_LINK_DESCRIPTION') }}
                  </p>
                </div>
              </div>

              <h5
                class="font-semibold text-lg pb-2"
              >
                {{ $t('STRUCTURED_DATA') }}
              </h5>

              <field
                :field-string="'Seo'"
                :item-value="parentData.seoData"
                :field-placeholder="''"
                :edit-allowed="editAllowed"
                :base-id="baseId"
                @update-value="setBaseSettingValue($event, 'seoData')"
              />
            </div>
          </perfect-scrollbar>
        </div>
        <div
          v-if="showBaseSettings === true"
          class="absolute z-90 w-site--content-editor h-site--content-editor overflow-hidden transition-all duration-300"
        >
          <perfect-scrollbar
            :options="{
              swipeEasing: true,
              wheelPropagation: false,
            }"
            class="h-full w-full"
          >
            <div class="bg-white shadow-inner border-l-2 border-gray h-full w-full p-5">
              <h2 class="flex flex-row flex-nowrap items-center mb-5 mt-8">
                <span class="flex-grow block border-t border-dark" />
                <span class="flex-none block mx-2 px-4 py-2.5 rounded leading-none bg-dark text-white">
                  {{ $t('BASE_SETTINGS') }}
                </span>
                <span class="flex-grow block border-t border-dark" />
              </h2>

              <div class="grid grid-cols-1 md:grid-cols-2 gap-5 my-5">
                <div>
                  <h5
                    class="font-semibold text-lg pb-2"
                  >
                    {{ $t('TITLE') }}
                  </h5>
                  <field
                    :field-string="'Text'"
                    :item-value="parentData.title"
                    :field-placeholder="'TITLE'"
                    :edit-allowed="editAllowed"
                    :base-id="baseId"
                    @update-value="setBaseSettingValue($event, 'title')"
                  />
                </div>
                <div>
                  <h5
                    class="font-semibold text-lg pb-2"
                  >
                    {{ $t('SLUG') }}
                  </h5>
                  <field
                    :field-string="'SlugField'"
                    :item-value="parentData.slug"
                    :field-placeholder="'SLUG'"
                    :edit-allowed="editAllowed"
                    :base-id="baseId"
                    @update-value="setBaseSettingValue($event, 'slug')"
                  />
                </div>
              </div>
              <div class="grid grid-cols-1 md:grid-cols-4 gap-5 my-5">
                <div>
                  <h5
                    class="font-semibold text-lg pb-2"
                  >
                    {{ $t('HIDE') }}
                  </h5>
                  <field
                    :field-string="'Checkbox'"
                    :item-value="parentData.hidden"
                    :field-placeholder="'HIDE'"
                    :edit-allowed="editAllowed"
                    :base-id="baseId"
                    @update-value="setBaseSettingValue($event, 'hidden')"
                  />
                </div>
                <div>
                  <h5
                    class="font-semibold text-lg pb-2"
                  >
                    {{ $t('HIDE_IN_MENU') }}
                  </h5>
                  <field
                    :field-string="'Checkbox'"
                    :item-value="parentData.hiddenInMenu"
                    :field-placeholder="'HIDE_IN_MENU'"
                    :edit-allowed="editAllowed"
                    :base-id="baseId"
                    @update-value="setBaseSettingValue($event, 'hiddenInMenu')"
                  />
                </div>
              </div>
              <script-select :base-id="parentData.id" />
              <style-select
                :base-id="parentData.id"
                @update-value="setBaseSettingValue($event, 'siteStyles')"
              />
            </div>
          </perfect-scrollbar>
        </div>
        <perfect-scrollbar
          :options="{
            swipeEasing: true,
            wheelPropagation: false,
          }"
          class="max-h-full py-3 min-h-12 mx-5"
        >
          <div
            v-if="list.length === 0"
          >
            <div
              :title="$t('ADD_CONTENT_ITEM_TOOLTIP')"
              class="text-center cursor-pointer text-primary rounded-lg py-2 my-2"
              @click="showItemSelectionModal(0)"
            >
              <icon
                :icon-string="'IconPlus'"
                :class="'w-4 h-4 flex-none mx-auto border border-primary rounded-full'"
              />
            </div>
          </div>
          <draggable
            v-model="list"
            class="list-group bg-white"
            ghost-class="ghost"
            :group="{ name: 'items', transitionMode: true, put: true }"
            :empty-insert-threshold="200"
            :transition-mode="true"
            @change="onOrderChanged()"
          >
            <div 
              v-for="(element, elementIndex) in list"
              :key="element.id"
              class="group"
            >
              <div
                class="relative"
                :class="getGridColumns()"
              >
                <div
                  :title="$t('ITEM_SETTINGS')"
                  class="absolute top-2 right-2 hidden group-hover:block bg-primary text-white text-center px-4 py-2 z-40 cursor-pointer rounded-md"
                  @click="handleItemConfig(element)"
                >
                  <icon
                    :icon-string="'IconSettings'"
                  />
                </div>
                <div
                  :title="$t('COPY_CONTENT_ELEMENT_HEADLINE')"
                  class="absolute top-2 right-16 hidden group-hover:block bg-dark text-white text-center px-4 py-2 z-40 cursor-pointer rounded-md"
                  @click="handleItemCopy(element)"
                >
                  <icon
                    :icon-string="'IconCopy'"
                  />
                </div>
                <div v-if="element.module.module.multi === false">
                  <content-item
                    v-if="!isLoading && loadingItemId !== element.id"
                    :key="element.id"
                    :field-value="element"
                    :base-id="baseId"
                    :config="fieldConfig"
                    @updateValue="onUpdateItemChange($event, element)"
                    @selectItemValue="handleEditValue($event, element)"
                  />
                </div>
                <div v-else>
                  <multi-content-item
                    v-if="!isLoading"
                    :element="element"
                    :base-id="baseId"
                    :config="fieldConfig"
                    :loading-item-id="loadingItemId"
                    @insertChildItem="showChildItemSelectionModal($event, elementIndex)"
                    @selectMultiItemValue="selectMultiItemValue($event)"
                    @handleMultiItemConfig="handleItemConfig($event, true, elementIndex)"
                    @updateMultiValue="updateMultiValue($event)"
                  />
                </div>
              </div>
              <div
                :title="$t('ADD_CONTENT_ITEM_TOOLTIP')"
                class="text-center cursor-pointer text-primary rounded-lg py-2 my-2"
                @click="showItemSelectionModal(elementIndex)"
              >
                <icon
                  :icon-string="'IconPlus'"
                  :class="'w-4 h-4 flex-none mx-auto border border-primary rounded-full'"
                />
              </div>
            </div>
          </draggable>
        </perfect-scrollbar>
      </div>
      <aside
        class="configuration--menu h-full"
        :style="getConfigurationStyles()"
      >
        <div
          class="pr-3"
        >
          <div v-if="showEditItemData === true">
            <element-configuration
              :key="editItemData.id"
              :base-id="baseId"
              :field-value="editItemData"
              :field-config="editItemDataConfig"
              :save-call="fieldConfig.items.saveItemCall"
              :tab-add-call="fieldConfig.items.tabAddCall"
              @close="closeItemConfiguration()"
              @delete="deleteItem()"
              @addTab="addTab($event)"
              @addColumn="addColumn($event)"
              @updateValue="onUpdateItemConfiguration($event)"
            />
          </div>
          <div v-if="showEditData === true">
            <item-configuration
              :key="editData.id"
              :base-id="baseId"
              :field-value="editData"
              :field-config="editDataConfig"
              :save-call="fieldConfig.items.saveModuleItemCall"
              @close="closeItemConfiguration()"
              @updateValue="onUpdateFromConfiguration($event)"
            />
          </div>
        </div>
      </aside>
    </div>
    <item-selection
      v-if="showItemSelection === true"
      :field-config="fieldConfig"
      :column="itemSelectionIndex"
      :position="itemSelectionIndex"
      :is-multi-item-selection="isMultiItemSelection"
      @selectItem="onAddedItem($event)"
    />
    <copy-content-element
      v-if="showCopyContentElement === true"
      :item="copyItemData"
      :parent-id="getParentId()"
      @copyItem="onElementCopied($event)"
    />
  </section>
</template>
<script>
import { defineAsyncComponent } from "vue";
import { defineComponent } from 'vue';
import { VueDraggableNext } from 'vue-draggable-next';
import ItemService from '@SmugAdministration/js/services/item/item.service';
import ApiService from '@SmugAdministration/js/services/api/api.service';
const ContentItem = defineAsyncComponent(() =>
  import("./additional/content/ContentItem.vue" /* webpackChunkName: "administration-content-editor-item" */)
);
const MultiContentItem = defineAsyncComponent(() =>
  import("./additional/content/MultiContentItem.vue" /* webpackChunkName: "administration-content-editor-multi-item" */)
);
const ItemSelection = defineAsyncComponent(() =>
  import("./additional/content/ItemSelection.vue" /* webpackChunkName: "administration-content-editor-item-selection" */)
);
const ItemConfiguration = defineAsyncComponent(() =>
  import("./additional/content/ItemConfiguration.vue" /* webpackChunkName: "administration-content-editor-item-configuration" */)
);
const CopyContentElement = defineAsyncComponent(() =>
  import("./additional/content/CopyContentElement.vue" /* webpackChunkName: "administration-content-editor-copy-content-element" */)
);
const ScriptSelect = defineAsyncComponent(() =>
  import("./additional/content/ScriptSelect.vue" /* webpackChunkName: "administration-content-editor-script-select" */)
);
const StyleSelect = defineAsyncComponent(() =>
  import("./additional/content/StyleSelect.vue" /* webpackChunkName: "administration-content-editor-script-select" */)
);
const ElementConfiguration = defineAsyncComponent(() =>
  import("./additional/content/ElementConfiguration.vue" /* webpackChunkName: "administration-content-editor-element-configuration" */)
);
const Icon = defineAsyncComponent(() =>
  import("@core/js/icons/Icon.vue" /* webpackChunkName: "icon" */)
);
const Field = defineAsyncComponent(() =>
  import("../Field.vue" /* webpackChunkName: "field" */)
);

export default defineComponent({
  name: 'Content',
  components: {
    draggable: VueDraggableNext,
    CopyContentElement,
    ContentItem,
    MultiContentItem,
    ItemSelection,
    ElementConfiguration,
    Icon,
    Field,
    StyleSelect,
    ScriptSelect,
    ItemConfiguration,
  },
  props: {
    editAllowed:{
      type: Boolean,
      required: true
    },
    fieldValue:{
      type: String,
      required: false,
      default: ''
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
    parentData:{
      type: Object,
      required: false,
      default: () => ({})
    },
    fieldPlaceholder:{
      type: String,
      required: false,
      default: 'CONTENT_EDITOR_PLACEHOLDER'
    }
  },
  data() {
    return {
      reload: 0,
      isMultiItemSelection: false,
      showItemSelection: false,
      showCopyContentElement: false,
      itemSelectionIndex: null,
      itemSelectionColumn: null,
      multiItemParent: null,
      multiItemParentIndex: null,
      configurationReload: 0,
      showSettingsMenu: false,
      disableSettings: false,
      isLoading: false,
      loadingItemId: '',
      showSuccess: false,
      showEditData: false,
      handleMultiItem: false,
      showEditItemData: false,
      showSeoSettings: false,
      showBaseSettings: false,
      showSelection: false,
      isGrabbing: false,
      enabled: true,
      list: [],
      selectedModuleCategory: null,
      elements: {
        contentElements: [],
        plugins: [],
        structureItems: [],
      },
      copyItemData: {},
      editData: {},
      editDataItem: {},
      editDataConfig: {},
      editItemData: {},
      editItemDataConfig: {},
      itemValues: []
    }
  },
  async created() {
    if (this.fieldConfig.disableSettings) {
      this.disableSettings = this.fieldConfig.disableSettings;
    }
    await this.getData();
  },
  methods: {
    getGridColumns() {
      return 'grid-cols-1';
    },
    getParentId() {
      return this.parentData.id;
    },
    removeSuccess() {
      this.showSuccess = false;
    },
    getConfigurationStyles() {
      if (this.showEditData === false && this.showEditItemData === false) {
        return 'width: 0px;';
      }

      return 'width: 260px;'
    },
    getSettingsStyles() {
      if (this.showSettingsMenu === false) {
        return 'width: 0px;';
      }

      return 'width: 260px;'
    },
    closeItemConfiguration() {
      this.showEditData = false;
      this.editDataItem = false;
      this.showEditItemData = false;
    },
    handleShowSettingsMenu() {
      this.showSettingsMenu = !this.showSettingsMenu;

      if (this.showSettingsMenu === false) {
        this.showBaseSettings = false;
        this.showSeoSettings = false;
      }
    },
    onElementCopied(event) {
      if (event === true) {
        this.showSuccess = true;
      }
      this.showCopyContentElement = false;
      this.copyItemData = {};
    },
    getItemHeight(column) {
      return 'height: ' + (100 / column.contents.length) + '%;';
    },
    setBaseSettingValue(value, property) {
      this.$emit('updateParent', {value: value, property: property});
    },
    setBaseSettings() {
      this.showSeoSettings = false;
      this.showBaseSettings = !this.showBaseSettings;
    },
    setSeoSettings() {
      this.showSeoSettings = !this.showSeoSettings;
      this.showBaseSettings = false;
    },
    addRowColumn(row) {
      row.columns.push({
        colSpan: "col-span-12",
        contents: [],
        position: row.columns.lenght,
        row: {
          id: row.id
        }
      })
    },
    updateMultiValue(event) {
      this.onUpdateItemChange(event.event, event.element);
    },
    selectMultiItemValue(event) {
      this.handleEditValue(event.event, event.element);
    },
    insertChildItem(event, element) {
      this.itemSelectionIndex = event.index;
      console.log("insertChildItem : " + this.itemSelectionIndex);
      this.multiItemParent = element;
      this.isMultiItemSelection = true;
      this.showItemSelection = true;
    },
    showItemSelectionModal(index) {
      this.itemSelectionIndex = index;
      console.log("showItemSelectionModal : " + this.itemSelectionIndex);
      this.showItemSelection = true;
    },
    showChildItemSelectionModal(event, parentIndex) {
      this.itemSelectionColumn = event.column;
      this.itemSelectionIndex = event.index;
      console.log("showChildItemSelectionModal : " + this.itemSelectionIndex);
      this.multiItemParent = event.parent;
      this.multiItemParentIndex = parentIndex;
      this.showItemSelection = true;
      this.isMultiItemSelection = true;
    },
    handleEditValue(event, element) {
      this.editData = {};
      this.editDataItem = {};
      this.editDataConfig = {};
      this.showEditData = false;
      this.showEditItemData = false;
      if (event.tab) {
        for (let count = 0; count <= element.module.tabs[parseInt(event.tab) - 1].fields.length - 1; count++) {
          if (element.module.tabs[parseInt(event.tab) - 1].fields[count].identifier === event.identifier.toString().replace(/["']/g, "")) {
            this.editData = element.module.tabs[parseInt(event.tab) - 1].fields[count];
            this.editDataItem = element;
            this.editDataConfig = element.module.tabs[parseInt(event.tab) - 1].fields[count].config;
            this.showEditData = true;
            this.showEditItemData = false;
          }
        }
      } else {
        for (let count = 0; count <= element.module.fields.length - 1; count++) {
          if (element.module.fields[count].identifier === event.identifier.toString().replace(/["']/g, "")) {
            this.editData = element.module.fields[count];
            this.editDataItem = element;
            this.editDataConfig = element.module.fields[count].config;
            this.showEditData = true;
            this.showEditItemData = false;
          }
        }
      }
    },
    handleItemConfig(element, multi = false, columnIndex = 0) {
      this.showEditItemData = false;
      this.handleMultiItem = multi;
      this.multiItemParentIndex = columnIndex;
      this.editItemData = element;
      this.showEditItemData = true;
      this.editItemDataConfig = element.module;
      this.showEditData = false;
    },
    handleItemCopy(element) {
      this.showCopyContentElement = true;
      this.copyItemData = element;
    },
    getContentHeight() {
      const height = window.innerHeight - 44 - 64;
      return 'height: ' + height + 'px';
    },
    onOrderChanged() {
      for (let count = 0; count <= this.list.length - 1; count++) {
        this.list[count].position = count;
      }
      this.$emit('updateValue', this.list);
    },
    onAddedItem(event) {
      const element = event.item;
      let payload = {
        position: this.getNewItemPosition(event),
        rowColumn: this.getNewItemColumn(event),
        parentId: (this.multiItemParent === null) ? '' : this.multiItemParent.id,
        module: element,
        title: '',
        additionalClasses: []
      };
      payload[this.fieldConfig.items.itemIdentifier ?? 'site'] = {id: this.baseId};

      if (element) {
        ApiService.post(
          this.fieldConfig.items.addItemCall,
          payload
        ).then(result =>  {
          if (this.isMultiItemSelection) {
            let after = (this.list[this.multiItemParentIndex].children[this.itemSelectionColumn].length === 0) ? 0 : this.itemSelectionIndex - 1;

            if (after < 0){
              after = 0;
            }

            ItemService.insertIntoObject(
              this.list[this.multiItemParentIndex].children[this.itemSelectionColumn],
              result,
              after
            ).then(res => {
              this.list[this.multiItemParentIndex].children[this.itemSelectionColumn] = res;
              this.$emit('updateValue', this.list);
            });
          } else {
            let after = (this.list.length === 0) ? 0 : this.itemSelectionIndex - 1;

            if (after < 0){
              after = 0;
            }

            ItemService.insertIntoObject(
              this.list,
              result,
              after
            ).then(res => {
              this.list = res;
              this.$emit('updateValue', this.list);
            });
          }

          this.itemSelectionIndex = null;
          this.showItemSelection = false;
          this.isMultiItemSelection = null;
          this.$emit('updateValue', this.list);
        });
      } else {
        this.itemSelectionIndex = null;
        this.showItemSelection = false;
        this.isMultiItemSelection = null;
      }
    },
    getNewItemPosition(event) {
      if (this.isMultiItemSelection === true) {
        return this.itemSelectionIndex;
      } else {
        return (this.list.length === 0) ? event.position : event.position + 1;
      }
    },
    getNewItemColumn(event) {
      if (this.isMultiItemSelection === true) {
        return this.itemSelectionColumn;
      } else {
        return 0;
      }
    },
    addColumn(event) {
      ItemService.getItemFromNestedItems(this.list, event.element.id).then(item => {
        this.loadingItemId = item.id;
        ItemService.getItemPositionInNestedArray(this.list, item).then(positions => {
          this.insertChildItem(
            {
              index: {
                parentIndex: positions.index,
                index: item.children.length
              }
            },
            item
          )
        });
      });
    },
    addTab(event) {
      ItemService.getItemFromNestedItems(this.list, event.id).then(item => {
        this.loadingItemId = item.id;
        ApiService.post(
          this.fieldConfig.items.tabAddCall,
          { data: event.tab }
        ).then(result =>  {
          item.module.tabs.push(result);
          
          ApiService.post(
            this.fieldConfig.items.pluginConfigRefreshCall,
            { data: item }
          ).then(result =>  {
            ItemService.getItemPositionInNestedArray(this.list, result).then(positions => {
              item.rendered = result.rendered;

              if (positions.parentKey === null) {
                this.list[parseInt(positions.index)] = item;
              } else {
                this.list[parseInt(positions.parentKey)].children[parseInt(positions.index)] = item;
              }

              this.loadingItemId = '';
              this.configurationReload++;
              this.reload++;
              this.$emit('updateValue', this.list);
            });
          });
        });
      });
    },
    deleteItem() {
      ApiService.put(
        this.fieldConfig.items.deleteItemCall,
        this.editItemData
      ).then(result =>  {
        if (!this.handleMultiItem) {
          this.list.splice(this.editItemData.position, 1);
        } else {
          try {
            delete this.list[this.multiItemParentIndex].children[this.editItemData.rowColumn][this.editItemData.position]
          } catch (e) {
            this.list[this.multiItemParentIndex].children[this.editItemData.rowColumn].splice(this.editItemData.position, 1);
          }
        }
        
        this.editItemData = {};
        this.showEditItemData = false;
        this.editItemDataConfig = {};
        this.showEditData = false;
        this.loadingItemId = '';
        this.reload++;
        this.$emit('updateValue', this.list);
      });
    },
    onUpdateItemConfiguration(event) {
      ItemService.getItemFromNestedItems(this.list, event.id).then(item => {
        this.loadingItemId = item.id;
        ApiService.post(
          this.fieldConfig.items.pluginConfigRefreshCall,
          { data: item }
        ).then(result =>  {
          ItemService.getItemPositionInNestedArray(this.list, result).then(positions => {
            event.rendered = result.rendered;

            if (positions.parentKey === null) {
              this.list[parseInt(positions.index)] = event;
            } else {
              this.list[parseInt(positions.parentKey)].children[parseInt(positions.index)] = event;
            }
            this.editDataItem = event;

            this.loadingItemId = '';
            this.configurationReload++;
            this.reload++;
            this.$emit('updateValue', this.list);
          });
        });
      });
    },
    onUpdateFromConfiguration(event) {
      ItemService.getItemFromNestedItems(this.list, this.editDataItem.id).then(item => {
        this.loadingItemId = item.id;

        ApiService.post(this.fieldConfig.items.refreshCall, item).then(result => {
          ItemService.getItemPositionInNestedArray(this.list, result).then(positions => {
            if (positions.parentKey === null) {
              this.list[parseInt(positions.index)] = result;
            } else {
              this.list[parseInt(positions.parentKey)].children[parseInt(positions.index)] = result;
            }
            this.editDataItem = result;

            this.loadingItemId = '';
            this.configurationReload++;
            this.reload++;
            this.$emit('updateValue', this.list);
          });
        });
      });
    },
    onUpdateItemChange(event, element) {
      if (event.value.tab) {
        const tab = parseInt(event.value.tab);
        for (let fieldCount = 0; fieldCount <= element.module.tabs[tab - 1].fields.length - 1; fieldCount++) {
          if (element.module.tabs[tab - 1].fields[fieldCount].identifier == event.identifier.replace(/"/g, "")) {
            element.module.tabs[tab - 1].fields[fieldCount].value = event.value.value;
          }
        }

        this.$emit('updateValue', this.list);
      } else {
        for (let fieldCount = 0; fieldCount <= element.module.fields.length - 1; fieldCount++) {
          if (element.module.fields[fieldCount].identifier == event.identifier.replace(/"/g, "")) {
            element.module.fields[fieldCount].value = event.value.value;
          }
        }
        
        this.$emit('updateValue', this.list);
      }
    },
    async getData() {
      if (this.fieldConfig.getCall) {
        ApiService.get(
          this.fieldConfig.getCall
        ).then(result =>  {
          this.list = Object.values(result);
          this.isLoading = false;
        });
      } else {
        this.list = Object.values(this.fieldValue);
        this.isLoading = false;
      }
    }
  },
})
</script>

<style scoped>
.ghost {
  min-height: 12rem;
  opacity: 0.5;
  background: #c8ebfb;
}
</style>