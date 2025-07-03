<template>
  <section>
    <div v-if="selections.length > 0">
      <div class="bh-datatable bh-antialiased bh-relative bh-text-black bh-text-sm bh-font-normal">
        <div class="datatable relative overflow-x-auto shadow-md sm:rounded-lg">
          <table class="w-full text-sm text-left text-gray-400">
            <thead class="text-xs">
              <tr>
                <th
                  scope="col"
                  class="px-6 py-3"
                >
                  <span>{{ $t('TITLE') }}</span>
                </th>
                <th
                  scope="col"
                  class="px-6 py-3"
                >
                  <span>{{ $t('ACTIVE') }}</span>
                </th>
              </tr>
            </thead>
            <tbody>
              <tr
                v-for="(selection, selectionIndex) in selections"
                :key="selectionIndex"
                class="bg-white border-b hover:bg-gray-50"
              >
                <td
                  scope="row"
                  class="px-6 py-4 font-medium text-gray-400 whitespace-nowrap"
                >
                  {{ selection.title }}
                </td>
                <td class="px-6 py-4">
                  <div class="flex items-start">
                    <input
                      :id="selectionIndex"
                      type="checkbox"
                      :checked="isChecked(selection)"
                      :model="selection"
                    >
                    <label
                      class="checkbox-label"
                      :class="{ active: isChecked(selection) }"
                      :for="id"
                      @click="selectItem(selection)"
                    >
                      <span
                        v-if="isChecked(selection)"
                        class="ms-3 text-sm font-medium text-dark"
                      >{{ $t('TRUE') }}</span>
                      <span
                        v-else
                        class="ms-3 text-sm font-medium text-dark"
                      >{{ $t('FALSE') }}</span>
                    </label>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </section>
</template>

<script>
import ApiService from '@SmugAdministration/js/services/api/api.service';

export default {
  name: "TableSelect",
  components: {
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
    fieldPlaceholder:{
      type: String,
      required: false,
      default: 'IMAGE_GALLERY_PLACEHOLDER'
    }
  },
  data() {
    return {
      selections: [],
      selectedItems: []
    };
  },
  mounted() {
    this.getData();
  },
  methods: {
    removeItem(item) {
      const index = this.selectedItems.findIndex(x => x.id === item.id);
      if (index > -1) {
        this.selectedItems.splice(index, 1);
        this.$emit('updateValue', this.selectedItems);
      }
    },
    selectItem(item) {
      if (item !== '') {
        const index = this.selectedItems.findIndex(x => x.id === item.id);
        if (index > -1) {
          this.removeItem(item);
        } else {
          this.selectedItems.push(item);
        }
        this.$emit('updateValue', this.selectedItems);
      }
    },
    isChecked(item) {
      const index = this.selectedItems.findIndex(x => x.id === item.id);
      return (index > -1);
    },
    getValue(value) {
      return JSON.stringify(value);
    },
    getData() {
      this.isLoading = true;
      if (this.fieldConfig.getCall) {
        if (this.fieldConfig.id) {
          ApiService.get(this.fieldConfig.model.getCall, this.fieldConfig.id)
            .then(result =>  {
              this.selectedItems = result;
              this.isLoading = false;
            })
            .catch(error => {
              this.isLoading = false;
            })
            .then(function () {
            });
        }
      } else {
        this.selectedItems = this.fieldValue;
      }

      ApiService.get(this.fieldConfig.selections.getCall)
        .then(result =>  {
          this.selections = result;
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