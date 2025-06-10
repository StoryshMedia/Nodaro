<template>
  <section>
    <p
      class="font-semibold text-lg pb-2"
    >
      {{ $t('SITE_SCRIPTS') }}
    </p>
    <div class="grid grid-cols-1 md:grid-cols-4 gap-5 mt-8 mb-4">
      <div
        class="text-center cursor-pointer md:col-span-1 text-white rounded-lg bg-dark bg-opacity-50 py-2"
        @click="showAddScript = !showAddScript"
      >
        {{ $t('ADD_SCRIPT') }}
      </div>
    </div>
    <div
      v-if="showAddScript === true"
      class="mb-8"
    >
      <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
        <div>
          <select
            class="form-select text-dark"
            :model="newScript.script"
            @change="setNewScript($event)"
          >
            <option value="">
              {{ $t('PLEASE_CHOOSE') }}
            </option>
            <option
              v-for="(script, scriptIndex) in availableScripts"
              :key="scriptIndex"
              :value="getValue(script)"
            >
              <span>
                {{ $t(script.title) }}
              </span>
            </option>
          </select>
        </div>
        <div>
          <select
            class="form-select text-dark"
            :model="newScript.area"
            @change="setNewScriptArea($event)"
          >
            <option value="">
              {{ $t('PLEASE_CHOOSE') }}
            </option>
            <option
              v-for="(area, areaIndex) in areas"
              :key="areaIndex"
              :value="getValue(area)"
            >
              <span>
                {{ $t(area.title) }}
              </span>
            </option>
          </select>
        </div>
        <div>
          <button
            type="button"
            class="btn btn-success mr-3"
            @click="addNewSiteScript()"
          >
            <p>
              {{ $t('SAVE') }}
            </p>
          </button>
        </div>
      </div>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-4 gap-5 my-5">
      <div>
        <p
          class="font-semibold text-sm pb-2"
        >
          {{ $t('HEADER_TOP_SCRIPTS') }}
        </p>
        <div class="w-full">
          <drag-list
            :items="siteScripts.ht"
            :list-name="'ht'"
            :sub-identifier="'script'"
            :config="config"
            @control-emitted="getData()"
            @order-changed="onOrderChanged($event, 'ht')"
          />
        </div>
      </div>
      <div>
        <p
          class="font-semibold text-sm pb-2"
        >
          {{ $t('HEADER_BOTTOM_SCRIPTS') }}
        </p>
        <div class="w-full">
          <drag-list
            :items="siteScripts.hb"
            :list-name="'hb'"
            :sub-identifier="'script'"
            :config="config"
            @control-emitted="getData()"
            @order-changed="onOrderChanged($event, 'hb')"
          />
        </div>
      </div>
      <div>
        <p
          class="font-semibold text-sm pb-2"
        >
          {{ $t('FOOTER_TOP_SCRIPTS') }}
        </p>
        <div class="w-full">
          <drag-list
            :items="siteScripts.ft"
            :list-name="'ft'"
            :sub-identifier="'script'"
            :config="config"
            @control-emitted="getData()"
            @order-changed="onOrderChanged($event, 'ft')"
          />
        </div>
      </div>
      <div>
        <p
          class="font-semibold text-sm pb-2"
        >
          {{ $t('FOOTER_BOTTOM_SCRIPTS') }}
        </p>
        <div class="w-full">
          <drag-list
            :items="siteScripts.fb"
            :list-name="'fb'"
            :sub-identifier="'script'"
            :config="config"
            @control-emitted="getData()"
            @order-changed="onOrderChanged($event, 'fb')"
          />
        </div>
      </div>
    </div>
  </section>
</template>

<script>
import ApiService from '@SmugAdministration/js/services/api/api.service';
import { defineAsyncComponent } from 'vue';
const DragList = defineAsyncComponent(() =>
  import("../list/DragList.vue" /* webpackChunkName: "administration-list-drag-list" */)
);

export default {
  name: "ScriptSelect",
  components: {
    DragList
  },
  props: {
    baseId:{
      type: String,
      required: false,
      default: null
    }
  },
  data() {
    return {
      config: {
        controls: [
          {
            'type': 'function',
            'config': {
              'confirm': true,
              'text': 'DELETE_CONFIRMATION_TEXT',
              'headline': 'DELETE_CONFIRMATION_HEADLINE',
              'icon': 'IconTrashLines',
              'call': '/be/api/smug/frontend/siteScript/delete'
            }
          },
          {
            'type': 'function',
            'config': {
              'confirm': true,
              'text': 'ADOPT_SITE_SCRIPT_TEXT',
              'headline': 'ADOPT_SITE_SCRIPT_HEADLINE',
              'icon': 'IconPaper',
              'call': '/be/api/custom/siteScript/adopt'
            }
          }
        ]
      },
      newScript: {
        site: {
          id: this.baseId
        },
        script: {},
        area: 0
      },
      areas: [
        {
          title: 'HEADER_TOP_SCRIPTS',
          area: 0
        },
        {
          title: 'HEADER_BOTTOM_SCRIPTS',
          area: 1
        },
        {
          title: 'FOOTER_TOP_SCRIPTS',
          area: 2
        },
        {
          title: 'FOOTER_BOTTOM_SCRIPTS',
          area: 3
        }
      ],
      showAddScript: false,
      availableScripts: [],
      siteScripts: {
        ht: [],
        hb: [],
        ft: [],
        fb: []
      }
    };
  },
  mounted() {
    this.getData();
  },
  methods: {
    setNewScript(event) {
      this.newScript.script = JSON.parse(event.target.value);
    },
    setNewScriptArea(event) {
      const area = JSON.parse(event.target.value);
      this.newScript.area = area.area;
    },
    addNewSiteScript() {
      ApiService.post('/be/api/smug/frontend/siteScript/add', this.newScript).then(result => {this.getData()});
    },
    getValue(value) {
      return JSON.stringify(value);
    },
    onOrderChanged(items, position) {
      this.siteScripts[position] = items;
      ApiService.put('/be/api/custom/site/script/reorder', this.siteScripts[position]);
    },
    getData() {
      ApiService.get('/be/api/smug/frontend/script').then(result => {
        const scripts = [];

        for (let count = 0; count <= result.length - 1; count++) {
          if (result[count].installed === true && result[count].active === true) {
            scripts.push(result[count]);
          }

          if (count === result.length - 1) {
            this.availableScripts = scripts;
          }
        }
      });
      ApiService.get(
        '/be/api/custom/site/scripts/',
        this.baseId
      ).then(result =>  {
        for (let count = 0; count <= result.length - 1; count++) {
          switch (result[count][0].area) {
          case 0:
            this.siteScripts.ht = result[count];
            break;
          case 1:
            this.siteScripts.hb = result[count];
            break;
          case 2:
            this.siteScripts.ft = result[count];
            break;
          case 3:
            this.siteScripts.fb = result[count];
            break;
          default:
            break;
          }
        }
      });
    }
  }
}
</script>