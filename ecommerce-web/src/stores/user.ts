import { defineStore } from 'pinia';
import { UserState, Menu, ProfileState } from 'src/boot/interfaces';


const user = () => ({
  optimus_id: 0,
  name: '',
  mobile: '',
  token: '',
  userMenu: []
});
export const useUserStore = defineStore('user', {
  state: (): UserState => ({
    profile: user(),
    user: user()
  }),
  getters: {
    isLoggedIn: (state) => {
      if (state.profile.token) {
        return true;
      }
      return false;
    },
  },
  actions: {
    setProfile(payload: ProfileState) {
      this.profile = payload;
    },
    setUser(payload: ProfileState) {
      this.user = payload;
    },
    resetProfile() {
      this.profile = user()
    }
  },
  persist: true,
});
