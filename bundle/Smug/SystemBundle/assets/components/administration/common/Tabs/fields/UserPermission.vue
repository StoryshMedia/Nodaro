<template>
  <div
    v-if="loaded === true"
    class="space-y-2 font-semibold"
  >
    <div
      v-for="(bundle, permissionindex) in permissions"
      :key="permissionindex"
    >
      <Permission
        :bundle="bundle"
        :field-config="fieldConfig"
        @update-value="setContent($event, permissionindex)"
      />
    </div>
  </div>
</template>

<script>
import { defineAsyncComponent } from "vue";
const Permission = defineAsyncComponent(() =>
  import("./additional/permission/Permission.vue" /* webpackChunkName: "user-group-permission" */)
);

export default {
  name: "UserPermission",
  components: {
    Permission
  },
  props: {
    editAllowed:{
      type: Boolean,
      required: true
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
      default: 'TEXT_PLACEHOLDER'
    }
  },
  data() {
    return {
      loaded: false,
      permissions: []
    };
  },
  mounted() {
    this.getData();
  },
  methods: {
    setContent(event, index) {
      this.permissions[index] = event;
      this.$emit('updateValue', this.permissions);
    },
    isDisabled() {
      if (this.editAllowed === false) {
        return true;
      }
      if (this.fieldConfig.disabled && this.fieldConfig.disabled === true) {
        return true;
      }
    },
    getData() {
      const orderedPermissions = Object.groupBy(this.fieldValue, ({ type }) => type);

      for (let count = 0; count <= Object.keys(orderedPermissions).length - 1; count++) {
        this.permissions.push(orderedPermissions[Object.keys(orderedPermissions)[count]]);

        if (count === Object.keys(orderedPermissions).length - 1) {
          this.loaded = true;
        }
      }
    }
  }
}
</script>