import { defineStore } from 'pinia'

export const useSidebarStore = defineStore('sidebar', {
  state: () => ({
    visible: true,
    unfoldable: false,
  }),
  actions: {
    toggleVisible(value) {
      if (value === undefined) {
        this.visible = !this.visible
      } else {
        this.visible = value
      }
    },
    toggleUnfoldable() {
      this.unfoldable = !this.unfoldable
    },
    hide() {
      this.visible = false
    },
    show() {
      this.visible = true
    },
  },
  persist: {
    enabled: true,
    strategies: [
      {
        key: 'sidebar-state',
        storage: localStorage,
        paths: ['visible', 'unfoldable'],
      },
    ],
  },
}) 