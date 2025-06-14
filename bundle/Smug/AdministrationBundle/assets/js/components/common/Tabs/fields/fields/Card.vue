<template>
  <div>
    <div
      v-if="showCard() === true"
      class="md:w-80 w-full bg-white shadow-lg rounded border border-gray-400 "
    >
      <div class="py-7 px-6">
        <div class="-mt-7 mb-7 -mx-6 rounded-tl rounded-tr h-52 overflow-hidden">
          <img
            :src="getImage()"
            alt=""
            class="w-full h-full object-cover"
          >
        </div>
        <div
          v-if="fieldConfig.badges"
          class="flex flex-row"
        >
          <badge
            v-for="(badge, badgeindex) in getBadges()"
            :key="badgeindex"
            :title="badge.title"
            :type="badge.type"
          />
        </div>
        <h5 class="text-dark text-xl font-semibold mb-4">
          {{ getHeadlineOutput(item[fieldConfig.headlineIdentifier]) }}
        </h5>
        <div class="h-14">
          <p
            class="text-white-dark"
            v-html="getOutput(item[fieldConfig.descriptionIdentifier])"
          />
        </div>
        <div
          class="
            bg-gray-400  inset-x-0 top-0 mx-auto my-3
          "
          style="height: 1px"
        />
        <div
          class="
            relative
            flex
            justify-between
          "
        >
          <a
            v-if="fieldConfig.buttonLink"
            :href="getLink()"
            target="_blank"
            class="flex items-center font-semibold"
          >
            <div class="w-9 h-9 rounded-full overflow-hidden inline-block ltr:mr-2 rtl:ml-2.5">
              <span class="flex w-full h-full items-center justify-center bg-dark text-white">
                AG
              </span>
            </div>
            <div class="text-dark pl-3">
              {{ $t(fieldConfig.buttonText) }}
            </div>
          </a>
          <div class="flex font-semibold">
            <button
              v-if="fieldConfig.edit"
              type="button"
              class="btn rounded-full w-9 h-9 mr-2 p-0 text-dark transition-all duration-300 mr-3"
              @click="setEdit()"
            >
              <icon :icon-string="'IconPencil'" />
            </button>
            <button
              v-if="fieldConfig.delete"
              type="button"
              class="btn rounded-full w-9 h-9 mr-2 p-0 text-dark transition-all duration-300"
              @click="showConfirmationClick()"
            >
              <icon :icon-string="'IconTrash'" />
            </button>
          </div>
        </div>
      </div>
    </div>
    <div
      v-if="(Object.keys(item).length === 0) && loaded === true"
      class="md:w-80 w-full bg-white shadow-lg rounded border border-gray-400 "
    >
      <div class="py-7 px-6">
        <div class="-mt-7 mb-7 -mx-6 rounded-tl rounded-tr h-52 overflow-hidden">
          <img
            :src="getImage()"
            alt=""
            class="w-full h-full object-cover"
          >
        </div>
        <h5 class="text-dark text-xl font-semibold mb-4">
          {{ $t('NO_DATA_CARD_HEADLINE') }}
        </h5>
        <div class="h-14">
          <p
            class="text-white-dark"
          >
            {{ $t('NO_DATA_CARD_TEXT') }}
          </p>
        </div>
        <div
          class="
            bg-gray-400  inset-x-0 top-0 mx-auto my-3
          "
          style="height: 1px"
        />
        <div
          class="
            relative
            flex
            justify-between
          "
        >
          <div />
          <div class="flex font-semibold">
            <button
              v-if="fieldConfig.edit"
              type="button"
              class="btn rounded-full w-9 h-9 mr-2 p-0 text-dark transition-all duration-300 mr-3"
              @click="setEdit()"
            >
              <icon :icon-string="'IconPencil'" />
            </button>
          </div>
        </div>
      </div>
    </div>
    <confirm
      v-if="showConfirmation === true"
      :text="fieldConfig.delete.text"
      :discard-button-text="(fieldConfig.delete.discardButtonText) ? fieldConfig.delete.discardButtonText : 'NO'"
      :confirm-button-text="(fieldConfig.delete.confirmButtonText) ? fieldConfig.delete.confirmButtonText : 'YES'"
      :modal-headline="fieldConfig.delete.headline"
      @confirm-reaction="handleConfirmValue($event)"
    />
    <data-modal
      v-if="showEdit === true"
      :item-data="item"
      :template="fieldConfig.edit.template.data"
      :config="fieldConfig.edit.template.config"
      :save-button-text="'SAVE'"
      :abort-button-text="'ABORT'"
      :headline="fieldConfig.edit.template.headline"
      @edit-reaction="handleAction($event)"
    />
  </div>
