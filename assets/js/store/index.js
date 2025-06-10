import { createStore } from "vuex";
import { auth } from "./auth.module";
import { responsibleAuth } from "./responsible.auth.module";
const store = createStore({
  modules: {
    auth,
    responsibleAuth
  },
});
export default store;