</template>

<script>
import { defineAsyncComponent } from "vue";
import ImageService from '@SmugAdministration/js/services/image/image.service';
import TextService from '@SmugAdministration/js/services/text/text.service';
import ConditionService from '@SmugAdministration/js/services/condition/condition.service';
import ValueService from '@SmugAdministration/js/services/value/value.service';
const Icon = defineAsyncComponent(() =>
  import("@core/js/icons/Icon.vue" /* webpackChunkName: "icon" */)
);
const Confirm = defineAsyncComponent(() =>
  import("../../../Modal/Confirm.vue" /* webpackChunkName: "administration-modal-confirm" */)
);
const DataModal = defineAsyncComponent(() =>
  import("../../../Modal/DataModal.vue" /* webpackChunkName: "administration-modal-dialog" */)
);
const Badge = defineAsyncComponent(() =>
  import("./additional/badge/Badge.vue" /* webpackChunkName: "administration-badge" */)
);

export default {
  name: "Card",
  components: {
    Icon,
    Badge,
    DataModal,
    Confirm
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
    fieldConfig:{
      type: Object,
      required: false,
      default: () => ({})
    },
    fieldPlaceholder:{
      type: String,
      required: false,
      default: 'TEXT_PLACEHOLDER'
    }
  },
  data() {
    return {
      item: {},
      showEdit: false,
      showConfirmation: false,
      loaded: false
    };
  },
  mounted() {
    this.item = ValueService.getValue(this.fieldValue, this.fieldConfig);
    this.loaded = true;
  },
  methods: {
    showCard() {
      return (this.item !== null && (Object.keys(this.item).length > 0 && this.item.constructor === Object));
    },
    getBadges() {
      let itemBadges = [];

      for (let count = 0; count <= this.fieldConfig.badges.length - 1; count++) {
        if (typeof this.fieldConfig.badges[count].condition === 'undefined') {
          itemBadges.push(this.fieldConfig.badges[count]);
          continue;
        }

        if (ConditionService.check(this.fieldConfig.badges[count], this.item)) {
          itemBadges.push(this.fieldConfig.badges[count]);
        }

        if (count === this.fieldConfig.badges.length - 1) {
          return itemBadges;
        }
      }
    },
    getOutput(value) {
      return TextService.getOutput(value);
    },
    getHeadlineOutput(value) {
      return TextService.getOutput(value, 25);
    },
    getLink() {
      if (this.fieldConfig.linkIdentifier) {
        if (typeof this.fieldConfig.linkIdentifier === 'string' && this.fieldConfig.linkIdentifier === 'media') {
          return process.env.frontendURL + '/' + this.item.path + this.item.file + '.' + this.item.extension;
        }
      }

      return process.env.frontendURL + this.fieldConfig.buttonLink + this.item.id;
    },
    setEdit() {
      this.showEdit = !this.showEdit;
    },
    showConfirmationClick() {
      this.showConfirmation = !this.showConfirmation;
    },
    handleConfirmValue(event) {
      if (event === true) {
        this.$emit('removeValue', {event: 'remove', item: this.item});
      }
      this.showConfirmationClick();
    },
    handleAction(event) {
      if (event.item) {
        this.item = event.item;
        this.$emit('updateValue', this.item);
      }
      this.setEdit();
    },
    getImage() {
      return ImageService.getImageFromItem(this.item);
    }
  }
}
</script>